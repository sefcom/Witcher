#!/usr/bin/env bash


while true; do
    echo "Clearing log files..."
    echo "" > /tmp/aflout.log
    echo "" > /tmp/queryfile.log
    echo "" > /tmp/aflerr.log
    #echo "" > /tmp/aflout-extras.log
    truncate -s 100000 /tmp/output/fuzzer-*.out
    truncate -s 100000 /tmp/output/fuzzer-*.err

    reset_cnt=0
    for x in {1..10};do
        # if we get more than 1000 save_* we need to delete them for safety, but exlude ones created by set_session_login
        if [[ $(ls /tmp/sess_*|wc -l) -gt 1000 ]]; then
            echo "Deleting extra sessions"
            find /tmp -name 's???_*' ! -name 's???_00*' -delete;
        fi

        for sess_fn in /tmp/sess*;do
          sess_size=`stat -c%s "$sess_fn" 2> /dev/null || echo 0`
          save_sess_fn=$(echo $sess_fn|sed 's/sess/save/g')

          save_sess_size=`stat -c%s "$save_sess_fn" 2> /dev/null || echo 0`
          # if session gains some info, then save it
          if [[ ${sess_size} -gt ${save_sess_size} ]]; then
              cp $sess_fn $save_sess_fn
              save_sess_size=`stat -c%s "$save_sess_fn" 2> /dev/null || echo 0`
          # if session losses info, then recover it from backup
          elif [[ ${sess_size} -lt ${save_sess_size} ]]; then
             cp $save_sess_fn $sess_fn
             reset_cnt=$(( ${reset_cnt} + 1 ))
             #printf "\tSession was restored because session size=%s v saved size=%s.\n" "${sess_size}" "${save_sess_size}"
          fi
        done
     done
     echo "Reset ${reset_cnt} session files"
     sleep 30

#    # clean up saved sessions that no longer have a session file.
#    for $save_sess_fn in /tmp/save*;do
#        sess=$(echo $save_sess_fn|sed 's/save/sss/g')
#        if [[ ! -f "$sess" ]]; then
#            rm $save_sess_fn
#        fi
#    done

#    cd /tmp
#    echo "Creating file list"
#    ls |grep sess_|grep -v sess_11deadcode22deadcode33deadcode44 > /tmp/sessfiles
#
##    echo "Preparing to delete"
##    cat /tmp/sessfiles | perl -e 'for(<>){ print($_); unlink($_);}'
##
#    cnt=0;
#    allfiles=""
#    filenamelen=$(echo "sess_11deadcode22deadcode33deadcode44"|wc -c)
#    max_file_cnt=$(( 65400 / $filenamelen ))
#    for fn in $(cat /tmp/sessfiles); do
#        cnt=$(( $cnt + 1 ));
#        calc=$(( $cnt % 1500 ));
#
#        if [[ $calc -eq 0 ]]; then
#            echo "$cnt -- $(echo $allfiles | wc -c) length";
#            rm $allfiles >/dev/null 2>&1
#            allfiles=""
#        else
#            allfiles+="$fn "
#        fi
#
#    done
    #echo "DELETING $(cat /tmp/sessfiles |wc -l) files, using $(cat /tmp/rm_sess_cmds | wc -l ) commands"
    #parallel --jobs 5 --progress -a /tmp/rm_sess_cmds

#    cd -
    #echo "done removing files, sleeping..."


done