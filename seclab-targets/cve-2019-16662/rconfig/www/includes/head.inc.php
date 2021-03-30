<?php
// PHP Includes
include("../config/config.inc.php");
include("../config/functions.inc.php");
include("../classes/usersession.class.php");

/* Turn on event logging */
include("../classes/ADLog.class.php");
$log = ADLog::getInstance();

/**
 * User has NOT logged in, so redirect to main login page
 */
if (!$session->logged_in) {
    header("Location: " . $config_basedir . "login.php");
}
$host = gethostname();
$ip = $_SERVER['SERVER_ADDR'];
?>
<!DOCTYPE html>
<html>
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
        <link rel="stylesheet" type="text/css" href="css/compareTable.css" />
        <link rel="stylesheet" type="text/css" href="css/compareTable.css" />
        <link rel="stylesheet" type="text/css" href="css/jqueryNotification/jquery_notification.css" />
        <link rel="stylesheet" type="text/css" href="css/jqueryui/jquery.ui.datepicker.css" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="css/toastr/toastr.min.css" />
        <link rel="stylesheet" type="text/css" href="js/codemirror/lib/codemirror.css" />

        <!-- file Tree CSS -->
        <link rel="stylesheet" type="text/css" href="css/filetreecss/default.css" />
        <!--[if lt IE 9]>
                <link rel="stylesheet" type="text/css" href="css/all-ie-only.css" />
        <![endif]-->
        <!-- jQuery -->
        <script type="text/javascript" src="js/jquery/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>
        <!-- jQuery UI -->
        <script type="text/javascript" src="js/jquery/jquery-ui.js"></script>
        <script type="text/javascript" src="js/filetree/php_file_tree.js" ></script>
        <script type="text/javascript" src="js/jqueryNotification/jquery_notification_v.1.js" ></script>
        <script type="text/javascript" src="js/jqueryNotification/jquery_notification_v.1.js" ></script>
        <script type="text/javascript" src="js/pagination/pagination.min.js" ></script>
        <script type="text/javascript" src="js/pageTimeout.js" ></script>
        <script type="text/javascript" src="js/toastr/toastr.min.js" ></script>       
        <!-- Custom JS -->
        <script type="text/javascript" src="js/rconfigFunctions.js"></script>
    </head>