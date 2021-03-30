<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <!-- jQuery -->
        <script type="text/javascript" src="../js/jquery/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="../js/jquery/jquery.validate.min.js"></script>
    </head>
    <body>
        <div id="container">
            <div id="top">
                <h1><img src="img/Install48.png"/>rConfig Installation</h1>
            </div>
            <div id="leftnav">
                <ul>
                    <li><a href="preinstall.php" <?php if (basename($_SERVER['SCRIPT_NAME']) == "preinstall.php") { echo 'class="selected"'; } ?> >Pre-installation Check</a></li>
                    <li><a href="license.php" <?php if (basename($_SERVER['SCRIPT_NAME']) == "license.php") { echo 'class="selected"'; } ?> >License</a></li>
                    <li><a href="dbinstall.php" id="dbinstall_a" <?php if (basename($_SERVER['SCRIPT_NAME']) == "dbinstall.php") { echo 'class="selected"'; } ?>>Database Setup</a></li>
                    <li><a href="finalcheck.php" id="finalcheck_a" <?php if (basename($_SERVER['SCRIPT_NAME']) == "finalcheck.php") { echo 'class="selected"'; } ?>>Final Check</a></li>
                </ul> 
            </div>