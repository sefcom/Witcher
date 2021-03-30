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
                <fieldset id="tableFieldset" style="width:50%; float:left;">
                    <legend>System Backups</legend>
                    <div id="backupsDiv" style="width:95%; padding-right:20px;">
                        <table class="tableSimple">
                            <thead>
                            <th>
                                Backup Archives
                            </th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br/>
                        <button class="smlButton" id="" onclick="createBackup('/home/rconfig/backups/', 'zip')">Create Full Backup</button>
                        <button class="smlButton" id="deleteFilesbtn" onclick="deleteFiles('/home/rconfig/backups/', 'zip')">Delete Backups</button>
                        <div class="spacer"></div>
                        <span id="pleaseWait1" style="display:none">Please wait... <img  width="12" height="12" src='images/ajax_loader.gif' alt='Please wait... '/></span>
                        <span id="BackupStatusPass" style="display:none; color:green;">Backup Successful</span>
                        <span id="BackupStatusFail" style="display:none; color:red;">Backup Failed</span>
                        <span id="refreshBackupDiv" style="display:none; color:Blue;"><a href="#" onclick="refreshBackupDiv();">Refresh</a></span>
                    </div>
                </fieldset>

                <fieldset id="tableFieldset" style="width:50%; float:left;">
                    <legend>System Log Dump</legend>
                    <div id="syslogsDiv" style="width:95%; padding-right:20px;">
                        <table class="tableSimple">
                            <thead>
                            <th>
                                System Log Archives
                            </th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br/>
                        <button class="smlButton" id="" onclick="createBackupSyslog('/home/rconfig/backups/syslogs', 'zip')">Create System Log Archive</button>
                        <button class="smlButton" id="deleteFilesbtn" onclick="deleteFiles('/home/rconfig/backups/syslogs/', 'zip')">Delete  System Log Archives</button>
                        <div class="spacer"></div>
                        <span id="pleaseWait2" style="display:none">Please wait... <img  width="12" height="12" src='images/ajax_loader.gif' alt='Please wait... '/></span>
                        <span id="syslogBackupStatusPass" style="display:none; color:green;">Log Backup Successful</span>
                        <span id="syslogBackupStatusFail" style="display:none; color:red;">Log Backup Failed</span>
                        <span id="refreshLogDiv" style="display:none; color:Blue;"><a href="#" onclick="refreshLogDiv();">Refresh</a></span>
                    </div>
                </fieldset>
                <div style="clear:both;">
                </div>
                <!-- End Main -->
                <!-- JS script Include -->
                <script type="text/JavaScript" src="js/settingsBackup.js"></script>
                <!-- Footer Include -->
                <?php include("includes/footer.inc.php"); ?>
            </div>
            <!-- End Mainwrap -->
            </body>
            </html>