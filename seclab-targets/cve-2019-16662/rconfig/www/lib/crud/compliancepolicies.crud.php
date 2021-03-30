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
        $policyName = $_POST['policyName'];
        $policyDesc = $_POST['policyDesc'];


        /* validations */

// validate policyName field
        if (empty($policyName)) {
            $errors['policyName'] = "Policy Name field cannot be empty";
        }

// validate policyDesc field
        if (empty($policyDesc)) {
            $errors['policyDesc'] = "Policy Description field cannot be empty";
        }

// validate policyDesc field
        if (empty($_POST['selectedElems'])) {
            $errors['selectedElems'] = "You must select at least one Policy Element";
        }

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
        if (!empty($errors)) {
            // set return vars if validation failure
            $errors['policyNameVal'] = $policyName;
            $errors['policyDescVal'] = $policyDesc;
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=" . $elementValue);
            exit();
        }

        if (empty($errors)) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */

            if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
                // add policy to compliancePolicies table
                $db2->query("INSERT INTO compliancePolicies (policyName, policyDesc) VALUES  (:policyName, :policyDesc)");
                $db2->bind(':policyName', $policyName);
                $db2->bind(':policyDesc', $policyDesc);
                $resultInsert = $db2->execute();
                // get policy insert ID from previous Insert stmt
                $lastInsertId = $db2->lastInsertId();
                if ($resultInsert) {
                    // insert compliancePolElemTbl values pairs
                    foreach ($_POST['selectedElems'] as $selectedElems) {
                        $db2->query("INSERT INTO compliancePolElemTbl (polId, elemId) VALUES (:lastInsertId, :selectedElems)");
                        $db2->bind(':lastInsertId', $lastInsertId);
                        $db2->bind(':selectedElems', $selectedElems);
                        $db2->execute();
                    }
                    $errors['Success'] = "Added Policy to DB";
                    $log->Info("Success: Added Policy to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicies.php?success");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not Add Policy to DB";
                    $log->Fatal("Fatal: Could not Add Policy to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=" . $elementValue);
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
                    header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=" . $elementValue);
                    exit();
                }
                // update the main policy details
                $q = "UPDATE compliancePolicies SET policyName = '" . $policyName . "', policyDesc = '" . $policyDesc . "' WHERE id = " . $id;
                $db2->query("UPDATE compliancePolicies SET policyName = :policyName, policyDesc = :policyDesc WHERE id = :id");
                $db2->bind(':policyName', $policyName);
                $db2->bind(':policyDesc', $policyDesc);
                $db2->bind(':id', $id);
                $resultUpdate = $db2->execute();
                if ($resultUpdate) {
                    // if main policy details updated, then delete all policy/element pairings and insert new batch per selected box on form
                    $db2->query("DELETE FROM compliancePolElemTbl WHERE polId = :id");
                    $db2->bind(':id', $id);
                    $db2->execute();
                    foreach ($_POST['selectedElems'] as $selectedElems) {
                        $db2->query("INSERT INTO compliancePolElemTbl (polId, elemId) VALUES (:id, :selectedElems)");
                        $db2->bind(':id', $id);
                        $db2->bind(':selectedElems', $selectedElems);
                        $db2->execute();
                    }
                    // return success
                    $errors['Success'] = "Edited Policy '" . $policyName . "' in Database";
                    $log->Info("Success: Edited Policy " . $policyName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicies.php?errors");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not edit Policy '" . $policyName . "' in Database";
                    $log->Fatal("Fatal: Could not edit Policy '" . $policyName . "' in Database-  (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=" . $elementValue);
                    exit();
                }
            }
        }/* end 'id' post check */
    } /* end 'add' if */

    /* begin delete check */ elseif (isset($_POST['del'])) {
        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int";
            $log->Fatal("Fatal: id not of type int - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicies.php?error");
            exit();
        }
        /* the query */
        $q = "UPDATE compliancePolicies SET status = 2 WHERE id = " . $id . ";";
        $db2->query("UPDATE compliancePolicies SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $resultUpdateDel = $db2->execute();
        if ($resultUpdateDel) {
            // hard delete policy/element pairings
            $db2->query("DELETE FROM compliancePolElemTbl WHERE polId = :id");
            $db2->bind(':id', $id);
            $db2->execute();
            $log->Info("Success: Deleted Policy in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete Policy in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ /* Below is used for an ajax call from vendors update 

      jquery function to get row information to present back to compliance reports edit form */ elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
        if (ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancepolicies.php?errors");
            exit();
        }
        $db2->query("SELECT policyName, policyDesc FROM compliancePolicies WHERE id = :id");
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