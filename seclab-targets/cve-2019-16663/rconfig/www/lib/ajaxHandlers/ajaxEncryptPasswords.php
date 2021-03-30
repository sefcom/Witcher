<?php
require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");
require_once("/home/rconfig/classes/updater.class.php");

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
    $update = new updater();
    
// check if encryption already set in DB and alert if it is
    $db2->query("SELECT passwordEncryption from settings");
    if($db2->resultsetCols()[0] == 1){
        // double verification as button for encryption is not shown if encryption set to one in DB
         echo json_encode(array('status' => 'error','message'=> 'Password encryption already set on this database. Process failed!'));
         die();
    } else {
        // carry on
        $sourceConfigFile = '/home/rconfig/config/config.inc.php';
        $tmpConfigFile = '/home/rconfig/tmp/config.inc.php.tmp';
        if ($update->backupConfigFile($sourceConfigFile, $tmpConfigFile)) {
            $response['configFileBackup'] = 'rConfig Configuration file backed up successfully';
            $log->Info("Copied file " . $sourceConfigFile . "... (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $response['configFileBackup'] = 'failed to copy ' . $sourceConfigFile;
            $log->Warn("failed to copy " . $sourceConfigFile . "...  (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        //update copied config file with secret
        $update->updateSecretKey($_POST['secret'], $tmpConfigFile);
        copy($tmpConfigFile, $sourceConfigFile);
        unlink($tmpConfigFile);

        // add encryption to Db
        $db2->query("SELECT id, devicePassword, deviceEnablePassword FROM nodes");
        $allNodes = $db2->resultset();
        foreach($allNodes as $k=>$v){
            if(!empty($v['devicePassword'])){ 
                $db2->query("UPDATE nodes SET devicePassword = :devicePassword WHERE id = " . $v['id']);
                $db2->bind(":devicePassword", first_time_encrypt($v['devicePassword'], $_POST['secret']));
                $db2->execute();
            }
            if(!empty($v['deviceEnablePassword'])){ 
                $db2->query("UPDATE nodes SET deviceEnablePassword = :deviceEnablePassword WHERE id = " . $v['id']);
                $db2->bind(":deviceEnablePassword", first_time_encrypt($v['deviceEnablePassword'], $_POST['secret']));
                $db2->execute();
            }
        }
        
        $db2->query("UPDATE settings SET passwordEncryption = 1");
        if($db2->execute()){
            $log->Info("Password encryption enabled and updated in the database: (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            echo json_encode(array('status' => 'error','message'=> 'Cloud not update database for password encryption. Process failed!'));
        }
        echo json_encode(array('status' => 'success','message'=> 'Device passwords successfully encrypted'));
    }
    
}