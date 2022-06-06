#! /usr/bin/env bash 

APPDIR=/p/webcam/javascript/tests/juice-shop2/app
CONFIG_JSON=${APPDIR}/../test_data.json

export LD_LIBRARY_PATH=/p/webcam/libs
#export AFL_PRELOAD=/p/webcam/libs/libcodetracer.so
export AFL_PRELOAD=/p/webcam/libs/libcgiwrapper.so
export WC_BINARY=/p/webcam/javascript/node/node

echo Using $LD_LIBRARY_PATH and $AFL_PRELOAD


if [ "$1" = "--afl" ]; then
    rm -f /tmp/wrapper*
fi
# trap ctrl-c and call ctrl_c()
trap ctrl_c INT

function ctrl_c() {
        echo "** Trapped CTRL-C, exiting..."
        exit 34
}

#cut_length=$(grep -zPo '(.*).*\n\K\1' /p/webcam/apps/wackpicko/files.dat|wc -c)
cut_length=$(<files.dat LC_ALL=C sort | sed -n 'N;h;s/^\(.*\).*\n\1.*/\1/p;g;D'|xargs -L1 -I% bash -c "echo %|wc -c"|sort -n|head -1)
cut_length=$((${cut_length}-1))
if [[ ! -d ${APPDIR}/fin_outs ]]; then
    mkdir ${APPDIR}/fin_outs
fi
if [[ ! -d /tmp/output ]]; then
    mkdir /tmp/output
    mkdir /tmp/output-mini
fi

copycnt=$(printf "%04d" "$(ls ${APPDIR}/fin_outs|wc -l)")

# if more than 10 users in DB then DB has already been used.
#usercnt=$(echo "select count(*) from users" | sudo mysql wackopicko|tail -1)
#if [[ ${usercnt} -gt 10 ]]; then
#    printf "\e[33m ALERT mysql has additional Rows in it, waiting 10 seconds to abort, press ctrl-c to abort.\e[0m \n"
#    printf '\t\e[32m cd /p/webcam/apps/wackpicko/code/ && echo "drop database wackopicko;drop user wackopicko;"|sudo mysql && sudo /p/webcam/apps/wackpicko/code/create_mysql_admin_user.sh && sudo /etc/init.d/mysql start && echo "show databases" | sudo mysql | grep "wackopicko" && rm /tmp/save_*; cd -; \e[0m\n\n'
#    sleep 1
#fi

# if any files in /tmp/trimmed then probably should abort and retry

# start cleaner program
mkdir -p /tmp/cleaner
/p/webcam/libs/cleaner.sh touts >> /tmp/cleaner/clean.log &

touch /tmp/save_11deadcode22deadcode33deadcode44

printf "Copy count for this run is ${copycnt}\n"
cd $APPDIR
for loopcnt in {1..3}; do
    phuzzer_timeout=$(( 60*5*6 ))
    do_resume=""
    if [[ ${loopcnt} -eq 1 ]]; then
        phuzzer_timeout=$(( 60*5*6 ))
    fi
    export STRICT=1
    filecnt=0
    for pscript in $(cat ${APPDIR}/../files.dat | shuf); do

        filecnt=$(( ${filecnt} + 1 ))
        export DOCUMENT_ROOT="/app"
        export SCRIPT_FILENAME="/app/interface/main/main_screen.php"
        unset LOGIN_COOKIE
        #
        export SCRIPT_FILENAME=${pscript}

        if [ "$1" = "--afl" ]; then
            if grep "${pscript} ${loopcnt}" files-processed.dat > /dev/null; then
                echo "Skipping ${SCRIPT_FILENAME} ${loopcnt}";
                continue
            fi
            export NODE_BASE_URL=${pscript}
            export AFL_BASE="/tmp/output/$(echo js${NODE_BASE_URL}|sed 's/\//-/g')"
            export PORT=5144
            printf  "Fuzzing #${filecnt} \e[32m ${SCRIPT_FILENAME} \e[0m loop:${loopcnt} \n"

            #node ../prepare_AFL.js $(pwd) $(basename ${SCRIPT_FILENAME})
            printf "\tSeeding with $(ls ../input/seed*|wc -l) files"

            # creating formmated name by removing the common elements from all the paths and replacing / with +
            fname=$(echo ${pscript:cut_length}|sed 's/\//+/g')

            # where the AFL output folder will go after this run completes
            final_output_loc=${APPDIR}/fin_outs/WT_${copycnt}_${fname}

            # does a prior run exist with this copycnt? if so, copy it and set phuzzer to resume
            if [[ -f "$final_output_loc/fuzzer-master.log" ]];then
                do_resume="--resume"
                printf "\t\e[32m Found prior output, doing resume.\e[0m \n"
                rm -rf /tmp/output
                cp -r $final_output_loc/. /tmp/output
                if [[ $? -eq 0 ]]; then
                    do_resume="--resume"
                    printf "\t\e[31mCopied in prior stuff and doing phuzzer RESUME...\e[0m\n"
                    #echo "sleeping 20"
                    #sleep 20
                else
                    printf "\t\e[33m UNABLE TO DO RESUME, file copy failed....\e[0m \n"
                    exit 33
                fi
            else
               if [[ ${copycnt} -ne 1 ]];then
                    printf "\t\e[33m UNABLE TO DO RESUME, fuzzer-master.log not found....\e[0m \n"
               fi
            fi

            #cleaning up for prior runs
            rm  -f /tmp/wrapper*

            # do we have a default session that's larger than the saved session?  if so, save that session off and cleanup all the sessions.
#            save_dead_size=`stat -c%s "/tmp/save_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
#            sess_dead_size=`stat -c%s "/tmp/sess_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
#            if [[ ${sess_dead_size} -gt ${save_dead_size} ]]; then
#                cp /tmp/sess_11deadcode22deadcode33deadcode44 /tmp/save_11deadcode22deadcode33deadcode44
#                save_dead_size=`stat -c%s "/tmp/save_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
#            else
#                printf "\tDefault session was not larger than saved session %s v %s.\n" "${sess_dead_size}" "${save_dead_size}"
#            fi
#
#            if [[ save_dead_size -gt 0 ]]; then
#                printf "\t\e[33mSAVED session contains data %d \e[0m\n" "${save_dead_size}"
#            else
#                printf "\t\e[31mNO data in saved session\e[0m\n"
#            fi

            # using this find means we don't need the session_default -> save, but keeping both just in case.
            #printf "\tCleaning up sessions ...\n"
            #find /tmp -size +0c -type f -name 'sess_11deadcode22deadcode33deadcode44' -exec cp {} /tmp/sess_11deadcode22deadcode33deadcode44 \;

            #cp /tmp/save_11deadcode22deadcode33deadcode44 /tmp/sess_11deadcode22deadcode33deadcode44
            #find /tmp -size 0 -type f -name 'sess_*' -delete > /dev/null 2>&1
            #
            # alright we are ready to run this dood
            printf "\tFuzzing\e[34m %s \e[0m\n" "${SCRIPT_FILENAME}"
            CORES="60"
            echo python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded'  -t ${phuzzer_timeout} -c ${CORES} --run-timeout 20000 -w ${AFL_BASE} -l ${CONFIG_JSON} -s ${APPDIR}/../input ${WC_BINARY}
            python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded'  -t ${phuzzer_timeout} -c ${CORES} --run-timeout 20000 -w ${AFL_BASE} -l ${CONFIG_JSON} -s ${APPDIR}/../input ${WC_BINARY}
#            echo python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded' -t ${phuzzer_timeout} -c 20 --run-timeout 20000 -w ${AFL_BASE} -s ${APPDIR}/../input /p/webcam/javascript/node/node
#            python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded' -t ${phuzzer_timeout} -c 20 --run-timeout 20000 -w ${AFL_BASE} -s ${APPDIR}/../input /p/webcam/javascript/node/node

            ret=$?
            if [[ ${ret} -ne 0 ]]; then
                echo "ERROR encountered with phuzzer, aborting run"
                exit ${ret}
            fi
            #mv /tmp/trace.log ${APPDIR}/trace_$(basename ${pscript})-$(date +%Y-%m-%d-%H-%M-%S)

            if grep -Fq "All set and ready to roll" /tmp/output/fuzzer-master.log; then
                echo -e "\tAFL started up successfully"
                echo "${pscript} ${loopcnt} SUCCESS">> files-processed.dat
            else
                printf "\n\t \e[31m **********************************************************************\n\t\tAFL STARTUP FAILED\n\t\tFOR ${pscript}\n\t**********************************************************************\n\n \e[0m"
                echo "${pscript} ${loopcnt} FAILED" >> files-processed.dat
            fi
            # trick to cut last X number of elements, rev|cut -d"/" -f1,2,3,4|rev)

            # remove session files
            #perl -e 'unlink(glob("/tmp/sess_*"))'

            #rm -rf ${final_output_loc}
            printf "\t COPYING from /tmp/output to ${final_output_loc}"
            cp -r /tmp/output/. ${final_output_loc}

        else
            #export LOGIN_COOKIE="OpenEMR=l1q6kfh7n31donnq5n6df7ojkp;"
            export STRICT=true
            #unset STRICT
            echo "Running a test"
            #export SCRIPT_FILENAME="/tmp/info.php"
            #printf "\x00auth=login&site=default\x00new_login_session_management=1&authProvider=TroubleMaker&authUser=admin&clearPass=password&languageChoice=1\x00" | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php-phpipam/sapi/cgi/php-cgi |tr -d '\0'
            export PORT=5144
            export SCRIPT_FILENAME="not_used"
            export NODE_BASE_URL=$(cat ${CONFIG_JSON} |jq .direct.url|tr -d '"')
            echo $NODE_BASE_URL
            export AFL_BASE="/tmp/output/$(echo js${NODE_BASE_URL}|sed 's/\//-/g')"

            unset LOGIN_COOKIE
            #

            for i in {1..5}; do

               direct_cookie_data=$(cat ${CONFIG_JSON} |jq .direct.cookieData|tr -d '"'|sed 's/null//g')
               direct_get_data=$(cat ${CONFIG_JSON} |jq .direct.getData|tr -d '"'|sed 's/null//g')
               direct_post_data=$(cat ${CONFIG_JSON} |jq .direct.postData|tail -c +2 | head -c -2|sed 's/\\"/"/g' |sed 's/null//g')
               echo "POST=${direct_post_data}"
               method=$(cat ${CONFIG_JSON} |jq .direct.method|tr -d '"');
               #echo $(printf "\x00%s\x00%s\x00%s" "" "${direct_get_data}" "${direct_post_data}") | METHOD=${method} LD_PRELOAD=${AFL_PRELOAD} ${WC_BINARY} |tr -d '\0'
               echo ""
               echo "cat /tmp/runinp.dat | METHOD=${method} LD_PRELOAD=${AFL_PRELOAD} ${WC_BINARY} |tr -d '\0'"
               echo ""


               printf "a=0;%s\x00b=0&%s\x00%s" "${direct_cookie_data}" "${direct_get_data}" "${direct_post_data}" > /tmp/runinp.dat
               export SOURCE="/tmp/runinp.dat"
               export METHOD=${method}

               printf "Testing\e[34m %s \e[0m\n" "${METHOD} localhost:${PORT}${NODE_BASE_URL}, output @ ${AFL_BASE}, ${SOURCE}"
               export JSON_POST=true

               login_attempt=$(${WC_BINARY} app --single-threaded |tr -d '\0')
               if [[ $login_attempt == *"$(cat ${CONFIG_JSON} |jq .direct.positiveMessage|tr -d '"')"* ]]; then
                   echo "valid response, setting cookie == ${login_attempt}"
                   login_session_key=$(cat ${CONFIG_JSON} |jq .direct.loginSessionCookie|tr -d '"'|sed 's/null//g')
                   export LOGIN_COOKIE="$(printf "%s" "${login_attempt}" | egrep -aoh "Set-Cookie.*${login_session_key}.*"|tail -1 |cut -d":" -f2- |cut -d";" -f1)"
                   export BEARER="$(printf "%s" "${login_attempt}" | egrep '{"authentication.*}'|jq  .authentication.token| tr -d '"')"
                   echo "BEARER = "${BEARER}
                   break;
               fi
               printf "\e[31m;Login attempt ${i} failed, retrying!\e[0m \n"
               echo "${login_attempt}";
               echo "---------------------------------------------------------------------------------------------"
               sleep 5
               break
            done
            if [[ -z "${LOGIN_COOKIE}" &&  -z "${BEARER}" ]]; then
               echo "\e[31m Login failed too many times \e[0m \n"
               exit 99
            fi
            if [[ -z "${LOGIN_COOKIE}" ]]; then
                printf "\nI GOT ME A COOKIE at $LOGIN_COOKIE\n\n"
            else
                printf "\nI GOT ME AUTHORIZATION ${BEARER:0:10}...\n\n"
            fi
            export MANDATORY_GET=$(cat ${CONFIG_JSON} |jq .direct.mandatoryGet|tr -d '"'|sed 's/null//g')

            if [[ ${MANDATORY_GET} =~ (.*<login>.*) ]]; then
                temp=$(echo ${LOGIN_COOKIE}|cut -d "=" -f2)
                export MANDATORY_GET=${MANDATORY_GET/<login>/$temp}
            fi

            printf "\nMANDATORY_GET = %s\n" "${MANDATORY_GET}"
            NODE_BASE_URL=${pscript}
            #cat ${APPDIR}/output/fuzzer-master/queue/id:000000,orig:seed-0 | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php5.5/sapi/cgi/php-cgi
            if [ "$1" = "--input" ]; then
                echo "catrting.................. $2"
                chmod 777 /tmp/s*$(echo $LOGIN_COOKIE|egrep -oh "[a-z0-9]{26}")
                ls -la /tmp/s*$(echo $LOGIN_COOKIE|egrep -oh "[a-z0-9]{26}")
                echo "HIUHI"
                if [[ -z "$3" ]]; then
                    export NODE_BASE_URL=${pscript}
                    ${WC_BINARY} app --single-threaded
                else
                    export ${NODE_BASE_URL}=$3
                    echo "SCRIPT_FILENAME = ${SCRIPT_FILENAME}"
                    ${WC_BINARY} app --single-threaded
                fi
                exit 0
            elif [[ "$1" = "--mini" ]];then
                if [[ ! -z "$3" ]]; then
                    SCRIPT_FILENAME=$3
                fi
                rm -rf /tmp/output-mini/fuzzer-master

                /p/afl/afl-fuzz -i /tmp/output-mini/initial_seeds -o /tmp/output-mini -m 8G -M fuzzer-master -x /tmp/output-mini/dict.txt -t 10000+ -- ${WC_BINARY}
                exit 0
            else
                #export SCRIPT_FILENAME="/app/interface/main/main_screen.php"
                printf "SCRIPT_FILENAME=${SCRIPT_FILENAME}\n\n"
                #printf "cooky=1\x00qs=2\x00postdata=3" | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php-phpipam/sapi/cgi/php-cgi

                ${WC_BINARY} app --single-threaded
                echo " "
            fi
            break
            continue
        fi

    #    rm ${APPDIR}/summary/*
    #    for f in /tmp/trace_out*; do
    #        cat $f |sort |uniq > ${APPDIR}/summary/trace_sums_$(basename ${f})-$(date +%Y-%m-%d-%H-%M-%S)
    #    done
    #
    #    for f in ${APPDIR}/summary/trace_sums*; do
    #        printf '%-22s %4s\n' "$(echo $f|cut -d '_' -f 10,11,12,13|sed 's/_/\//g' )"  "$(cat $f|wc -l)"
    #    done

#        echo "cleaning up remaining sessions that have a size of 0"
#        find /tmp -size 0 -type f -name 'sess_*'  -delete > /dev/null 2>&1
        echo "~ FINISHED  with ${pscript} ~"

    done

done # loopcnt

pkill -f '/p/webcam/libs/cleaner.sh'

printf "\n\e[32mDONE\e[0m\n"



