<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Masthead Include -->
    <?php include("includes/masthead.inc.php"); ?>
    <div id="mainwrap">
        <!-- TopNav Include -->
        <?php include("includes/topnav.inc.php"); ?>
        <div id="main">
            <?php
            /* Custom Devices Custom Form Functions */
            require_once("lib/crud/compliancePolicies.frm.func.php");
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
                    <legend> Compliance Policies </legend>
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
                        <button class="show_hide">Add Policy</button>
                        <button onclick="editPolicy()">Edit Policy</button>
                        <button onclick="delPolicy()">Remove Policy</button>
                    </div>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="policyAddForm" name="policyAddForm" method="post" action="lib/crud/compliancepolicies.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">

                            <div id="formDiv" style="width:600px; margin-bottom:10px;">
                                <div>
                                    <label for="policyName"><font color="red">*</font> Policy Name:</label>
                                    <input name="policyName" id="policyName" size="75" tabindex='1' <?php
                                    if (isset($errors['policyNameVal'])) {
                                        echo 'value="' . $errors['policyNameVal'] . '"';
                                    }
                                    ?>>
                                    <div  id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
                                        <?php
                                        if (isset($errors['policyName'])) {
                                            echo "<span class=\"error\">" . $errors['policyName'] . "</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div style="float:left;">
                                    <label for="policyDesc"><font color="red">*</font> Policy Description:</label>
                                    <input name="policyDesc" id="policyDesc" size="150" tabindex='2'  <?php
                                        if (isset($errors['policyDescVal'])) {
                                            echo 'value="' . $errors['policyDescVal'] . '"';
                                        }
                                        ?>>
                                    <div id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
<?php
if (isset($errors['policyDesc'])) {
    echo "<span class=\"error\">" . $errors['policyDesc'] . "</span>";
}
?>
                                    </div>
                                </div>
                                <div style="clear:both;">
                                </div>

                                <table border="0" cellpadding="3" cellspacing="0">
                                    <thead style="padding-left:20px;">
                                    <th>Available Policy Elements</th>
                                    <th></th>
                                    <th>Selected Policy Elements</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select id="availableElemsSel" name="availableElems[]" multiple="multiple" size="15" MULTIPLE>
<?php
// get list of available elements from lib/crud/compliancePolicies.frm.func.php
echo availableElems();
?>
                                                </select>
                                            </td>
                                            <td align="center" style="vertical-align:middle">
                                                <button  type="button" name="addBtn" id="addBtn" tabindex='11' class="paginate" style="width:22px; margin-left:10px; margin-top:2px;" onclick="SelectMoveRows(document.policyAddForm.availableElemsSel, document.policyAddForm.selectedElemsSel)">+</button>
                                                <br>
                                                <button  type="button" name="removeBtn" id="removeBtn" tabindex='11' class="paginate" style="width:22px; margin-left:10px; margin-top:2px;" onclick="SelectMoveRows(document.policyAddForm.selectedElemsSel, document.policyAddForm.availableElemsSel)">-</button>
                                            </td>
                                            <td>
                                                <select id="selectedElemsSel" name="selectedElems[]" multiple="multiple" size="15" MULTIPLE>
                                <?php
// get list of selected elements from lib/crud/compliancePolicies.frm.func.php
// echo selectElems(); 
                                ?>				
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
<?php
if (isset($errors['selectedElems'])) {
    echo "<span class=\"error\" style=\"padding-left:252px\">" . $errors['selectedElems'] . "</span>";
}
?>

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
include("compliancepolicies.inc.php");
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
        <script type="text/JavaScript" src="js/compliancepolicies.js"></script>
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>