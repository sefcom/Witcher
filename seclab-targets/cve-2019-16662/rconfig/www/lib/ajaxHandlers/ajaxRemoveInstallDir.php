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
// remove install directory
    $dir = '/home/rconfig/www/install/';
    if (is_dir($dir)) {
        rrmdir($dir);
        sleep(1); // wait for command to execute on the shell
        if (!file_exists($dir)) { // check if install  does not dir exist after delete and return success
            $response = 'success';
        } else if (file_exists($dir)) { // else return failure as dir still exists
            $response = 'failure';
        }
    } else if (!is_dir($dir)) { // first if - return success as dir does not exist
        $response = 'success';
    }
    echo json_encode($response);
}