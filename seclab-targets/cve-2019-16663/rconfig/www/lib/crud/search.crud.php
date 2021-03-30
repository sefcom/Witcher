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

// simple script runtime check 
    $Start = getTime();

    $errors = array();

    if (isset($_GET['searchTerm']) && is_string($_GET['searchTerm']) && !empty($_GET['searchTerm'])) {
        /* validation */
        $searchTerm = '"' . $_GET['searchTerm'] . '"';
        $catId = $_GET['catId'];
        $catCommand = $_GET['catCommand'];
        $nodeId = $_GET['nodeId'];
        $grepNumLineStr = $_GET['numLinesStr'];
        $grepNumLine = $_GET['noLines'];
        $username = $_SESSION['username'];

        // if nodeId was empty set it to blank
        if (empty($nodeId)) {
            $nodeId = '';
        } else {
            $nodeId = '/' . $nodeId . '/';
        }

        $returnArr = array();

        // Get the category Name from the Category selected    
        $db2->query("SELECT categoryName from `categories` WHERE id = :catId");
        $db2->bind(':catId', $catId);
        $resultCat = $db2->resultset();
        $returnArr['category'] = $resultCat[0]['categoryName'];

        // get total file count
        $fileCount = array();
        $subDir = "";
        if (!empty($returnArr['category'])) {
            $subDir = "/" . $returnArr['category'];
        }

        exec("find /home/rconfig/data" . $subDir . $nodeId . " -maxdepth 10 -type f | wc -l", $fileCountArr);
        $returnArr['fileCount'] = $fileCountArr['0'];

        //next find all instances of the search term under the specific cat/dir	
        $command = 'find /home/rconfig/data' . $subDir . $nodeId . ' -name ' . $catCommand . ' | xargs grep -il ' . $grepNumLineStr . ' ' . $searchTerm . ' | while read file ; do echo File:"$file"; grep ' . $grepNumLineStr . ' ' . $searchTerm . ' "$file" ; done';
        // echo $command;die();
        exec($command, $searchArr);

        if (!empty($searchArr)) {
            // iterate array for all lines begining with 'File:' and add to $lines array
            // this is to create a multidimensional array for each files output
            foreach ($searchArr as $key => $val) {
                if (substr($val, 0, 5) == 'File:') {
                    $lines[] = $key;
                }
            }

            // count the difference in keys between first and second keys this gives the actual line count from 'File:' to 'File:' line from the grep output
            if (count($lines) > 1) {
                $lineCnt = $lines[1] - $lines[0];
                $searchArr = array_chunk($searchArr, $lineCnt); // create mDimension array for the value of $lineCnt for each device
            } else if (count($lines) == 1) {
                $searchArr = array_chunk($searchArr, count($searchArr));
            }

            $output = array();
            foreach ($searchArr as $line) {
                $val = $line[0];
                if (substr($val, 0, 5) == 'File:') {
                    // replace everything before deviceName returned
                    $firstChkDevName = str_replace("File:/home/rconfig/data" . $subDir . "/", "", $val);
                    //Remove everything after the first '/'
                    $deviceName = substr($firstChkDevName, 0, strpos($firstChkDevName, "/"));

                    // replace everything before date returned
                    $firstChkDate = str_replace("File:/home/rconfig/data" . $subDir . "/" . $deviceName . "/", "", $val);
                    //Remove everything after the first '/'
                    $date = explode("/", $firstChkDate);
                    $date = $date['0'] . "-" . $date['1'] . "-" . $date['2'];

                    // get filePath
                    $filePath = substr($val, 5);
                }

                $textOut = array();
                foreach ($line as $l) {
                    if (substr($l, 0, 5) == 'File:') {
                        unset($l); // unset line beginning with 'File:'
                    } else {
                        array_push($textOut, $l . "<br/>");
                    }
                }

                $newSearchArr = array(
                    "device" => $deviceName,
                    "filePath" => $filePath,
                    "date" => $date,
                    "lines" => $textOut
                );
                array_push($output, $newSearchArr);
            }

            $End = getTime();
            $returnArr['timeTaken'] = number_format(($End - $Start), 2);

            $returnArr['searchResult'] = $output;
        } else {
            $returnArr['searchResult'] = 'Empty';
        }

        echo json_encode($returnArr);
    } else { //searchTerm was not filed in, and so end back error and kill script
        $errors['searchTerm'] = "Search Field cannot be empty or is incorrect format ";
        $log->Warn("Failure: Search Field cannot be empty or is incorrect format(File: " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "search.php?error");
        exit();
    }
}