<?php

// requires - full path required
require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
require("/home/rconfig/classes/sshlib/Net/SSH2.php"); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
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
// get task details from DB
// get mailConnectionReport Status form tasks table and send email
$db2->query("SELECT * FROM tasks WHERE id = :tid AND status = '1'");
$db2->bind(':tid', $tid);
$taskRow = $db2->resultset();
$taskname = $taskRow[0]['taskname'];
// create connection report file
$reportFilename = 'compareStartRunReport' . $date . '.html';
$reportDirectory = 'compareReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$reportFail = '<font color="red">Fail</font>';
$reportPass = '<font color="green">Success</font>';
// Get active nodes for a given task ID
// Query to retireve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1");
$resultSelect = $db2->resultset();
if (!empty($resultSelect)) {
    // push rows to $devices array
    $devices = array();
    foreach ($resultSelect as $row) {
        array_push($devices, $row);
    }
    $startupConfigFile = "showstartup-config.txt";
    $runningConfigFile = "showrun.txt";
    foreach ($devices as $device) {
        $deviceId = $device['id'];
        $db2->query("SELECT * FROM configs WHERE deviceId = $deviceId AND configFilename LIKE '%$runningConfigFile%' ORDER BY configDate DESC LIMIT 1");
        $db2->bind(':deviceId', $deviceId);
        $pathResultRunning = $db2->resultset();

        $db2->query("SELECT * FROM configs  WHERE deviceId = $deviceId AND configFilename LIKE '%$startupConfigFile%' ORDER BY configDate DESC LIMIT 1");
        $db2->bind(':deviceId', $deviceId);
        $pathResultStartup = $db2->resultset();
        if (empty($pathResultRunning) || empty($pathResultStartup)) {
            // continue for the foreach if one of the files is not available as this compare will be invalid
            echo 'continue invoked for ' . $device['deviceName'];
            continue;
        }
        $pathResult_a = $pathResultRunning[0]['configLocation'];
        $pathResult_b = $pathResultStartup[0]['configLocation'];
        $path_a = $pathResult_a . '/' . $runningConfigFile;
        $path_b = $pathResult_b . '/' . $startupConfigFile;
        // run the comapre with no linepadding set
        $diff = new diff;
        $text = $diff->inline($path_a, $path_b);
        $count = count($diff->changes) . ' changes';
        // send output to the report
        $report->eachData($device['deviceName'], $count, $text); // log to report
    } // End Data insert loop
    // script endTime
    extract($backendScripts->endTime($time_start));
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    $report->footer();
    // Check if mailConnectionReport value is set to 1 and send email
    if ($taskRow[0]['mailConnectionReport'] == '1') {
        $backendScripts->reportMailer($db2, $log, $title, $config_reports_basedir, $reportDirectory, $reportFilename, $taskname);
    }
} else {
    echo $backendScripts->finalAlert($log, $_SERVER['PHP_SELF']);
}