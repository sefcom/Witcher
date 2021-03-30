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
    $deviceName = $_GET['deviceName'];
    $command = $_GET['command'];
    $originalDate = $_GET['date'];

    $newDate = date("Y-m-d", strtotime($originalDate));

    $db2->query("SELECT configLocation, configFileName FROM configs as c
                WHERE c.deviceId = (SELECT id FROM nodes as n WHERE n.deviceName = :deviceName)
                AND c.configFilename LIKE '" . $command . "%'
                AND c.configDate = :date
                LIMIT 1");
    $db2->bind(':deviceName', $deviceName);
    $db2->bind(':date', $newDate);
    $rows = $db2->resultset();
    $path = $rows[0]['configLocation'] . '/' . $rows[0]['configFileName'];
//echo $path;
    echo json_encode($path);
}