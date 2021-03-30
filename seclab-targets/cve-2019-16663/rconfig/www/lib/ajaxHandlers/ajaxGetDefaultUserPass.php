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
// Gets default device username and password from DB
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $db2->query("SELECT defaultNodeUsername, defaultNodePassword, defaultNodeEnable FROM settings WHERE id = 1");
    
    $rows = $db2->resultset();
     //decrypt PWs if key is set
    $db2->query("SELECT passwordEncryption from settings");
    if($db2->resultsetCols()[0] == 1){
            $rows[0]['defaultNodePassword'] = encrypt_decrypt('decrypt', $rows[0]['defaultNodePassword']);
            $rows[0]['defaultNodeEnable'] = encrypt_decrypt('decrypt', $rows[0]['defaultNodeEnable']);
        }             
    echo json_encode($rows);
}