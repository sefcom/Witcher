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
                    <legend> Policy Elements </legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    if (isset($errors['selectRadioErr'])) {
                        echo "<span class=\"error\">" . $errors['selectRadioErr'] . "</span>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add Element</button>
                        <button onclick="editPolElem()">Edit Element</button>
                        <button onclick="delPolElem()">Remove Element</button>
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="elementAddForm" name="elementAddForm" method="post" action="lib/crud/compliancepolicyelements.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">

                            <div id="formDiv" style="width:600px; margin-bottom:10px;">
                                <div>
                                    <label for="elementName"><font color="red">*</font> Element Name:</label>
                                    <input name="elementName" id="elementName" size="75" tabindex='1' <?php
                                    if (isset($errors['elementNameVal'])) {
                                        echo 'value="' . $errors['elementNameVal'] . '"';
                                    }
                                    ?>>
                                    <div  id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
                                        <?php
                                        if (isset($errors['elementName'])) {
                                            echo "<span class=\"error\">" . $errors['elementName'] . "</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div style="float:left;">
                                    <label for="elementDesc"><font color="red">*</font> Element Description:</label>
                                    <input name="elementDesc" id="elementDesc" size="150" tabindex='2'  <?php
                                        if (isset($errors['elementDescVal'])) {
                                            echo 'value="' . $errors['elementDescVal'] . '"';
                                        }
                                        ?>>
                                    <div id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
<?php
if (isset($errors['elementDesc'])) {
    echo "<span class=\"error\">" . $errors['elementDesc'] . "</span>";
}
?>
                                    </div>
                                </div>

                                <div style="clear:both">
                                    <select id="singleParam1" name="singleParam1" style="width: 60px" tabindex='5'>
                                        <option value="1">equals</option>
                                        <option value="2">contains</option>
                                    </select>
                                    <input type="text" id="singleLine1" name="singleLine1" tabindex='6' style="width:345px;" <?php
if (isset($errors['singleLine1val'])) {
    echo 'value="' . $errors['singleLine1val'] . '"';
}
?>/>

                                    <div class="spacer"></div>
                                    <div  id="errorDiv" style="float:left;margin-left:77px; margin-top:-10px; margin-bottom:10px;">
<?php
if (isset($errors['singleLine1'])) {
    echo "<span class=\"error\">" . $errors['singleLine1'] . "</span>";
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
include("compliancepolicyelements.inc.php");
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
        <script type="text/JavaScript" src="js/compliancepolicyelements.js"></script>
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>