<?php include("includes/head.inc.php"); ?>
<body style="overflow:hidden;">
    <?php
    /**
     * userAccountEdit.php
     *
     * This page is for users to edit their account information
     * such as their password, email address, etc. Their
     * usernames can not be edited. When changing their
     * password, they must first confirm their current password.
     */
    ?>	
    <div id="forgotPasswordForm" class="myform stylizedForm stylized">
        <?php
        /**
         * User has submitted form without errors and user's
         * account has been edited successfully.
         */
        if (isset($_SESSION['useredit'])) {
            unset($_SESSION['useredit']);

            echo "<h3>User Account Edit Success!</h3>";
            echo "<p><b>$session->username</b>, your account has been successfully updated "
            . "<a href=\"#\" onClick=\"javascript:window.close();\">Close</a></p>";
        } else {

            /**
             * If user is not logged in, then do not display anything.
             * If user is logged in, then display the form to edit
             * account information, with the current email address
             * already in the field.
             */
            if ($session->logged_in) {

                // echo error message if is sent back in GET from lib/crud/ userproess.php
                if (isset($_SESSION['errors'])) {
                    // move nested errors array to new array
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>

                <h1>User Account Edit : <?php echo $session->username; ?></h1>
                <p>You can update your password and change your email address here.<br><br></p>
        <?php
        if ($form->num_errors > 0) {
            echo "<td><font size=\"2\" color=\"#ff0000\">" . $form->num_errors . " error(s) found</font></td>";
        }
        ?>
                <?php echo $form->error("username"); ?>
                <form action="lib/crud/userprocess.php" method="POST"  style="margin-left:50px;">
                    <input type="hidden" name="editid" value="<?php echo $session->userid; ?>">
                    <input type="hidden" name="username" value="<?php echo $session->username; ?>">
                    <input type="hidden" name="ulevelid" value="<?php echo $session->userlevel; ?>">

                    <label style="font-size:0.9em">Current Password:
        <?php
        // echo error message if is sent back in GET from CRUD
        if (isset($errors['curpass'])) {
            echo "<span class=\"error\">" . $errors['curpass'] . "</span>";
        } else {
            echo "<span class=\"small\">Your current password</span>";
        }
        ?>
                    </label>
                    <input type="password" name="curpass" maxlength="30" value="<?php echo $form->value("curpass"); ?>">


                    <div class="spacer"></div>
                    <label style="font-size:0.9em">New Password:
                        <?php
                        // echo error message if is sent back in GET from CRUD
                        if (isset($errors['password'])) {
                            echo "<span class=\"error\">" . $errors['password'] . "</span>";
                        } else {
                            echo "<span class=\"small\">Type Chosen new password</span>";
                        }
                        ?>
                    </label>
                    <input type="password" name="newpass" maxlength="30" value="<?php echo $form->value("newpass"); ?>">

                    <div class="spacer"></div>
                    <label style="font-size:0.9em">Confirm Password:
                        <?php
                        // echo error message if is sent back in GET from CRUD
                        if (isset($errors['passconf'])) {
                            echo "<span class=\"error\">" . $errors['passconf'] . "</span>";
                        } else {
                            echo "<span class=\"small\">Confirm new password</span>";
                        }
                        ?>
                    </label>
                    <input type="password" name="passconf" maxlength="30" value="<?php echo $form->value("passconf"); ?>">

                    <div class="spacer"></div>
                    <label style="font-size:0.9em">Email Address:
                        <?php
                        // echo error message if is sent back in GET from CRUD
                        if (isset($errors['email'])) {
                            echo "<span class=\"error\">" . $errors['email'] . "</span>";
                        } else {
                            echo "<span class=\"small\">Edit your email address</span>";
                        }
                        ?>
                    </label>
                    <input type="text" name="email" maxlength="50" value="<?php
                    if ($form->value("email") == "") {
                        echo $session->userinfo['email'];
                    } else {
                        echo $form->value("email");
                    }
                    ?>
                           ">
                    <div class="spacer"></div>

                    <input type="hidden" name="subedit" value="1">
                    <button type="submit" value="Submit" class="forgotBtn">Submit</button>


                <!--<table align="left" border="0" cellspacing="0" cellpadding="3">
                <tr>
                <td>Current Password:</td>
                <td><input type="password" name="password" maxlength="30" value="
                    <?php echo $form->value("password"); ?>"></td>
                <td><?php echo $form->error("curpass"); ?></td>
                </tr>
                <tr>
                <td>New Password:</td>
                <td><input type="password" name="newpass" maxlength="30" value="
                    <?php echo $form->value("newpass"); ?>"></td>
                <td><?php echo $form->error("newpass"); ?></td>
                </tr>
                <tr>
                <td>Email:</td>
                <td><input type="text" name="email" maxlength="50" value="<?php
                    if ($form->value("email") == "") {
                        echo $session->userinfo['email'];
                    } else {
                        echo $form->value("email");
                    }
                    ?>
                ">
                </td>
                <td><?php echo $form->error("email"); ?></td>
                </tr>
                <tr><td colspan="2" align="right">
                <input type="hidden" name="subedit" value="1">
                <input type="submit" value="Edit Account"></td></tr>
                <tr><td colspan="2" align="left"></td></tr>
                </table>
        </form>
        <?php
    }
}
