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
                if (isset($_SESSION['errors'])) {
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset" style="width:90%;">
                    <legend>Search Form</legend>
                    <!-- begin devices form -->
                    <div class="mainformDiv">
                        <form id="searchForm" method="post" enctype="multipart/form-data" class="myform stylizedForm stylized">

                            <label for="searchTerm"><font color="red">*</font> Search Term:</label>
                            <span class="small">enter search string</span>
                            <input type="text" name="searchTerm" id="searchTerm" title="Minimum 2 chars for autocomplete" tabindex="1" placeholder="string here..">
                            <?php
                            // echo error message if is sent back in GET from CRUD
                            if (isset($errors['searchTerm'])) {
                                echo "<br /><span class='\"error\"'>" . $errors['searchTerm'] . "</span>";
                            }
                            ?> 
                            <div class="spacer"></div>

                            <label><font color="red">*</font> Category</label>
                            <span class="small">Choose category</span>
                            <select name="catId" id="catId" onchange="changeType()" tabindex='2'>
                            <?php categories(); /* taken from devices.frm.func.php */ ?>
                            </select>
                            <?php
                            if (isset($errors['catId'])) {
                                echo "<br /><span class='\"error\"'>" . $errors['catId'] . "</span>";
                            }
                            ?>
                            <div class="spacer"></div>

                            <div id="catCommandDiv" style="display:none;">
                                <label><font color="red">*</font> Command</label>
                                <span class="small">Choose command</span>
                                <select name="catCommand" id="catCommand" tabindex='3'>
                                </select>
                            </div>
                            <div class="spacer"></div>

                            <div id="nodeIdDiv" style="display:none;">
                                <label>Device</label>
                                <span class="small">Choose device</span>
                                <select name="nodeId" id="nodeId" tabindex='4'>
                                </select>
                            </div>
                            <div class="spacer"></div>

                            <fieldset id="tableFieldset">
                                <legend>Optional Parameters</legend>
                                <label for="noLines">No. Lines Leading/Trailing:</label>
                                <span class="small">(1-99)</span>
                                <input name="noLines" id="noLines" title="number of lines to display before/after each result" size="1" maxlength="2" tabindex="5" style="width:18px;">
                                <select name="linesBeforeAfter" id="linesBeforeAfter" tabindex="6" style="width:100px;">
                                    <option value=""></option>
                                    <option value="-B">Leading</option>
                                    <option value="-A">Trailing</option>
                                </select>
                                <div class="spacer"></div>
                            </fieldset>
                            <!-- end search form -->
                        </form>
                        <div id="toolbar">
                            <button id="search" onclick="search()" tabindex='7'>Search</button>
                            <button id="reset" onclick="formReset()" tabindex='8'>Reset</button>
                        </div>
                    </div>
                    <!-- End mainformDiv -->
                    <div id="timeTaken"></div>
                    <div class="spacer"></div>
                    <div id="filesSearched"></div>
                    <div class="spacer"></div>
                    <table id="resultsTable" name="resultsTable" class="tableSimple">
                        <thead>
                            <tr>
                                <th>
                                    Device
                                </th>
                                <th>
                                    Date
                                </th>
                                <th>
                                    Results
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/search.js"></script>
        <!-- Footer Include -->
<?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>