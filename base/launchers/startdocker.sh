#!/usr/bin/env bash

wc_type=$1
if [[ -z $wc_type ]]; then
    echo "The type is required, php7, php5, cgi, python, javascript "
    exit 8
fi
shift

appname=$1
if [[ -z $appname ]]; then
    echo "The name of the application must be supplied"
    exit 9
else
    wc_root=$(cd "$(dirname $0)/.." && pwd)
    if [[ ! -d ${wc_root}/${wc_type}/tests/${appname} ]]; then
        echo "The path '${wc_root}/${wc_type}/tests/${appname}' does not exist, please adjust arguments 1 and 2 and try again "
    fi
fi
shift

testver=$1
if [[ -z $testver ]]; then
    testver="EXWICHR"
fi

WC_LIST_TESTVERS="AFLR AFLHR WIC WICH WICR WICHR EXWIC EXWICR EXWICH EXWICHR "
if [[ ${WC_LIST_TESTVERS} =~ (^|[[:space:]])${testver}($|[[:space:]]) ]]; then
      echo "Using $testver featureset"
else
  echo "The 3rd argument is for the featureset to use but '$testver' is not in the list of known featuresets"
  echo "    The acceptable featuresets include ${WC_LIST_TESTVERS}"
  exit 32
fi
shift

apptestdir_at="/p/Witcher/$wc_type/tests/$appname"
echo "Found apptestdir_at : $apptestdir_at"

function get_config_val(){

    got_val=$(cat ${apptestdir_at}/test_data.json |jq .${1}|tr -d '"'|sed 's/null//g')

    if [[ -z ${got_val} ]]; then
        echo "${2}"
    else
        echo "${got_val}"
    fi
}

cores=$(get_config_val "cores" 30)
cores=8
echo ${cores}
test="--test"
timeout=$(get_config_val "timeout" 14400 )
first=""
single_script=""
skip_build=false
results_at="/p/Witcher/results"
dev_options="-v /p:/p "
no_run="--no-run"
while [[ $# -gt 0 ]]
do
    key="$1"

    case $key in
        --first)
            first="--first"
        ;;
        --prod|--production)
          dev_options=""
          no_run=""
        ;;
        --cpustart|--cpu-start)
            cpu_start=$2
            echo "Using provided argument and cpu_start is now ${cpu_start}"
            shift
        ;;
        --single|--single-script|target)
            single_script="--target $2"
            shift
        ;;
        --skip-build)
            skip_build=true
            shift
        ;;
        --results-at)
            results_at=$2
            shift
        ;;
        --kill)
            if [[ $(docker ps --format "{{.Names}} " | grep "wc-${wc_type}-${appname}-${testver}"| wc -l) -gt 1 ]]; then
                if [[ -z ${2} ]]; then
                    echo "error more than one container exists for the provided appplication, so kill must be followed by an identifier"
                    docker ps -q | xargs -n1 docker inspect --format "{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}} {{ .Name }}"|grep $container_name
                    exit 11
                else
                    docker kill wc-${wc_type}-${appname}-testver-${2}
                    shift
                fi
            else
                docker kill $(docker ps --format "{{.Names}} " | grep "wc-${wc_type}-${appname}-${testver}")
            fi
        ;;
        *)
           echo "Uknown flag used "
        ;;
    esac
    shift # past argument or value
done

cnt=2
current_max_ip=1
next_ip=0
for val in $(docker ps -q | xargs -n1 docker inspect --format "{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}} {{ .Name }}"|cut -d "." -f4|sort -n|cut -d " " -f1);do
    if [[ $cnt -ne $val ]]; then
        next_ip=$cnt
        break
    fi
    current_max_ip=$cnt
    cnt=$(( $cnt + 1 ))
done
if [[ ${next_ip} -eq 0 ]]; then
    next_ip=$(( ${current_max_ip} + 1 ))
fi

container_name="wc-${wc_type}-${appname}-${testver}-${next_ip}"

PORT1=$(( 8000 + next_ip ))
PORT2=$(( 5000 + next_ip ))

echo "New Container name will be : $container_name"
if [[ ${skip_build} == false ]];then
    cd ${apptestdir_at}
    docker build -t witcher/${wc_type}/${appname} .
fi

#wc_cnt=$(docker ps --format "{{.Names}} " |grep "^wc-"|wc -l)
if [[ -z cpu_start ]]; then
    cpu_start=$(( ($next_ip -2) * 10 ))
    echo "Calcuated CPU_START to be ${cpu_start}"
else
    echo "CPU start = ${cpu_start}"
fi

echo CPU_START="--affinity ${cpu_start}"

sudo chmod 777 ${results_at}

docker run -id --privileged --tmpfs /tmp --rm -p ${PORT1}:80 -p ${PORT2}:5900 --name ${container_name} \
          -v ${results_at}:/results           \
          -v ${apptestdir_at}:/test           \
          ${dev_options}                      \
          -e CONTAINER_NAME=${container_name} \
          -e WC_TEST_VER=${testver}           \
          -e WC_NO_RUN=${no_run}              \
          -e WC_FIRST=${first}                \
          -e WC_SET_AFFINITY=$cpu_start       \
          -e WC_SINGLE_SCRIPT=$single_script  \
          witcher/${wc_type}/${appname}

echo "Created container : $container_name"
