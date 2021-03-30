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
    require('../../../classes/compareClass.php');

// simple script runtime check 
    $Start = getTime();

    require_once("../../../classes/db2.class.php");


    $db2 = new db2();
    $log = ADLog::getInstance();

// Validate and set full path vars
    $verified = TRUE;
    foreach ($_GET as $v) {
        if (!isset($v) || empty($v)) {
            $verified = FALSE;
        }
    }
    if (!$verified) {
        echo "<font color=\"red\">*</font> You must select all items with an asterisk"; // put in session to break and return error to script here
        exit();
    } else {
        // initialise Vars
        // clean the strings
        $path_a = stripslashes(str_replace('"', "", $_GET['path_a']));
        $path_b = stripslashes(str_replace('"', "", $_GET['path_b']));
        $linepadding = $_GET['linepadding'];
    }

    $diff = new diff;
    if ($linepadding >= 1 && $linepadding <= 99) { //if linepadding var is number and is between 1 and 99
        $text = $diff->inline($path_a, $path_b, $linepadding);
    } else {
        $text = $diff->inline($path_a, $path_b);
    }

    echo count($diff->changes) . ' changes';
    echo $text; // echo output
// Print time taken script
    $End = getTime();
    echo "<div class=\"spacer\"></div>";
    echo "<Strong>Time taken = " . number_format(($End - $Start), 2) . " secs </strong>";
}