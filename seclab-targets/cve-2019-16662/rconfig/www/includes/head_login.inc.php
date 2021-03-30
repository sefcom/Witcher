<?php
// PHP Includes
include("../config/config.inc.php");
include("../config/functions.inc.php");
include("../classes/usersession.class.php");

/* Turn on event logging */
include("../classes/ADLog.class.php");
$log = ADLog::getInstance();

/**
 * User has already logged in, so direct to main dashboard page - 
 * applicable to login page only
 */
if ($session->logged_in) {
    header("Location: " . $config_basedir . "dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>rConfig - Configuration Management</title>
        <meta name="description" content="Configuration management utility for CLI based devices">
        <meta name="copyright" content="Copyright (c) <?php echo date("Y"); ?> - rConfig">
        <meta name="author" content="Stephen Stack">

        <!-- Add ICO -->
        <link rel="Shortcut Icon" href="<?php echo $config_basedir; ?>favicon.ico"> 
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" type="text/css" href="css/content.css" />
        <link rel="stylesheet" type="text/css" href="css/forms.css" />

        <!-- jQuery -->
        <script type="text/javascript" src="js/jquery/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="js/detect/detect.min.js"></script>
    </head>