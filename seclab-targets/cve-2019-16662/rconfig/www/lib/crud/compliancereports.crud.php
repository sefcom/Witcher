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
        $reportsName = $_POST['reportsName'];
        $reportsDesc = $_POST['reportsDesc'];

        /* validations */

// validate reportsName field
        if (empty($reportsName)) {
            $errors['reportsName'] = "Report Name field cannot be empty";
        }

// validate reportsDesc field
        if (empty($reportsDesc)) {
            $errors['reportsDesc'] = "Report Description field cannot be empty";
        }

// validate selectedPolicies field
        if (empty($_POST['selectedPolicies'])) {
            $errors['selectedPolicies'] = "You must select at least one Policy";
        }

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
        if (!empty($errors)) {
            // set return vars if validation failure
            $errors['reportsNameVal'] = $reportsName;
            $errors['reportsDescVal'] = $reportsDesc;
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "compliancereports.php?errors&elem=" . $elementValue);
            exit();
        }

        if (empty($errors)) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */

            if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
                // add reports to complianceReports table
                $db2->query("INSERT INTO complianceReports (reportsName, reportsDesc) VALUES (:reportsName, :reportsDesc)");
                $db2->bind(':reportsName', $reportsName);
                $db2->bind(':reportsDesc', $reportsDesc);
                // get reports insert ID from previous Insert stmt
                $lastInsertId = $db2->lastInsertId();
                $resultInsert = $db2->execute();
                if ($resultInsert) {
                    // insert complianceReportPolTbl values pairs
                    foreach ($_POST['selectedPolicies'] as $selectedPolicies) {
                        $db2->query("INSERT INTO complianceReportPolTbl (reportId, polId) VALUES  (:lastInsertId, :selectedPolicies)");
                        $db2->bind(':lastInsertId', $lastInsertId);
                        $db2->bind(':selectedPolicies', $selectedPolicies);
                        $db2->execute();
                    }
                    $errors['Success'] = "Added Report to DB";
                    $log->Info("Success: Added Report to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancereports.php?success");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not add Report to DB";
                    $log->Fatal("Fatal: Could not add Report to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancereports.php?errors&elem=" . $elementValue);
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
                    header("Location: " . $config_basedir . "compliancereports.php?errors&elem=" . $elementValue);
                    exit();
                }
                // update the main reports details
                $db2->query("UPDATE complianceReports SET reportsName = :reportsName, reportsDesc = :reportsDesc WHERE id = :id");
                $db2->bind(':reportsName', $reportsName);
                $db2->bind(':reportsDesc', $reportsDesc);
                $db2->bind(':id', $id);
                $resultUpdate = $db2->execute();

                if ($resultUpdate) {
                    // if main reports details updated, then delete all reports/element pairings and insert new batch per selected box on form
                    $db2->query("DELETE FROM complianceReportPolTbl WHERE reportId = :id");
                    $db2->bind(':id', $id);
                    $db2->execute();
                    foreach ($_POST['selectedPolicies'] as $selectedPolicies) {
                        $db2->query("INSERT INTO complianceReportPolTbl (reportId, polId) VALUES (:id, :selectedPolicies)");
                        $db2->bind(':id', $id);
                        $db2->bind(':selectedPolicies', $selectedPolicies);
                        $db2->execute();
                    }

                    // return success
                    $errors['Success'] = "Edited Report '" . $reportsName . "' in Database";
                    $log->Info("Success: Edited Report " . $reportsName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancereports.php?errors");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not edit Report";
                    $log->Fatal("Fatal: Could not edit Report (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "compliancereports.php?errors&elem=" . $elementValue);
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
            header("Location: " . $config_basedir . "compliancereports.php?error");
            exit();
        }
        /* the query */
        $db2->query("UPDATE complianceReports SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $resultDeleteUpdate = $db2->execute();
        if ($resultDeleteUpdate) {
            // hard delete reports/element pairings
            $db2->query("DELETE FROM complianceReportPolTbl WHERE reportId = :id");
            $db2->bind(':id', $id);
            $db2->execute();
            $num_rows = $db2->rowCount();
            $log->Info("Success: Deleted Report in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete Report in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
            header("Location: " . $config_basedir . "compliancereports.php?errors");
            exit();
        }
        $db2->query("SELECT reportsName, reportsDesc FROM complianceReports WHERE id = :id");
        $db2->bind(':id', $id);
        $resultSel = $db2->resultset();

        $items = array();
        foreach ($resultSel as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}