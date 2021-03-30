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

    /* SPECIAL NOTE - due to the use of Login System 2.0, i am using this ../../classes
      features to add/ edit users from the DB. It incorportates ready made error handling, validation and emailing etc..
      There are not any ADD or EDIT CRUD fuctions on this page
     */

    /* begin delete check */
    if (isset($_POST['del'])) {

        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "useradmin.php?error");
            exit();
        }

        /* the query */
        $db2->query("UPDATE users SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $resultUpdate = $db2->execute();

        if ($resultUpdate) {
            $log->Info("Success: Deleted user of ID: " . $id . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete user of ID: " . $id . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    }
    /* end 'delete' if */

    /* Below is used for an ajax call from vendors update 
      jquery function to get row information to present back to vendor edit form */ elseif (isset($_GET['getRow']) && isset($_GET['id'])) {

        if (ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "useradmin.php?error");
            exit();
        }
        $db2->query("SELECT id, username, email, userlevel FROM users WHERE status = 1 AND id = :id");
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