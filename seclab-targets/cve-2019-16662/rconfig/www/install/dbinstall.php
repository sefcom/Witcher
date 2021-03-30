<?php
require('includes/head.php');
?>	
<div id="content">
    <h2>Database Setup</h2>
    <h3>Settings</h3>
    <hr/>
    <div id="section">
        <div id="notes">
            <p>
                Please complete the form with your Database server settings
            </p>
            <p>
                If you need to modify these settings later, you will have to update includes/config.inc.php file manually
            </p>		
        </div>
        <div id="items">
            <div class="cell">
                <ul>
                    <li><label><strong>Database Server</strong></label></li>
                    <li><label><strong>Database Port</strong></label></li>			
                    <li><label><strong>Database Name</strong></label></li>
                    <li><label><strong>Database Username</strong></label></li>
                    <li><label><strong>Database Password</strong></label></li>
                </ul>
            </div>

            <div class="cellinside">
                <ul>
                    <li><input type="text" id="dbServer" name="dbServer" placeholder="x.x.x.x / hostname"></li>
                    <li><input type="text" id="dbPort" name="dbPort" value="3306"></li>
                    <li><input type="text" id="dbName" name="dbName" placeholder="my_Database"></li>
                    <li><input type="text" id="dbUsername" name="dbUsername" placeholder="username"></li>
                    <li><input type="password" id="dbPassword" name="dbPassword" placeholder="password"></li>
                </ul>
            </div>

        </div>	
    </div>	
    <div class="clear"></div>	

    <h3>Verify Settings</h3>
    <hr/>
    <div id="section">
        <div id="notes">
            <p>
                Please click the 'check settings' button before installation to ensure settings are valid	
            </p>
            <div id="chkDbSettingsBtn"><a href="#javascript:void(0);" onclick="getStatus();">Check Settings</a></div> 
        </div>			
        <div id="items">

            <div class="cell">
                <ul>
                    <li><label><strong>Database Server/Port</strong></label><span id="dbServerPortTest"></span></li>
                    <li><label><strong>Database Credentials</strong></label><span id="dbNameTest"></span></li>
                    <li><label><strong>Database Name</strong></label><span id="dbCredTest"></span></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="clear"></div><br/><br/>

    <h3>Install Configuration Settings</h3>
    <hr/>
    <div id="section">
        <div id="notes">
            <div id="chkDbSettingsBtn"><a href="#javascript:void(0);" onclick="installConfig();">Install Database</a></div> 
        </div>			
        <div id="items">
            <div class="cellinside">
                <ul>
                    <li><div id="msg"><br/></div></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="clear"></div>

</div>
<!-- JS script Include -->
<script type="text/JavaScript" src="js/dbinstall.js"></script>
<div id="footer">
    <div id="footerNav">
        <div id="last"><a href="license.php"><< Last</a></div>
        <div id="next"><a href="finalcheck.php">Next >></a></div>
    </div>
</div>
</div>
</body>
</html>