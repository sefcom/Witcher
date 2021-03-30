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
    require_once("../../../classes/crontab.class.php");
    require_once("../../../classes/phpmailer/class.phpmailer.php");

    $db2 = new db2();
    $log = ADLog::getInstance();

    /* Add tasks Here */
    if (isset($_POST['add'])) {
        $errors = array();

        /* FORM FIELD VALIDATION BELOW */

        // validate taskType field
        if (!empty($_POST['taskType'])) {
            $taskType = $_POST['taskType'];
        } else {
            $errors['taskType'] = "Task Type cannot be empty";
        }

        // validate taskName & escape field
        if (!empty($_POST['taskName']) && is_string($_POST['taskName'])) {
            $taskName = $_POST['taskName'];
        } else {
            $errors['taskName'] = "Task Name cannot be empty";
        }

        // validate taskDesc & escape field
        if (!empty($_POST['taskDesc']) && is_string($_POST['taskDesc'])) {
            $taskDesc = $_POST['taskDesc'];
        } else {
            $errors['taskDesc'] = "Task Description cannot be empty";
        }

        // validate mailReport checkbox
        if (!empty($_POST['mailConnectionReport'])) {
            $mailConnectionReportChk = $_POST['mailConnectionReport'];
        } else {
            $mailConnectionReportChk = '0';
        }

        // validate snippetSlct select 
        if (isset($_POST['snippetSlct'])) {

            if ($_POST['snippetSlct'] != 'select') {
                $snipId = deleteChar($_POST['snippetSlct'], 10); // delete snippetId- from returned value
            } else {
                $errors['snippetSlct'] = "Please select a snippet";
            }
        } else {
            $errors['snippetSlct'] = "Select a Snippet";
            $snipId = '';
        }

        // validate mailReport checkbox
        if (!empty($_POST['mailErrorsOnly'])) {
            $mailErrorsOnlyChk = $_POST['mailErrorsOnly'];
        } else {
            $mailErrorsOnlyChk = '0';
        }

        // validate selectRadio field
        if (isset($_POST['selectRadio'])) {
            if ($_POST['selectRadio'] == 'deviceSelectRadio' && empty($_POST['deviceSelect'])) {
                $errors['deviceSelectRadio'] = "You must choose some Devices";
            } elseif ($_POST['selectRadio'] == 'catSelectRadio' && empty($_POST['catSelect'])) {
                $errors['catSelectRadio'] = "You must choose a category(s)";
            }
        } else {
            $errors['selectRadio'] = "You must choose either devices or categories";
        }

        // validate catSelect select // used only if download type is selected on scheduler
        $categories = "";
        if (isset($_POST['catSelect'])) {
            // serialsed data should always be an array for checks in devies.crud.php for example if not already an array
            $categories = serialize($_POST['catSelect']);
        }

        // validate catId select // used only if report type is selected on scheduler
        if (isset($_POST['catId'])) {
            // serialsed data should always be an array for checks in devies.crud.pp for example
            $categories = serialize($_POST['catId']);
        }

        // validate $deviceSelect select
        if (isset($_POST['deviceSelect'])) {
            $nodeIdArr = $_POST['deviceSelect'];
        } else {
            $errors['deviceSelectRadio'] = "Error: the deviceSelect array was empty";
        }
        // validate $deviceSelect select
        if (isset($_POST['catCommand'])) {
            $catCommand = $_POST['catCommand'];
        } else {
            $errors['catCommand'] = "Error: the catCommand select was empty";
        }

        // get the posts from the cron form and trim
        $minute = trim($_POST['minute']);
        $hour = trim($_POST['hour']);
        $day = trim($_POST['day']);
        $month = trim($_POST['month']);
        $weekday = trim($_POST['weekday']);

        // put them in an array
        $cronArray = array(
            "minute" => $minute,
            "hour" => $hour,
            "day" => $day,
            "month" => $month,
            "weekday" => $weekday
        );
        $regexPattern = '/^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/';

        // test if any of the cron fields are empty
        foreach ($cronArray as $cronK => $cronV) {
            if ($cronV === null || $cronV === '') {
                $errors['cron'] = 'A field was empty!';
                break;
            }
            if (!preg_match($regexPattern, $cronV)) {
                $errors['cron'] = 'Field ' . $cronK . ' contains invalid characters';
                // break;	
            }
        }

        /* END - VALIDATION */

        /* SQL and CRONTAB Additions below
         * Next code is based on the fact that the above validation has passed
         */
        if (!empty($_POST['taskName'])) {
            /* INSTALL CRON TABS */
            // Create Random Task ID to identify for scheduled script later
            $randNum = rand(100000, 999999);

            // pre-pend crontab task name with  # 
            $taskName = "#" . $randNum . " - " . $_POST['taskName']; // add hash for comment in CRON script
            $taskDesc = "#" . $randNum . " - " . $_POST['taskDesc'];
            $cronPattern = $minute . ' ' . $hour . ' ' . $day . ' ' . $month . ' ' . $weekday . ' ';

            // check the task type selected and update the $cronScript VAR with the correct taskType
            if ($_POST['taskType'] == 1) { // taskType is download Configurations
                $script = "showCmdScript.php";
            } else if ($_POST['taskType'] == 2) {
                if (strstr($_POST['reportTypeSlct'][0], '-', true) == 'compliance') { // check if the value form the select begins with str compliance
                    $complianceId = substr($_POST['reportTypeSlct'][0], strrpos($_POST['reportTypeSlct'][0], '-') + 1); // get everything after the '-' and set the complianceId
                    $script = "complianceScript.php";
                } else {
                    $script = "compareReportScript.php"; // default script name for $_POST['taskType'] == 2 (reports)
                }
            } else if ($_POST['taskType'] == 3) { // taskType is Schedule Config Snippet
                $script = "configCategoryScript.php";
            } else {
                $errors['taskTypeError'] = "There was a problem selecting the Task Type"; // throw an error
                $log->Warn("Failure: There was a problem selecting the Task Type (File: " . $_SERVER['PHP_SELF'] . ")");
            }

            $cronScript = "php /home/rconfig/lib/" . $script . " " . $randNum;

            $crontab = new crontab($cronScript, $taskName, $taskDesc, $cronPattern);
            $crontabUpdateResult = $crontab->addCron();

            if ($crontabUpdateResult == 0) {
                $errors['crontab'] = "Failure: Could not update crontab on the server "; // throw an error
                $log->Fatal("Failure: Could not update crontab on the server (File: " . $_SERVER['PHP_SELF'] . ")");
            }

            /* END - INSTALL CRON TABS */

            /* ADD CRONTAB RECORDS TO DB */
            if ($_POST['taskType'] == 1) { //taskType is download Configurations
                $db2->query("INSERT INTO tasks(id, taskType, taskName, taskDescription, crontime, croncmd, addedby, dateadded, status, catId, mailConnectionReport, mailErrorsOnly) 
                        VALUES  (:randNum, :taskType, :taskName, :taskDesc, :cronPattern, :cronScript, :username,  NOW(),  '1', :categories, :mailConnectionReportChk, :mailErrorsOnlyChk)");
                $db2->bind(':randNum', $randNum);
                $db2->bind(':taskType', $_POST['taskType']);
                $db2->bind(':taskName', $_POST['taskName']);
                $db2->bind(':taskDesc', $_POST['taskDesc']);
                $db2->bind(':cronPattern', $cronPattern);
                $db2->bind(':cronScript', $cronScript);
                $db2->bind(':username', $_SESSION['username']);
                $db2->bind(':categories', $categories);
                $db2->bind(':mailConnectionReportChk', $mailConnectionReportChk);
                $db2->bind(':mailErrorsOnlyChk', $mailErrorsOnlyChk);
                $queryResult = $db2->execute();
            } else if ($_POST['taskType'] == 2) {
                $db2->query("INSERT INTO tasks (id, taskType, taskName, taskDescription, crontime, croncmd, addedby, dateadded, status, mailConnectionReport, catId, catCommand, complianceId) 
                    VALUES (:randNum, :taskType, :taskName, :taskDesc, :cronPattern, :cronScript, :username, NOW(), '1', :mailConnectionReportChk, :categories, :catCommand, :complianceId)");
                $db2->bind(':randNum', $randNum);
                $db2->bind(':taskType', $taskType);
                $db2->bind(':taskName', $_POST['taskName']);
                $db2->bind(':taskDesc', $_POST['taskDesc']);
                $db2->bind(':cronPattern', $cronPattern);
                $db2->bind(':cronScript', $cronScript);
                $db2->bind(':username', $_SESSION['username']);
                $db2->bind(':mailConnectionReportChk', $mailConnectionReportChk);
                $db2->bind(':categories', $categories);
                $db2->bind(':catCommand', $catCommand);
                $db2->bind(':complianceId', $complianceId);
                $queryResult = $db2->execute();
            } else if ($_POST['taskType'] == 3) {
                $db2->query("INSERT INTO tasks (id, taskType, taskName, taskDescription, crontime, croncmd, addedby, dateadded, status, mailConnectionReport, catId, snipId) 
                    VALUES (:randNum, :taskType, :taskName, :taskDesc, :cronPattern, :cronScript, :username, NOW(), '1', :mailConnectionReportChk, :categories, :snipId)");
                $db2->bind(':randNum', $randNum);
                $db2->bind(':taskType', $taskType);
                $db2->bind(':taskName', $_POST['taskName']);
                $db2->bind(':taskDesc', $_POST['taskDesc']);
                $db2->bind(':cronPattern', $cronPattern);
                $db2->bind(':cronScript', $cronScript);
                $db2->bind(':username', $_SESSION['username']);
                $db2->bind(':mailConnectionReportChk', $mailConnectionReportChk);
                $db2->bind(':categories', $categories);
                $db2->bind(':snipId', $snipId);
                $queryResult = $db2->execute();
            }
            if ($queryResult) {
                /* ADD NEW COLUMN TO NODES TABLE */
                /*  add to taskID column to nodes table in database to specify which nodes belong to this task 
                  default of '2' means all nodes are not part of this task will update with 1 for node selection
                  in next query
                 */
                $db2->query("ALTER TABLE `nodes` ADD COLUMN taskId" . $randNum . " VARCHAR(20) NOT NULL DEFAULT '2' AFTER `id`");
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $log->Info("Success: Added task Column to nodes table to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                } else {
                    $errors['Fail'] = "ERROR: could not update table nodes";
                    $log->Fatal("Fatal: could not update table nodes (File: " . $_SERVER['PHP_SELF'] . ")");
                }
                /* END - ADD NEW COLUMN TO NODES TABLE */

                /* UPDATE NEW TASK COLUMN IN NODES TBL WITH '1' FOR SELECTED NODES/CATEGORIES */
                // Amend all selected Nodes new TaskID with a '1' to identify an active state for this task

                $categories = unserialize($categories); // unserialize $categories before next tasks because was serialised for DB input

                if (!empty($categories)) {
                    if (is_array($categories)) { // check if $categories is an array because when selecting compare report, cat option is single value
                        $sanitized_post_ids = array();
                        foreach ($categories as $id) {
                            $sanitized_post_ids[] = intval($id);
                        }
                        $in_str = implode(',', $sanitized_post_ids);
                        $db2->query("SELECT id FROM nodes WHERE nodeCatId IN (:in_str)");
                        $db2->bind(':in_str', $in_str);
                        $catRes = $db2->resultset();
                    } else {
                        $catRes = $db2->query("SELECT id FROM nodes WHERE nodeCatId = $categories ");
                        $db2->query("SELECT id FROM nodes WHERE nodeCatId IN (:categories)");
                        $db2->bind(':categories', $categories);
                        $catRes = $db2->resultset();
                    }

                    $nodeIdArr = array();
                    foreach ($catRes as $catRow) {
                        $nodeIdArr[] = $catRow['id'];
                    }
                }

                if (!empty($nodeIdArr)) {
                    //Let's sanitize it
                    $sanitized_ids = array();
                    foreach ($nodeIdArr as $nodeId) {
                        $sanitized_ids[] = intval($nodeId);
                    }

                    //Get the ids and add a trailing ",", but remove the last one
                    $in_str = implode(',', $sanitized_ids);
                    //Build the sql and execute it
                    $db2->query("UPDATE nodes SET taskId" . $randNum . " = 1 WHERE id IN (" . $in_str . ") AND status = 1");
                    $queryResult = $db2->execute();
                    if ($queryResult) {
                        $log->Info("Success: Added task Column to nodes table to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    } else {
                        $errors['Fail'] = "ERROR: Could not add task Column to nodes table";
                        $log->Fatal("Fatal: ERROR: Could not add task Column to nodes table (File: " . $_SERVER['PHP_SELF'] . ")");
                    }
                }
                /* END - UPDATE NEW COLUMN WITH '1' FOR SELECTED NODES/CATEGORIES */
            }

            $errors['Success'] = "Added Task Successfully";
            $log->Info("Success: Added Task to DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "scheduler.php");
            exit();
        } else {
            $errors['Fail'] = "ERROR: Could not add task";
            $log->Fatal("Fatal: Could not add task (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        /* END - ADD CRONTAB RECORDS TO DB */


        // set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "scheduler.php?errors");
            exit();
        }
    } // end add code

    /* begin delete check */ elseif (isset($_POST['del'])) {
        $tid = $_POST['id'];

        // Query to retrieve row for given ID
        $db2->query("SELECT taskName, taskDescription, crontime, croncmd  FROM tasks WHERE id = :tid");
        $db2->bind(':tid', $tid);
        $taskSelectResult = $db2->resultset();
        // Put row results into variables
        foreach ($taskSelectResult as $row) {
            $delTaskName = "#" . $tid . " - " . $row['taskName'];
            $delTaskDesc = "#" . $tid . " - " . $row['taskDescription'];
            $delCronJob = $row['crontime'] . $row['croncmd'];
        }

        /* The cronfeed.txt/CronTab Delete */
        $crontab = new crontab($delCronJob, $delTaskName, $delTaskDesc, $row['crontime']);
        $crontabUpdateResult = $crontab->removeCron($delTaskName, $delTaskDesc, $delCronJob);

        if ($crontabUpdateResult == 0) {
            $errors['crontab'] = "Failure:Could not update crontab on the server "; // throw an error
            $log->Fatal("Failure: Could not update crontab on the server (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        /* the DB query */
        $db2->query("UPDATE tasks SET status = 2 WHERE id = :tid");
        $db2->bind(':tid', $tid);
        $delTaskQResult = $db2->execute();

        // Delete the tid column from the nodes table
        $db2->query("ALTER TABLE nodes DROP COLUMN taskId" . $tid . ";");
        $db2->execute();

        if ($delTaskQResult) {
            $log->Info("Success: Deleted task " . $_POST['id'] . " in DB and CRONTAB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete task " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ 
    elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $db2->query("SELECT * FROM tasks WHERE status = 1 AND id = :id");
        $db2->bind(':id', $id);
        $tasksResult = $db2->resultset();
        $items = array();
        foreach ($tasksResult as $row) {
            array_push($items, $row);
        }
        $db2->query("SELECT deviceName FROM nodes WHERE taskId" . $id . " = 1");
        $db2->bind(':id', $id);
        $devicesTaskQResult = $db2->resultset();
        $devices = array();
        foreach ($devicesTaskQResult as $rowDev) {
            array_push($devices, $rowDev);
        }
        // assumption is, if its blank, when the task was created we selected specific devices and not a full category
        // then we set the catOrDevices value to 1, meaning we have a list of devices to show and not a category
        if($items[0]['catId']){ 
            if (count(unserialize($items[0]['catId'])) >= 1 && is_array(unserialize($items[0]['catId']))) {
                $cats = implode(",", unserialize($items[0]['catId']));
            } else {
                $cats = unserialize($items[0]['catId']); // if an array wasn't passed, its a single string item unserialised from DB
            } 
            $db2->query("SELECT categoryName FROM categories WHERE id IN (". $cats .")");
            $catTaskQResult = $db2->resultsetCols();       
            $result['categoryName'] = $catTaskQResult;
        }
        $result["rows"] = $items;
        $result["devices"] = $devices;
        echo json_encode($result);
    }
    /* end GetId */
}