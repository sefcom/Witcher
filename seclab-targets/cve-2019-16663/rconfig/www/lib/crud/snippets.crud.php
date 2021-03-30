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

    /* Add snippets Here */

    if (isset($_POST['add'])) {
        $errors = array();

// escaped variables
        $snippetName = $_POST['snippetName'];
        $snippetDesc = $_POST['snippetDesc'];
        $snippet = $_POST['snippet'];
        $snippet = $snippet;

        /* validations */

// validate snippetName field
        if (empty($snippetName)) {
            $errors['snippetName'] = "Snippet Name field cannot be empty";
        }

// validate snippetDesc field
        if (empty($snippetDesc)) {
            $snippetDesc = $snippetName;
//	$errors['snippetDesc'] = "Snippet Description field cannot be empty";
        }

// validate single snippet is not empty
        if (empty($snippet)) {
            $errors['snippet'] = "Snippet cannot be empty";
        }

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
        if (!empty($errors)) {
            $errors['snippetNameVal'] = $snippetName;
            $errors['snippetDescVal'] = $snippetDesc;
            $errors['snippetVal'] = $_POST['snippet'];
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "snippet.php?errors&snippet=" . $snippetValue);
            exit();
        }

        if (empty($errors)) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */

            if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
                // add snippets to table
                $db2->query("INSERT INTO snippets (snippetName, snippetDesc, snippet) VALUES (:snippetName, :snippetDesc, :snippet)");
                $db2->bind(':snippetName', $snippetName);
                $db2->bind(':snippetDesc', $snippetDesc);
                $db2->bind(':snippet', $snippet);
                $resultInsert = $db2->execute();
                if ($resultInsert) {
                    $errors['Success'] = "Added Snippet to DB";
                    $log->Info("Success: Added Snippet to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "snippets.php?success");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not add Snippet to DB";
                    $log->Fatal("Fatal: Could not add Snippet to DB(File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "snippets.php?errors&elem=" . $snippetValue);
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
                    header("Location: " . $config_basedir . "snippets.php?errors&snippet=" . $snippetValue);
                    exit();
                }

                $q = "UPDATE snippets SET snippetName = '" . $snippetName . "', snippetDesc = '" . $snippetDesc . "', snippet = '" . $snippet . "' WHERE id = " . $id;
                $db2->query("UPDATE snippets SET snippetName = :snippetName, snippetDesc = :snippetDesc, snippet = :snippet WHERE id = :id");
                $db2->bind(':snippetName', $snippetName);
                $db2->bind(':snippetDesc', $snippetDesc);
                $db2->bind(':snippet', $snippet);
                $db2->bind(':id', $id);
                $resultUpdate = $db2->execute();

                if ($resultUpdate) {
                    // return success
                    $errors['Success'] = "Edited Snippet '" . $snippetName . "' in Database";
                    $log->Info("Success: Edited Snippet " . $snippetName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "snippets.php?errors");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not edit snippet";
                    $log->Fatal("Fatal: Could not edit snippet (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "snippets.php?errors&snippet=" . $snippetValue);
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
            header("Location: " . $config_basedir . "snippets.php?error");
            exit();
        }
        /* the query */
        $db2->query("DELETE FROM snippets WHERE id = :id");
        $db2->bind(':id', $id);
        $resultDelete = $db2->execute();
        if ($resultDelete) {
            $log->Info("Success: Deleted Snippet in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete Snippet in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
            header("Location: " . $config_basedir . "snippets.php?errors");
            exit();
        }

        $db2->query("SELECT snippetName, snippetDesc, snippet FROM snippets WHERE id = :id");
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