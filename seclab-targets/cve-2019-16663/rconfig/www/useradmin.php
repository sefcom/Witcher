<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Headwrap Include -->
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
                <?php
                // echo error message if is sent back in GET from CRUD
                if (isset($_SESSION['errors'])) {
                    // move nested errors array to new array
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>User Management</legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    ?> 
                    <?php
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add User</button>
                        <button onclick="editUser()">Edit User</button>
                        <button onclick="delUser()">Remove User</button>
                    </div>
                    <!-- begin devices form -->
                    <div id="userAddDiv" class="mainformDiv">

                        <form id="userAdd" method="post" action="lib/crud/userprocess.php" enctype="multipart/form-data" class="myform stylizedForm stylized">

                            <div style="width:300px; margin-bottom:10px;">
                                <label for="username"><font color="red">*</font> Username: </label>
                                <input name="username" id="username" tabindex='1'>
                                <div class="spacer"></div>
                                <?php
                                // echo error message if is sent back in GET from CRUD
                                if (isset($errors['username'])) {
                                    echo "<span class=\"error\">" . $errors['username'] . "</span>";
                                }
                                ?>

                                <label for="password"><font color="red">*</font> Password:</label>
                                <input name="password" id="password" type="password" tabindex='2'>
                                <div class="spacer"></div>
<?php
// echo error message if is sent back in GET from CRUD
if (isset($errors['password'])) {
    echo "<br /><span class=\"error\">" . $errors['password'] . "</span>";
}
?>

                                <label for="passconf"><font color="red">*</font> Password Confirm:</label>
                                <input name="passconf" id="passconf" type="password" tabindex='3'>
                                <div class="spacer"></div>
<?php
// echo error message if is sent back in GET from CRUD
if (isset($errors['passconf'])) {
    echo "<br /><span class=\"error\">" . $errors['passconf'] . "</span>";
}
?>

                                <label for="email"><font color="red">*</font> E-mail:</label>
                                <input name="email" id="email" size="40" tabindex='4'>
                                <div class="spacer"></div>
<?php
// echo error message if is sent back in GET from CRUD
if (isset($errors['email'])) {
    echo "<br /><span class=\"error\">" . $errors['email'] . "</span>";
}
?>
<div class="spacer"></div>
                                <label for="ulevelid"><font color="red">*</font> User Level:</label>
                                <select name="ulevelid" id="ulevelid" tabindex='5'>
                                    <option value="1" selected>User</option>
                                    <option value="9">Admin</option>
                                </select>
                                <div class="spacer"></div>

                                <input type="hidden" id="add" name="add" value="add">
                                <input type="hidden" id="editid" name="editid" value="">

                                <button id="save" tabindex='6' type="submit">Save</button>
                                <button class="show_hide" type="button" tabindex='7'>Close</button><?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
                                <div class="spacer"></div>
                            </div>
                        </form>
                    </div>
                    <!-- End mainformDiv -->
                    <div id="table">
<?php
/* full table stored off in different script */
include("useradmin.inc.php");
?>
                    </div>
                </fieldset>
            </div><!-- End Content -->
            <div style="clear:both;"></div>
        </div><!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/useradmin.js"></script>
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>