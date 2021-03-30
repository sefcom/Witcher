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
// Get Snippet from DB based on Snippet ID
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $id = $_GET['id'];
    $db2->query("SELECT snippet FROM snippets WHERE id = :id");
    $db2->bind(':id', $id); //bind here and create wildcard search term here also
    $rows = $db2->resultsetCols();
    echo json_encode($rows[0]); // send value in the array only
}