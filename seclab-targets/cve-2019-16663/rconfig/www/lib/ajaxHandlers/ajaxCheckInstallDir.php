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
// checks if www/install dir is present for dashboard alert

    if (defined('WEB_DIR')) {
        if (is_dir(WEB_DIR . '/install')) {
            $response = json_encode(array(
                'result' => 'present'
            ));
        } else {
            $response = json_encode(array(
                'result' => 'notpresent'
            ));
        }
    } else {
        echo 'WEB_DIR not defined';
    }

    echo $response;
}