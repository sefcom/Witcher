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
    /* Gets pageTimeout Value from settings table */
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $q = $db2->query("SELECT pageTimeout
        FROM settings 
        WHERE id = 1");
    $result = $db2->resultsetCols();
    echo json_encode($result);
}