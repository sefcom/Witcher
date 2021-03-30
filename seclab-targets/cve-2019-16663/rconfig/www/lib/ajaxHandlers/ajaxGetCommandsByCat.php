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
    /* this will retrieve commands based on submitted CatId */
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $catId = $_GET['catId'];
    $db2->query("SELECT id, command FROM configcommands 
                WHERE id IN (SELECT DISTINCT configCmdId 
                FROM cmdCatTbl 
                WHERE nodeCatId = :catId)
                AND status = 1
                ORDER BY command ASC");

    $db2->bind(':catId', $catId); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
    $rows = $db2->resultset();
    echo json_encode($rows);
}