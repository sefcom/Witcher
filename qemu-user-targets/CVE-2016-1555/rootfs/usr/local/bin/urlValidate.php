#!/usr/local/bin/php -q
<?php
if (preg_match("/^((http|https):\/\/)?(([A-Za-z0-9\.-]{3,}\.[A-Za-z]{2,})|(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}))\S*$/",$argv[1])){
    echo "0";
} else {
    echo "1";
}
?>

