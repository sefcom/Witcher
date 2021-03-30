<?php

include("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();

if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Someone tried a Local File Inclusion attempt from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    // realpath used to prevent path traversal exploit
    $fullPath = realpath($_GET['download_file']);

    $pathWhiteList = array('/home/rconfig/backups/', '/home/rconfig/logs/', '/home/rconfig/reports/', '/home/rconfig/data/', '/home/rconfig/backups/syslogs/');

// prevent local file inclusion, by ensuring path confrorms to whitelist
    $secureAccess = 0; // if this is set 0 by default at the end of the script, then a log entry will be made to report LFI intrusion attempt
    foreach ($pathWhiteList as $WhitePath) {
        if (0 === strpos($fullPath, $WhitePath)) {
            $secureAccess = 1;
            if ($fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                $path_parts = pathinfo($fullPath);
                $ext = strtolower($path_parts["extension"]);
                switch ($ext) {
                    case "pdf":
                        header("Content-type: application/pdf"); // add here more headers for diff. extensions
                        header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a download
                        break;
                    default;
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
                }
                header("Content-length: $fsize");
                header("Cache-control: private"); //use this to open files directly
                while (!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
            }
            fclose($fd);
        }
    }
    if ($secureAccess == 0) {
        echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
        $log->Warn("Security Issue: Some tried a Local File Inclusion attempt from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
        // need to add authentication to this script
    }
}  // end session check
exit;
// example: place this kind of link into the document where the file download is offered:
// <a href="download.php?download_file=some_file.pdf">Download here</a>