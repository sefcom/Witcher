<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Masthead Include -->
    <?php include("includes/masthead.inc.php"); ?>
    <div id="mainwrap">
        <!-- TopNav Include -->
        <?php include("includes/topnav.inc.php"); ?>
        <div id="main">
            <!-- Breadcrumb Include -->
            <?php include("includes/breadcrumb.inc.php"); ?>
            <!-- Announcement Include -->
            <?php include("includes/announcement.inc.php"); ?>
            <div id="content">
                <!-- Main Content Start-->
                <fieldset id="tableFieldset" style="width:40%;">
                    <legend>Historical Reports</legend>

                    <br/>
                    <div id="complianceReportsDiv">
                        <table class="tableSimple">
                            <thead>
                            <th>Compliance Reports</th>
                            </thead>
                            <tbody>
                                <tr id="complianceReports">
                            <div id="complianceReportsOvervewFileContent"></div>
                            </tr>
                        </tbody>
                        </table>
                        <br/>
                        <button class="smlButton" id="deleteFilesbtn" onclick="deleteFiles('/home/rconfig/reports/complianceReports/', 'html', '2')">Delete Reports</button>
                        <span id="pleaseWait2" style="display:none">Please wait... <img src='images/ajax_loader.gif'  width="12" height="12" alt='Please wait... '/></span>
                    </div>				
                </fieldset>
            </div>
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/reports.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>