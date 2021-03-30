<?php

require_once("../classes/db2.class.php");
require_once("../classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");


function vendorId($id = null) {
    // $id is set if from is reloaded with errors so that selected item is pre-populated after form reload
    $db2 = new db2();
    $log = ADLog::getInstance();

    //Extract Nodes vendorId for select list below
    $db2->query("SELECT * FROM vendors WHERE status = 1 ORDER BY vendorname ASC");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying vendorId options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying vendorId - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }
    if ($id == null) {
        echo "<option value=\"\" selected></option>";
    } else {
        echo "<option value=\"\"></option>";
    }
    for ($i = 0; $i < $num_rows; $i++) {
        $vendorId = $result[$i]['id'];
        $vendorName = $result[$i]['vendorName'];
        if ($id == $vendorId && $id != null) {
            echo "<option value=" . $vendorId . " selected>" . $vendorName . "</option>";
        } else {
            echo "<option value=" . $vendorId . ">" . $vendorName . "</option>";
        }
    }
}

function templateId($id = null) {
// $id is set if from is reploaded with errors so that selected item is pre-populated after form reload

    $db2 = new db2();
    $log = ADLog::getInstance();
    //Extract Nodes accessMethod for select list below
    $db2->query("SELECT * FROM templates WHERE status = 1");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying template options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying templates - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }
    if ($id == null) {
        echo "<option selected=\"selected\" value=\"\">- Select an option -</option>";
    } else {
        echo "<option value=\"\">- Select an option -</option>";
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $templateId = $result[$i]['id'];
        $templateName = $result[$i]['name'];
        $templateFileName = $result[$i]['fileName'];
        if ($id == $templateId && $id != null) {
            echo "<option value=" . $templateId . " selected>" . $templateName . " - " . basename($templateFileName) . "</option>";
        } else {
            echo "<option value=" . $templateId . ">" . $templateName . " - " . basename($templateFileName) . "</option>";
        }
    }
}

function categories($id = null) {
    // $id is set if from is reloaded with errors so that selected item is pre-populated after form reload	
    $db2 = new db2();
    $log = ADLog::getInstance();
    //Extract Categories for select list below

    $db2->query("SELECT * FROM categories WHERE status = 1");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        $log->Warn("Failure: Problem Displaying categories options (File: " . $_SERVER['PHP_SELF'] . ")");
        return;
    }
    if ($num_rows == 0) {
        echo "Database table empty";
        $log->Warn("Failure: Database table returned empty on categories - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        return;
    }
    if ($id == null) {
        echo "<option value=\"\" selected>Select a Category </option>";
    } else {
        echo "<option value=\"\">Select a Category </option>";
    }
    for ($i = 0; $i < $num_rows; $i++) {
        $catId = $result[$i]['id'];
        $catName = $result[$i]['categoryName'];
        if ($id == $catId && $id != null) {
            echo "<option value=" . $catId . " selected>" . $catName . "</option>";
        } else {
            echo "<option value=" . $catId . ">" . $catName . "</option>";
        }
    }
}

// end categories function
function customProp() {
    $db2 = new db2();
    $db2->query("SELECT * FROM customProperties");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();

    for ($i = 0; $i < $num_rows; $i++) {
        $custprop = $result[$i]['customProperty'];
        // remove 'custom_' bit for display purposes
        $newcustprop = substr($custprop, 7);
        echo "<label>" . $newcustprop . ":</label>  
            <input type=\"text\" name=\"$custprop\" id=\"$custprop\" tabindex=12  style=\"width:150px;\"/> ";
    }
}

// end custom properties function