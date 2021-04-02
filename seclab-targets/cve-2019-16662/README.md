
#afl-fuzz
python -m witcher /p/Witcher/seclab-targets/cve-2019-16662/ EXWICHR

#showmap
cat /tmp/output/initial_seeds/seed-0 | STRICT=3 WC_INSTRUMENTATION=1 METHOD=GET DOCUMENT_ROOT=/home/rconfig/www LD_PRELOAD=/wclibs/libcgiwrapper.so SCRIPT_NAME="/home/rconfig/www/install/lib/ajaxHandlers/ajaxServerSettingsChk.php" SCRIPT_FILENAME="/home/rconfig/www/install/lib/ajaxHandlers/ajaxServerSettingsChk.php" /afl/afl-showmap -m4G -o /tmp/mapout /usr/local/bin/php-cgi

#login
printf "\x00\x00user=admin&pass=admin&sublogin=1" | PHP_PATH="$PHP_PATH:/home/rconfig/www" METHOD=POST DOCUMENT_ROOT=/home/rconfig/www LD_PRELOAD=/wclibs/libcgiwrapper.so SCRIPT_NAME="/home/rconfig/www/lib/crud/userprocess.php" SCRIPT_FILENAME="/home/rconfig/www/lib/crud/userprocess.php" /usr/local/bin/php-cgi

#crashes

