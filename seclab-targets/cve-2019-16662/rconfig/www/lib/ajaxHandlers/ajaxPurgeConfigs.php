<?php

require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/config/functions.inc.php");
require_once("/home/rconfig/classes/ADLog.class.php");
$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
// loaded from www\js\configoverview.js. 
// php errors supressed on this page because they should not interrupt the JSON response. 
//i.e. if errors were made due to SQL errors etc.. JSON would not be processed by JS on the SettingsDB.php page    
    require_once("../../../classes/db2.class.php");

    $log = ADLog::getInstance();
// set vars
    $purgeDays = $_GET['purgeDays'];

// tasks: delete all files entries in DB by ID and then delete all Dirs
// 1. get all ids from DB older than X days
    $db2 = new db2();
    $db2->query("SELECT id FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL " . $purgeDays . " DAY) >= configDate");
    $getIDs = $db2->resultset();
    $getIDs = flatten($getIDs);
// logging below
    if ($getIDs) {

        $log->Info("Info: Start manual " . $purgeDays . " day Config File Purge - GET DB IDs(File: " . $_SERVER['PHP_SELF'] . ")");
    } else {
        $response['response'] = "ERROR: executing query getIDs, becuase there was a problem, or no configs were returned.";
        $log->Fatal($response['response'] ."  (File: " . $_SERVER['PHP_SELF'] . ")");
        echo json_encode($response);
    }
    $iDlist = implode(", ", $getIDs);
// 2. get all dirs using a group by from DB older than X days
    $db2->query("SELECT configLocation FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL " . $purgeDays . " DAY) >= configDate GROUP BY configLocation;");
    $getDirRes = $db2->resultsetCols();

    if (count($getDirRes) > 0) {
        $log->Info("Info: Start manual " . $purgeDays . " day Config File Purge - GET DIRs (File: " . $_SERVER['PHP_SELF'] . ")");

// physically remove directories
        foreach ($getDirRes as $row) {
                echo $row;
                exec('rm -fr ' . $row);
        }
// delete all empty Dirs under /home/rconfig/data/ for completeness
        exec('find /home/rconfig/data/. -type d -empty -delete');
        
// 3. Delete all ids from DB older than X days - if these are delete first - step 2 cannot 
//    work as will not be able to get unique dirs older than X days

        if (!empty($iDlist)) {
            $db2->query("DELETE FROM configs WHERE id IN ($iDlist)");
            $executeRes = $db2->execute();
            if ($executeRes) {
                $log->Info("Info: Start manual " . $purgeDays . " day Config File Purge - Delete DB Rows (File: " . $_SERVER['PHP_SELF'] . ")");
                $response['response'] = "SUCCESS: " . $purgeDays . " day Config File Purge Completed";
            } else {
                $response['response'] = "ERROR: executing query Delete ID";
                $log->Fatal("Fatal: Could not Config File Purge - Delete DB Rows (File: " . $_SERVER['PHP_SELF'] . ")");
            }
        }

        echo json_encode($response);
    } else {
        $response['response'] = "ERROR: executing query ";
        $log->Fatal("Fatal: Could not Config File Purge - GET DIRs (File: " . $_SERVER['PHP_SELF'] . ")");
    }
}