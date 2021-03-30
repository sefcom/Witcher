<?php

require_once("../classes/db2.class.php");
require_once("../classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

function reportsOptions() {
    $db2 = new db2();
    $log = ADLog::getInstance();

    /*
     * Extract all Policy Elements for select list below
     */
    $db2->query("SELECT id, reportsName,  reportsDesc FROM complianceReports WHERE status = 1 ORDER BY id ASC");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying complianceReports options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for reportsOptions() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying reportsOptions() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id = $result[$i]['id'];
        $reportsName = $result[$i]['reportsName'];
        echo "<option value=compliance-" . $id . ">" . $reportsName . "</option>";
    }
}

function snippetsOptions() {
    $db2 = new db2();
    $log = ADLog::getInstance();

    /*
     * Extract all snippets for select list below
     */
    $db2->query("SELECT id, snippetName FROM snippets ORDER BY snippetName ASC");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying snippetsOptions() options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for reportsOptions() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying snippetsOptions() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id = $result[$i]['id'];
        $snippetName = $result[$i]['snippetName'];
        echo "<option value=snippetId-" . $id . ">" . $snippetName . "</option>";
    }
}
