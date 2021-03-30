<?php
// requires - full path required
require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
//require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection2.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
require("/home/rconfig/classes/reportTemplate.class.php");
require('/home/rconfig/classes/spyc.class.php');
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
$log->logDir = $config_app_basedir . "logs/";
// script startTime and use extract to convert keys into variables for the script
extract($backendScripts->startTime());
// get ID from argv input
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

// turn on/off debugging based on $agrv
extract($backendScripts->debugOnOff($db2, $argv));
$debug = new debug($debugPath);
// check how the script was run and log the info
extract($backendScripts->invokationCheck($log, $tid, php_sapi_name(), $_SERVER['TERM'], $_SERVER['PHP_SELF']));
echo $alert;

// get mailConnectionReport Status from tasks table and send email
$db2->query("SELECT taskname, mailConnectionReport, mailErrorsOnly FROM tasks WHERE status = '1' AND id = :tid");
$db2->bind(':tid', $tid);
$taskRow = $db2->resultset();
$taskname = $taskRow[0]['taskname'];
// create connection report file
$reportFilename = 'deviceConnectionReport' . $date . '.html' . date('h_i_s');
$reportDirectory = 'connectionReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$connStatusFail = '<font color="red">Connection Fail</font>';
$connStatusPass = '<font color="green">Connection Success</font>';
// get timeout setting from DB
$timeoutSql = $db2->query("SELECT deviceConnectionTimout FROM settings");
$connResult = $db2->resultset();
$timeout = $connResult[0]['deviceConnectionTimout'];
// Get active nodes for a given task ID
// Query to retrieve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName,  deviceIpAddr, deviceEnablePrompt, devicePrompt, deviceUsername, devicePassword, deviceEnablePassword, templateId, nodeCatId
                FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1");
$getNodes = $db2->resultset();


if (!empty($getNodes)) {
    // push rows to $devices array
    $devices = array();
    foreach ($getNodes as $row) {
        array_push($devices, $row);
    }
    // start looping over every device returned 
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
            $report->eachData($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }
        // get command list for device. This is based on the catId. i.e. catId->cmdId->CmdName->Node
        $db2->query("SELECT cmd.command 
                        FROM cmdCatTbl AS cct
                        LEFT JOIN configcommands AS cmd ON cmd.id = cct.configCmdId
                        WHERE cct.nodeCatId = :nodeCatId");
        $db2->bind(':nodeCatId', $device['nodeCatId']);
        $commands = $db2->resultsetCols();
        $cmdNumRows = count($commands);
        // get the category for the device						
        $db2->query("SELECT categoryName FROM categories WHERE id = :nodeCatId");
        $db2->bind(':nodeCatId', $device['nodeCatId']);
        $catNameRow = $db2->resultset();
        $catName = $catNameRow[0];
        
        // check if there are any commands for this devices category, and if not, error and break the loop for this iteration
        if ($cmdNumRows == 0) {
            $text = "Failure: There are no commands configured for category " . $catName['categoryName'] . " when running taskID " . $tid;
            $report->eachData($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }
        
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

        // if connection is telnet, connect to device function
        if ($templateparams['connect']['protocol'] == 'telnet') {
            $failureText = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
            $connectedText = "Success: Connected to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for taskID " . $tid;

            if ($conn->connectTelnet() === false) {
                $report->eachData($device['deviceName'], $connStatusFail, $failureText); // log to report
                echo $failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                $log->Conn($failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file

                continue; // continue; probably not needed now per device connection check at start of foreach loop - failsafe
            }
            // telnet success logging
            // check if mailErrorsOnly flag is set and if it is, do not log successfuly connections
            if ($taskRow[0]['mailErrorsOnly'] == '0') {
                $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report
            }
            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
        } // end if device access method

        $i = -1; // set i to prevent php notices & becuase the $commands array will always have a start key at 0	
        // loop over commands for given device
        foreach ($commands as $cmd) {
            $i++;
            // Set VARs
            $command = $cmd;
            $prompt = $device['devicePrompt'];

            if (!$command || !$prompt) {
                echo "Command or Prompt Empty - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                $log->Conn("Command or Prompt Empty - for function switch in  Success:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                break;
            }

            // debugging check & write to file
            if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                $debug->debug($command);
            }

            //create new filepath and filename based on date and command -- see testFileClass for details - $fullpath return for use in insertFileContents method
            $fullpath = $file->createFile($command);

            // check for connection type i.e. telnet SSHv1 SSHv2 & run the command on the device
        if ($templateparams['connect']['protocol'] == 'telnet') {
                $showCmd = $conn->showCmdTelnet($command, $cliDebugOutput);

            } elseif ($templateparams['connect']['protocol'] == 'ssh') { //SSHv2 - cause SSHv2 is likely to come before SSHv1
                
                $showCmd = $conn->connectSSH($command, $prompt, $debugOnOff);

                // if false returned, log failure
                if ($showCmd == false) {
                    $sshFailureText = "Failure: Unable to connect via SSH to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " for command (" . $command . ")  when running taskID " . $tid;
                    echo $sshFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                    $log->Conn($sshFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                    $report->eachData($device['deviceName'], $connStatusFail, $sshFailureText); // log to report
                } else {

                    $sshConnectedText = "Success: Connected via SSH to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for command (" . $command . ") for taskID " . $tid;
                    echo $sshConnectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                    $log->Conn($sshConnectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
                    if ($taskRow[0]['mailErrorsOnly'] == '0') {
                        $report->eachData($device['deviceName'], $connStatusPass, $sshConnectedText); // log to report
                    }
                }
            } else {
                continue;
            }

            // debugging check & write to file
            if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                $debug->debug($showCmd);
            }

            // create new array with PHPs EOL parameter
            $filecontents = implode(PHP_EOL, $showCmd);

            // insert $filecontents to file
            $file->insertFileContents($filecontents, $fullpath);

            $filename = basename($fullpath); // get filename for DB entry
            $fullpath = dirname($fullpath); // get fullpath for DB entry
            // insert info to DB
            $db2->query("INSERT INTO configs (deviceId, configDate, configTime, configLocation, configFilename) 
                            VALUES (:id, NOW(), CURTIME(), :fullpath, :filename)");
            $db2->bind(':id', $device['id']);
            $db2->bind(':fullpath', $fullpath);
            $db2->bind(':filename', $filename);
            $configDbQExecute = $db2->execute();
            if ($configDbQExecute) {
                $log->Conn("Success: Show Command '" . $command . "' for device '" . $device['deviceName'] . "' successful (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $log->Fatal("Failure: Unable to insert config information into DataBase Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR:" . mysql_error());
                die();
            }
            //check for last command iteration...
            // reason for minus 1 is, $cmdNumRows is number of commands sent back. THe while loop starts a key 0, 
            // there for $i needs to equal $cmdNumRows -minus one for a match on the last commend that was run
            if ($i == $cmdNumRows-1) { 
                if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                    echo "###########################   CLOSING TELNET CONNECTION   ######################" . PHP_EOL;
                }
                if ($templateparams['connect']['protocol'] == 'telnet') {
                    $conn->closeTelnet($templateparams['config']['resetPagingCmd'], $templateparams['config']['saveConfig'], $templateparams['config']['exitCmd']); // close telnet connection - ssh already closed at this point
                    break;
                }
            }
        }// end command while loop
    }// devices foreach
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
