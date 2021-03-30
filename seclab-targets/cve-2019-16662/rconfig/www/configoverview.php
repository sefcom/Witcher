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
                <fieldset id="dashboardFieldset"  style="width:78%">
                    <legend>Device Configuration Statistics</legend>
                    <div class="tableSummary">
                        <div class="row">
                            <div class="cell">
                                Total Categories
                            </div>
                            <div class="cell last">
                                <?php echo cntCategories(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell">
                                Total Devices
                            </div>
                            <div class="cell last">
                                <?php echo cntDevices(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell">
                                Total Configuration Files
                            </div>
                            <div class="cell last">
                                <?php
                                $files = scan_dir($config_data_basedir);
                                $filecnt = $files['total_files'];
                                $fileByte = $files['total_size'];
                                $fileByte = str_replace(",", "", $fileByte);
                                echo "$filecnt files, " . $fileByte . "\n";
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <!-- blank -->
                            <div class="cell"></div>
                            <div class="cell last"></div>
                        </div>
                        <div class="row">
                            <div class="cell">
                                Purge Configuration Files
                            </div>
                            <div class="cell last">
                                Purge files older than <input id="purgeDays" type="text" size="2" maxlength="3" /> days <button id="purgeBtn" onclick="purge()">Go!</button>
                                <span id="pleaseWait" style="display:none">Please wait... <img  width="12" height="12" src='images/ajax_loader.gif' alt='Please wait... '/></span>
                            </div>
                        </div>

                    </div>
                </fieldset>
                <fieldset id="dashboardFieldset" style="width:78%">
                    <legend>Connection Log</legend>
                    <a id="refreshLog" href="#"><img src="images/refresh.png" alt="Click to refresh system log" title="Click to refresh system log"/></a> Displaying last <font color="red">
                    <select name="displayNoLogs" id="displayNoLogs" tabindex='11' onchange="getLog(this.value)">
                        <option value ="10" selected>10</option>
                        <option value ="20">20</option>
                        <option value ="50">50</option>
                        <option value ="100">100</option>
                    </select>
                    </font> entries - <a href="#" onclick="javascript:openFile('/home/rconfig/logs/Conn-default.log');">View current connection log file</a>
                    <div class="spacer"></div><br/>
                    <table id="logDiv">
                    </table>					
                    <div id="logDivError" style="display:none;">
                        <div class="spacer"></div><br/>
                        <font color="red">Could not retrieve logging information</font>
                    </div>
                </fieldset>

            </div>
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/configoverview.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>