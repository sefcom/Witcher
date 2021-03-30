<!-- Head Include -->    
<?php include("includes/head_login.inc.php"); ?>
<body style="overflow:hidden;">
    <div id="forgotPasswordForm" class="myform stylizedForm stylized">
        <?php
        // echo error message if is sent back in GET from lib/crud/ userproess.php
        if (isset($_SESSION['errors'])) {
            // move nested errors array to new array
            $errors = $_SESSION['errors'];
        }
        /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
        $_SESSION['errors'] = array();
        /**
         * Forgot password form is displayed, if error found
         * it is displayed.
         */
        ?>
        <h1>Forgot Password</h1>
        <p>A new password will be generated for you and sent to the email address
            associated with your account, all you have to do is enter your
            username.<br><br></p>
        <form action="lib/crud/userprocess.php" method="POST" style="margin-left:50px;">
            <div class="spacer"></div>
            <label style="font-size:0.9em">Username:
                <div class="spacer"></div>
                <?php
                // echo error message if is sent back in GET from CRUD
                if (isset($errors['user'])) {
                    echo "<span class=\"error\">" . $errors['user'] . "</span>";
                } else {
                    echo "<span class=\"small\">Type your username</span>";
                }
                ?>
            </label> 
            <input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>" tabindex="1">
            <input type="hidden" name="subforgot" value="1">
            <button type="submit" value="Get New Password" tabindex="2" class="forgotBtn">Submit</button>
        </form>
    </div>
</body>
</html>