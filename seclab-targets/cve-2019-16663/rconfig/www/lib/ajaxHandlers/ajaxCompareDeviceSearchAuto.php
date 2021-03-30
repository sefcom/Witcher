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
    $term = $_GET['term'];
    $db2->query("SELECT id, deviceName AS value FROM nodes WHERE deviceName LIKE :term AND status = 1");
    $db2->bind(':term', '%' . $term . '%'); //bind here and create wildcard search term here also
    $rows = $db2->resultset();
    echo json_encode($rows);
}