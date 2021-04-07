#!/bin/sh

TEST_RESULT=/tmp/mdb_test
rm -rf $TEST_RESULT

MDB_PATH=`mdb`

echo -e "mdb test start...." > $TEST_RESULT
echo -e "===========mdb test version 101==============" >> $TEST_RESULT
####################
# meb command test
####################
echo -e "  ------------------------------System Requirements testing------------- " >> $TEST_RESULT

#check /tmp path
echo test > /tmp/test
ls /tmp/test | grep test
if [ $? = "0" ]; then
    echo -e "\033[1;32;40m............................../tmp read write ok.\033[0m" >> $TEST_RESULT
    rm /tmp/test
else    
    echo -e "\033[1;31;40m[Fail]............................../tmp read write fail.\033[0m" >> $TEST_RESULT
fi

#check /mydlink folder
cd /mydlink
if [ $? = "0" ]; then
    echo -e "\033[1;32;40m............................../mydlink folder exist.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]............................../mydlink folder not exist.\033[0m" >> $TEST_RESULT
fi

#check mydlink in lib path
echo $PATH | grep mydlink
if [ $? = "0" ]; then
    echo -e "\033[1;32;40m............................../mydlink in lib path.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]............................../mydlink NOT in lib path.\033[0m" >> $TEST_RESULT
fi

libPath_1="/lib"
libPath_2="/usr/lib"

#check openssl
ls $libPath_1 | grep libssl
if [ $? = "0" ]; then
    ls $libPath_1 | grep libcrypto
    if [ $? = "0" ]; then
        echo -e "\033[1;32;40m..............................Openssl in system.\033[0m" >> $TEST_RESULT
    else 
        echo -e "\033[1;31;40m[Fail]..............................Can not find libcrypto.\033[0m" >> $TEST_RESULT
    fi
else
    ls $libPath_2 | grep libssl
    if [ $? = "0" ]; then
        ls $libPath_2 | grep libcrypto
        if [ $? = "0" ]; then
            echo -e "\033[1;32;40m..............................Openssl in system.\033[0m" >> $TEST_RESULT
        else 
            echo -e "\033[1;31;40m[Fail]..............................Can not find libcrypto.\033[0m" >> $TEST_RESULT
        fi
    else
        echo -e "\033[1;31;40m[Fail]..............................Can not find openssl.\033[0m" >> $TEST_RESULT
    fi
fi

#check libpthread
ls $libPath_1 | grep libpthread
if [ $? = "0" ]; then
   echo -e "\033[1;32;40m..............................libpthread in system.\033[0m" >> $TEST_RESULT
else    
    ls $libPath_2 | grep libpthread
    if [ $? = "0" ]; then
        echo -e "\033[1;32;40m..............................libpthread in system.\033[0m" >> $TEST_RESULT
    else 
        echo -e "\033[1;31;40m[Fail]..............................Can not find libpthread.\033[0m" >> $TEST_RESULT
    fi
fi


echo -e "\n------------------------------System Commands testing------------------" >> $TEST_RESULT

#check reboot command
IFS=":"
factory=0
export IFS;
for word in $PATH; do
    #echo "$word"
    ls $word | grep reboot
    if [ $? = "0" ]; then
        factory=1
    fi
done
if [ $factory = 1 ]; then
    echo -e "\033[1;32;40m..............................reboot command in system.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]..............................reboot command NOT in system.\033[0m" >> $TEST_RESULT
fi

#check killall command
IFS=":"
factory=0
export IFS;
for word in $PATH; do
    ls $word | grep killall
    if [ $? = "0" ]; then
        factory=1
    fi  
done
if [ $factory = 1 ]; then
    echo -e "\033[1;32;40m..............................killall command in system.\033[0m" >> $TEST_RESULT
else   
    echo -e "\033[1;31;40m[Fail]..............................killall command NOT in system.\033[0m" >> $TEST_RESULT 
fi

#check factory_reset command
IFS=":"
factory=0
export IFS;
for word in $PATH; do
    #echo "$word"
    ls $word | grep factory_reset
    if [ $? = "0" ]; then
        factory=1
    fi
done
if [ $factory = 1 ]; then
    echo -e "\033[1;32;40m..............................factory_reset command in system.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]..............................factory_reset command NOT in system.\033[0m" >> $TEST_RESULT
fi

#check fw_upgrade command
IFS=":"
factory=0
export IFS;
for word in $PATH; do
    echo "$word"
    ls $word | grep fw_upgrade
    if [ $? = "0" ]; then
        factory=1
    fi
done
if [ $factory = 1 ]; then
    echo -e "\033[1;32;40m..............................fw_upgrade command in system.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]..............................fw_upgrade command NOT in system.\033[0m" >> $TEST_RESULT
fi

#check sed command
IFS=":"
factory=0
export IFS;
for word in $PATH; do
    echo "$word"
    ls $word | grep sed
    if [ $? = "0" ]; then
        factory=1
    fi
done
if [ $factory = 1 ]; then
    echo -e "\033[1;32;40m..............................sed command in system.\033[0m" >> $TEST_RESULT
else    
    echo -e "\033[1;31;40m[Fail]..............................sed command NOT in system.\033[0m" >> $TEST_RESULT
fi
   
####################
# meb command test
####################
echo -e "\n------------------------------MDB testing------------------------------" >> $TEST_RESULT

#fw_version
FW_VERSION=`mdb get fw_version 2>/dev/null`
if [ $FW_VERSION ]; then
    echo -e "\033[1;32;40m..............................mdb get fw_version [$FW_VERSION]\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get fw_version fail.\033[0m" >> $TEST_RESULT
fi

#dev_model
MODEL=`mdb get dev_model 2>/dev/null`
if [ $MODEL ]; then
    echo -e "\033[1;32;40m..............................mdb get dev_model[$MODEL]\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get dev_model fail.\033[0m" >> $TEST_RESULT
fi

#dev_name
NAME=`mdb get dev_name 2>/dev/null`
if [ $NAME ]; then
    echo -e "\033[1;32;40m..............................mdb get dev_name[$NAME] OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get dev_name fail.\033[0m" >> $TEST_RESULT
fi

#admin_passwd
OLD_PASSWD=`mdb get admin_passwd 2>/dev/null`
testPasswd=%23%24%25%26%2B%2C%2F%3A
mdb set admin_passwd $testPasswd 2>/dev/null
mdb apply
sleep 3
PASSWD=`mdb get admin_passwd 2>/dev/null`

if [ "$PASSWD" = "$testPasswd" ]; then
    echo -e "\n\033[1;32;40m..............................mdb set admin_passwd OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\n\033[1;31;40m[Fail]..............................mdb set admin_passwd failed SET:[$testPasswd], GET:[$PASSWD]\033[0m" >> $TEST_RESULT
fi

#restore password
mdb set admin_passwd $OLD_PASSWD
mdb apply
sleep 3

#http_port
HTTP=`mdb get http_port 2>/dev/null`
if [ $HTTP ]; then
    echo -e "\033[1;32;40m..............................mdb get http_port($HTTP) OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get http_port failed\033[0m" >> $TEST_RESULT
fi

#https_port
HTTPS=`mdb get https_port 2>/dev/null`
if [ $HTTPS ]; then
    echo -e "\033[1;32;40m..............................mdb get https_port($HTTPS) OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get https_port failed\033[0m" >> $TEST_RESULT
fi

#register_st
REG=`mdb get register_st 2>/dev/null`
if [ $REG ]; then
    echo -e "\033[1;32;40m..............................mdb get register_st($REG) OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get register_st failed\033[0m" >> $TEST_RESULT
fi

mdb set register_st 0
mdb apply
sleep 3

SET_REG=`mdb set register_st 1 2>/dev/null`
mdb apply
sleep 3
REG=`mdb get register_st 2>/dev/null`
if [ $REG = "1" ]; then
    echo -e "\n\033[1;32;40m..............................mdb set register_st($REG) OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\n\033[1;31;40m[Fail]..............................mdb set register_st failed\033[0m" >> $TEST_RESULT
fi
mdb set register_st $REG


#mac_addr
MAC=`mdb get mac_addr 2>/dev/null`
if [ $MAC ]; then
    echo -e "\033[1;32;40m..............................mdb get mac_addr($MAC) OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb get mac_addr failed\033[0m" >> $TEST_RESULT
fi

mdb set attr_0 1 2>&1 >/dev/null
mdb set attr_1 1 2>&1 >/dev/null
mdb set attr_2 1 2>&1 >/dev/null
mdb set attr_3 1 2>&1 >/dev/null
mdb set attr_4 1 2>&1 >/dev/null
mdb set attr_5 1 2>&1 >/dev/null
mdb set attr_6 1 2>&1 >/dev/null
mdb set attr_7 1 2>&1 >/dev/null
mdb set attr_8 1 2>&1 >/dev/null
mdb set attr_9 1 2>&1 >/dev/null
mdb apply
sleep 3

#attr0
ATTR=`mdb get attr_0 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_0 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_0 failed\033[0m" >> $TEST_RESULT
fi

#attr1
ATTR=`mdb get attr_1 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_1 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_1 failed\033[0m" >> $TEST_RESULT
fi

#attr2
ATTR=`mdb get attr_2 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_2 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_2 failed\033[0m" >> $TEST_RESULT
fi

#attr3
ATTR=`mdb get attr_3 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_3 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_3 failed\033[0m" >> $TEST_RESULT
fi

#attr4
ATTR=`mdb get attr_4 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_4 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_4 failed\033[0m" >> $TEST_RESULT
fi

#attr5
ATTR=`mdb get attr_5 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_5 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_5 failed\033[0m" >> $TEST_RESULT
fi

#attr6
ATTR=`mdb get attr_6 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_6 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_6 failed\033[0m" >> $TEST_RESULT
fi

#attr7
ATTR=`mdb get attr_7 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_7 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_7 failed\033[0m" >> $TEST_RESULT
fi

#attr8
ATTR=`mdb get attr_8 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_8 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_8 failed\033[0m" >> $TEST_RESULT
fi

#attr9
ATTR=`mdb get attr_9 2>/dev/null`
if [ $ATTR = "1" ]; then
    echo -e "\033[1;32;40m..............................mdb set attr_9 OK!\033[0m" >> $TEST_RESULT
else
    echo -e "\033[1;31;40m[Fail]..............................mdb set attr_9 failed\033[0m" >> $TEST_RESULT
fi


cat $TEST_RESULT
