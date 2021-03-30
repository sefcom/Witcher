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
                    <legend>Compare</legend>
                    <div class="mainformDiv">
                        <p>
                            Complete the fields below and click compare to see differences between configuration files
                        </p>
                        <div id="spacer">
                        </div>
                        <br/>
                        <div name="compareForm" class="myform stylizedForm stylized" style="width:100%;">
                        <!-- New Config Search Feature Below -->
                        <div id="left" style="width:50%">
                            <label><font color="red">*</font>First Device:</label>
                            <input name="firstdevice" class="tooltip-right" data-original-title="Press enter to complete device selection" id="" placeholder="First Device" tabindex='1' style="width:150px;" value="">
                            <div class="spacer"></div>
                            <label><font color="red">*</font>Select Config to Compare:</label>
                            <select name="firstCommandSelect" id="firstCommandSelect" tabindex='2' style="width:155px;"></select> 
                            <div class="spacer"></div>
                            <div id="firstdatepickerDiv">
                                <label><font color="red">*</font>Date of Config:</label>
                                <input type="text" id="firstdatepickerSelect" class="datepicker datePickerCalendar" tabindex='3' style="width:150px;">
                            </div>
                        </div>
                        <div id="right" style="width:50%">
                            <label><font color="red">*</font>Second Device:</label>
                            <input name="seconddevice" id="" class="tooltip-right" data-original-title="Press enter to complete device selection" placeholder="Second Device" tabindex='4' style="width:150px;" value="" title="Press enter to complete device selection" alt="Press enter to complete device selection">
                            <div class="spacer"></div>
                            <label><font color="red">*</font>Select Config to Compare:</label>
                            <select name="secondCommandSelect" id="secondCommandSelect" tabindex='5' style="width:155px;"> </select> 
                            <div class="spacer"></div>
                            <div id="seconddatepickerDiv">
                                <label><font color="red">*</font>Select Config to Compare:</label>
                                <input type="text" id="seconddatepickerSelect" class ="datepicker datePickerCalendar" tabindex='6' style="width:150px;">
                            </div>
                        </div>
                        <!-- End mainformDiv -->
                        <div style="clear:both;"></div>
                        <div id="spacer">
                        </div>
                        <hr>
                        <div id ="linepaddingDiv">
                            <label for="linepadding">Line Padding:
                                <span class="smallwide">Number of lines to display before/after <br />each difference </span>
                            </label>
                            <input name="linepadding" id="linepadding" title="number of lines to display before/after each difference" size="1" maxlength="2" tabindex='7'>
                        </div>           
                        <div id="toolbar">
                            <button id="search" onclick="compare()" tabindex='8'>Compare</button>
                            <button id="reset" onclick="reloadPage()" tabindex='9'>Reset Page</button>
                        </div>
                        <div id="resultsDiv" name="resultsDiv">
                        </div>
                    </div>
                </fieldset>

            </div>
            <!-- End Content -->
            <div style="clear:both;"></div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/configcompare.js"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>