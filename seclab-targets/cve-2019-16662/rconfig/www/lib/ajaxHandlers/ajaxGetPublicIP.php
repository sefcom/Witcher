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
    $NoIp = "<font color=\"red\">Public IP Address Not Available</font>";
    $notConnect = "<font color=\"red\">Could Not Connect to Remote Server</font>";

    if (!$sock = @fsockopen('www.rconfig.com', 80, $num, $error, 5)) {
        $response = json_encode($notConnect);
        file_put_contents("publicIp.txt", $notConnect);
    } else {
        if ($ip = file_get_contents("http://www.rconfig.com/ip.php", 0, $ctx)) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                // test if what is returned is actually an IP address
                file_put_contents("publicIp.txt", $ip);
                $response = json_encode(array(
                    'success' => true
                ));
            } else {
                $response = json_encode(array(
                    'failure' => true
                ));
                file_put_contents("publicIp.txt", $NoIp);
            }
        }
    }
    echo $response;
}