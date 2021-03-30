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
    /* will return all error entires for the Conn Log file in /home/rconfig/logging 
     *  Will include HTML to color Code gets actual log file based on passed GET i.e. logType
     *  does not take log file rotation into account, so the idea is that CRON taks care of archiveing. The logs files with .log
     *  in the DIR are todays only
     */

    $dirPath = $config_log_basedir; // from included config file
    $logType = $_GET['logType'];

    if (isset($_GET['value'])) {
        $logValue = $_GET['value'];
    }

    if (empty($logValue)) {
        $logValue = 10;
    }
    $logName = $logType . "-default.log";
    $fullpath = $dirPath . $logName;

    $lines = array();
    if (file_exists($fullpath)) { // open the logfile
        $file = fopen($fullpath, "r");
        while (!feof($file)) {
            //read file line by line into a new array element
            $lines[] = fgets($file, 4096); // put all lines into array
        }

        $output = array(); // new array
        $array = array(); // new array


        foreach ($lines as $line) { // loop array and place matchs to Error into new array for output
            if ($line != false) { // remove first false line
                $output['line'] = $line;
                array_push($array, $output);
            }
        }
        fclose($file);
        $reversed_arr = array_reverse($array); // reverse the array so that latest entries are first
        $return_arr = array_slice($reversed_arr, 0, $logValue); // slice after reverse so only the last 10 rows are returned
        $response = json_encode($return_arr);
    } else {
        $response = json_encode("Failed");
    }
    echo $response;
}