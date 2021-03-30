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
    $path = $_GET['path'];
    $ext = "*." . $_GET['ext'];
    $fullpath = $path . $ext;
// echo $fullpath;

    $output = array();
    $return_arr = array();
    $array = glob($fullpath);
    usort($array, create_function('$b,$a', 'return filemtime($a) - filemtime($b);')); // sort by most recent modified
    foreach ($array as $file) {
        $output['filename'] = basename($file);
        $output['filepath'] = $file;
        $output['filesize'] = _format_bytes(filesize($file)); // dir needs writeable to be set
        array_push($return_arr, $output);
    }
    // reversed so last file in dir is displayed first in the output
    $return_arr = array_reverse($return_arr);
    echo json_encode($return_arr);
}