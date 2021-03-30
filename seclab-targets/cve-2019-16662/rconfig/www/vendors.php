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
                if (isset($_SESSION['errors'])) {
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Vendor Management</legend>
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
                        <button class="show_hide">Add Vendor</button>
                        <button onclick="editVendor()">Edit Vendor</button>
                        <button onclick="delVendor()">Remove Vendor</button>
                    </div>
                    <!-- begin Vendor form -->
                    <div class="mainformDiv">
                        <form id="vendorsAdd" method="post" action="lib/crud/vendors.crud.php" enctype="multipart/form-data"  class="myform stylizedForm stylized"  style="width:100%;">

                            <div style="width:300px; margin-bottom:10px;">
                                <label for="vendorName"><font color="red">*</font> Vendor Name:</label>
                                <input name="vendorName" id="vendorName">
                                <div class="spacer"></div>
                                <?php
// echo error message if is sent back in GET from CRUD
                                if (isset($errors['vendorName'])) {
                                    echo "<span class=\"error\">" . $errors['vendorName'] . "</span>";
                                }
                                ?>

                                <label for="vendorLogo">Vendor Logo:</label>
                                <input name="vendorLogo" type="file" id="vendorLogo" size="40"/>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['fileInvalid'])) {
                                    echo "<span class=\"error\">" . $errors['fileInvalid'] . "</span>";
                                }
                                ?> 
                                <?php
                                if (isset($errors['fileError'])) {
                                    echo "<span class=\"error\">" . $errors['fileError'] . "</span>";
                                }
                                ?> 

                                <input type="hidden" id="add" name="add" value="add">
                                <input type="hidden" id="editid" name="editid" value="">
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
                        include("vendors.inc.php");
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
        <script type="text/JavaScript" src="js/vendors.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>