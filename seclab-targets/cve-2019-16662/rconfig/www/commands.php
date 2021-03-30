<?php include("includes/head.inc.php"); ?>
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
                // echo error message if is sent back in GET from CRUD
                if (isset($_SESSION['errors'])) {
                    // move nested errors array to new array
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Commands Management</legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add Command</button>
                        <button onclick="editCommand()">Edit Command</button>
                        <button onclick="delCommand()">Remove Command</button>
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="commandAdd" method="post" action="lib/crud/commands.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">

                            <div style="width:300px; margin-bottom:10px;">
                                <label for="command"><font color="red">*</font> Command:</label>
                                <input name="command" id="command" size="75" tabindex='1'>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['command'])) {
                                    echo "<span class=\"error\">" . $errors['command'] . "</span>";
                                }
                                ?>
                                <br/>

                                <label><font color="red">*</font> Category</label>
                                <select name="catId[]" id="catId" tabindex='2' size='6' multiple>
                                <?php categories(); /* taken from devices.frm.func.php */ ?>
                                </select>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['catId'])) {
                                    echo "<span class=\"error\">" . $errors['catId'] . "</span>";
                                }
                                ?>

                                <input type="hidden" id="add" name="add" value="add" tabindex='3'>
                                <input type="hidden" id="editid" name="editid" value="" tabindex='4'>
                                <div class="spacer"></div>
                                <button id="save" type="submit">Save</button>
                                <button class="show_hide" type="button">Close</button><?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
                            </div>
                        </form>
                    </div>
                    <!-- End mainformDiv -->
                    <div id="table">
<?php
/* full table stored off in different script */
include("commands.inc.php");
?>
                    </div>
                </fieldset>
            </div>
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/commands.js"></script>
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>