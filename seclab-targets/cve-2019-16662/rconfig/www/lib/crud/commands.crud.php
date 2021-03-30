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

        /* validations */

        // validate command field

        if (!empty($_POST['command'])) {
            $command = str_replace(" ", "", $_POST['command']); // remove spaces for alpha validation
            if (ctype_alpha($command)) {
                $command = $_POST['command']; // reset $command var to actual input
            } else {
                $errors['command'] = "Command input invalid";
            }
        } else {
            $errors['command'] = "Command field cannot be empty";
        }


        if (preg_match('/(?:\\\[trn])+/', $_POST['command'])) {
            $errors['command'] = "Command input invalid";
        } else {
            $command = $_POST['command']; // reset $command var to actual input and escape
        }


        /* if command inputted and no cat selected - return error */
        $catId = $_POST['catId'];

        if ($catId['0'] == 0) {
            $errors['catId'] = "You must select a category";
        } else {
            $catId = $_POST['catId'];
        }


        if (!empty($command) && $catId['0'] != 0) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */

            if (empty($_POST['editid'])) { // actual add
                // add command to configcommand table
                $db2->query("INSERT INTO configcommands (command, status) VALUES (:command, '1' )");
                $db2->bind(':command', $command);
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $db2->query("SELECT id FROM configcommands WHERE command = :command");
                    $db2->bind(':command', $command);
                    $commandRes = $db2->resultset();
                    foreach ($commandRes as $row) {
                        $cmdId = $row['id'];
                    }

                    // next loop over catId Post and get all IDs selected and insert to cmdCatTbl
                    $catIds = $_POST['catId'];
                    for ($i = 0; $i < count($catIds); $i++) {
                        $catId = $catIds[$i];
                        $db2->query('INSERT INTO cmdCatTbl (configCmdId, nodeCatId) VALUES (:cmdId, :catId)');
                        $db2->bind(':cmdId', $cmdId);
                        $db2->bind(':catId', $catId);
                        $db2->execute();
                    }

                    $errors['Success'] = "Added command '" . $command . "' to Database";
                    $log->Info("Success: Added command " . $command . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "commands.php");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not Add command '" . $command . "' to Database ";
                    $log->Fatal("Fatal: Could not Add command '" . $command . "' to Database (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "commands.php?error");
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
                    header("Location: " . $config_basedir . "categories.php?error");
                    exit();
                }

                $db2->query("UPDATE configcommands SET command = :command WHERE id = :id");
                $db2->bind(':command', $command);
                $db2->bind(':id', $id);
                $queryResult = $db2->execute();
                if ($queryResult) {
                    //then delete all entires from the cmdCatTbl with related ID first. Then update with new values
                    $db2->query("DELETE FROM cmdCatTbl WHERE configCmdId = :id");
                    $db2->bind(':id', $id);
                    $db2->execute();
                    // next loop over catId Post and get all IDs selected and insert to cmdCatTbl
                    $catIds = $_POST['catId'];
                    for ($i = 0; $i < count($catIds); $i++) {
                        $catId = $catIds[$i];
                        $db2->query('INSERT INTO cmdCatTbl (configCmdId, nodeCatId) VALUES (:id, :catId);');
                        $db2->bind(':catId', $catId);
                        $db2->bind(':id', $id);
                        $db2->execute();
                    }

                    // return success
                    $errors['Success'] = "Edited command '" . $command . "' in Database";
                    $log->Info("Success: Edited command " . $command . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "commands.php");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not edit command '" . $command . "' in Database";
                    $log->Fatal("Fatal: Could not edit command '" . $command . "' in Database (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "commands.php?error");
                    exit();
                }
            }
        }
        /* end 'id' post check */
    }

// set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "commands.php?errors");
        exit();
    }

    /* end 'add' if */


    /* begin delete check */ elseif (isset($_POST['del'])) {
        /* the query */
        $db2->query("UPDATE configcommands SET status = 2 WHERE id = " . $_POST['id'] . ";");
        $db2->bind(':id', $_POST['id']);
        $queryResult = $db2->execute();
        if ($queryResult) {
            //then delete all entires from the cmdCatTbl with related ID first. Then update with new values
            $db2->query("DELETE FROM cmdCatTbl WHERE configCmdId = :id");
            $db2->bind(':id', $_POST['id']);
            $db2->execute();
            $log->Info("Success: Deleted Command " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to deleted Command " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
            header("Location: " . $config_basedir . "categories.php?error");
            exit();
        }
        $db2->query("SELECT command FROM configcommands WHERE status = 1 AND id = :id");
        $db2->bind(':id', $id);
        $queryResult = $db2->resultset();
        $items = array();
        foreach ($queryResult as $row) {
            array_push($items, $row);
        }

        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}