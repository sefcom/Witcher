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
// Purge Deleted Items from the Database. i.e. all items with a 'status' of 2
// used here settings.js and settingsDb.js
//php errors supressed on this pagre beacuse they should not interupt the JSON repsonse. 
// i.e. if errors were made due to SQL errors etc.. JSOn would not be processed by JS on the SettingsDB.php page
    error_reporting(0);
    
    require_once("../../../classes/db2.class.php");

    $db2 = new db2();
    $log = ADLog::getInstance();

// query DB for all tables with 'status' columns
    if (defined('DB_NAME')) {
        $db2->query("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
                    AND COLUMN_NAME = 'status'");
        $tbls = $db2->resultsetCols();
    } else {
        $response['response'] = "ERROR: executing query";
        $log->Fatal("Fatal: Could not execute query (File: " . $_SERVER['PHP_SELF'] . ")");
    }

    foreach ($tbls as &$tbl) {
        $db2->query("DELETE FROM " . $tbl . " WHERE status = 2");
        if ($db2->execute()) {
            $response['response'] = "SUCCESS: Deleted items purged";
            $log->Info("Info: Deleted rows purged from Table:" . $tbl . "  (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $response['response'] = "ERROR: Could not delete rows. Errors logged to log file";
            $log->Fatal("Fatal: Delete rows from Table:" . $tbl . " (File: " . $_SERVER['PHP_SELF'] . ")");
        }
    }
    echo json_encode($response);
}