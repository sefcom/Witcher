<?php
require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/classes/spyc.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    require_once("../../../classes/db2.class.php");
    require_once("../../../classes/ADLog.class.php");
    $db2 = new db2();
    $log = ADLog::getInstance();

    $db2->query("SELECT * from templates WHERE `id` = :id");
    $db2->bind(':id', $_POST['id']);
    $queryResult1 = $db2->resultset();
    $fullpath = $queryResult1[0]['fileName'];

    if($queryResult1){
        $db2->query("UPDATE templates SET status = 2 WHERE `id` = :id");
        $db2->bind(':id', $_POST['id']);
        $queryResult2 = $db2->execute();
        $log->Info("Success: Template ".$fullpath." set to status 2 in DB - deleted");
    } else {
        /* Update failed */ 
        $response = "failed";
        $log->Warn("Failed: Could not delete Template ".$fullpath." from Database");
    }
    
    $deletedFileName = $fullpath.'.old';
    rename($fullpath, $deletedFileName);

    if(file_exists($deletedFileName) && !file_exists($fullpath)){
        $response = "deleted";
        $log->Info("Success: Template: ".$fullpath." deleted from templates folder");
    } else {
         /* Delete failed */
        $response = "failed";
        $log->Warn("Success: Could not edit Template ".$fullpath." in templates folder");
    }
    echo json_encode($response);    
}  // end session check