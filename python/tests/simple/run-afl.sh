#! /usr/bin/env bash

APPDIR=/p/webcam/apps/wp320
export LD_LIBRARY_PATH=/p/webcam/libs
#export AFL_PRELOAD=/p/webcam/libs/libcodetracer.so
export AFL_PRELOAD=/p/webcam/libs/lib_db_fault_escalator.so

trap ctrl_c INT

function ctrl_c() {
        echo "** Trapped CTRL-C"
        pids=$(pgrep -f phuzzer)
        if [[ -z ${pids} ]]; then
            echo "Phuzzer shutdown properly..."
        else
            printf "${pids}" | xargs echo "Sending SIGINT to "
            pgrep -f phuzzer|xargs kill -2
        fi
        pgrep -f cleaner.sh|xargs kill -9 2> /dev/null
        exit 32
}

echo Using $LD_LIBRARY_PATH and $AFL_PRELOAD

if [ "$1" = "--afl" ]; then
    rm -f /tmp/wrapper*
fi

cut_length=$(<${APPDIR}/files.dat LC_ALL=C sort | sed -n 'N;h;s/^\(.*\).*\n\1.*/\1/p;g;D'|xargs -L1 -I% bash -c "echo %|wc -c"|sort -n|head -1)
cut_length=$((${cut_length}-1))
copycnt=$(printf "%04d" "$(ls ${APPDIR}/fin_outs|wc -l)")

# if more than 10 users in DB then DB has already been used.


# if any files in /tmp/trimmed then probably should abort and retry
trimcnt=$(ls /tmp/trimmed/* |wc -l)
if [[ trimcnt -gt 0 ]]; then
    printf "\e[33mALERT trimmed traces currently exist, waiting 10 seconds to abort, press ctrl-c to abort.\e[0m \n"
    sleep 1
fi

# start cleaner program
mkdir -p /tmp/cleaner
/p/webcam/libs/cleaner.sh touts >> /tmp/cleaner/clean.log &

touch /tmp/save_11deadcode22deadcode33deadcode44

printf "Copy count for this run is ${copycnt}\n"

# number of cores to use for phuzzer
core_cnt=30

for loopcnt in {1..1}; do
    phuzzer_timeout=180
    do_resume=""
    if [[ ${loopcnt} -eq 1 ]]; then
        phuzzer_timeout=$((10*60*60))
    fi

    for pscript in $(cat ${APPDIR}/files.dat); do

        export SCRIPT_FILENAME=${pscript}
        echo Running for ${SCRIPT_FILENAME}

        if [ "$1" = "--afl" ]; then
            # creating formmated name by removing the common elements from all the paths and replacing / with +
            fname=$(echo ${pscript:cut_length}|sed 's/\//+/g')

            # where the AFL output folder will go after this run completes
            final_output_loc=${APPDIR}/fin_outs/WT_${copycnt}_${fname}

            # does a prior run exist with this copycnt? if so, copy it and set phuzzer to resume
            if [[ -f "$final_output_loc/fuzzer-master.log" ]];then
                do_resume="--resume"
                printf "\e[32m Found prior output, doing resume.\e[0m \n"
                rm -rf /tmp/output
                cp -r $final_output_loc /tmp/output
                if [[ $? -eq 0 ]]; then
                    do_resume="--resume"
                    printf "\e[31mCopied in prior stuff and doing phuzzer RESUME...\e[0m\n"
                    #echo "sleeping 20"
                    #sleep 20
                else
                    printf "\e[33m UNABLE TO DO RESUME, file copy failed....\e[0m \n"
                    exit 33
                fi
            else
               if [[ ${copycnt} -ne 1 ]];then
                    printf "\e[33m UNABLE TO DO RESUME, fuzzer-master.log not found....\e[0m \n"
               fi
            fi

            #cleaning up for prior runs
            rm  -f /tmp/wrapper*

            # do we have a default session that's larger than the saved session?  if so, save that session off and cleanup all the sessions.
            save_dead_size=`stat -c%s "/tmp/save_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
            sess_dead_size=`stat -c%s "/tmp/sess_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
            if [[ ${sess_dead_size} -gt ${save_dead_size} ]]; then
                cp /tmp/sess_11deadcode22deadcode33deadcode44 /tmp/save_11deadcode22deadcode33deadcode44
                save_dead_size=`stat -c%s "/tmp/save_11deadcode22deadcode33deadcode44" 2> /dev/null || echo 0`
            else
                printf "Default session was not larger than saved session %s v %s.\n" "${sess_dead_size}" "${save_dead_size}"
            fi

            if [[ save_dead_size -gt 0 ]]; then
                printf "\e[33mSAVED session contains data %d \e[0m\n" "${save_dead_size}"
            else
                printf "\e[31mNO data in saved session\e[0m\n"
            fi

            # using this find means we don't need the session_default -> save, but keeping both just in case.
            printf "\tCleaning up 0 length sessions...\n"
            find /tmp -size 0 -type f -name 'sess_*' -delete > /dev/null 2>&1
            cp /tmp/save_11deadcode22deadcode33deadcode44 /tmp/sess_11deadcode22deadcode33deadcode44

            # alright we are ready to run this dood
            printf "Fuzzing\e[34m %s \e[0m\n" "${SCRIPT_FILENAME}"
            echo python -m phuzzer ${do_resume} --dictionary ${APPDIR}/dict.txt -t ${phuzzer_timeout} -c ${core_cnt} --run-timeout 61 -w /tmp/output -s ${APPDIR}/input /p/webcam/php/php5.5/sapi/cgi/php-cgi

            python -m phuzzer ${do_resume} --dictionary ${APPDIR}/dict.txt -t ${phuzzer_timeout} -c ${core_cnt} --run-timeout 61 -w /tmp/output -s ${APPDIR}/input /p/webcam/php/php5.5/sapi/cgi/php-cgi

            #mv /tmp/trace.log ${APPDIR}/trace_$(basename ${pscript})-$(date +%Y-%m-%d-%H-%M-%S)

            if grep -Fq "All set and ready to roll" /tmp/output/fuzzer-master.log; then
                echo -e "\tAFL started up successfully"
            else
                printf "\n\t \e[31m **********************************************************************\n\t\tAFL STARTUP FAILED\n\t\tFOR ${pscript}\n\t**********************************************************************\n\n \e[0m"
            fi
            # trick to cut last X number of elements, rev|cut -d"/" -f1,2,3,4|rev)

            rm -rf ${final_output_loc}
            mv /tmp/output ${final_output_loc}

        else
            echo "Running a test"
            #cat ${APPDIR}/output/fuzzer-master/queue/id:000000,orig:seed-0 | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php5.5/sapi/cgi/php-cgi
            if [ "$1" = "--input" ]; then
                #echo "catting.................. $2"
                cat $2 | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php5.5/sapi/cgi/php-cgi
            else
                printf "A\x00A=A\x00B=B\x00" | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php5.5/sapi/cgi/php-cgi

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




