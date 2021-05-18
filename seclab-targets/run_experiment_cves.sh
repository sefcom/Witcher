#!/usr/bin/env bash

function print_help(){
    printf "USAGE: ./run_experiment_cves.sh cve# sub_test [OPTIONS]\n"
    printf "\t--delete-all \t Delete the results from last run and start fresh\n"
    printf "\t--delete-sub \t Just delete the results for this sub test \n"
    printf "\t--no-crawl   \t Skip crawling step \n"
    printf "\t--build      \t Rebuild the container \n"
    printf "\t--burp      \t Exit so that Burp can be run vs container \n"
    printf "\t--no-run     \t Do not kill and re-run the container \n"
    printf "\t--port       \t port of container  \n"
    exit 0
}

if [[ "$1" == "--help" || "$1" == "-h" ]]; then
    print_help
fi

cve=$1;
shift
plus=$1;
shift


DO_DELETE_ALL=false
DO_DELETE_SUB=false
DO_CRAWL=true
DO_DOCKER_BUILD=false
DO_DOCKER_RUN=true
DO_BURP=false
port=80

while [[ $# -gt 0 ]]
do
    key="$1"

    case $key in
        --delete_all|--delete-all)
            DO_DELETE_ALL=true
            DO_DELETE_SUB=true
        ;;
        --delete_sub|--delete-sub|--delete)
            DO_DELETE_SUB=true
        ;;
        --no_crawl|--no-crawl)
            DO_CRAWL=false
        ;;
        --build)
            DO_DOCKER_BUILD=true
        ;;
        --no-run|--no_run)
            DO_DOCKER_RUN=false
        ;;
        --burp)
            DO_BURP=true
        ;;
        --port)
            shift
            port=$1
        ;;
        -h|--help)
            print_help
        ;;
        *)
           echo "Uknown flag used "
        ;;
    esac
    shift # past argument or value
done


function _term() {
        children="$(ps -s $$ -o pid=)"

        if [ -z "$children" ];then
            printf "\n[WC] Trapped CTRL-C --> No children found  \n"
        else
            printf "\n[WC] Trapped CTRL-C --> killing all children %s \n" "$children"
            kill -9 $children
        fi
        if [ -f /tmp/witcher_exp_to.pid ];then
            timeout_pid="$(cat /tmp/witcher_exp_to.pid)"
            printf "\n[WC] Found timeout pid: %s  children: %s \n" "$timeout_pid" "$(pgrep -P $timeout_pid)"
            pkill -9 -P "$timeout_pid"
            kill -9 "$timeout_pid"
            rm -f /tmp/witcher_exp_to.pid
        fi
        exit 128
}

# trap ctrl-c and call ctrl_c()
trap _term INT TERM


if [[ ${DO_BURP} = false ]]; then
  if [ -d $cve/$plus ] && [ -f $cve/$plus/witcher_config.json ]; then
      printf "\033[32mWitcher test starting \033[0m\n"
  else

      printf "\033[31mCannot start Witcher test \033[0m\n"
      exit 1
  fi
fi

mkdir -p "$cve/$plus"

cd "$cve/$plus" || exit 254

base_url_path=$(jq .base_url_path witcher_config.json|tr -d '"')
if [[ "$base_url_path" == "null" ]]; then
    base_url_path="/"
fi

if [[ ${DO_DELETE_ALL} = true ]]; then
    rm -rf ../ccov; rm -rf ../wcov

fi
if [[ ${DO_DELETE_SUB} = true ]]; then
    rm -f request_data.json; rm -rf WICHR; rm -rf coverages; rm -rf crawl-coverages; rm -rf fuzz-coverages

    mkdir -p coverages
fi
if [[ ${DO_DOCKER_BUILD} = true ]]; then
    if docker build -t puppeteer1337/$cve .. ;then
        printf "[\033[32mWitcher\033[0m] Docker container build completed \n"
    else
        exit 253
    fi

fi

if [[ ${DO_DOCKER_RUN} = true ]]; then
    docker kill $cve-$plus; sleep 1;

    if docker run -id --rm --name $cve-$plus -v /p:/p -v /p/Witcher/seclab-targets/$cve/$plus/coverages:/tmp/coverages puppeteer1337/$cve; then
        printf "[\033[32mWitcher\033[0m] Issued docker run \n"
    else
        exit 2
    fi

    if docker exec -it $cve-$plus bash -c "chown www-data:wc /tmp/coverages; sudo chmod 777 /tmp/coverages"; then
        printf "[\033[32mWitcher\033[0m] Issued file permissions changes \n"
    else
        exit 3
    fi

    if [ -f ../setup.sh ]; then
        printf "[Witcher] running additional seutp after starting container\n"
        ipaddr=$(docker inspect $cve-$plus | jq '.[]|.NetworkSettings.Networks.bridge.IPAddress'|tr -d '"')
        ../../wait-for-tcp $ipaddr $port
        if ../setup.sh; then
            printf "[\033[32mWitcher\033[0m] Setup completed successfully\n"
        else
            printf "[\033[31mWitcher\033[0m] Setup failed  \n"
            exit 252
        fi
    fi

    if docker exec -it -w "$(pwd)" -u wc $cve-$plus bash -i -c 'touch /tmp/start_test.dat'; then
        printf "[\033[32mWitcher\033[0m] Created /tmp/start_test \n"
    else
        exit 4
    fi
fi

ipaddr=$(docker inspect $cve-$plus | jq '.[]|.NetworkSettings.Networks.bridge.IPAddress'|tr -d '"')

if [ -z $ipaddr ]; then
    printf "[\033[31mWitcher\033[0m] Failed to gather ipaddr \n"
    exit 5
else
    printf "[\033[32mWitcher\033[0m] IP address is $ipaddr \n"
fi

if [[ ${DO_BURP} = true ]]; then
    printf "[\033[32mWitcher\033[0m] Exiting to run allow Burp \n"
    exit 0
fi

if sed -r 's/(^.*form_url.*http:\/\/)[0-9\.]+(.*)/\1'"$ipaddr"'\2/' witcher_config.json > /tmp/tmpcfg.json; then
    printf "[\033[32mWitcher\033[0m] Fixed witcher_config.json \n"
else
    printf "[\033[31mWitcher\033[0m] Failed to fix witcher_config.json \n"
    exit 10
fi

cp /tmp/tmpcfg.json witcher_config.json

if [[ ${DO_CRAWL} = true ]]; then
    printf "[\033[32mWitcher\033[0m] Starting CRAWL of $cve-$plus @ http://$ipaddr$base_url_path  \n"
    timeout --signal KILL 4h execute request_crawler /p/Witcher/base/helpers/request_crawler/main.js http://$ipaddr$base_url_path "$(pwd)" &
    pid=$!
    echo $pid >> /tmp/witcher_exp_to.pid
    wait $pid

    ret=$?;
    if [ $ret -eq 137 ] || [ $ret -eq 124 ] || [ $ret -eq 0 ]; then
        mkdir -p ../ccov && find . -name '*.cc.json' -exec cp {} ../ccov \; && echo "Crawl reached completion ";
        rm -rf crawl-coverages
        mkdir -p crawl-coverages
        cp -a coverages/. crawl-coverages
    else
        printf "[\033[31mWitcher\033[0m] Failed exit crawl properly\n"
        exit 15
    fi
fi




# fuzz app
printf "[\033[32mWitcher\033[0m] Fuzz command : "
echo docker exec -it -w "$(pwd)" -u wc $cve-$plus bash -i -c 'p'


if docker exec -it -w "$(pwd)" -u wc $cve-$plus bash -i -c 'p'; then
    mkdir -p ../wcov && find . -name '*.cc.json' -exec cp {} ../wcov \; && echo "Witcher results copied "
    rm -rf fuzz-coverages
    mkdir -p fuzz-coverages
    cp -a coverages/. fuzz-coverages
else
    printf "[\033[31mWitcher\033[0m] Exited fuzzer with an error \n"
    exit 20
fi

cd .. || exit 25
archive_fn="results-$cve-$(date -d "today" +"%Y%m%d%H%M").tar.gz"

find . -name 'fuzzer-master.out' -exec truncate --size=5M {} \;

find . -name 'coverages'|cut -d "/" -f2|xargs tar cvzf "$archive_fn" wcov ccov

if [ -f $archive_fn ]; then
    tar tzf $archive_fn > /tmp/tarresults.dat
    echo "$(wc -l /tmp/tarresults.dat) files archived "
    printf "[\033[32mWitcher\033[0m] Completed succesfully \n "
else
    printf "[\033[31mWitcher\033[0m] Failed to archive results \n"
    exit 30
fi
