<?php include("includes/head_login.inc.php"); ?>
<body>
    <div id="headwrap">
        <div id="header">
            <div id="title"><h1>rConfig - Configuration Management</h1></div>
            <div id="logo"><img src="images/logos/Coding72.png" alt="rConfigLogo" title="rConfigLogo"></img></div>
        </div>
    </div>
    <div id="mainwrap">

        <div id="main">
            <!-- Breadcrumb Include -->    
            <?php include("includes/breadcrumb.inc.php"); ?>

            <!-- Announcement Include -->    
            <?php include("includes/announcement.inc.php"); ?>

            <div id="content"> <!-- Main Content Start-->
                <div id="dashboardLeft">
                    <div id="browserRequirements">
                        <h2 id="_h">Your web browser is out of date!</h2>
                        <p id="_p">To get the best possible experience using our website we recommend that you upgrade to a newer version or other web browser. A list of the most popular web browsers can be found below.</p>
                        <div class="spacer"></div><br/>
                        <p id="_p">Just click on the icons to get to the download page</p>

                        <ul>
                            <li id="_li1" onclick="window.open('http://www.microsoft.com/windows/Internet-explorer/default.aspx');">
                                <div id="_ico1">
                                </div>
                                <div id="_lit1">Internet Explorer 7+
                                </div>
                            </li>
                        </ul>

                        <ul>
                            <li id="_li2" onclick="window.open('http://www.mozilla.com/firefox/');">
                                <div id="_ico2">
                                </div>
                                <div id="_lit2">Firefox 3.6+
                                </div>
                            </li>
                        </ul>

                        <ul>
                            <li id="_li3" onclick="window.open('http://www.google.com/chrome');">

                                <div id="_ico3">
                                </div>
                                <div id="_lit3">Chrome 11+
                                </div>
                            </li>
                        </ul>

                        <ul>
                            <li id="_li4" onclick="window.open('http://www.opera.com/download/');">
                                <div id="_ico4">
                                </div>
                                <div id="_lit4">Opera 9.5+
                                </div>
                            </li>
                        </ul>

                        <ul>
                            <li id="_li5" onclick="window.open('http://www.apple.com/safari/download/');">
                                <div id="_ico5">
                                </div>
                                <div id="_lit5">Safari 3+
                                </div>
                            </li>
                        </ul>
                    </div>
                    <a href="login.php">Back to Login</a>
                    <div class="spacer"></div>
                </div><!-- End Content -->
                <div style="clear:both;"></div>
            </div><!-- End Main -->

            <!-- Footer Include -->    
            <?php include("includes/footer.inc.php"); ?>
        </div> <!-- End Mainwrap -->
</body>
</html>