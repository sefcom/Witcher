<?php

require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
// Gets OS level DNS Settings fro display on dashboard.php
    $resolv = "/etc/resolv.conf";
    $file_handle = fopen($resolv, "r");
    while (!feof($file_handle)) {
        $line = fgets($file_handle);
        if (strstr($line, "nameserver") || strstr($line, "search")) {
            $line = sscanf($line, "%s %s", $tag, $value);
            // echo $value;
            $dnsArr[] = $value;
        }
    }
    fclose($file_handle);
    $result = array();
    foreach ($dnsArr as $k => $v) {
        array_push($result, $v);
    }
    return implode(", ", $result);
}