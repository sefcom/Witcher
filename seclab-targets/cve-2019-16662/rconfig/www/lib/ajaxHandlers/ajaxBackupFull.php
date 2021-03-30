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

// get & set timezone from functions.inc.php
    
    getSetTimeZone();
    $today = date("Ymd");

// get each dir in /home/rconfig except, backups, tmp
    $dirsToBackup = array();
    foreach (glob($config_app_basedir . '/*', GLOB_ONLYDIR) as $dir) {
        if (basename($dir) != 'backups' && basename($dir) != 'tmp') {
            $dirsToBackup[] .= basename($dir);
        }
    }
// then zip each folder to a zip file in the tmp dir
    foreach ($dirsToBackup as $k => $v) {
        //Then create dir backup and ZIP it
        $backupFile = $config_temp_dir . 'backup-' . $v . '-' . $today . '.zip';
        folderBackup('/home/rconfig/' . $v, $backupFile);
    }

//  backup MySQL DB - vars from ../../../config/config.inc.php
    $sqlBackupFile = sqlBackup(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $config_temp_dir);

    /**
     * Then Zip all  zip files into one
     */
    $fullBackupFile = $config_backup_basedir . 'fullBackup-' . $today . '.zip';

    /**
     * create array of filenames in /home/tmp dir
     */
    $dirhandler = opendir($config_temp_dir);
    $nofiles = 0;
    while ($file = readdir($dirhandler)) {
        // if $file isn't this directory or its parent 
        // add to the $file_names array
        if ($file != '.' && $file != '..') {
            $nofiles++;
            $file_names[$nofiles] = $config_temp_dir . $file;
        }
    }

//close the handler
    closedir($dirhandler);

    /**
     * add all.zip files (array from above readdir) in /home/tmp to zip and create zip in /home/backups
     * below is a private function in functions file
     */
    folderBackup($config_temp_dir,$fullBackupFile);
    /**
     * delete all .zip files in /home/rconfig/tmp
     */
    foreach ($file_names as $file) {
        unlink($file);
    }

    if (file_exists($fullBackupFile)) {
        $response = json_encode(array('success' => true));
    } else {
        $response = json_encode(array('failure' => true));
    }

    echo $response;
}  // end session check