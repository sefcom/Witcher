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
    ob_start(); // begin collecting output
    $passedRid = $_GET['rid'];
    $passedSnipId = $_GET['snipId'];
    include($config_app_basedir . 'lib/configDeviceScript.php');
    $result = ob_get_clean(); // retrieve output from myfile.php, stop buffering
    echo $result;
}