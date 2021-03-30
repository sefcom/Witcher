<?php

// called from www\lib\crud\scheduler.crud.php to setup a task to configure devices
// requires - full path required
require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
//require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection2.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
require('/home/rconfig/classes/spyc.class.php');
require("/home/rconfig/classes/reportTemplate.class.php");
require_once("/home/rconfig/config/config.inc.php");
require_once("/home/rconfig/config/functions.inc.php");
// declare DB Class
$db2 = new db2();
//setup backend scripts Class
$backendScripts = new backendScripts($db2);
// get & set time for the script
$backendScripts->getTime();
// declare Logging Class
$log = ADLog::getInstance();
$log->logDir = $config_app_basedir . "logs/";
// script startTime and use extract to convert keys into variables for the script
extract($backendScripts->startTime());
// get ID from argv[1] input
/// if statement to check first argument in phpcli script - otherwise the script will not run under phpcli - similar to PHP getopt()
// script will exit with Error if not TID is sent
if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
    // Get/Set Task ID - as sent from cronjob when this script is called and is stored in DB.nodes table also
    $tid = $_GET['id']; // set the Task ID
} else {
    echo $backendScripts->errorId($log, 'Task ID');
}
//set $argv to true of false based on 2nd parameter input
if (isset($argv[2]) && $argv[2] == 'true') {
    $argv = true;
} else {
    $argv = false;
}
extract($backendScripts->debugOnOff($db2, $argv));
$debug = new debug($debugPath);
// check how the script was run and log the info
extract($backendScripts->invokationCheck($log, $tid, php_sapi_name(), $_SERVER['TERM'], $_SERVER['PHP_SELF']));

// get task details from DB
$db2->query("SELECT taskname, mailConnectionReport, snipId FROM tasks WHERE status = '1' AND id = :tid");
$db2->bind(':tid', $tid);
$taskRow = $db2->resultset();
$taskname = $taskRow[0]['taskname'];
$snipId = $taskRow[0]['snipId'];

// create connection report file
$reportFilename = 'configSnippetReport' . $date . '.html';
$reportDirectory = 'configSnippetReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$connStatusFail = '<font color="red">Connection Fail</font>';
$connStatusPass = '<font color="green">Connection Success</font>';

// get timeout setting from DB
$db2->query("SELECT deviceConnectionTimout FROM settings");
$timeoutResult = $db2->resultset();
$timeout = $timeoutResult[0]['deviceConnectionTimout'];

// Get active nodes for a given task ID
// Query to retrieve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName,  deviceIpAddr, deviceEnablePrompt, devicePrompt, deviceUsername, devicePassword, deviceEnablePassword, templateId, nodeCatId
		FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1");
$resultNodesRes = $db2->resultset();
if (!empty($resultNodesRes)) {
    // push rows to $devices array
    $devices = array();
    foreach ($resultNodesRes as $row) {
        array_push($devices, $row);
    }

// get the config snippet data from the DB
    $db2->query("SELECT * FROM snippets WHERE id = :snipId");
    $db2->bind(':snipId', $snipId);
    $cmdsSql = $db2->resultset();
    $snippet = $cmdsSql[0]['snippet'];
    $snippetArr = explode("\n", $snippet); // explode text new lines to array
    $snippetArr = array_map('trim', $snippetArr); // trim whitespace from each array value
    $tableRow = "";
    foreach ($devices as $device) {
// debugging check and action
        if ($debugOnOff === '1' || isset($cliDebugOutput)) {
            $debug->debug($device);
        }
        // decrypt PWs if key is set
        // check if encryption already set in DB
        $db2->query("SELECT passwordEncryption from settings");
        if($db2->resultsetCols()[0] == 1){
            $devicePassword = encrypt_decrypt('decrypt', $device['devicePassword']);
            $deviceEnablePassword = encrypt_decrypt('decrypt', $device['deviceEnablePassword']);
        } else {
            $devicePassword = $device['devicePassword'];
            $deviceEnablePassword = $device['deviceEnablePassword'];
        }
        
        // get template
        $db2->query("SELECT fileName FROM templates WHERE id = " . $device['templateId']);
        $getTemplate = $db2->resultsetCols();
        $templateparams = Spyc::YAMLLoad($getTemplate[0]);

        // ok, verification of host reachability based on socket connection to port i.e. 22 or 23. If fails, continue to next foreach iteration
        $status = getHostStatus($device['deviceIpAddr'], $templateparams['connect']['port']); // getHostStatus() from functions.php 
        if (preg_match("/Unavailable/", $status) === 1) {
            $text = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
            $report->eachComplianceDataRowDeviceName($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }
        // get the category for the device						
        $db2->query("SELECT categoryName FROM categories WHERE id = :nodeCatId");
        $db2->bind(':nodeCatId', $device['nodeCatId']);
        $catNameRow = $db2->resultset();
        $catName = $catNameRow[0]; // select only first value returned
        // declare file Class based on catName and DeviceName
        $file = new file($catName['categoryName'], $device['deviceName'], $config_data_basedir);
        // Connect for each row returned - might want to do error checking here based on if an IP is returned or not
        $conn = new Connection($device['deviceIpAddr'], 
                $device['deviceUsername'], 
                $devicePassword, 
                $deviceEnablePassword, 
                $templateparams['connect']['port'], 
                $templateparams['connect']['timeout'],
                $templateparams['auth']['username'], 
                $templateparams['auth']['password'],
                $templateparams['auth']['enable'],
                $templateparams['auth']['enableCmd'],
                $device['deviceEnablePrompt'],
                $templateparams['auth']['enablePassPrmpt'],
                $device['devicePrompt'],
                $templateparams['config']['linebreak'],
                $templateparams['config']['paging'],
                $templateparams['config']['pagingCmd'],
                $templateparams['config']['pagerPrompt'],
                $templateparams['config']['pagerPromptCmd'],
                $templateparams['config']['resetPagingCmd'],
                $templateparams['auth']['hpAnyKeyStatus'],
                $templateparams['auth']['hpAnyKeyPrmpt']
                );

        $failureText = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
        $connectedText = "Success: Connected to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for taskID " . $tid;
        // Set VARs
        $prompt = $device['devicePrompt'];
        if (!$prompt) {
            echo "Command or Prompt Empty - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn("Command or Prompt Empty - for function switch in  Success:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
        }
        // if connection is telnet, connect to device function
        if ($templateparams['connect']['protocol'] == 'telnet') {
            if ($conn->connectTelnet() === false) {
                $log->Conn($connFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                $report->eachData($device['deviceName'], $connStatusFail, $failureText); // log to report
                echo $failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                continue; // continue; probably not needed now per device connection check at start of foreach loop - failsafe?
            }
            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
            $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report
            foreach ($snippetArr as $k => $command) {
                $conn->writeSnippetTelnet($command, $result);
                $tableRow .= "
			<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
                            <td>" . nl2br($result) . "</td>							
			</tr>
			";
                // debugging check & write to file
                if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                    $debug->debug($result);
                    echo $result;
                }
            }
            if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                 echo 'Notice: Close Connection';
            }           
            // close the connection if it still open. It maybe already closed if a quit was in the snippet
                    $conn->closeTelnet($templateparams['config']['resetPagingCmd'], $templateparams['config']['saveConfig'], $templateparams['config']['exitCmd']); // close telnet connection - ssh already closed at this point
        } elseif ($templateparams['connect']['protocol'] == 'ssh') { //SSHv2 - cause SSHv2 is likely to come before SSHv1
            // SSH conn failure 
            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
            $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report
            $result = $conn->writeSnippetSSH($snippetArr, $prompt);
            $tableRow .= "
		<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
			<td>" . nl2br($result) . "</td>							
		</tr>
		";
                // debugging check & write to file
                if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                    $debug->debug($result);
                    echo $result;
                }
        } else {
            continue;
        }
        // send data output to the report
        $report->eachConfigSnippetData($tableRow);
        // unset tableRow data for next iteration
        $tableRow = "";
    }// devices foreach
    // close table row tags	
    // script endTime
    extract($backendScripts->endTime($time_start));
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    $report->footer();
    // Check if mailConnectionReport value is set to 1 and send email
    if ($taskRow[0]['mailConnectionReport'] == '1') {
        $backendScripts->reportMailer($db2, $log, $title, $config_reports_basedir, $reportDirectory, $reportFilename, $taskname);
    }
    // reset folder permissions for data directory. This means script was run from the shell as possibly root 
    // i.e. not apache user and this cause dir owner to be reset causing future downloads to be permission denied
    // check if $resetPerms is set to 1, and invoke permissions reset for /home/rconfig/*
    extract($backendScripts->resetPerms($log, $resetPerms));
    echo $resetAlert; // from resetPerms method
} else {
    echo $backendScripts->finalAlert($log, $_SERVER['PHP_SELF']);
}
