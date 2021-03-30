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
// Gets all Policies for a given Policy Report ID
    require_once("../../../classes/db2.class.php");
    $db2 = new db2();
    $id = $_GET['id'];
    $db2->query("SELECT r.polId, cp.policyName
                FROM complianceReportPolTbl as r
                LEFT JOIN compliancePolicies AS cp ON r.polId = cp.id
                WHERE reportId = :id 
                ORDER BY policyName ASC");
    $db2->bind(':id', $id); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
    $rows = $db2->resultset();
// loop through results and create HTML for the 'selected' select box
    $options = '';
    for ($i = 0; $i < count($rows); $i++) {
        $options .= '<option value=' . $rows[$i]['polId'] . '>' . $rows[$i]['policyName'] . '</option>';
    }
    echo json_encode($options);
}