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
    require_once("../../../classes/db2.class.php");

    $db2 = new db2();
    $log = ADLog::getInstance();

    /* Add Categories Here */

    if (isset($_POST['add'])) {
        $errors = array();

// escaped variables
        $elementName = $_POST['elementName'];
        $elementDesc = $_POST['elementDesc'];
        $singleParam1 = $_POST['singleParam1'];
        $singleLine1 = $_POST['singleLine1'];

        /* validations */

// validate elementName field
        if (empty($elementName)) {
            $errors['elementName'] = "Element Name field cannot be empty";
        }

// validate elementDesc field
        if (empty($elementDesc)) {
            $errors['elementDesc'] = "Element Description field cannot be empty";
        }

// validate single element is not empty
        if (empty($singleLine1)) {
            $errors['singleLine1'] = "Input cannot be empty";
        }

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
        if (!empty($errors)) {
            $errors['elementNameVal'] = $elementName;
            $errors['elementDescVal'] = $elementDesc;
            $errors['singleLine1val'] = $_POST['singleLine1'];
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=" . $elementValue);
            exit();
        }

        if (empty($errors)) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */

            if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
                // add element to compliancePolElem table
                $db2->query("INSERT INTO compliancePolElem (elementName, elementDesc, singleParam1, singleLine1) 
			VALUES (:elementName, :elementDesc, :singleParam1, :singleLine1)");
                $db2->bind(':elementName', $elementName);
                $db2->bind(':elementDesc', $elementDesc);
                $db2->bind(':singleParam1', $singleParam1);
                $db2->bind(':singleLine1', $singleLine1);
                $resultInsert = $db2->execute();
                if ($resultInsert) {
                    $errors['Success'] = "Added Policy Element to DB";
                    $log->Info("Success: Added Policy Element to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicyelements.php?success");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not add Policy Element to DB";
                    $log->Fatal("Fatal: Could not add Policy Element to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=" . $elementValue);
                    exit();
                }
            } else { // actual edit

                /* validate editid is numeric */
                if (ctype_digit($_POST['editid'])) {
                    $id = $_POST['editid'];
                } else {
                    $errors['Fail'] = "Fatal: editid not of type int for edit";
                    $log->Fatal("Fatal: editid not of type int for edit - " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=" . $elementValue);
                    exit();
                }
                $db2->query("UPDATE compliancePolElem SET elementName = :elementName, elementDesc = :elementDesc, singleParam1 = :singleParam1, singleLine1 = :singleLine1 WHERE id = :id");
                $db2->bind(':elementName', $elementName);
                $db2->bind(':elementDesc', $elementDesc);
                $db2->bind(':singleParam1', $singleParam1);
                $db2->bind(':singleLine1', $singleLine1);
                $db2->bind(':id', $id);
                $resultUpdate = $db2->execute();
                if ($resultUpdate) {
                    // return success
                    $errors['Success'] = "Edited Policy Element '" . $elementName . "' in Database";
                    $log->Info("Success: Edited Policy Element " . $elementName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicyelements.php?errors");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not Edit Policy Element";
                    $log->Fatal("Fatal: Could not Edit Policy Element - (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=" . $elementValue);
                    exit();
                }
            }
        }/* end 'id' post check */
    } /* end 'add' if */

    /* begin delete check */ elseif (isset($_POST['del'])) {
        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicyelements.php?error");
            exit();
        }
        /* the query */
        $q = "UPDATE compliancePolElem SET status = 2 WHERE id = " . $id . ";";
        $db2->query("UPDATE compliancePolElem SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $resultUpdated = $db2->execute();
        if ($resultUpdated) {
            $log->Info("Success: Deleted Policy Element in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete Policy Element in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ /* Below is used for an ajax call from vendors update 

      jquery function to get row information to present back to vendor edit form */ elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
        if (ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicyelements.php?errors");
            exit();
        }

        $db2->query("SELECT elementName, elementDesc, singleParam1, singleLine1 FROM compliancePolElem WHERE id = :id");
        $db2->bind(':id', $id);
        $resultSelect = $db2->resultset();
        $items = array();
        foreach ($resultSelect as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}