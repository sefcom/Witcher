<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Masthead Include -->    
    <?php include("includes/masthead.inc.php"); ?>

    <div id="mainwrap">
        <!-- TopNav Include -->    
        <?php include("includes/topnav.inc.php"); ?>
        <?php
// start DB for queries on this page
        require_once("../classes/db2.class.php");
        $db2 = new db2();
        ?>
        <div id="main">
            <!-- Breadcrumb Include -->    
            <?php include("includes/breadcrumb.inc.php"); ?>
            <!-- Announcement Include -->    
            <?php include("includes/announcement.inc.php"); ?>
            <div id="content"> <!-- Main Content Start-->
                <?php
                // echo error message if is sent back in GET from CRUD
                if (isset($_SESSION['errors'])) {
                    // move nested errors array to new array
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="settings">
                    <legend>Server Details</legend>
                    <?php
                    // set vars for page output
                    $ds = disk_total_space("/");
                    $fs = disk_free_space("/");

                    $db2->query("SELECT defaultNodeUsername, defaultNodePassword, defaultNodeEnable, useDefaultCredsManualSet, commandDebug, commandDebugLocation, deviceConnectionTimout, ldapServer, ldap_host, ldap_dn, ldap_user_group, ldap_admin_group, ldap_usr_dom, pageTimeout FROM settings WHERE id = 1");
                    $result = $db2->resultset();
                    $defaultNodeUsername = $result[0]['defaultNodeUsername'];
                    $defaultNodePassword = $result[0]['defaultNodePassword'];
                    $defaultNodeEnable = $result[0]['defaultNodeEnable'];
                    $defaultCredsManualSet = $result[0]['useDefaultCredsManualSet'];
                    $status = $result[0]['commandDebug'];
                    $debugLocation = $result[0]['commandDebugLocation'];
                    $timeout = $result[0]['deviceConnectionTimout'];
                    $ldapServer = $result[0]['ldapServer'];
                    $ldap_host = $result[0]['ldap_host'];
                    $ldap_dn = $result[0]['ldap_dn'];
                    $ldap_user_group = $result[0]['ldap_user_group'];
                    $ldap_admin_group = $result[0]['ldap_admin_group'];
                    $ldap_usr_dom = $result[0]['ldap_usr_dom'];
                    $pageTimeout = $result[0]['pageTimeout'];
                    ?>	
                    <div style="width:60%;">
                        <div class="tableSummary">
                            <div class="row">
                                <div class="cell">
                                    CPU
                                </div>
                                <div class="cell last">
                                    <?php echo get_cpu_type(); ?>
                                </div>
                            </div>

                            <div class="row even">
                                <div class="cell">
                                    Memory Free
                                </div>
                                <div class="cell last">
                                    <?php echo get_memory_free() . "%"; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="cell">
                                    Memory Total
                                </div>
                                <div class="cell last">
                                    <?php echo _format_bytes(get_memory_total()); ?>
                                </div>
                            </div>

                            <div class="row even">
                                <div class="cell">
                                    Disk Size
                                </div>
                                <div class="cell last">
                                    <?php echo _format_bytes($ds); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="cell">
                                    Disk Free
                                </div>
                                <div class="cell last">
                                    <?php echo _format_bytes($fs); ?>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="spacer"></div>
                        <label>Timezone </label>
                        <select id="timeZone" name="timeZone" onChange="timeZoneChange()">
                            <option value="" selected>Select</option>
                            <?php
                            $timezone_identifiers = DateTimeZone::listIdentifiers();
                            for ($i = 0; $i < count($timezone_identifiers); $i++) {
                                echo "<option value=\"$timezone_identifiers[$i]\">$timezone_identifiers[$i]</option>";
                            }
                            ?>
                        </select>
                        <div class="spacer"></div>
                        <div id="timeZoneNoticeDiv"></div>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <label title="Time before webpages expire and the user will be logged out" alt="Time before webpages expire and the user will be logged out">Webpage Timeout:
                            <span class="small">Timeout in seconds</span>
                        </label>
                        <input type="text" value="<?php echo $pageTimeout; ?>" id="pageTimeout" name="pageTimeout" size="3" maxlength="6" style="width:45px;margin-right:5px;"/>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <label>
                            <button class="smlButton" id="pageTimeoutGo" onclick="pageTimeoutGo()">Update Timeout</button>
                            <span id="pageTimeOutUpdated" style="display:none; color:green;">Updated!</span>
                        </label>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <hr/>
                        <label title="Enable/ disable device password encryption">Password Encryption:
                            <span class="small"><?php echo passwordEncryptionCheck(); ?></span>
                        </label>
                    </div>
                </fieldset>
                
                
                <fieldset id="settings">
                    <legend>Authentication Details</legend>
                        <div class="spacer"></div>
                        <label style="width: 120px;">
                            Enable LDAP Authentication
                        </label>
                        <input type="checkbox" id="enableLDAPAuth" style="width: 80px;" <?php echo ($ldapServer == 1) ? 'checked' : '' ; ?>> 
                        <div class="spacer"></div>
                        <div id="deviceSettings" class="myform stylizedForm stylized">
                        <label>
                            LDAP Host
                        </label>
                        <input type="text" value="<?php echo (isset ($ldap_host)) ? $ldap_host : '' ; ?>" id="ldap_host" name="ldap_host" placeholder="x.x.x.x" />
                        
                        <label>
                            LDAP DN
                        </label>
                        <input type="text" value="<?php echo (isset ($ldap_dn)) ? $ldap_dn : '' ;?>" id="ldap_dn" name="ldap_dn" placeholder="LDAP DN" />
                        
                        <label>
                            LDAP User Group
                        </label>
                        <input type="text" value="<?php echo (isset ($ldap_user_group)) ? $ldap_user_group : '' ;?>" id="ldap_user_group" name="ldap_user_group" placeholder="LDAP User Group" />

                        <label>
                            LDAP Admin Group
                        </label>
                        <input type="text" value="<?php echo (isset ($ldap_admin_group)) ? $ldap_admin_group : '' ;?>" id="ldap_admin_group" name="ldap_admin_group" placeholder="LDAP Admin Group" />

                        <label>
                            LDAP Domain
                        </label>
                        <input type="text" value="<?php echo (isset ($ldap_usr_dom)) ? $ldap_usr_dom : '' ;?>" id="ldap_usr_dom" name="ldap_usr_dom" placeholder="@domain.local" />
                        
                        </div>
                        <div class="spacer"></div>
                        <div class="spacer"></div>
                        <button class="smlButton" id="saveLDAP" onclick="enableLDAPAuth()">Update LDAP</button> 
                        <div class="spacer"></div>
                        <span id="ldapUpdated" style="display:none; color:green;">LDAP Updated!</span>
                </fieldset>
                
                
                
                <fieldset id="settings">
                    <legend>Device Settings</legend>
                    <div id="deviceSettings" class="myform stylizedForm stylized">
                        <label>
                            Default Node Username:
                        </label>
                        <input type="text" value="<?php echo $defaultNodeUsername; ?>" id="defaultNodeUsername" name="defaultNodeUsername" placeholder="username" />
                        <label>
                            Default Node Password:
                        </label>
                        <input type="password" value="<?php echo $defaultNodePassword; ?>" id="defaultNodePassword" name="defaultNodePassword" placeholder="password" />
                        <label>
                            Default Enable Mode Password:
                        </label>
                        <input type="password" value="<?php echo $defaultNodeEnable; ?>" id="defaultNodeEnable" name="defaultNodePassword" placeholder="password" />
                        
                        <div class="spacer"></div>
                        <label style="width: 120px;">
                            Show passwords
                        </label>
                        <input type="checkbox" id="passwordChkBox" onchange="showPasswords(this)" style="width: 80px;"> 
                        <div class="spacer"></div>

                        <label>
                            <button class="smlButton" id="updateDefaultPass" onclick="updateDefaultPass(
                                            document.getElementById('defaultNodeUsername').value,
                                            document.getElementById('defaultNodePassword').value,
                                            document.getElementById('defaultNodeEnable').value
                                            )">Update Credentials
                            </button>
                        </label>
                        <div class="spacer"></div>
                        <span id="updatedDefault" style="display:none; color:green;">Updated!</span>
                        <div class="spacer"></div>
                        <hr />
                        <br />
                        <?php
                        // check if logged in user is admin and display next lines
                        if ($session->isAdmin()) {
                            ?>
<!--                            <label class="labelwide">Manual upload/download credentials
                                <span class="smallwide">Globally force users to use their credentials for manual config downloads and config snippet uploads</span>
                            </label>
                            <select id="defaultCredsManualSet" name="defaultCredsManualSet" onChange="defaultCredsManualSet()">
                                <option value="" <?php if (!isset($defaultCredsManualSet) || ($defaultCredsManualSet == '')) { ?>selected<?php } ?>>Select</option>
                                <option value="0" <?php if ($defaultCredsManualSet == '0') { ?>selected<?php } ?>>No</option>
                                <option value="1" <?php if ($defaultCredsManualSet == '1') { ?>selected<?php } ?>>Yes</option>
                            </select>
                            <div class="spacer"></div>
                            <span id="updatedDefaultCredsManualSet" style="display:none; color:green;">Updated!</span>
                            <div class="spacer"></div>
                            <br />
                            <hr />-->
                            <?php
                        } // End check if logged in user is admin
                        ?>
                        <label>Connection Timeout:
                            <span class="small">Timeout in seconds</span>
                        </label>
                        <input type="text" value="<?php echo $timeout; ?>" id="deviceTout" name="deviceTout" size="1" maxlength="3" style="width:25px;margin-right:5px;"/>
                        <label>
                            <button class="smlButton" id="deviceToutGo" onclick="deviceToutGo()">Update Connection Timout</button>
                            <span  id="updated" style="display:none; color:green;">Updated!</span>
                        </label>
                        <div class="spacer"></div>
                        <br/>
                        <label>Debug device output:
                            <span class="small">Turn on device debug</span>
                        </label>
                        <select id="debugOnOff" name="debugOnOff" onChange="debugOnOff()">
                            <option value="" selected>Select</option>
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                        <div class="spacer"></div>
                        <div id="debugNoticeDiv"></div>
                        <div id="debugInfoDiv">
                            <div class="tableSummary">		
                                <div id="debugLogFiles">
                                    <table class="tableSimple">
                                        <thead>
                                                <th>Debugging Logs</th>
                                        </thead>
                                        <tbody>
                                            <tr id="settingsDebugLogs">
                                                <div id="logFileContent"></div>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br/>
                                    <button class="smlButton" id="deleteDebugsBtn" onclick="deleteDebugFiles('<?php echo $debugLocation ?>', 'txt')">Delete Logs</button> 
                                </div>	
                            </div>	
                        </div>	
                    </div>
                </fieldset>	
                <fieldset id="settings">
                    <legend>Email Settings</legend><a name="emailSettings"></a>
                    <form id="emailSettingsForm" method="post" action="lib/crud/settingsEmail.crud.php" enctype="multipart/form-data">
                        <?php
                        if (isset($errors['Success'])) {
                            echo "<span class=\"error\">" . $errors['Success'] . "</span><br />";
                        }
                        ?>
<?php
if (isset($errors['Fail'])) {
    echo "<span class=\"error\">" . $errors['Fail'] . "</span><br />";
}
?>

                        <div id="emailSettingsDiv" class="myform stylizedForm stylized">

                            <label>Local SMTP Server:
                                <span class="small">Server IP or hostname</span>
                            </label>
                            <input type="text" id="smtpServerAddr" name="smtpServerAddr" placeholder="mail.example.com" />
                            <div class="spacer"></div>

                            <label>From Address:
                                <span class="small">Mail from address:</span>
                            </label>
                            <input type="text" id="smtpFromAddr" name="smtpFromAddr" size="40" placeholder="admin@example.com">
                            <?php
// echo error message if is sent back in GET from CRUD
                            if (isset($errors['smtpFromAddr'])) {
                                echo "<br /><span class=\"error\">" . $errors['smtpFromAddr'] . "</span>";
                            }
                            ?>					
                            <div class="spacer"></div>

                            <label>Authentication:</label>
                            <input type="checkbox" id="smtpAuth" name="smtpAuth" value="1">
                            <div class="spacer"></div>

                            <div id="authDiv" style="display:none;">
                                <label>Username:</label>
                                <input type="text" id="smtpAuthUser" name="smtpAuthUser" size="40" placeholder="username">
                                <label>Password:</label>
                                <input type="password" id="smtpAuthPass" name="smtpAuthPass" size="40" placeholder="password">

                            </div>
                            <div class="spacer"></div>

                            <b>E-mail Recipients	</b><br/>
                            Email Recipient Address:<br/><textarea type="textarea" rows="4" cols="30" id="smtpRecipientAddr" name="smtpRecipientAddr" placeholder="user@example.com"></textarea><br/>
                            <?php
// echo error message if is sent back in GET from CRUD
                            if (isset($errors['smtpRecipientAddr'])) {
                                echo "<br /><span class=\"error\">" . $errors['smtpRecipientAddr'] . "</span>";
                            }
                            ?><br/>
                            <em>Seperate multiple address with a semi-colon and a space i.e. user@example.com; user2@example.com</em><br/><br/>

                            <button class="smlButton" id="smtpSaveButton" name="smtpSaveButton">Save</button>
                            <button class="smlButton" id="smtpUpdateButton" name="smtpUpdateButton">Update SMTP Details</button> 
                            <button class="smlButton" id="smtpClearButton" name="smtpClearButton" type="button" onclick="smtpClearSettings()">Clear SMTP Settings</button> <br/><br/>

                            <input type="hidden" id="add" name="add" value="add">
                            <input type="hidden" id="editid" name="editid" value="">
                            <hr/>
                        </div>
                    </form>

                    <div class="spacer"  style="padding-top:10px;"></div>
                    Last Test Result: <span id="smtpLastTest" name="smtpLastTest"></span>
                    <div class="spacer"></div>
                    <button class="smlButton" id="smtpUpdateButton" name="smtpUpdateButton" onclick="smtpTest()">Test Mail Server</button> 
                    <span  id="pleaseWait" style="display:none">Please wait... <img src='images/ajax_loader.gif' alt='Please wait... ' /></span>
                </fieldset>	

                <fieldset id="settings">
                    <legend>Software & Database Details</legend>
                    <?php
                    $db2->query("SELECT DATABASE()");
                    $dbNameRes = $db2->resultsetCols();
                    $db2->query("SELECT count(*) as total FROM nodes WHERE status = 1");
                    $nodesCntRes = $db2->resultset();
                    ?>
                    <div style="width:60%;">
                        <div class="tableSummary">
                            <div class="row even">
                                <div class="cell">
                                    PHP Version
                                </div>
                                <div class="cell last">
<?php echo phpversion(); ?>
                                </div>
                            </div>							
                            <div class="row even">
                                <div class="cell">
                                    OS Version
                                </div>
                                <div class="cell last">
<?php echo php_uname(); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="cell">
                                    Database Verson
                                </div>
                                <div class="cell last">
<?php echo $db2->pdo_get_server_info(); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="cell">
                                    Database Name
                                </div>
                                <div class="cell last">
<?php echo $dbNameRes[0]; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="cell">
                                    Node Count
                                </div>
                                <div class="cell last">
<?php echo $nodesCntRes[0]['total']; ?>
                                </div>
                            </div> 

                            <div class="row even">
                                <div class="cell">
                                    Database Connection
                                </div>
                                <div class="cell last">
<?php echo $db2->pdo_get_host_info(); ?>
                                </div>
                            </div>
                        </div>	
                    </div>
                    <div class="spacer"></div>
                    <span class="small">Purge deleted items from all database tables:</span>
                    <button class="smlButton" onclick="purgeDevice()">Purge</button>
                    <div class="spacer"></div>		
                    <span class="small">Turn on PHP Error Logging</span>
                    <select id="phpLoggingOnOff" name="phpLoggingOnOff" onChange="phpLoggingOnOff()">
                        <option value="" selected>Select</option>
                        <option value="0">Off</option>
                        <option value="1">On</option>
                    </select>
                    <div class="spacer"></div>	
                    <div id="getPhpLoggingStatusDiv"></div>
                    <div class="spacer"></div>					
                </fieldset>	
            </div><!-- End Content -->
            <div style="clear:both;"></div>
        </div><!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/settings.js"></script> 
        <!-- Footer Include -->    
<?php include("includes/footer.inc.php"); ?>
    </div> <!-- End Mainwrap -->
</body>
</html>