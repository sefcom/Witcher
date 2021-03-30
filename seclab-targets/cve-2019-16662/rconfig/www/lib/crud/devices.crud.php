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

    /* Add Custom Property Here */
    if (isset($_POST['add'])) {
        $errors = array();

        // validate deviceName field
        if (!empty($_POST['deviceName'])) {
            $deviceName = $_POST['deviceName'];
            $deviceName = cleanDeviceName($deviceName);
            // check device name for whitespace
            if (!chkWhiteSpaceInStr($deviceName) === false) {
                $errors['deviceName'] = "Device Name cannot contain spaces";
                $log->Warn("Failure: Device Name cannot contain spaces (File: " . $_SERVER['PHP_SELF'] . ")");
                $deviceName = ""; // set back to blank so text with spaces is not returned to devices form
            }
        } else {
            $errors['deviceName'] = "Device Name cannot be empty";
            $log->Warn("Failure: Device Name cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        if (!empty($_POST['deviceIpAddr'])) {
            // validate deviceIpAddr IP address
            if (!filter_var($_POST['deviceIpAddr'], FILTER_VALIDATE_IP)) {
                $errors['deviceIpAddr'] = "IP Address is not valid ";
                $log->Warn("Failure: IP Address is not valid (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $deviceIpAddr = $_POST['deviceIpAddr'];
            }
        } else {
            $errors['deviceIpAddr'] = "IP Address cannot be empty ";
            $log->Warn("Failure: IP Address cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate devicePrompt field
        if (!empty($_POST['devicePrompt'])) {
            $devicePrompt = $_POST['devicePrompt'];
        } else {
            $errors['devicePrompt'] = "Device Prompt cannot be empty";
            $log->Warn("Failure: Device Prompt cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        // validate deviceEnablePrompt field
        $deviceEnablePrompt = str_replace(' ', '', $_POST['deviceEnablePrompt']);

        // validate vendorId field
        if (!empty($_POST['vendorId']) && ctype_digit($_POST['vendorId'])) {
            $vendorId = $_POST['vendorId'];
        } else {
            $errors['vendorId'] = "Vendor field cannot be empty";
            $log->Warn("Failure: Vendor Field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate deviceModel field
        if (!empty($_POST['deviceModel'])) {
            $deviceModel = $_POST['deviceModel'];
        } else {
            $errors['deviceModel'] = "Device Model cannot be empty";
            $log->Warn("Failure: Device Model cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate defaultCreds check boxes
        if (isset($_POST['defaultCreds'])) {
            $defaultCreds = '1';
        } else {
            $defaultCreds = '0';
        }

        // validate deviceUsername field
        if (!empty($_POST['deviceUsername']) && is_string($_POST['deviceUsername'])) {
            $deviceUsername = $_POST['deviceUsername'];
        } else {
            $deviceUsername = '';
        }
        
        // check if PW encryption enabled
        $db2->query("SELECT passwordEncryption from settings");
        $encryptionEnabled = false;
        if($db2->resultsetCols()[0] == 1){
           $encryptionEnabled = true; 
        }
        // validate devicePassword field
        if (!empty($_POST['devicePassword']) && is_string($_POST['devicePassword'])) {
            if($encryptionEnabled == true){ $devicePassword = encrypt_decrypt('encrypt', $_POST['devicePassword']); } else { $devicePassword = $_POST['devicePassword']; }
        } else {
            $devicePassword = '';
        }
        // validate devicePassword field
        if (!empty($_POST['deviceEnablePassword']) && is_string($_POST['deviceEnablePassword'])) {
            if($encryptionEnabled == true){ $deviceEnablePassword = encrypt_decrypt('encrypt', $_POST['deviceEnablePassword']); } else { $deviceEnablePassword = $_POST['deviceEnablePassword']; }
        } else {
            $deviceEnablePassword = '';
        }

        // validate vendorId field
        if (!empty($_POST['templateId']) && ctype_digit($_POST['templateId'])) {
            $templateId = $_POST['templateId'];
        } else {
            $errors['templateId'] = "Template field cannot be empty";
            $log->Warn("Failure: Template Field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        // validate catId field
        if (ctype_digit($_POST['catId'])) {
            $catId = $_POST['catId'];
        } else {
            $errors['catId'] = "Category field cannot be empty";
            $log->Warn("Failure: Category field did not pass numeric value i.e. catId OR awas empty (File: " . $_SERVER['PHP_SELF'] . ")");
            $catId = '';
        }

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        } else {
            $errors['username'] = "Username passed to devices.crud.php was not valid";
            $log->Warn("Failure: Username passed to devices.crud.php was not valid (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        /* See if category is added to any scheduled tasks and get correct column name if it is */
        $db2->query("SELECT id, catId FROM tasks WHERE status = '1'");
        $resultCatSelect = $db2->resultset();
        $taskIdColumns = '';
        $taskValue = '';
        foreach ($resultCatSelect as $taskRow) {
            if (!empty($taskRow['catId'])) {
                $catIdArray = unserialize($taskRow['catId']);
                if(gettype($catIdArray) == 'string'){ // if value return is a string and not an array, convert it
                    $catIdArray = explode(',', $catIdArray);
                }
            }
            if (!empty($taskRow['catId']) && is_array($taskRow) && in_array($catId, $catIdArray)) {
                $taskIdColumns .= "taskId" . $taskRow['id'] . ", ";
                $taskValue .= "'1',";
            }
        }

        // set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 
        if (!empty($errors)) {
            if (isset($deviceName)) {
                $_SESSION['deviceName'] = $deviceName;
            }
            if (isset($deviceIpAddr)) {
                $_SESSION['deviceIpAddr'] = $deviceIpAddr;
            }
            if (isset($devicePrompt)) {
                $_SESSION['devicePrompt'] = $devicePrompt;
            }
            if (isset($deviceEnablePrompt)) {
                $_SESSION['deviceEnablePrompt'] = $deviceEnablePrompt;
            }
            if (isset($vendorId)) {
                $_SESSION['vendorId'] = $vendorId;
            }
            if (isset($deviceModel)) {
                $_SESSION['deviceModel'] = $deviceModel;
            }
            if (isset($defaultCreds)) {
                $_SESSION['defaultCreds'] = $defaultCreds;
            }
            if (isset($deviceUsername)) {
                $_SESSION['deviceUsername'] = $deviceUsername;
            }
            if (isset($devicePassword)) {
                $_SESSION['devicePassword'] = $devicePassword;
            }
            if (isset($deviceEnablePassword)) {
                $_SESSION['deviceEnablePassword'] = $deviceEnablePassword;
            }
            if (isset($catId)) {
                $_SESSION['catId'] = $catId;
            }
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "devices.php?error");
            exit();
        } else {
            // Search POST for any key with partial string 'custom_' to get the names of the 
            // custom fields column names in DB
            $custom_results = array();
            foreach ($_POST as $k => $v) {
                if (strstr($k, 'custom_')) {
                    // create new 'custom_results' array with key and values from the post matching 'custom'
                    $custom_results[$k] = $v;
                }
            }

            /* http://php.net/manual/en/function.extract.php */
            /* Extract Keys as Column Names for dynamic Query
             * and extract values as DB values for dynamic query */
            $dynamicValues = array();
            $dynamicTbls = array();
            $customPropEditQueryStr = '';
            foreach ($custom_results as $key => $value) {
                $customPropEditQueryStr .= $key . " = " . "'" . $value . "', "; // create the edit query for any custom properties fields
                array_push($dynamicValues, $value);
                array_push($dynamicTbls, $key);
            }
            // Output above arrays to simple string variables for use in the query
            $dynamicValuesBlk = implode("', '", $dynamicValues);
            $dynamicTblsBlk = implode(", ", $dynamicTbls);

            // create part of the UPDATE query for custom_ fields
            $customPropQueryStr = "";
            foreach ($custom_results as $k => $v) {
                $customPropQueryStr = $customPropQueryStr . $k . " = '" . $v . "', ";
            }

            // next if vars are not empty, add a comma to complete SQL statement
            // because if no custom props, or Tasks added errors will occur
            if (!empty($dynamicTblsBlk)) {
                $dynamicTblsBlk = $dynamicTblsBlk . ",";
                if (empty($dynamicValuesBlk)) {
                    $dynamicValuesBlk = "NULL" . ",";
                } else {
                    if (!empty($dynamicValuesBlk)) {
                        $dynamicValuesBlk = "'" . $dynamicValuesBlk . "',";
                    }
                }
            }

            if (!empty($taskIdColumns)) {
//            $taskIdColumns = $taskIdColumns . ",";
            }
            if (!empty($taskValue)) {
                $taskValue = $taskValue;
            } else {
                $taskValue = '';
            }
            /* Begin DB query. This will either be an Insert if $_POST ID is not set - or an edit/Update if ID is set in POST */
            if (empty($_POST['editid'])) {

                $db2->query("INSERT INTO nodes
                (deviceName, 
                deviceIpAddr,
                devicePrompt,
                deviceEnablePrompt,
                deviceUsername,
                devicePassword,
                deviceEnablePassword,
                templateId,
                model,
                vendorId,
                nodeCatId,
                nodeAddedBy,
                defaultCreds,
                " . $dynamicTblsBlk . "
                " . $taskIdColumns . "
                deviceDateAdded,
                status
                )
                VALUES 
                    (:deviceName,
                    :deviceIpAddr,
                    :devicePrompt,
                    :deviceEnablePrompt,
                    :deviceUsername,
                    :devicePassword,
                    :deviceEnablePassword,
                    :templateId,
                    :deviceModel,
                    :vendorId,
                    :catId,
                    :username,
                    :defaultCreds,
                    $dynamicValuesBlk
                    $taskValue
                    CURDATE(),
                    '1')");
                var_dump($taskValue);
                $db2->bind(':deviceName', $deviceName);
                $db2->bind(':deviceIpAddr', $deviceIpAddr);
                $db2->bind(':devicePrompt', $devicePrompt);
                $db2->bind(':deviceEnablePrompt', $deviceEnablePrompt);
                $db2->bind(':deviceUsername', $deviceUsername);
                $db2->bind(':devicePassword', $devicePassword);
                $db2->bind(':deviceEnablePassword', $deviceEnablePassword);
                $db2->bind(':templateId', $templateId);
                $db2->bind(':deviceModel', $deviceModel);
                $db2->bind(':vendorId', $vendorId);
                $db2->bind(':catId', $catId);
                $db2->bind(':username', $username);
                $db2->bind(':defaultCreds', $defaultCreds);
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $errors['Success'] = "Added new device " . $deviceName . " to Database";
                    $log->Info("Success: Added new device, " . $deviceName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php");
                } else {
                    $errors['Fail'] = "ERROR: Could not Insert Record to Database, Check Logs";
                    $log->Fatal("Fatal: Error executing Node Insert (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php?error");
                    exit();
                }
            } else { // if ID is set in post when running a save from the form do an UPDATE
                $id = $_POST['editid'];
                // reset all taskID*  columns to 2 before updating them in the UPDATE query for correct taskId assignments
                $db2->query("SELECT column_name FROM information_schema.COLUMNS c
                        WHERE c.table_schema = 'rconfig35' 
                        AND c.table_name='nodes' 
                        AND c.column_name LIKE '%taskId%'");
                $taskColNames = $db2->resultsetCols();
                foreach ($taskColNames as $taskColName) {
                    $db2->query("UPDATE nodes SET " . $taskColName . " = 2 WHERE id = :id");
                    $db2->bind(":id", $id);
                    $db2->execute();
                }
                // updated taskId Columns partial query for the update statement
                //  explode to array for previously built string. array_filter to remove blanks
                // array_flip to move all values from $taskIdColumns to key names

                $taskIdColumns = rtrim($taskIdColumns, ", "); // delete last comma and space
                $taskIdColumns = array_flip(array_filter(explode(",", $taskIdColumns)));
                // set all values in array to 1 as these are the correct taskId assignments for this device when updating
                $taskIdColumns = array_fill_keys(array_keys($taskIdColumns), 1);
                //iterate over $taskIdColumn array and chnage it to a string for the sql update
                $prefix = '';
                $taskIdColumnList = '';
                foreach ($taskIdColumns as $taskIdColumnK => $taskIdColumnV) {
                    $taskIdColumnList .= $prefix . $taskIdColumnK . ' = ' . $taskIdColumnV;
                    $prefix = ', ';
                }
                // add a trailing comma if the $taskIdColumnList is not empty, otherwise the query is broken
                if ($taskIdColumnList != '') {
                    $taskIdColumnList = $taskIdColumnList . ',';
                }
                echo '<pre>';
                echo 'test';
                $db2->query("UPDATE nodes SET 
                            deviceName = :deviceName,
                            deviceIpAddr = :deviceIpAddr,
                            devicePrompt = :devicePrompt,
                            deviceEnablePrompt = :deviceEnablePrompt,
                            deviceUsername = :deviceUsername, 
                            devicePassword = :devicePassword, 
                            deviceEnablePassword = :deviceEnablePassword, 
                            templateId = :templateId, 
                            model = :deviceModel, 
                            vendorId = :vendorId, 
                            nodeCatId = :catId, 
                            defaultCreds = :defaultCreds,
                            $customPropEditQueryStr
                            $taskIdColumnList
                            deviceDateAdded = CURDATE()
                            WHERE id = :id");
                $db2->bind(':deviceName', $deviceName);
                $db2->bind(':deviceIpAddr', $deviceIpAddr);
                $db2->bind(':devicePrompt', $devicePrompt);
                $db2->bind(':deviceEnablePrompt', $deviceEnablePrompt);
                $db2->bind(':deviceUsername', $deviceUsername);
                $db2->bind(':devicePassword', $devicePassword);
                $db2->bind(':deviceEnablePassword', $deviceEnablePassword);
                $db2->bind(':templateId', $templateId);
                $db2->bind(':deviceModel', $deviceModel);
                $db2->bind(':vendorId', $vendorId);
                $db2->bind(':catId', $catId);
                $db2->bind(':defaultCreds', $defaultCreds);
                $db2->bind(':id', $id);
                $db2->debugDumpParams();
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $errors['Success'] = "Edit device " . $deviceName . " successful";
                    $log->Info("Success: Edit device " . $deviceName . " in DB successful (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php");
                } else {
                    $errors['Fail'] = "ERROR: Could not Edit device " . $deviceName;
                    $log->Fatal("Fatal: ERROR: Could not Edit device " . $deviceName . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php?error");
                    exit();
                }
            }
            /* end check if 'id' is iset in input field */
        }
        /* end '!empty($errors)' check */
    }
    /* end 'add' if */


    /* begin delete check */ elseif (isset($_POST['del'])) {

        /* the query */
        $q = "UPDATE nodes SET status = 2 WHERE id = " . $_POST['id'] . ";";
        $db2->query("UPDATE nodes SET status = 2 WHERE id = :id");
        $db2->bind(':id', $_POST['id']);
        $result = $db2->execute();
        if ($result) {
            $log->Info("Success: Deleted Node ID = " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete node id:" . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ 
    // retirve device details for edit
    elseif (isset($_GET['getRow']) && isset($_GET['id'])) {

        if (ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "devices.php?error");
            exit();
        }

        /* first get custom fieldnames  and impode to create part of final SQL query */
        $db2->query("SELECT customProperty FROM customProperties");
        $qCustProp = $db2->resultset();
        $items = array();
        foreach ($qCustProp as $row) {
            $customProperty = $row['customProperty'];
            array_push($items, $customProperty);
            $customProp_string = implode(", ", $items) . ', ';
        }

        $db2->query("SELECT 
                    n.id,
                    n.deviceName,
                    n.deviceIpAddr,
                    n.devicePrompt,
                    n.deviceEnablePrompt,
                    v.id vendorId,
                    n.model,
                    n.defaultCreds,
                    n.deviceUsername,
                    n.templateId,
                    " . $customProp_string . "
                    cat.id catId
		FROM nodes n
		LEFT OUTER JOIN vendors v ON n.vendorId = v.id
		LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
		LEFT OUTER JOIN categories cat ON n.nodeCatId = cat.id
		WHERE n.status = 1
		AND n.id = :id");
        $db2->bind(':id', $id);
        $qSelectnodeData = $db2->resultset();

        $items = array();
        foreach ($qSelectnodeData as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}