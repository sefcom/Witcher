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
        if (!empty($_POST['categoryName'])) {
            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
            /* Validate Input from Form */
            if (!ctype_alnum($_POST['categoryName'])) {
                $errors['categoryName'] = "Input was not a valid string!";
                $log->Warn("Failure: categoryName Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
            }
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "categories.php?error");
                exit();
            } else {
                $categoryName = $_POST['categoryName'];
            }
            /* end validate */

            if (empty($_POST['editid'])) { // becuase editid as set in form is empty, this is an Add and NOT an Edit
                $db2->query("INSERT INTO categories (categoryName, status) VALUES (:categoryName, '1')");
                $db2->bind(':categoryName', $categoryName);
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $errors['Success'] = "Added category to DB";
                    $log->Info("Success: Added category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "categories.php");
                    exit();
                } else {
                    $errors['Fail'] = "ERROR: Could not add category to DB";
                    $log->Fatal("Fatal: Could not add category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "categories.php?error");
                    exit();
                }
            } else { // end empty_$POST['editid'] check : next section is an actual edit

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

                $db2->query("UPDATE categories SET categoryName = :categoryName WHERE id = :id");
                $db2->bind(':categoryName', $categoryName);
                $db2->bind(':id', $id);
                $queryResult = $db2->execute();
                if ($queryResult) { // if Q was good, send back a sucess to the file
                    $errors['Success'] = "Edited category to DB";
                    $log->Info("Success: Edited category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "categories.php");
                    exit();
                } else { // else Q failed, send back an error
                    $errors['Fail'] = "ERROR: Could not Edit category to DB ";
                    $log->Fatal("Fatal: Could not Edit category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "categories.php?error");
                    exit();
                }
            }
            /* end 'id' post check */
        } else { // categoryName was not filed in, and so send back error and kill script
            $errors['categoryName'] = "Category Field cannot be empty";
            $log->Warn("Failure: Category Name Field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "categories.php?error");
            exit();
        }
    }
    /* end 'add/editid' if */


    /* begin delete check */ elseif (isset($_POST['del'])) {
        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for del";
            $log->Fatal("Fatal: id not of type int  for del - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "categories.php?error");
            exit();
        }

        /* the query */
        $db2->query("UPDATE categories SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $result = $db2->execute();

        if ($result) {
            $log->Info("Success: Deleted category in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete category in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
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
        $db2->query("SELECT id, categoryName FROM categories WHERE status = 1 AND id = :id");
        $db2->bind(':id', $id);
        $result = $db2->resultset();

        $items = array();
        foreach ($result as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}
