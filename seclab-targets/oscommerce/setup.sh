#!/usr/bin/env bash

dname=$(docker ps |grep puppeteer1337/oscommerce|cut -d " " -f1)

ipaddr=$(docker inspect $dname | jq '.[]|.NetworkSettings.Networks.bridge.IPAddress'|tr -d '"')

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

if $SCRIPT_DIR/wait-for-tcp $ipaddr 3306 && $SCRIPT_DIR/wait-for-tcp $ipaddr 80 ; then

printf "\033[32m[Witcher] Starting Install ...\033[0m \n"
curl -vv "http://$ipaddr" -c /tmp/cookies.txt
curl -vv "http://$ipaddr/install/install.php" -c /tmp/cookies.txt
curl -vv "http://$ipaddr/install/install.php?site=2" -c /tmp/cookies.txt
curl "http://$ipaddr/install/rpc.php?action=dbCheck" --data-raw 'server=127.0.0.1&username=root&password=root&name=oscommerce&prefix=osc_'
curl "http://$ipaddr/install/rpc.php?action=dbImport" -H 'Cookie: oscomid=694taag64bgfaomajigs4ara5m'  --data-raw 'server=127.0.0.1&username=root&password=root&name=oscommerce&prefix=osc_'
#curl -vv -b /tmp/cookies.txt -L "http://$ipaddr/install/install.php?step=2"  --data-raw 'DB_SERVER=127.0.0.1&DB_SERVER_USERNAME=root&DB_SERVER_PASSWORD=root&DB_DATABASE=oscommerce&DB_TABLE_PREFIX=osc_'

printf "\033[36m ------------------------  3  --------------------------- \033[0m\n"
curl -vv -b /tmp/cookies.txt -L "http://$ipaddr/install/install.php?step=3" --data-raw "HTTP_WWW_ADDRESS=http%3A%2F%2F$ipaddr%2F&DIR_FS_DOCUMENT_ROOT=%2Fapp%2F"
printf "\033[36m ---------------------  4  ------------------------------ \033[0m\n"
curl -vv 'http://172.17.0.13/install/install.php?step=4' --data-raw 'CFG_STORE_NAME=Witcher2&CFG_STORE_OWNER_NAME=witcher&CFG_STORE_OWNER_EMAIL_ADDRESS=erik_biz2%40trickel.com&CFG_ADMINISTRATOR_USERNAME=admin&CFG_ADMINISTRATOR_PASSWORD=admin&CFG_ADMIN_DIRECTORY=admin&TIME_ZONE=Europe%2FBerlin&HTTP_WWW_ADDRESS=http%3A%2F%2F172.17.0.13%2F&DIR_FS_DOCUMENT_ROOT=%2Fapp%2F&DB_SERVER=127.0.0.1&DB_SERVER_USERNAME=root&DB_SERVER_PASSWORD=root&DB_DATABASE=oscommerce&DB_TABLE_PREFIX=osc_'


echo 'timeout 4h node /p/Witcher/base/helpers/request_crawler/main.js http://$ipaddr $(pwd) ; docker exec -it -w $(pwd) -u wc $cve-$plus bash -i -c '"'"'p'"'"
else
  printf "[\033[31mWitcher\033[0m]Failed to get to port at $ipaddr \n"
fi
