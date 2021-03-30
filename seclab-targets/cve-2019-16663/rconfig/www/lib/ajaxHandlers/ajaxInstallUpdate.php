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
    require_once("../../../classes/updater.class.php");

    /* Updater Steps 
     * 1. get latest version from rconfig.com
     * 2. set or correct owner permissions to all /home/rconfig/ to apache account
     * 3. Assume correct version file uploaded and extract zip file completely to /home/rconfig/tmp/update-x.x.x/
     * 4. backup /home/rconfig/config/config.inc.php to updater files directory
     * 5. change version in config.inc.php to $latestVer
     * 6. Copy all from tmp update folder to prod rconfig folder
     * 7. Check installed version on config.inc.php 
     * 8. check for sql file in update tmp folder
     * 9. execute sql changes if file is present
     * 10. delete all tmp update files/folders using Linux rm command * 
     * 
     * */

    // initiate classes
    $log = ADLog::getInstance();
    $update = new updater();

//Setting the timeout properly without messing with ini values: 
    $ctx = stream_context_create(array('http' => array('timeout' => 5)));
// here we assume we can already connect to net as ../www/updater.php will not allow us to proceed to this point i.e. no error check
    $latestVer = file_get_contents("http://www.rconfig.com/downloads/version.txt", 0, $ctx);
    $updateFileName = 'rconfig-' . $latestVer . '.zip';

    $updateFile = $config_temp_dir . $updateFileName;
//extracted files path
    $extractDir = '/home/rconfig/tmp/update-' . $latestVer;

// set json array for ultimate response to updater.php
    $response = array();

// set chwon apache on /home/rconfig/ in case any are misconfigured
    shell_exec('chown -R apache ' . $config_app_basedir);

// check if update file exists
    if ($update->checkForUpdateFile($updateFile)) {
        if ($update->extractUpdate($updateFile, $extractDir)) {
            $response['zip'] = 'ZIP file successfully extracted';
            $log->Info("ZIP successfully extracted - " . $updateFile . " (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $response['zip'] = 'Could not extract ZIP - ' . $updateFile;
            $log->Warn("Could not extract ZIP - " . $updateFile . " (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // backup /home/rconfig/config/config.inc.php to update Dir
        $sourceConfigFile = '/home/rconfig/config/config.inc.php';
        $destinationConfigFile = '/home/rconfig/tmp/update-' . $latestVer . '/rconfig/config/config.inc.php';

        if ($update->backupConfigFile($sourceConfigFile, $destinationConfigFile)) {
            $response['configFileBackup'] = 'rConfig Configuration file backed up successfully';
            $log->Info("Copied file " . $sourceConfigFile . "... (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $response['configFileBackup'] = 'failed to copy ' . $sourceConfigFile;
            $log->Warn("failed to copy " . $sourceConfigFile . "...  (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        //update copied config file with new version info
        $update->updateConfigVersionInfo($latestVer, $destinationConfigFile);

        // Copy App folders only	
        $folderstoCopy = array('classes', 'config', 'lib', 'www', 'vendor');
        $update->copyAppDirsToProd($latestVer, $folderstoCopy);      
        
        // check version updated correctly
        if ($config_version == $latestVer) {
            $response['configFileVersionUpdate'] = 'rConfig application files updated';
            $log->Info("rConfig files updated - (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // check for and install sql file
        $sqlUpdateFile = $extractDir . '/rconfig/updates/sqlupdate.sql';
        if ($update->checkForUpdateFile($sqlUpdateFile) && filesize($sqlUpdateFile) !== 0) {
            // loadSqlFile from classes/updater.class.php
            if ($update->loadSqlFile($sqlUpdateFile, DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME)) {
                $response['sqlUpdateComplete'] = 'rConfig Database was updated';
                $log->Info("Database was updated - (File: " . $_SERVER['PHP_SELF'] . ")");
            }
        }

        // create any new dirs as required
        $dirsToCreateArr = array('/home/rconfig/reports/complianceReports/');
        $update->createDirs($dirsToCreateArr);

        // Delete all /home/rconfig/tmp/ data
        exec('rm -fr /home/rconfig/tmp/*.*');
        if ($update->dirIsEmpty('/home/rconfig/tmp/')) {
            $response['tmpFolderEmpty'] = 'rConfig update files removed';
            $log->Info("rConfig update files removed - (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $response['tmpFolderEmpty'] = 'Could not remove rConfig update files';
            $log->Info("Could not remove rConfig update files - (File: " . $_SERVER['PHP_SELF'] . ")");
            ;
        }

        // remove rconfig/www/install directory as should already be removed for upgrade
        $installDir = '/home/rconfig/www/install/';
        if (is_dir($installDir)) {
            rrmdir($installDir);
            sleep(1); // pause while deleting

            if (!file_exists($installDir)) { // check if install  does not dir exist after delete and return success
                $log->Info($installDir . " dir removed - " . $updateFile . " (File: " . $_SERVER['PHP_SELF'] . ")");
            } else if (file_exists($installDir)) { // else return failure as dir still exists
                $response['zip'] = 'Could remove installation directory - ' . $updateFile;
                $log->Warn("Could remove installation directory - " . $updateFile . " (File: " . $_SERVER['PHP_SELF'] . ")");
            }
        } else if (!is_dir($installDir)) { // first if - return success as dir does not exist
            $log->Info($installDir . " dir removed - " . $updateFile . " (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        echo json_encode($response);
    } else {
        // could not find update file in tmp dir
        $response['noUpdateFile'] = 'Could not find update File';
        $log->Fatal("Could not find update File (File: " . $_SERVER['PHP_SELF'] . ")");
        echo json_encode($response);
    }
}