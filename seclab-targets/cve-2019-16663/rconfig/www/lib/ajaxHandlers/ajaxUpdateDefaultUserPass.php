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
// used by settings.js to update the default username and password for NEW devices added to the database
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $defaultNodeUsername = $_REQUEST['defaultNodeUsername'];

    // decrypt PWs if key is set
    // check if encryption already set in DB
    $db2->query("SELECT passwordEncryption from settings");
    if($db2->resultsetCols()[0] == 1){
            $defaultNodePassword = encrypt_decrypt('encrypt', $_REQUEST['defaultNodePassword']);
            $defaultNodeEnable   = encrypt_decrypt('encrypt', $_REQUEST['defaultNodeEnable']);
        } else {
            $defaultNodePassword = $_REQUEST['defaultNodePassword'];
            $defaultNodeEnable = $_REQUEST['defaultNodeEnable'];
        }        

    
    $db2->query("UPDATE settings SET
            defaultNodeUsername = :defaultNodeUsername, 
            defaultNodePassword = :defaultNodePassword,
            defaultNodeEnable =  :defaultNodeEnable
            WHERE id = 1");
    $db2->bind(':defaultNodeUsername', $defaultNodeUsername);
    $db2->bind(':defaultNodePassword', $defaultNodePassword);
    $db2->bind(':defaultNodeEnable', $defaultNodeEnable);
    $queryResult = $db2->execute();

    if ($queryResult) {
        $response = 'Success - Username & Password details saved';
    } else {
        $response = 'Failed: on all nodes update from some reason, check ths logs - ajaxUpdateDefaultUserPass.php';
    }
    echo json_encode($response);
}