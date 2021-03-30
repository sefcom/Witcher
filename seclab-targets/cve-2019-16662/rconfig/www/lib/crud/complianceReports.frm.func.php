<?php
require_once("../classes/db2.class.php");
require_once("../classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

function availablePolicies() {
    $db2 = new db2();
    $log = ADLog::getInstance();

    /*
     * Extract all Policy Elements for select list below
     */
    $db2->query("SELECT id, policyName FROM compliancePolicies WHERE status = 1 ORDER BY policyName ASC");
    $resultSelect = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$resultSelect || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying compliancePolicies options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for availablePolicies() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying availablePolicies() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id = $resultSelect[0]['id'];
        $policyName = $resultSelect[0]['policyName'];
        echo "<option value=" . $id . ">" . $policyName . "</option>";
    }
}