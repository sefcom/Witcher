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
            <div id="content" >
                
                <?php
                if (isset($_SESSION['errors'])) {
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Connection Template Management</legend>
                    <div id="pane1"  style="width: 100%; align: left;">
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
                        <button onclick="createTemplate()">Create Template</button>
                        <button onclick="deleteTemplate()">Delete Template</button>
                        <button onclick="backupTemplate()">Backup Templates</button>
                    </div>

                    <div id="table">
                        <?php
                        /* full table stored off in different script */
                        include("deviceConnTemplates.inc.php");
                        ?>
                    </div>
                    </div>  <!-- pane1 -->
                    <div id="pane2" style="width: 80%; align: left; padding: 20px;">
                        <span id="createEditNotice" class="warning" style="display: none;">&ensp;</span><br /><br />
                        <textarea id="code" name="code" style=" height:800px; display: none;">

                        </textarea>
                    <div id="toolbar2" style="display: none;">
                        <label for="fileName" title="you may add, or not add the .yml extension - spaces and unusal chars not allowed except '-' and '_'"><font color="red">*</font> File Name:</label>
                            <input name="fileName" id="fileName" style="margin:10px;">
                            <div class="spacer"></div>
                            <button onclick="saveCreate()">Save</button>
                            <button onclick="cancelCreate()">Cancel</button>
                    </div>
                    <div id="toolbar3" style="display: none;">
                        <input type="hidden" id="editID" value="">
                        <label for="fileName" title="you may add, or not add the .yml extension - spaces and unusal chars not allowed except '-' and '_'"><font color="red">*</font> File Name:</label>
                            <input name="fileName" id="fileName" style="margin:10px;">
                            <div class="spacer"></div>
                            <button onclick="saveEdit()">Edit</button>
                            <button onclick="cancelCreate()">Cancel</button>
                    </div>                        
                    </div> <!-- pane2 -->                    
                    </div>
                </fieldset>
            
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/deviceConnTemplates.js"></script>
        <script type="text/JavaScript" src="js/codemirror/lib/codemirror.js"></script>
        <script type="text/JavaScript" src="js/codemirror/mode/yaml/yaml.js"></script>

        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>