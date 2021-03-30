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
//archive logs files
    $mainPath = $_GET['path'];
    $archiveMainPath = $mainPath . "archive/";
    $ext = "*." . $_GET['ext'];
    $fullpath = $mainPath . $ext;
// create and archive dir if not already created
    if (!is_dir($archiveMainPath)) {
        mkdir("$archiveMainPath");
    }
    $today = date("Ymd");
    $commandString = "sudo -u apache zip -r -j " . $archiveMainPath . "filename" . $today . ".zip " . $mainPath . $ext;
    exec($commandString);
    foreach (glob($fullpath) as $v) {
        unlink($v);
    }

    $fileCount = count(glob($mainPath . $ext));

    if ($fileCount > 0) {
        $response = json_encode(array(
            'failure' => true
        ));
    } else {
        $response = json_encode(array(
            'success' => true
        ));
    }
    echo $response;
}  // end session check