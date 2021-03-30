<?php
// PHP Includes
include("../config/config.inc.php");
include("../config/functions.inc.php");
include("../classes/usersession.class.php");
/**
 * User has NOT logged in, so redirect to main login page
 */
if (!$session->logged_in) {
    header("Location: " . $config_basedir . "login.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <title>rConfig - Configuration Management</title>
        <meta name="description" content="Configuration management utility for CLI based devices">
        <meta name="copyright" content="Copyright (c) 2012 - rConfig">
        <meta name="author" content="Stephen Stack">

        <!-- Add ICO -->
        <link rel="Shortcut Icon" href="<?php echo $config_basedir; ?>favicon.ico"> 
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" type="text/css" href="css/content.css" />
        <link rel="stylesheet" type="text/css" href="css/forms.css" />
        <!-- file Tree CSS -->
        <link rel="stylesheet" type="text/css" href="css/filetreecss/default.css" />
        <!--[if lt IE 9]>
                <link rel="stylesheet" type="text/css" href="css/all-ie-only.css" />
        <![endif]-->
        <!-- jQuery -->
        <script type="text/javascript" src="js/jquery/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>
        <!-- Custom JS -->
        <script type="text/javascript" src="js/rconfigFunctions.js"></script>
        <style type="text/css">
            html {
                overflow-x: hidden;
                overflow-y: auto;
            }
            #loading {
                position: absolute;
                height: 100%;
                width: 100%;
            }
            #innerDiv {
                /*Centering Method 2*/
                position: absolute;
                margin: -50px 0 0 -50px;
                left: 50%;
                top: 50%;
            }
            #noticeBoard {
                text-align:left;
                font-size: 14px;
                font-family: courier;
                margin-left: 10px;
                margin-top: 10px;
                line-height:1.3em;
                max-width:500px;
            }
            .alertSnippet {
                -moz-box-align: center;
                padding: 10px;
                font-size:12px;
                font-family:courier;
                color:black;		  
                text-align:left;	
                margin-top:10px;		  
                border: 1px solid #A8B8D1;
                border-radius: 8px;
                background-image: linear-gradient(#FFF, #ECF1F7);
                background-clip: padding-box;
                box-shadow: 2px 2px 4px #999; 
                max-width:auto;
            }
            .alert {
                -moz-box-align: center;
                padding: 10px;
                color: #373D48;
                border: 1px solid #A8B8D1;
                border-radius: 8px;
                background-image: linear-gradient(#FFF, #ECF1F7);
                background-clip: padding-box;
                box-shadow: 2px 2px 4px #999; 
                max-width:150px;
            }
        </style>
    </head>
    <body>
        <div id="loading">
            <div id="innerDiv" class="alert">
                <img src='images/ajax_loader.gif'  height="16" width="16"/>
                <span>Downloading...</span> 
            </div>
        </div>
        <div id="noticeBoard" class="alertSnippet"></div>
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/downloadNow.js"></script> 
    </body>
</html>

