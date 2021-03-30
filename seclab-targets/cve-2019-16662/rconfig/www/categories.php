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
                /*
                 * clear the $_SESSSIONS var - 
                 * Do NOT unset the whole $_SESSION with unset($_SESSION) as this will 
                 * disable the registering of session variables through the $_SESSION superglobal
                 */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Category Management</legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    if (isset($errors['categoryName'])) {
                        echo "<span class=\"error\">" . $errors['categoryName'] . "</span>";
                    }
                    ?> 						

                    <div id="toolbar">
                        <button class="show_hide">Add Category</button>
                        <button onclick="editCategory()">Edit Category</button>
                        <button onclick="delCategory()">Remove Category</button>
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="categoryAdd" method="post" action="lib/crud/categories.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">
                            <div style="width:300px; margin-bottom:10px;">
                                <label for="categoryName"><font color="red">*</font> Category Name:</label>
                                <div class="spacer"></div>
                                <input name="categoryName" id="categoryName">
                                <div class="spacer"></div>
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
                        include("categories.inc.php");
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
        <script type="text/JavaScript" src="js/categories.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>