#! /bin/bash

cd /app

test_file="./cmd_test.php"
printf  "\x00" | WC_INSTRUMENTATION=1 SCRIPT_FILENAME=$test_file LD_PRELOAD=/wclibs/lib_db_fault_escalator.so STRICT=3 /p/Witcher/base/afl/afl-showmap -o /tmp/mapout -m 4G php-cgi  >> /tmp/segtest.out 2>&1

if grep -a  "+++ Program killed by signal 11 +++" /tmp/segtest.out > /dev/null; then
    echo "Received signal for $test_file, dash is working"
else
    echo "FAILED to receive  SIGSEGV for $test_file"
    exit 199
fi 

lines=$(wc -l /tmp/mapout|cut -d " " -f1)
if [[ $lines -gt 2 ]]; then
    echo "Mapped more than 2 instructions!"
else
    echo "FAILED to map more than 2 instructions on $test_file"
    exit 234
fi 


test_file="./db_test.php"
printf  "\x00" | WC_INSTRUMENTATION=1 SCRIPT_FILENAME=$test_file LD_PRELOAD=/wclibs/lib_db_fault_escalator.so STRICT=3 /p/Witcher/base/afl/afl-showmap -o /tmp/mapout -m 4G php-cgi  >> /tmp/segtest.out 2>&1

if  grep -a "+++ Program killed by signal 11 +++" /tmp/segtest.out > /dev/null; then
    echo "Received SIGSEGV for $test_file, lib_db_fault_escalator is working"
else
    echo "FAILED to receive  SIGSEGV for $test_file"
    exit 23
fi 

lines=$(wc -l /tmp/mapout |  cut -d " " -f1)
if [ $lines -gt 2 ]; then
    echo "Mapped more than 2 instructions!"
else
    echo "FAILED to map more than 2 instructions on $test_file"
    echo 87
fi 

printf "\033[32m\nAll tests passed\033[0m\n\n"

