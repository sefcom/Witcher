<?php include("includes/head_login.inc.php"); ?>
<body>
    <div id="headwrap">
        <div id="header">
            <div id="title"><h1>rConfig - Configuration Management</h1></div>
            <div id="logo"><img src="images/logos/rConfig_red_trnsprnt_1_64px.png" alt="rConfigLogo" title="rConfigLogo"></img></div>
        </div>
    </div>
    <div id="mainwrap">
        <div id="main">
            <!-- Breadcrumb Include -->    
            <?php include("includes/breadcrumb.inc.php"); ?>

            <!-- Announcement Include -->    
            <?php include("includes/announcement.inc.php"); ?>
            <div id="broswerNotice"  class="notification warning" style="display:none;">
                <p> 
                </p>
            </div>

            <div id="login_content">
                <div class="mainformDiv">
                    <form action="lib/crud/userprocess.php" method="POST" class="myform stylizedForm stylized">
                        <!-- <h1>Login</h1>	
                        <p>Enter login credentials here</p> -->
                        <?php echo "<span class=\"error\">" . $form->error("user") . "</span><br />"; ?>
                        <?php echo $form->error("pass"); ?>
                        <div style="width:100%;float:left;">
                            <label class="loginlabel">Username</label>
                            <input type="text" value="<?php echo $form->value("user"); ?>" name="user" tabindex="1" class="loginInput"/>
                        </div>	
                        <div style="width:100%;float:left;">
                            <label class="loginlabel">Password</label>
                            <input type="password" value="<?php echo $form->value("pass"); ?>" name="pass" tabindex="2" class="loginInput"/>
                        </div>
                        <label>
                            <span class="small">Remember me on this computer</span>
                        </label>
                        <input type="checkbox" class="checkbox" id="remember" name="remember" <?php if ($form->value("remember") != "") {
                            echo "checked";
                        } ?> tabindex="3"/>

                        <div class="spacer"></div>
                        <a href="javascript: void(0)" 
                           onclick="window.open('userforgot.php',
                                                           'ForgotPassword',
                                                           'width=400, \
					   height=250, \
					   directories=no, \
					   location=no, \
					   menubar=no, \
					   resizable=no, \
					   scrollbars=0, \
					   status=no, \
					   toolbar=no');
                                                   return false;"
                           tabindex="4"
                           style="font-size: 11px;">
                            Forgot my password!</a> 
                        <img src="images/icon_popup_dark.gif" alt="Forgot Password dialog box opens in a new window!" title="Password dialog box opens in a new window"/>
                        <input type="hidden" name="sublogin" value="1">
                        <button type="submit" value="Login"tabindex="3">Login</button>
                    </form>
                </div>
                <div class="spacer"></div>
            </div><!-- End Login Content -->
            <div id="loginMainLogo">
                <img src="images/logos/social_logo.png" alt="rConfigLogo" title="rConfigLogo" height="196" width="196"></img>
            </div>
            <div style="clear:both;"></div>
        </div><!-- End Main -->

        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/login.js"></script> 

        <!-- Footer Include -->    
<?php include("includes/footer.inc.php"); ?>
    </div> <!-- End Mainwrap -->
</body>
</html>