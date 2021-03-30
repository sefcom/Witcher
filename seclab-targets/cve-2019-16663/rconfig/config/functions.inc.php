<?php

/*
 * Functions file containing generic single methods/ Functions for rConfig. If an function is called in a script for ease it should
 * reference this file
 */

// gets & sets data for Annoucements section and Bredcrumb text on pages from the DB
function pageTitles($pageName, $pageType = NULL) {
    require_once("/home/rconfig/classes/db2.class.php");
    $db2 = new db2();
    $db2->query("SELECT * FROM menuPages WHERE pageName = :pageName");
    $db2->bind(':pageName', $pageName);
    $result = $db2->resultset();
    if ($pageType == 'announcement') {
        $text = $result[0]['annoucementText'];
    } elseif ($pageType == 'breadcrumb') {
        $text = $result[0]['breadcrumbText'];
    } else {
        $text = ' <font color=\"red\">$pageType Not Found for function pageTitles()</font>';
    }
    return $text;
}

/**
 * get/set timezone
 *
 */
function getSetTimeZone() {
    require_once("/home/rconfig/classes/db2.class.php");
    $db2 = new db2();
    // check and set timeZone to avoid PHP errors
    $q = $db2->query("SELECT timeZone FROM settings");
    $row = $db2->single();
    $timeZone = $row['timeZone'];
    if($timeZone){
        return date_default_timezone_set($timeZone);
    }
}

/**
 * phpErrorReporting to check if PHP Error reporting is set to on in dbase_add_record
 * if it is start logging errors to DB file. Function is added to head.inc.php on each and every page
 *
 */
function phpErrorReporting() {
    require_once("/home/rconfig/classes/db2.class.php");
    $db2 = new db2();
    // check and set timeZone to avoid PHP errors
    $db2->query("SELECT timeZone FROM settings");
    $result = $db2->resultsetCols();
    $timeZone = $result[0];
    date_default_timezone_set($timeZone);
    $db2->query("SELECT phpErrorLogging, phpErrorLoggingLocation FROM settings WHERE id = '1'");
    $result = $db2->resultset();
    $phpErrorLevel = $result[0]['phpErrorLogging'];
    $phpErrorLocation = $result[0]['phpErrorLoggingLocation'];
    $phpErrorDate = date('Ymd');
    $phpErrorFile = $phpErrorLocation . 'error_log' . $phpErrorDate . '.txt';
    if ($phpErrorLevel === '1') {
        if (!file_exists($phpErrorFile)) {
            exec("touch " . $phpErrorFile);
            chmod($phpErrorFile, 0644);
            $handle = fopen($phpErrorFile, 'w');
            if (!$handle) {
                $log->Fatal("Cannot open file  Func: createFile():  " . $this->fullReportPath . "(File: " . $_SERVER['PHP_SELF'] . ")");
            }
        }
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', $phpErrorFile);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
    }
}

// check for and implement pop reporting by default as functions.inc.php is called on almost all scripts
phpErrorReporting();

/**
 * urlsearch function is to aid in the search for relevant portion of page name
 * to add the selected="true" attr to the menu to keep open the relevant accordion 
 * page
 */
function urlsearch($string) {
    include("includes/config.inc.php");
    if (strstr($config_page, $string)) {
        echo 'selected="true"';
    } else {
        echo '';
    }
}


/**
 * regexpMatch function used to compare inputted string against anither string 
 * i.e. from a config file, to ensure a match. used in complianceScript.php
 */
function regexpMatch($string, $line) {
    if (preg_match($string, $line)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Delete the $value Character(s) of a String with PHP
 * 
 * Example;
 * $newString = deleteChar( $oldString,7);
 * 
 */
function deleteChar($string, $value) {
    return substr($string, $value);
}

/*
 * Extract leaf nodes of multi-dimensional array in PHP - as used in cmd_cat_update.php
 */

function getLeafs($element) {
    $leafs = array();
    foreach ($element as $e) {
        if (is_array($e)) {
            $leafs = array_merge($leafs, getLeafs($e));
        } else {
            $leafs[] = $e;
        }
    }
    return $leafs;
}

/**
 * @desc Outputs inserted value to B, KiB, GiB etc.. based on size
 * @param inout byes in intval
 * @return str 
 */
function _format_bytes($a_bytes) {
    if ($a_bytes < 1024) {
        return $a_bytes . 'B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) . ' KiB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) . ' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) . 'PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) . 'EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) . 'ZiB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) . 'YiB';
    }
}

/**
 * @desc read filename contents and return lines line by line
 * @param input filename
 * @return array of lines from config file
 */
function fileRead($filename) {
    $lines = file($filename);
    if (!empty($lines)) {
        foreach ($lines as $line_num => $line) {
            echo "<font color=red>{$line_num}: </font>" . $line . "<br />\n";
            //If you are reading HTML code use this line instead
            //print "<font color=red>Line #{$line_num}</font> : " . htmlspecialchars($line) . "<br />\n";
        }
    } else {
        echo "<font color=red>0 : </font><strong>This file is empty</strong><br />\n";
    }
}

/*
 * @desc get server memory free
 *
 */

function get_memory_free() {
    foreach (file('/proc/meminfo') as $ri)
        $m[strtok($ri, ':')] = strtok('');
	foreach($m as $key => $value){
            $m[$key] = trim(str_replace(" kB","",$value));
	}	
    return 100 - round(($m['MemFree'] + $m['Buffers'] + $m['Cached']) / $m['MemTotal'] * 100);
}

/*
 * @desc get server memory total
 *
 */

function get_memory_total() {
    foreach (file('/proc/meminfo') as $ri)
        $m[strtok($ri, ':')] = strtok('');
    return round(substr($m['MemTotal'], 0, -4) * 1000); // by 1000 to xlate from KB from proc file to B
}

/*
 * @desc get CPU Type
 *
 */

function get_cpu_type() {
    foreach (file('/proc/cpuinfo') as $ri)
        $m[trim(str_replace(" ", "", strtok($ri, ':')))] = strtok('');
    return $m['modelname'];
}

/**
 * @desc Connect to given IP address and port and return result
 * See user notes in http://php.net/manual/en/function.socket-close.php
 * @param input IP of host, and TCP port
 * @return output
 */

 
function getHostStatus($host, $port, $timeout = 3) { 
error_reporting(E_ALL);
ini_set('display_errors', 1);
        # this works whether $host is a hostname or IP 
        $ip = ""; 
        if( !preg_match('/^\d+\.\d+\.\d+\.\d+$/', $host) ) { 
            $ip = gethostbyname($host); 
            if ($ip == $host) { 
				$status = "Error Connecting Socket: Unknown host"; 
                return "<font color=\"red\">Unavailable: " . $status . "</font>";  
            } 
        } else $ip = $host; 

        if (!($socket= @socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) { 
            $status = "Error Creating Socket: ".socket_strerror(socket_last_error()); 
                return "<font color=\"red\">Unavailable: " . $status . "</font>";  
        } 

        socket_set_nonblock($socket); 

        $error = NULL; 
        $attempts = 0; 
        $timeout *= 1000;  // adjust because we sleeping in 1 millisecond increments 
        $connected; 
        while (!($connected = @socket_connect($socket, $host, $port+0)) && $attempts++ < $timeout) { 
            $error = socket_last_error(); 
            if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY) { 
                $status = "Error Connecting Socket: ".socket_strerror($error); 
                socket_close($socket); 
                return "<font color=\"red\">Unavailable: " . $status . "</font>";  
            } 
            usleep(1000); 
        } 

        if (!$connected) { 
            $status = "Error Connecting Socket: Connect Timed Out After $timeout seconds. ".socket_strerror(socket_last_error()); 
            socket_close($socket); 
            return "<font color=\"red\">Unavailable: " . $status . "</font>";  
        } 
        socket_set_block($socket); 
        return "<font color=\"green\"> Online </font>";      
}  


// array_search with partial matches and optional search by key
function array_find($needle, $haystack, $search_keys = false) {
    if (!is_array($haystack))
        return false;
    foreach ($haystack as $key => $value) {
        $what = ($search_keys) ? $key : $value;
        if (strpos($what, $needle) !== false)
            return $key;
    }
    return false;
}

function getTime() {
    $a = explode(' ', microtime());
    return(double) $a[0] + $a[1];
}

// from here http://stackoverflow.com/questions/10895343/php-count-total-files-in-directory-and-subdirectory-function
function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL; 
  return (count(scandir($dir)) == 2);
}

function scan_dir($path) {
    if (is_dir_empty($path) || is_dir_empty($path.'/data')) {
      return array('total_files' => '0', 'total_size' => '0', 'files' => '0');
      return;
    }
    exec('find /home/rconfig/data -type f -name "*.txt" | wc -l', $nbfiles);
    exec('find /home/rconfig/data -type f -name "*.txt" -exec du -ch {} + | grep total$', $bytestotal);

    return array('total_files' => $nbfiles[0], 'total_size' => $bytestotal[0]);
}

/** backup the db OR just a table 
 */
function sqlBackup($host, $user, $pass, $name, $backupPath, $tables = '*') {
    try {
        // tmp dir
        $tmpDir = "/home/rconfig/tmp";
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0700);
        }
        $db = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
        $today = date("Ymd");
        $filenamePath = $backupPath . 'rconfig-db-backup-' . $today . '.sql';
        $f = fopen($filenamePath, 'w+');
        $tables = $db->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
        foreach ($tables as $table) {
            //        echo $table[0] . ' ... '; 
            flush();
            $sql = '-- TABLE: ' . $table[0] . PHP_EOL;
            $create = $db->query('SHOW CREATE TABLE `' . $table[0] . '`')->fetch();
            $sql .= $create['Create Table'] . ';' . PHP_EOL;
            fwrite($f, $sql);

            $rows = $db->query('SELECT * FROM `' . $table[0] . '`');
            $rows->setFetchMode(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $row = array_map(array($db, 'quote'), $row);
                $sql = 'INSERT INTO `' . $table[0] . '` (`' . implode('`, `', array_keys($row)) . '`) VALUES (' . implode(', ', $row) . ');' . PHP_EOL;
                fwrite($f, $sql);
            }

            $sql = PHP_EOL;
            $result = fwrite($f, $sql);
            flush();
        }
        fclose($f);
        return $filenamePath;
    } catch (Exception $e) {
        echo 'Damn it! ' . $e->getMessage() . PHP_EOL;
    }
}

function fileBackup($file, $backupFile) {
    $zip = new ZipArchive();
    $zip->open($backupFile, ZipArchive::CREATE);

    if (!is_file($file)) {
        throw new Exception('SQL file ' . $file . ' does not exist');
    }
    $zip->addFile($file);

    echo $zip->close();
}

function folderBackup($dir, $backupFile) {
    $zip = new ZipArchive();
    $zip->open($backupFile, ZipArchive::CREATE);
    $dirName = $dir;
    $dirName = realpath($dirName);
    if (substr($dirName, -1) != '/') {
        $dirName.= '/';
    }

    $dirStack = array($dirName);
    //Find the index where the last dir starts
    $cutFrom = strrpos(substr($dirName, 0, -1), '/') + 1;

    while (!empty($dirStack)) {
        $currentDir = array_pop($dirStack);
        $filesToAdd = array();

        $dir = dir($currentDir);
        while (false !== ($node = $dir->read())) {
            if (($node == '..') || ($node == '.')) {
                continue;
            }
            if (is_dir($currentDir . $node)) {
                array_push($dirStack, $currentDir . $node . '/');
            }
            if (is_file($currentDir . $node)) {
                $filesToAdd[] = $node;
            }
        }

        $localDir = substr($currentDir, $cutFrom);
        $zip->addEmptyDir($localDir);

        foreach ($filesToAdd as $file) {
            $zip->addFile($currentDir . $file, $localDir . $file);
        }
    }
    $zip->close();
}

/* creates a compressed zip file 
 *  From here http://davidwalsh.name/create-zip-php
 */

function createZip($files = array(), $destination = '', $overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    //vars
    $valid_files = array();
    //if files were passed in...
    if (is_array($files)) {
        //cycle through each file
        foreach ($files as $file) {
            //make sure the file exists
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if (count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach ($valid_files as $file) {
            $new_filename = substr($file, strrpos($file, '/') + 1); // strip filePath
            $zip->addFile($file, $new_filename);
        }
        //debug
        // echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        //close the zip -- done!
        $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    } else {
        return false;
    }
}

// functions for the configoverview.php output
function cntCategories() {
    require_once("../classes/db2.class.php");
    $db2 = new db2();
    $db2->query("SELECT COUNT(*) as total FROM categories WHERE status = 1");
    $result = $db2->resultset();
    return $result[0]['total'];
}

function cntDevices() {
    require_once("../classes/db2.class.php");
    $db2 = new db2();
    $db2->query("SELECT COUNT(*) as total FROM nodes WHERE status = 1");
    $result = $db2->resultset();
    return $result[0]['total'];
}

// recursivley remove items from a directory
function rrmdir($dir) {
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}

/**
 * @desc Check if sting contains whitespace
 * @param str
 * @return bool 
 */
function chkWhiteSpaceInStr($string) {
    return preg_match("/\\s/", $string);
}

// flatten multidimensional array
function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}

/**
 * simple method to encrypt a plain text string for first time in rConfig settings wizard
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * PHP 5.4.9 ( check your PHP version for function definition changes )
 *
 * this is a beginners template for simple encryption decryption
 * before using this in production environments, please read about encryption
 * use at your own risk
 *
 * @param string $string: string to encrypt or decrypt
 * @param string $secret: secret as passed from ajaxEncryptPasswords.php
 *
 * @return string
 */
function first_time_encrypt($string, $secret) {
    $output = false;
    // if key is blank, then store PWs nativley
	$encrypt_method = "AES-256-CBC";
	$secret_key = $secret;
	$secret_iv = $secret;
	// hash
	$key = hash('sha256', $secret_key);
	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);
	$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	$output = base64_encode($output);
    return $output;
}

/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * PHP 5.4.9 ( check your PHP version for function definition changes )
 *
 * this is a beginners template for simple encryption decryption
 * before using this in production environments, please read about encryption
 * use at your own risk
 *
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */
function encrypt_decrypt($action, $string) {
    $output = false;
    // if key is blank, then store PWs nativley
    if(SECRETKEY != ''){

        $encrypt_method = "AES-256-CBC";
        $secret_key = SECRETKEY;
        $secret_iv = SECRETIV;

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
    }
    return $output;
}


function passwordEncryptionCheck(){
    require_once("../classes/db2.class.php");
    $db2 = new db2();
    $db2->query("SELECT passwordEncryption FROM settings");
    $result = $db2->resultset();
    $encrptionStatus = $result[0]['passwordEncryption'];
    if($encrptionStatus == 0){
        $notice = "<font color='red'>Password encryption disabled</font>"
                . "<div class='spacer'></div>"
                . "<button class='smlButton' id='pwencryption' onclick='pwencryption()'>Enable Encryption</button>";
    } elseif($encrptionStatus == 1){
        $notice = "<font color='green'>Password encryption enabled</font>";
    } else {
        $notice = "<font color='orange'>Something wrong with password encryption</font>";
    }
    return $notice;
        
}

// cleans deviceNames and commands from .'s and other special characters. used in textFile.class and devices.crud
function cleanDeviceName($string) {
   $string = str_replace('.', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
