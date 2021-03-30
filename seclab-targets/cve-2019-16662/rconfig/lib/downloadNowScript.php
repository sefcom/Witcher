<?php
/*
 * Why i am creating a new downloadNowScript when a lot of the code is already in the showCmdScript already?
 * well, most of the code is similar for sure, but the functionality between the required features is different, where
 * the showCmdScript is based of the Task ID (tid) and this script is based off the routers own DB ID. More than
 * that, I will build JS call backs into the script so that the GUI is updated with the device output. I will also remove the reporting
 * elements as they appear in the showCmdScript script.
 */

// requires - full path required
//require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
//require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection2.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
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
// script startTime and use extract to convert keys into variables for the script
extract($backendScripts->startTime());

// create array for json output to return to downloader window
$jsonArray = array();

// check if this script was CLI Invoked and throw an error to the CLI if it was.
if (php_sapi_name() == 'cli') {  // if invoked from CLI
    $text = "You are not allowed to invoke this script from the CLI - unable to run script";
    echo $text . "\n";
    $log->Fatal("Error: " . $text . " (File: " . $_SERVER['PHP_SELF'] . ")");
    die();
}

// set vars passed from ajaxDownloadNow.php on require()
$rid = $passedRid;
// Log the script start
$log->Info("The " . $_SERVER['PHP_SELF'] . " script was run manually invoked with Router ID: $rid "); // logg to file
// get time-out setting from DB
$timeoutSql = $db2->query("SELECT deviceConnectionTimout FROM settings");
$result = $db2->resultsetCols();
$timeout = $result[0];

// Get active nodes for a given task ID
// Query to retrieve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName,  deviceIpAddr, deviceEnablePrompt, devicePrompt, deviceUsername, devicePassword, deviceEnablePassword, templateId, nodeCatId
                FROM nodes WHERE id = " . $rid . " AND status = 1");
$getNodes = $db2->resultset();

if (!empty($getNodes)) {

    // push rows to $devices array
    $devices = array();
    foreach ($getNodes as $row) {
        array_push($devices, $row);
    }
    

    foreach ($devices as $device) { // iterate over each device - in this scripts case, there will only be a single device
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
            $text = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running Router ID " . $rid;
            $jsonArray['connFailMsg'] = $text;
            $log->Conn($text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            echo json_encode($jsonArray);
            continue;
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
        $catName = $catNameRow[0]['categoryName'];
        // check if there are any commands for this devices category, and if not, error and break the loop for this iteration
        if ($cmdNumRows == 0) {
            $text = "Failure: There are no commands configured for category " . $catName . " when running Router ID " . $rid;
            $log->Conn($text . " - Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            $jsonArray['cmdNoRowsFailMsg'] = $text;
            echo json_encode($jsonArray);
            continue;
        }

        // declare file Class based on catName and DeviceName
        $file = new file($catName, $device['deviceName'], $config_data_basedir);
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
        $connFailureText = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " for Router ID " . $rid . ". See Connection logs for details";
        $connSuccessText = "Success: Connected to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for Router ID " . $rid;
        // if connection is telnet, connect to device function
        if ($templateparams['connect']['protocol'] == 'telnet') {
            
            if ($conn->connectTelnet() === false) {

                $log->Conn($connFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                $jsonArray['failTelnetConnMsg'] = $connFailureText;
                echo json_encode($jsonArray);
                exit; // continue; probably not needed now per device connection check at start of foreach loop - failsafe?
            }

            $jsonArray['telnetConnMsg'] = $connSuccessText . '<br /><br />';
            $log->Conn($connSuccessText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
        } // end if device access method

        $i = -1; // set i to prevent php notices & becuase the $commands array will always have a start key at 0	
        // loop over commands for given device
        foreach ($commands as $cmd) {
            
            $i++;
            $command = $cmd;
            $prompt = $device['devicePrompt'];

            if (!$command || !$prompt) {
                $text = "Command or Prompt Empty - in (File: " . $_SERVER['PHP_SELF'] . ")\n";
                $log->Conn("Command or Prompt Empty - for function switch in  Success:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                $jsonArray['emptyCommandMsg' . $i] = $text;
                echo json_encode($jsonArray);
                break;
            }

            //create new filepath and filename based on date and command -- see testFileClass for details - $fullpath return for use in insertFileContents method
            $fullpath = $file->createFile($command);

            // check for connection type i.e. telnet SSHv1 SSHv2 & run the command on the device
            if ($templateparams['connect']['protocol'] == 'telnet') {
                $showCmd = $conn->showCmdTelnet($command, false);
            } elseif ($templateparams['connect']['protocol'] == 'ssh') { //SSHv2 
                $showCmd = $conn->connectSSH($command, $prompt);

                // if false returned, log failure
                if ($showCmd == false) {
                    $sshFailureText = "Failure: Unable to connect via SSH to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " for command (" . $command . ")  when running Router ID " . $rid;
                    $log->Conn($sshFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
                    $jsonArray['sshConnFailureMsg'] = $sshFailureText;
                    // echo json_encode($jsonArray); 
                } else {
                    $sshConnectedText = "Success: Connected via SSH to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for command (" . $command . ") for Router ID " . $rid;
                    $log->Conn($sshConnectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
                    $jsonArray['sshConnSuccessMsg'] = $sshConnectedText;
                    // echo json_encode($jsonArray);
                }
            } else {
                continue;
            }

            // output command json for response to web page
            $jsonArray['cmdMsg' . $i] = "Command '" . $command . "' ran successfully";

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
                if ($templateparams['connect']['protocol'] == 'telnet') {
                    $conn->closeTelnet($templateparams['config']['resetPagingCmd'], $templateparams['config']['saveConfig'], $templateparams['config']['exitCmd']); // close telnet connection - ssh already closed at this point
                    break;
                }
            }
        }// end command while loop
    } //end foreach
// final msg
    $jsonArray['finalMsg'] = "<b>Manual download completed</b> <br/><br/> <a href='javascript:window.close();window.opener.location.reload();'>close</a>";

// echo json response for msgs back to page
// echo '<pre>';
// print_r($jsonArray);
    echo json_encode($jsonArray);
} else {
    echo "Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR: " . mysql_error();
    $log->Fatal("Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR: " . mysql_error());
    die();
}
