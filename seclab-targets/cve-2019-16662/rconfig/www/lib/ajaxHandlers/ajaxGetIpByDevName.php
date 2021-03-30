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
// gethostbyname — Get the IPv4 address corresponding to a given Internet host name
// validate returned IP address and send the IP back to calling JS Script
    $hostname = $_GET['hostname'];
    $ip = gethostbyname($hostname);
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        $response = $ip;
    } else {
        $response = '';
    }
    echo json_encode($response);
}