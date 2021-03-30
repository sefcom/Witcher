<?php
include("../config/config.inc.php");
include("../config/functions.inc.php");
include("../classes/usersession.class.php");
/**
 * User has NOT logged in, so redirect to main login page
 */
if (!$session->logged_in) {
    header("Location: " . $config_basedir . "login.php");
}
// did not include db2.class.php becuase it is already declared in usersession class. Just needed to call it here.
$db2 = new db2();
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
                margin-left:24px;
                margin-top:10px;
                font-size:12px;
                margin-top:30px;
            }
            #noticeBoard {
                margin-left:24px;
                margin-top:10px;
                line-height:1.3em;
                text-align:left;
                font-size:12px;
                font-family:courier;
                color:black;
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
        <?php
        /* Get all snippets for the snippetSelect Selection list */
        $db2->query("SELECT `id`, `snippetName` FROM `snippets` ORDER BY `snippetName` ASC");
        $snippetQ = $db2->resultset();
        ?>
        <div id="snippetSelectDiv" style="width:500px;">
            <fieldset style="width:500px;">
                <label for="snippetSelect" style="font-size:12px; float:left;">Snippet Name:</label>
                <legend>Select Config Snippet</legend>
                <select name="snippetSelect[]" id ="snippetSelect" style="font-size:14px; float:left;margin-left:10px;" onchange="switchSnippet(this.value)">
                    <?php
                    echo "<option value=\"\">  Select  </option>";
                    foreach ($snippetQ as $row) {
                        echo "<option value=" . $row['id'] . ">" . $row['snippetName'] . "</option>";
                    }
                    ?>
                </select>
                <div style="clear:both;"></div>
                <div id="snippetDiv" class="alertSnippet">
                </div>
                <div style="clear:both;"></div>
                <?php
                echo '<button id="uploadButton" onclick="startConfigurationScript(\'' . $_GET['rid'] . '\')" tabindex="8" class="smlButton" style="margin-right:5px;margin-top:5px;margin-left:-2px;float:left;" title="Begin Configuration">Upload Configuration</button>';
                ?>
            </fieldset>
        </div>
        <div id="loading">
            <div id="innerDiv" class="alert">
                <img src='images/ajax_loader_gray_32.gif'  height="16" width="16"/>
                <span>Uploading Snippet...</span> 
            </div>
        </div>
        <div id="noticeBoard" class="alertSnippet"></div>
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/configDevice.js"></script> 
    </body>
</html>

