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
    require_once("../../../classes/imageResize.class.php");

    $db2 = new db2();
    $log = ADLog::getInstance();

    /* Add Vendors Here */
    if (isset($_POST['add'])) {
        $errors = array();

        if (!empty($_POST['vendorName'])) {
            /* Validate Input from Form */
            if (!ctype_alnum($_POST['vendorName'])) {
                $errors['vendorName'] = "Input was not a valid string - alphaNumeric Characters only, and no spaces!";
                $log->Warn("Failure: categoryName Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "vendors.php?error");
                exit();
            } else {
                $vendorName = $_POST['vendorName'];
            }
// var_dump($_FILES);die();
            if (!empty($_FILES["vendorLogo"]["name"])) {
                if ((($_FILES["vendorLogo"]["type"] == "image/gif") 
                        || ($_FILES["vendorLogo"]["type"] == "image/jpeg") 
                        || ($_FILES["vendorLogo"]["type"] == "image/jpg") 
                        || ($_FILES["vendorLogo"]["type"] == "image/pjpeg"))
                        || ($_FILES["vendorLogo"]["type"] == "image/png")
                        && ($_FILES["vendorLogo"]["size"] < 20000)) {
                    if ($_FILES["vendorLogo"]["error"] > 0) {
                        $errors['fileError'] = "File Error Return Code: " . $_FILES["vendorLogo"]["error"];
                        $log->Warn("File Error Return Code: " . $_FILES["vendorLogo"]["error"] . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    } else {
                        $filename = $config_basedir . "images/vendor/" . $_FILES["vendorLogo"]["name"];
                        $location = $config_app_basedir  . "www/images/vendor/" . $_FILES["vendorLogo"]["name"];
                        if (file_exists($location)) {
                            $log->Warn("Failure: " . $_FILES["vendorLogo"]["name"] . " already exists (File: " . $_SERVER['PHP_SELF'] . ")");
                        } else {
							if (!copy($_FILES['vendorLogo']['tmp_name'], $location)) {
								$errors['fileInvalid'] = "Upload Failed";
								$log->Warn("Failure: Invalid File(File: " . $_SERVER['PHP_SELF'] . ")");
								$_SESSION['errors'] = $errors;
								session_write_close();
								header("Location: " . $config_basedir . "vendors.php?error");
								exit();
							}
                            // *** 1) Initialize / load image  
                            $resizeObj = new resize($location);
                            // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)  
                            $resizeObj->resizeImage(16, 16, 'auto');
                            // *** 3) Save image  
                            $resizeObj->saveImage($location, 100);
                        }
                    }
                } else {
                    $errors['fileInvalid'] = "Invalid File";
                    $log->Warn("Failure: Invalid File(File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php?error");
                    exit();
                }
            } else {
                /* set location variable as defaultImg for later use in SQL statement, reason is user is not obliged to upload a file */
                $filename = "images/logos/rconfig16.png";
            }
            /* end validate */

            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
            if (empty($_POST['editid'])) { // do the add/ INSERT
                if (ctype_alnum($vendorName)) {
                    $db2->query("INSERT INTO vendors (vendorName, vendorLogo, status) VALUES (:vendorName, :filename, '1')");
                    $db2->bind(':vendorName', $vendorName);
                    $db2->bind(':filename', $filename);
                    $queryResult = $db2->execute();
                    if ($queryResult) { // if Q was good, send back a sucess to the file
                        $errors['Success'] = "Added new vendor " . $vendorName . " to Database";
                        $log->Info("Success: Added new vendor, " . $vendorName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "vendors.php");
                    } else {
                        $errors['Fail'] = "ERROR: Could not add new vendor " . $vendorName . " to Database";
                        $log->Fatal("Fatal: ERROR: Could not add new vendor " . $vendorName . " to Database (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "vendors.php?error");
                        exit();
                    }
                } else {
                    $errors['vendorName'] = "Vendor Name Field was not a string";
                    $log->Warn("Failure: vendorName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php?error");
                    exit();
                }
            } else { // do the UPDATE/EDIT
                if (ctype_alnum($vendorName)) {
                    $id = $_POST['editid'];
                    // if an edit takes place and a new logo is not updated, keep the current logo
                    $db2->query("SELECT vendorLogo FROM vendors WHERE status = 1 AND id = :id");
                    $db2->bind(':id', $id);
                    $queryResult = $db2->resultset();
                    // only update $location if I get a result from above for given vendor ID
                    if (empty($_FILES["vendorLogo"]["name"]) && !empty($queryResult[0])) { // if an image was not chosen to be uploaded & the select query returned a result
                        $location = $queryResult[0]['vendorLogo'];
                    }
                    $db2->query("UPDATE vendors SET vendorName = :vendorName, vendorLogo = :location WHERE id = :id");
                    $db2->bind(':vendorName', $vendorName);
                    $db2->bind(':location', $location);
                    $db2->bind(':id', $id);
                    $queryResult = $db2->execute();
                    if ($queryResult) {
                        $errors['Sucess'] = "Edited vendor " . $vendorName . " in Database";
                        $log->Info("Success: Edited vendor, " . $vendorName . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "vendors.php");
                    } else {
                        $errors['Fail'] = "ERROR: Could not edit vendor " . $vendorName . " in Database";
                        $log->Fatal("Fatal: ERROR: Could not edit vendor " . $vendorName . " in Database (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "vendors.php?error");
                        exit();
                    }
                } else {
                    $errors['vendorName'] = "Vendor Name Field was not a string";
                    $log->Warn("Failure: vendorName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php?error");
                    exit();
                }
            }
            /* end 'id' post check */
        } else {
            $errors['vendorName'] = "Vendor Name Field cannot be empty";
            $log->Warn("Failure: vendorName was empty(File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "vendors.php?error");
            exit();
        }
    }
    /* end 'add' if */

    /* begin delete check */ elseif (isset($_POST['del'])) {
        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "vendors.php?error");
            exit();
        }
        /* the query */
        $db2->query("UPDATE vendors SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $queryResult = $db2->execute();
        if ($queryResult) {
            $log->Info("Success: Deleted vendor in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete vendor in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
        $db2->query("SELECT id, vendorName, vendorLogo FROM vendors WHERE status = 1 AND id = :id");
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