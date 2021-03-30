<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Masthead Include -->
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
                    <legend> Configuration Snippets </legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add Snippet</button>
                        <button onclick="editSnippet()">Edit Snippet</button>
                        <button onclick="delSnippet()">Remove Snippet</button>
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="snippetAddForm" name="snippetAddForm" method="post" action="lib/crud/snippets.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">

                            <div id="formDiv" style="width:600px; margin-bottom:10px;">
                                <div>
                                    <label for="snippetName"><font color="red">*</font> Snippet Name:</label>
                                    <input name="snippetName" id="snippetName" size="75" tabindex='1' <?php
                                    if (isset($errors['snippetNameVal'])) {
                                        echo 'value="' . $errors['snippetNameVal'] . '"';
                                    }
                                    ?>>
                                    <div  id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
                                        <?php
                                        if (isset($errors['snippetName'])) {
                                            echo "<span class=\"error\">" . $errors['snippetName'] . "</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div style="float:left;">
                                    <label for="snippetDesc"><font color="red">*</font> Snippet Description:</label>
                                    <input name="snippetDesc" id="snippetDesc" size="150" tabindex='2'  <?php
                                    if (isset($errors['snippetDescVal'])) {
                                        echo 'value="' . $errors['snippetDescVal'] . '"';
                                    }
                                    ?>>
                                    <div id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
                                        <?php
                                        if (isset($errors['snippetDesc'])) {
                                            echo "<span class=\"error\">" . $errors['snippetDesc'] . "</span>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div style="clear:both">
                                    <label for="snippet"><font color="red">*</font> Snippet:</label>
                                    <textarea rows="8" cols="100" wrap="off" id="snippet" name="snippet" tabindex='6' style="width:500px;margin-left:10px;float:clear;" <?php
                                    if (isset($errors['snippetVal'])) {
                                        echo 'value="' . $errors['snippetVal'] . '"';
                                    }
                                    ?>/>
                                    </textarea> 
                                    <div class="spacer"></div>
                                    <div  id="errorDiv" style="float:left;margin-left:77px; margin-top:-10px; margin-bottom:10px;">
                                        <?php
                                        if (isset($errors['snippet'])) {
                                            echo "<span class=\"error\">" . $errors['snippet'] . "</span>";
                                        }
                                        ?>
                                    </div>						
                                </div>

                                <input type="hidden" id="add" name="add" value="add" tabindex='3'>
                                <input type="hidden" id="editid" name="editid" value="" tabindex='4'>
                                <div class="spacer"></div>
                                <button id="save" type="submit" tabindex='21'>Save</button>
                                <button class="show_hide" type="button" tabindex='22'>Close</button><?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
                            </div>
                        </form>
                    </div>
                    <!-- End mainformDiv -->
                    <div id="table">
                        <?php
                        /* full table stored off in different script */
                        include("snippets.inc.php");
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
        <script type="text/JavaScript" src="js/snippets.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>