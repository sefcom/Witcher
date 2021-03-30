<?php
require('includes/head.php');
?>	
<div id="content">
    <h2>Final Checks</h2>

    <hr/>
    <div id="section">
        <div id="notes">
            <p>
                Please click the 'Final Check' button to verify all settings before launching rConfig
            </p>	
            <div id="chkDbSettingsBtn"><a href="#" onclick="finalCheck();">Final Check</a></div> 
        </div>
        <div id="items">
            <div class="cell">
                <ul>
                    <li><label><strong>Configuration File</strong></label><span id="configFile"></span></li>
                    <li><label><strong>Database Read</strong></label><span id="dbRead"></span></li>
                    <li><label><strong>Database Write</strong></label><span id="dbWrite"></span></li>
                    <li><label><strong>rConfig Directory Read</strong></label><span id="appDirRead"></span></li>
                    <li><label><strong>rConfig Directory Write</strong></label><span id="appDirWrite"></span></li>
                    <li><label><strong>Backup Read</strong></label><span id="backupDirRead"></span></li>
                    <li><label><strong>Backup Write</strong></label><span id="backupDirWrite"></span></li>
                    <li><label><strong>Tmp Dir Read</strong></label><span id="tmpDirRead"></span></li>
                    <li><label><strong>Tmp Dir Write</strong></label><span id="tmpDirWrite"></span></li>
                    <li><label><strong></strong></label><span id="finalNotice"></span></li>
                </ul>
            </div>

            <div id="passDiv" class="cell">
                <hr style="color:#898989;background-color:#898989;height:1px;border: 0;"/>
                <img src="img/greenCheck.png" style="vertical-align:middle;float:left;">
                <div style="float:left;width:300px;padding-left:25px;"><b>All tests have passed. rConfig is installed successfully. </b></div><br />
                <div class="clear"></div>	

                <img src="img/warning-sheild48.png" style="vertical-align:middle;float:left;">
                <div style="float:left;width:300px;padding-left:25px;"><b>You must delete the /install/ directory from /home/rconfig/www/ after you close this page </b></div><br />
                <div class="clear" style="line-height:15px;">&nbsp;</div>	

                <b>Login Credentials:-</b><br/>
                Default username:- <b>admin</b><br/>
                Default password:- <b>admin</b><br/><br/>
                Before Logging into rConfig, please carry out the following actions
                <ul STYLE="list-style-image: url(img/bullet_delete.png); padding-left:20px;">
                    <li>Login to System Shell and run '<b>chown -R apache /home/rconfig/</b>' </li>
                </ul>
                <br /><br /><br />
                Please carry out the following tasks once you are logged in to rConfig
                <ul STYLE="list-style-image: url(img/bullet_delete.png); padding-left:20px;">
                    <li>Reset admin password on the <b>settings/users</b> page</li>
                    <li>Create new users on the <b>settings/users</b> page</li>
                    <li>Add a vendor on the <b>devices/vendors</b> page</li>
                    <li>Add commands on the <b>devices/commands</b> page</li>
                    <li>Add custom properties on the <b>devices/Custom Properties</b> page</li>
                    <li>Verify and update all settings on the <b>settings</b> page</li>
                    <li>Take a system backup on the <b>settings/system backups</b> page</li>
                    <li>Add a device</li>
                    <li>Add a scheduled task</li>
                </ul>
                <div class="clear"></div>	
                <br/>
                Please go to the <a href="<?php echo 'https://' . $_SERVER['SERVER_NAME'] . '/login.php'; ?>">login page</a> to launch rConfig
            </div>
            <div id="failDiv" class="cell">
                rConfig installation failed. Please re-try installation, or contact technical support for installation assistance.
            </div>
        </div>	
    </div>	
    <div class="clear"></div>	

</div>
<!-- JS script Include -->
<script type="text/JavaScript" src="js/finalcheck.js"></script>
<div id="footer">
    <div id="footerNav">
        <div id="last"><a href="dbinstall.php"><< Last</a></div>
        <div id="next"  class="disabled"><a href="#">Next >></a></div>
    </div>
</div>
</div>
</body>
</html>