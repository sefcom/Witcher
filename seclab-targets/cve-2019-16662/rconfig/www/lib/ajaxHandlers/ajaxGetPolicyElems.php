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
// Gets all Policy Elements for a given Policy ID
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $id = $_GET['id'];
    $db2->query("SELECT e.elemId, cpe.elementName
                FROM compliancePolElemTbl as e
                LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
                WHERE polId = :id
                ORDER BY elementName ASC");
    $db2->bind(':id', $id); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
    $rows = $db2->resultset();

// loop through results and creates needed HTML for the renedered 'selected' select box
    $options = '';
    for ($i = 0; $i < count($rows); $i++) {
        $options .= '<option value=' . $rows[$i]['elemId'] . '>' . $rows[$i]['elementName'] . '</option>';
    }
    echo json_encode($options);
}