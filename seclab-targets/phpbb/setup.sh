#!/usr/bin/env bash

dname=$(docker ps |grep puppeteer1337/phpbb|cut -d " " -f1)

ipaddr=$(docker inspect $dname | jq '.[]|.NetworkSettings.Networks.bridge.IPAddress'|tr -d '"')

curl "http://$ipaddr/index.php?page=settings&mode=update_settings" -H 'Cookie: qi_profile=default' --data-raw 'qi_config%5Bqi_lang%5D=en&qi_config%5Bcache_dir%5D=cache%2F&qi_config%5Bboards_dir%5D=boards%2F&qi_config%5Bboards_url%5D=boards%2F&qi_config%5Bmake_writable%5D=0&qi_config%5Bgrant_permissions%5D=&qi_config%5Bdbms%5D=mysqli&qi_config%5Bdbhost%5D=127.0.0.1&qi_config%5Bdbport%5D=&qi_config%5Bdbuser%5D=root&qi_config%5Bdbpasswd%5D=&qi_config%5Bno_dbpasswd%5D=1&qi_config%5Bdb_prefix%5D=qi_&qi_config%5Btable_prefix%5D=phpbb_&qi_config%5Bserver_name%5D=localhost&qi_config%5Bserver_port%5D=80&qi_config%5Bcookie_domain%5D=&qi_config%5Bcookie_secure%5D=0&qi_config%5Badmin_name%5D=admin&qi_config%5Badmin_pass%5D=admin&qi_config%5Badmin_email%5D=qi_admin%40phpbb-quickinstall.tld&qi_config%5Bsite_name%5D=Testing+Board&qi_config%5Bsite_desc%5D=QuickInstall+sandbox&qi_config%5Bdefault_lang%5D=en&qi_config%5Bqi_tz%5D=UTC&qi_config%5Bother_config%5D=session_length%3B999999%0D%0A%23A+comment...&qi_config%5Bboard_email%5D=qi_board%40phpbb-quickinstall.tld&qi_config%5Bemail_enable%5D=0&qi_config%5Bsmtp_delivery%5D=0&qi_config%5Bsmtp_host%5D=&qi_config%5Bsmtp_port%5D=25&qi_config%5Bsmtp_auth%5D=PLAIN&qi_config%5Bsmtp_user%5D=&qi_config%5Bsmtp_pass%5D=&qi_config%5Bpopulate%5D=0&qi_config%5Bnum_users%5D=100&qi_config%5Bnum_new_group%5D=10&qi_config%5Bemail_domain%5D=phpbb-quickinstall.tld&qi_config%5Bcreate_admin%5D=1&qi_config%5Bcreate_mod%5D=1&qi_config%5Bnum_cats%5D=2&qi_config%5Bnum_forums%5D=10&qi_config%5Bnum_topics_min%5D=5&qi_config%5Bnum_topics_max%5D=25&qi_config%5Bnum_replies_min%5D=0&qi_config%5Bnum_replies_max%5D=50&qi_config%5Bchunk_post%5D=1000&qi_config%5Bchunk_topic%5D=2000&qi_config%5Bchunk_user%5D=5000&qi_config%5Balt_env%5D=&qi_config%5Bredirect%5D=1&qi_config%5Binstall_styles%5D=0&qi_config%5Bdefault_style%5D=&qi_config%5Bdrop_db%5D=0&qi_config%5Bdelete_files%5D=0&qi_config%5Bdebug%5D=0&sel_lang=en&used_profile=&save_profile=default&submit=Save'

curl "http://$ipaddr/index.php?page=create"  -H 'Cookie: qi_profile=default'  --data-raw 'site_name=Testing+Board&site_desc=QuickInstall+sandbox&dbname=test&redirect=1&populate=1&debug=0&install_styles=0&admin_name=&admin_pass=&table_prefix=phpbb_&db_prefix=qi_&drop_db=0&delete_files=0&make_writable=0&grant_permissions=&default_style=&alt_env=&num_users=100&num_new_group=10&email_domain=phpbb-quickinstall.tld&create_admin=1&create_mod=1&num_cats=2&num_forums=10&num_topics_min=5&num_topics_max=25&num_replies_min=0&num_replies_max=50&chunk_post=1000&chunk_topic=2000&chunk_user=5000&other_config=session_length%3B999999%0D%0A%23A+comment...'



if docker exec -it phpbb-user bash -c "mysql qi_test < /test_config.sqldump"; then
    wget http://$ipaddr/boards/test -O /dev/null
    if [ -f "$(pwd)/coverages/+app+boards+test+index.cc.json" ]; then
        printf "\033[36mReady for test @ http://$ipaddr/boards/test \n"
        echo 'timeout 4h node /p/Witcher/base/helpers/request_crawler/main.js http://$ipaddr/board/phpbb $(pwd) ; docker exec -it -w $(pwd) -u wc $cve-$plus bash -i -c '"'"'p'"' "
        echo " "
    else
      printf "\033[31mFailed to find coverages\033[0m\n\n"
      exit 123
    fi

else
    printf "\033[31mDatabase configuration import failed \033[0m\n\n"
    exit 193
fi




