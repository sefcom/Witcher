#! /usr/bin/env bash 

APPDIR=/p/webcam/javascript/tests/juice-shop/app
cd $APPDIR
#export LD_LIBRARY_PATH=/p/webcam/libs
#export AFL_PRELOAD=/p/webcam/libs/libcodetracer.so
#export AFL_PRELOAD=/p/webcam/libs/libcgiwrapper.so


#echo Using $LD_LIBRARY_PATH and $AFL_PRELOAD
sort -d ${APPDIR}/../urls.dat |uniq > ${APPDIR}/../sorted_urls.dat
if [[ ! -f "${APPDIR}/../completed_urls.dat" ]];then
    echo "" > ${APPDIR}/../completed_urls.dat;
fi
for loopcnt in {1..1}; do
    phuzzer_timeout=180
    do_resume=""
    if [[ ${loopcnt} -eq 1 ]]; then
        phuzzer_timeout=300
    fi

    comm -2 -3 ${APPDIR}/../sorted_urls.dat ${APPDIR}/../completed_urls.dat > /tmp/urls.dat

    while read pscript; do
        # STRICT=true METHOD=POST AFL_BASE=/tmp/output2 PORT=5144 NODE_BASE_URL=/rest/user/login
        export STRICT=true
        echo ${pscript}
        METHOD=$(echo $pscript|cut -f1 -d' ')
        export METHOD=${METHOD^^}
        export NODE_BASE_URL=$(echo $pscript|cut -f2 -d' '|sed 's/\/:id/\/1/g')

        export PORT=5144
        export SCRIPT_FILENAME="not_used"
        export AFL_BASE="/tmp/output/$(echo ${NODE_BASE_URL}|sed 's/\//-/g')"

        printf "Testing\e[34m %s \e[0m\n" "${METHOD} localhost:${PORT}${NODE_BASE_URL}, output @ ${AFL_BASE}"

        if [ "$1" = "--afl" ]; then
            # alright we are ready to run this dood
            #echo  /p/afl/afl-fuzz -t 20000 -m 2048 -i ../input -o /tmp/output2 -- /p/webcam/javascript/node/node --single_threaded app

            #echo python -m phuzzer ${do_resume} --dictionary ${APPDIR}/dict.txt -t ${phuzzer_timeout} -c 20 --run-timeout 61 -w /tmp/output -s ${APPDIR}/input /p/webcam/php/php5.5/sapi/cgi/php-cgi

            echo python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded' -t ${phuzzer_timeout} -c 20 --run-timeout 20000 -w ${AFL_BASE} -s ${APPDIR}/../input /p/webcam/javascript/node/node
            python -m phuzzer ${do_resume} --dictionary ${APPDIR}/../dict.txt --target-opts $'app' $'~~single-threaded' -t ${phuzzer_timeout} -c 20 --run-timeout 20000 -w ${AFL_BASE} -s ${APPDIR}/../input /p/webcam/javascript/node/node
            #mv /tmp/trace.log ${APPDIR}/trace_$(basename ${pscript})-$(date +%Y-%m-%d-%H-%M-%S)

            if grep -Fq "All set and ready to roll" ${AFL_BASE}/fuzzer-master.log; then
                echo -e "\tAFL started up successfully"
            else
                printf "\n\t \e[31m **********************************************************************\n\t\tAFL STARTUP FAILED\n\t\tFOR ${pscript}\n\t**********************************************************************\n\n \e[0m"
            fi
            # trick to cut last X number of elements, rev|cut -d"/" -f1,2,3,4|rev)

            echo ${pscript} >> ${APPDIR}/../completed_urls.dat

        else
            echo "Running a test"
            #cat ${APPDIR}/output/fuzzer-master/queue/id:000000,orig:seed-0 | LD_PRELOAD=${AFL_PRELOAD} /p/webcam/php/php5.5/sapi/cgi/php-cgi
            if [ "$1" = "--input" ]; then
                export SOURCE=$2
            else
                export SOURCE=/tmp/fuzz_input_temp
                printf "A\x00A=A\x00B=B\x00" > ${SOURCE}
            fi

            /p/webcam/javascript/node/node app --single-threaded

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

    done </tmp/urls.dat

done # loopcnt





