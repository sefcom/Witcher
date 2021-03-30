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
// Get Devices models from the devicemodelview based on $_GET['term'] for Ajax output on devices.php
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $deviceId = $_GET['deviceId'];
    $command = $_GET['command'];
    $db2->query("SELECT configDate FROM configs 
                WHERE deviceid = :deviceId
                AND configFilename LIKE '$command%'");
    $db2->bind(':deviceId', $deviceId); //bind here and create wildcard search term here also
    $rows = $db2->resultsetCols();
// remove duplicates 
    $uniqueResults = array_unique($rows);
    $newDateFormatArray = array();
    foreach ($uniqueResults as $k => $v) {
        $newDate = date("n-j-Y", strtotime($v));
        $newDateFormatArray[] = $newDate;
    }
    echo json_encode($newDateFormatArray);
}