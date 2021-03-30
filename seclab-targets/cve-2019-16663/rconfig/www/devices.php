<?php
include("includes/head.inc.php");
?>
<body>
    <!-- Headwrap Include -->
        <?php include("includes/masthead.inc.php"); ?>
    <div id="mainwrap">
        <!-- TopNav Include -->
            <?php include("includes/topnav.inc.php"); ?>
        <div id="main">
            <?php
            /* Custom Devices Custom Form Functions */
            require_once("lib/crud/devices.frm.func.php");
            ?>
            <!-- Breadcrumb Include -->
            <?php include("includes/breadcrumb.inc.php"); ?>
            <!-- Announcement Include -->
                <?php include("includes/announcement.inc.php"); ?>
            <div id="content">
                <?php
                if (isset($_SESSION['errors'])) {
                    $errors = $_SESSION['errors'];
                }
                // below are populated if errors are sent back from CRUD script to re-populate from				
                if (isset($_SESSION['deviceName'])) {
                    $deviceName = $_SESSION['deviceName'];
                    unset($_SESSION['deviceName']);
                }
                if (isset($_SESSION['deviceIpAddr'])) {
                    $deviceIpAddr = $_SESSION['deviceIpAddr'];
                    unset($_SESSION['deviceIpAddr']);
                }
                if (isset($_SESSION['devicePrompt'])) {
                    $devicePrompt = $_SESSION['devicePrompt'];
                    unset($_SESSION['devicePrompt']);
                }
                if (isset($_SESSION['deviceEnablePrompt'])) {
                    $deviceEnablePrompt = $_SESSION['deviceEnablePrompt'];
                    unset($_SESSION['deviceEnablePrompt']);
                }
                if (isset($_SESSION['vendorId'])) {
                    $vendorId = $_SESSION['vendorId'];
                    unset($_SESSION['vendorId']);
                }
                if (isset($_SESSION['deviceModel'])) {
                    $deviceModel = $_SESSION['deviceModel'];
                    unset($_SESSION['deviceModel']);
                }
                if (isset($_SESSION['defaultCreds'])) {
                    $defaultCreds = $_SESSION['defaultCreds'];
                    unset($_SESSION['defaultCreds']);
                }
                if (isset($_SESSION['deviceUsername'])) {
                    $deviceUsername = $_SESSION['deviceUsername'];
                    unset($_SESSION['deviceUsername']);
                }
                if (isset($_SESSION['devicePassword'])) {
                    $devicePassword = $_SESSION['devicePassword'];
                    unset($_SESSION['devicePassword']);
                }
                if (isset($_SESSION['deviceEnablePassword'])) {
                    $deviceEnablePassword = $_SESSION['deviceEnablePassword'];
                    unset($_SESSION['deviceEnablePassword']);
                }
                if (isset($_SESSION['catId'])) {
                    $catId = $_SESSION['catId'];
                    unset($_SESSION['catId']);
                }
				
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Device Management</legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    if (isset($errors['username'])) {
                        echo "<span class=\"error\">" . $errors['username'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <div id="toolbarButtons">
                            <button class="show_hide">Add Device</button>
                            <button onclick="editDevice('button')">Edit Device</button>
                            <button onclick="delDevice()">Remove Device</button>
                            <!-- set below to let scripts know that the from is in edit mode - set in editDevice() -->
                            <input type="hidden" id="editModeOn" name="editModeOn" value="">
                            <!-- <button class="show_import"><img src="images/icon_import_dark.gif"/> &nbsp;Bulk Import</button> -->
                        </div>
                        <!-- end toolbarButtons -->
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form method="post" action="lib/crud/devices.crud.php"  class="myform stylizedForm stylized" onsubmit="return checkPwInput(this);">
                            <div id="left">
                                <legend>Device Details</legend>
                                <label for="deviceName"><font color="red">*</font> Device Name:</label>
                                <input name="deviceName" id="deviceName" placeholder="Device Name" tabindex='1' style="width:150px;" value="<?php if (isset($deviceName)) echo $deviceName; ?>" class="tooltip-right" data-original-title="You cannot edit a Device Name after it has been created" title="You cannot edit a Device Name after it has been created" alt="You cannot edit a Device Name after it has been created">
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['deviceName'])) {
                                    echo "<span class=\"error\">" . $errors['deviceName'] . "</span>";
                                }
                                ?>

                                <label><font color="red">*</font> IP Address:</label>
                                <span class="small"><a href="javascript:void(0)" onclick="resolveDevice(document.getElementById('deviceName').value);">resolve device name</a></span>
                                <input name="deviceIpAddr" id="deviceIpAddr" placeholder="x.x.x.x" tabindex='2' style="width:150px;" value="<?php if (isset($deviceIpAddr)) echo $deviceIpAddr; ?>">
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['deviceIpAddr'])) {
                                    echo "<span class=\"error\">" . $errors['deviceIpAddr'] . "</span>";
                                }
                                ?> <br/>
 <label> Enable Prompt:</label>
                                <input name="deviceEnablePrompt" id="deviceEnablePrompt" placeholder="router>" tabindex='2' style="width:150px;" value="<?php if (isset($deviceEnablePrompt)) echo $deviceEnablePrompt; ?>">
                                <div class="spacer"></div>
<?php
if (isset($errors['deviceEnablePrompt'])) {
    echo "<span class=\"error\">" . $errors['deviceEnablePrompt'] . "</span>";
}
?> <br/>
                                <label><font color="red">*</font> Main Prompt:</label>
                                <input name="devicePrompt" id="devicePrompt" placeholder="router#" tabindex='3' style="width:150px;" value="<?php if (isset($devicePrompt)) echo $devicePrompt; ?>">
                                <div class="spacer"></div>
<?php
if (isset($errors['devicePrompt'])) {
    echo "<span class=\"error\">" . $errors['devicePrompt'] . "</span>";
}
?> <br/>
                               
                                <label><font color="red">*</font> Vendor:</label>
                                <select name="vendorId" id="vendorId" tabindex='4' style="width:155px;">
                                    <?php
                                    if (isset($vendorId)) {
                                        vendorId($vendorId);
                                    } else {
                                        vendorId();
                                    }
                                    /* taken from devices.frm.func.php */
                                    ?>
                                </select>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['vendorId'])) {
                                    echo "<span class=\"error\">" . $errors['vendorId'] . "</span>";
                                }
                                ?> 
                                <label><font color="red">*</font>Model:</label>
                                <input name="deviceModel" id="deviceModel" placeholder="Model" tabindex='5' style="width:150px;" value="<?php if (isset($deviceModel)) echo $deviceModel; ?>">
                                <div class="spacer"></div>
<?php
if (isset($errors['deviceModel'])) {
    echo "<span class=\"error\">" . $errors['deviceModel'] . "</span>";
}
?>

                            </div>
                            <div id="right">
                                <legend>Other Details</legend>
                                <label><font color="red">*</font> Category:</label>
                                <select name="catId" id="catId" style="width:155px;" tabindex='6' value="<?php if (isset($catId)) echo $catId; ?>" onchange="changeCatAlert(document.getElementById('editModeOn').value)">
                                <?php
                                if (isset($catId)) {
                                    categories($catId);
                                } else {
                                    categories();
                                }
                                /* taken from devices.frm.func.php */
                                ?>
                                </select>
                                <div class="spacer"></div>
<?php
if (isset($errors['catId'])) {
    echo "<span class=\"error\">" . $errors['catId'] . "</span>";
}
?> <br/>

                                <label>Custom Properties:</label>
<?php customProp(); /* taken from devices.frm.func.php */ ?>
                                <br/>
                                <br/>
                            </div>

                            <div id="left">
                                <legend>Credentials</legend>

                                <label>Default username/password?</label>
                                <!-- <span class="small"><a href="javascript:void(0)" onclick="getDefaultUserPass();">default username/password</a></span> -->
                                <input type="checkbox" name="defaultCreds" id="defaultCreds" onclick="getDefaultUserPass(this);" style="width:15px;" <?php if (isset($defaultCreds)) echo 'checked'; ?>>
                                <br />

                                <label>Username:</label>
                                <input name="deviceUsername" id="deviceUsername" placeholder="username" tabindex='7' style="width:150px;" value="<?php if (isset($deviceUsername)) echo $deviceUsername; ?>" autocomplete="off">
                                <div class="spacer"></div>
<?php
if (isset($errors['deviceUsername'])) {
    echo "<span class=\"error\">" . $errors['deviceUsername'] . "</span>";
}
?>
                                <br/>

                                <label>Password:</label>
                                <input type="password" name="devicePassword" id="devicePassword" placeholder="password" tabindex='8'  style="width:150px;" value="<?php if (isset($devicePassword)) echo $devicePassword; ?>" autocomplete="off">
                                <div class="spacer"></div>
<?php
if (isset($errors['devicePassword'])) {
    echo "<span class=\"error\">" . $errors['devicePassword'] . "</span>";
}
?> <br/>

                                <label>Enable Password:</label>
                                <input type="password" name="deviceEnablePassword" id="deviceEnablePassword" placeholder="Enable Password" tabindex='9' style="width:150px;" value="<?php if (isset($deviceEnablePassword)) echo $deviceEnablePassword; ?>" autocomplete="off">
                                <div class="spacer"></div>
                                    <?php
                                    if (isset($errors['deviceEnableMode'])) {
                                        echo "<span class=\"error\">" . $errors['deviceEnableMode'] . "</span>";
                                    }
                                    ?> <br/>

                                    
                                <label><font color="red">*</font> Template:</label>
                                <select name="templateId" id="templateId" tabindex='10' style="width:155px;">
                                    <?php
                                    if (isset($templateId)) {
                                        templateId($templateId);
                                    } else {
                                        templateId();
                                    }
                                    /* taken from devices.frm.func.php */
                                    ?>
                                </select>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['templateId'])) {
                                    echo "<span class=\"error\">" . $errors['templateId'] . "</span>";
                                }
                                ?>         

                            </div>
                            <input type="hidden" id="add" name="add" value="add">
                            <input type="hidden" id="username" name="username" value="<?php echo $session->username; ?>">
                            <input type="hidden" id="editid" name="editid" value="">
                            <!-- end devices form -->
                            <div id="saveButtons">
                                <button class="" tabindex='13' id="submit" type="submit">Save</button>
                                <button class="show_hide" type="button" tabindex='14'>Close</button>
                            </div>
                        </form>
                    </div>
                    <!--  end mainformDiv -->
                    <div id="devicesTable">
<?php include("devices.inc.php"); ?>
                    </div>
                </fieldset>
            </div>
            <!-- End Content -->
            <div style="clear:both;"></div>
            <!-- JS script Include -->
            <script type="text/JavaScript" src="js/devices.js"></script>
        </div>
        <!-- End Main -->
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>
