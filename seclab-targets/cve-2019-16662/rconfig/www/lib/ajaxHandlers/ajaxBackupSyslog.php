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

// will backup syslog files - called from settingsBackup.js 
//    include("../../../classes/db2.class.php");

    $db2 = new db2();
// check and set timeZone to avoid PHP errors
    getSetTimeZone();
    $today = date("Ymd");

    /**
     * Create Logs backup and ZIP it to tmp dir
     */
    $backupFile = $config_syslogBackup_basedir . 'syslogbackup-' . $today . '.zip';
    touch($backupFile);
    folderBackup($config_log_basedir, $backupFile);

    if (file_exists($backupFile)) {
        $response = json_encode(array('success' => true));
    } else {
        $response = json_encode(array('failure' => true));
    }
    echo $response;
}  // end session check