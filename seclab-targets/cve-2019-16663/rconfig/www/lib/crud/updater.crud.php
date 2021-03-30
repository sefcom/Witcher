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

    $log = ADLog::getInstance();

    if (isset($_POST['upload'])) {
        $errors = array();

        // validations	
        //Setting the timeout properly without messing with ini values: 
        $ctx = stream_context_create(array('http' => array('timeout' => 5)));
        $latestVer = file_get_contents("http://www.rconfig.com/downloads/version.txt", 0, $ctx);
        $expectedFileName = 'rconfig-' . $latestVer . '.zip';

        if ($_FILES['updateFile']['name'] === $expectedFileName) {
            if ($_FILES["updateFile"]["error"] > 0) {
                $errors['fileError'] = "Update File Error Return Code: " . $_FILES["updateFile"]["error"];
                $log->Warn("File Error Return Code: " . $_FILES["updateFile"]["error"] . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "updater.php?error&chk=1");
                exit();
            } else {
                $updateTmpLocation = $config_temp_dir . $_FILES["updateFile"]["name"];
                move_uploaded_file($_FILES['updateFile']['tmp_name'], $updateTmpLocation);
                if (is_file($updateTmpLocation)) {
                    $errors['success'] = "Update File <strong>" . $_FILES["updateFile"]["name"] . "</strong> - Uploaded and verified";
                    $log->Info("Failure: Update File " . $_FILES["updateFile"]["name"] . " -  Uploaded and verified - (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "updater.php?error&chk=1");
                    exit();
                } else {
                    $errors['fileError'] = "Could not move file to " . $updateTmpLocation;
                    $log->Warn("Could not move file to " . $updateTmpLocation . " - (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "updater.php?error&chk=1");
                    exit();
                }
            }
        } else {
            $errors['fileInvalid'] = "Invalid update file";
            $log->Warn("Failure: Invalid update file(File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "updater.php?error&chk=1");
            exit();
        }
    }
}