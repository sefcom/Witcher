<?php
require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/classes/spyc.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    $ymlData = Spyc::YAMLLoad($_POST['code']);
    $fileName = $_POST['fileName'];
    $check_yml_extension = explode('.', $fileName);
    if(@!array_key_exists($check_yml_extension[1])){
        if(@$check_yml_extension[1] != 'yml'){
            $fileName = $fileName . '.yml';
        }
    }
    $fullpath = $config_templates_basedir.$fileName;

    $username = $_SESSION['username'];
    require_once("../../../classes/db2.class.php");
    require_once("../../../classes/ADLog.class.php");
    $db2 = new db2();
    $log = ADLog::getInstance();

    if (!is_dir('templates')) {
        mkdir('templates');
        chown('templates', 'apache');
    }

    // if'' to create the filename based on the command if not created & chmod to 666
    if (!file_exists($fullpath)) {
        exec("touch " . $fullpath);
        chmod($fullpath, 0666);
    }
    // if the file is alread in place chmod it to 666 before writing info
    chmod($fullpath, 0666);

    // dump array into file & chmod back to RO
    $filehandle = fopen($fullpath, 'w+');
    file_put_contents($fullpath, $_POST['code']);
    fclose($filehandle);
    chmod($fullpath, 0444);
 
    $db2->query("UPDATE `templates` SET `fileName` = :fileName, `name` = :name, `desc` = :desc, `dateLastEdit` = NOW(), `addedby` = :username WHERE `id` = :id");
    $db2->bind(':id', $_POST['id']);
    $db2->bind(':fileName', $fullpath);
    $db2->bind(':name', $ymlData['main']['name']);
    $db2->bind(':desc', $ymlData['main']['desc']);
    $db2->bind(':username', $username);

    $queryResult = $db2->execute();
    /* Update successful */
    if ($queryResult && file_exists($fullpath)) {
        $response = "success";
        $log->Info("Success: Template: ".$fullpath." edited in templates folder");
    }
    /* Update failed */ else {
        $response = "failed";
        $log->Warn("Success: Could not edit Template ".$fullpath." in templates folder");
    }
    echo json_encode($response);    
}  // end session check