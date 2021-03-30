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
// Get actual Public/ Natted IP for rConfig instance
//Setting the timeout properly without messing with ini values: 
    $ctx = stream_context_create(array(
        'http' => array(
            'timeout' => 5
        )
    ));

    if ($sock = @fsockopen('www.rconfig.com', 80, $num, $error, 5)) {
        $data = file_get_contents("http://www.rconfig.com/downloads/dashboardNotice.php", 0, $ctx);
    }
    echo json_encode($data);
}