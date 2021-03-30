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
            require_once("lib/crud/scheduler.frm.func.php");
            ?>
            <!-- Breadcrumb Include -->
            <?php include("includes/breadcrumb.inc.php"); ?>
            <!-- Announcement Include -->
            <?php include("includes/announcement.inc.php"); ?>
            <?php
            /* PHP Script for the task Scheduler Page */
            /* Includes */
            require_once("../classes/db2.class.php");
            /* Instantiate DB Class */
            $db2 = new db2();
            /* Get all Category names for the catSelect Selection list where NOT deleted *///          
            $db2->query('SELECT `id`, `categoryName` FROM `categories` WHERE status = 1 ORDER BY `categoryName` ASC');
            $catQ = $db2->resultset();
            /* Get all Hostnames for the deviceSelect Selection list where NOT deleted */
            $db2->query('SELECT `id`, `deviceName`, `status` FROM `nodes` WHERE status = 1 ORDER BY `deviceName` ASC');
            $devQ = $db2->resultset();
            ?>
            <div id="content">
                <?php
                // echo error message if is sent back in GET from CRUD
                if (isset($_SESSION['errors'])) {
                    // move nest errors array to new array
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Current Tasks</legend>
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
                    <?php
                    if (isset($errors['hostfolder'])) {
                        echo "<span class=\"error\">" . $errors['hostfolder'] . "</span><br/>";
                    }
                    ?> 
                    <?php
                    if (isset($errors['fileCreateError'])) {
                        echo "<span class=\"error\">" . $errors['fileCreateError'] . "</span><br/>";
                    }
                    ?>
                    <?php
                    if (isset($errors['crontab'])) {
                        echo "<span class=\"error\">" . $errors['crontab'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add Scheduled Task</button>
                        <button onclick="delTask()">Remove Scheduled Task</button>
                        <button onclick="getTask()">View Scheduled Task</button>
                    </div>
                    <div id="table">
                        <?php
                        /* full table stored off in different script */
                        include("scheduler.inc.php");
                        ?>
                    </div>
                </fieldset>
                <div class="schedulerForm">
                    <fieldset id="tableFieldset">
                        <legend>Task Scheduler</legend>
                        <form name="taskForm" action="lib/crud/scheduler.crud.php" method="POST" class="myform stylizedForm stylized" style="width:100%;">
                            <label><font color="red">*</font> Select a Task Type:
                                <div class="spacer">
                                </div>
                                <?php
                                // echo error message if is sent back in GET from CRUD
                                if (isset($errors['taskType'])) {
                                    echo "<span class=\"error\">" . $errors['taskType'] . "</span>";
                                }
                                ?>
                            </label>
                            <?php //http://stackoverflow.com/questions/3988938/onclick-in-select-not-working-in-ie  ?>
                            <select name="taskType" id ="taskType" size="3" 
                                    onchange="switch (this.value) {
                                                case '1':
                                                    displayDownloadElements();
                                                    break;
                                                case '2':
                                                    displayReportElements();
                                                    break;
                                                case '3':
                                                    displaySnippetElements();
                                                    break;
                                            }">
                                <option value="1">Download Configurations</option>
                                <option value="2">Run Report</option>
                                <option value="3">Schedule Config Snippet</option>
                            </select>
                            <div class="spacer">
                            </div>
                            <!-- List Available Reports Select -->
                            <div name="reportTypeSlctDiv" id ="reportTypeSlctDiv">
                                <label><font color="red">*</font> Report Name:</label>
                                <select name="reportTypeSlct[]" id="reportTypeSlct">
                                    <option value="compare">Configuration Comparison</option>
                                    <?php echo reportsOptions(); // output full report name list options from scheduler.frm.func.php  ?>
                                </select>
                            </div>

                            <!-- List Available Snippets Select -->
                            <div name="snippetSlctDiv" id ="snippetSlctDiv">
                                <label><font color="red">*</font> Snippet Name:
                                    <div class="spacer"></div>
                                    <?php
                                    if (isset($errors['snippetSlct'])) {
                                        echo "<span class=\"error\">" . $errors['snippetSlct'] . "</span><br/>";
                                    }
                                    ?>
                                </label>
                                <select name="snippetSlct" id="snippetSlct">
                                    <option value="select">Select</option>
                                    <?php echo snippetsOptions(); // output full report name list options from scheduler.frm.func.php  ?>
                                </select>
                            </div>

                            <div class="spacer"></div>
                            <label><font color="red">*</font> Task Name:
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['taskName'])) {
                                    echo "<span class=\"error\">" . $errors['taskName'] . "</span><br/>";
                                }
                                ?>
                            </label>
                            <input type="text" name="taskName" id ="taskName" size="50"/>
                            <div class="spacer">
                            </div>
                            <label><font color="red">*</font> Task Description:
                                <div class="spacer"></div>
                                <?php
                                // echo error message if is sent back in GET from CRUD
                                if (isset($errors['taskDesc'])) {
                                    echo "<span class=\"error\">" . $errors['taskDesc'] . "</span>";
                                }
                                ?>
                            </label>
                            <input type="text" name="taskDesc" id ="taskDesc" size="50"/>
                            <div class="spacer">
                            </div>
                            <label>Email report:</label>
                            <input type="checkbox" name="mailConnectionReport" id="mailConnectionReport" value="1" onclick="mailErrorsChkBox();" class="checkbox"/>
                            <div class="spacer">
                            </div>
                            <div id="mailErrorsOnlyDiv">
                                <label>Email Errors Only:</label>
                                <input type="checkbox" name="mailErrorsOnly" id="mailErrorsOnly" value="1" class="checkbox"/>
                            </div>
                            <div class="spacer">
                            </div>
                            <div id="chooseCatDiv">
                                <label>Choose category: <?php
                                    // echo error message if is sent back in GET from CRUD
                                    if (isset($errors['catId'])) {
                                        echo "<span class=\"error\">" . $errors['catId'] . "</span>";
                                    }
                                    ?>
                                </label>
                                <select name="catId" id="catId" onchange="changeType()" tabindex='2'>
                                    <?php categories(); /* taken from devices.frm.func.php */ ?>
                                </select>
                                <div class="spacer">
                                </div>
                            </div>
                            <div id="catCommandDiv" style="display:none;">
                                <label>Choose command: <?php
                                    // echo error message if is sent back in GET from CRUD
                                    if (isset($errors['catCommand'])) {
                                        echo "<span class=\"error\">" . $errors['catCommand'] . "</span>";
                                    }
                                    ?>
                                </label>
                                <select name="catCommand" id="catCommand" tabindex='3'>
                                </select>
                            </div>
                            <div class="spacer">
                            </div>
                            <div id="deviceSelectRadioDiv">
                                <label>Select Devices:
                                    <div class="spacer">
                                    </div>
                                    <?php
// echo error message if is sent back in GET from CRUD
                                    if (isset($errors['selectRadio'])) {
                                        echo "<span class=\"error\">" . $errors['selectRadio'] . "</span>";
                                    }
                                    if (isset($errors['deviceSelectRadio'])) {
                                        echo "<span class=\"error\">" . $errors['deviceSelectRadio'] . "</span>";
                                    }
                                    ?>
                                </label>
                                <input type="radio" name="selectRadio" id="selectRadio" value="deviceSelectRadio" onclick="deviceOrCatSelect();" class="checkbox">
                                <select name="deviceSelect[]" id ="deviceSelect" size="10" multiple disabled>
                                    <?php
                                    foreach ($devQ as $row) {
                                        echo "<option value=" . $row['id'] . "> " . $row['deviceName'] . "</option> ";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="spacer">
                            </div>
                            <div id="catSelectRadioDiv">
                                <label>Select Categories:
                                    <div class="spacer">
                                    </div>
                                    <?php
                                    if (isset($errors['catSelectRadio'])) {
                                        echo "<span class=\"error\">" . $errors['catSelectRadio'] . "</span>";
                                    }
                                    ?>
                                </label>
                                <input type="radio" name="selectRadio" id="selectRadio" value="catSelectRadio" onclick="deviceOrCatSelect();" class="checkbox">
                                <select name="catSelect[]" id ="catSelect" size="10" multiple disabled>
                                    <?php
                                    foreach ($catQ as $row) {
                                        echo "<option value=" . $row['id'] . ">" . $row['categoryName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="spacer">
                            </div>
                            <div id="contabDiv">
                                <div id="divFormTitle" name="divFormTitle">
                                    <font color="red">*</font> <b>Enter Schedule Details:-</b>
                                </div>
                                <div class="spacer">
                                </div>
                                <?php
// echo error message if is sent back in GET from CRUD
                                if (isset($errors['cron'])) {
                                    echo "<span class=\"error\">" . $errors['cron'] . "</span><br/>";
                                }
                                ?>
                                <div class="spacer">
                                </div>

                                <table cellpadding="3" border="0" id="cronTable">
                                    <tr>
                                        <td align="right" style="vertical-align:middle;">Example<br />Settings:</td>
                                        <td>
                                            <select id="sampleOptions" onchange="selectSample(document.getElementById('sampleOptions').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="* * * * *">Every minute (* * * * *)</option>
                                                <option value="*/5 * * * *">Every 5 minutes (*/5 * * * *)</option>
                                                <option value="0,30 * * * *">Twice an hour (0,30 * * * *)</option>
                                                <option value="0 * * * *">Once an hour (0 * * * *)</option>
                                                <option value="0 0,12 * * *">Twice a day (0 0,12 * * *)</option>
                                                <option value="0 0 * * *">Once a day (0 0 * * *)</option>
                                                <option value="0 0 * * 0">Once a week (0 0 * * 0)</option>
                                                <option value="0 0 1,15 * *">1st and 15th (0 0 1,15 * *)</option>
                                                <option value="0 0 1 * *">Once a month (0 0 1 * *)</option>
                                                <option value="0 0 1 1 *">Once a year (0 0 1 1 *)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"  style="vertical-align:middle;">Minute:</td>
                                        <td>
                                            <input type="text" size="4" id="minute" name="minute">
                                            <select style="width: 175px" id="minuteSelect" onchange="selectOption('minute', document.getElementById('minuteSelect').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="*">Every minute (*)</option>
                                                <option value="*/2">Every other minute (*/2)</option>
                                                <option value="*/5">Every 5 minutes (*/5)</option>
                                                <option value="*/10">Every 10 minutes (*/10)</option>
                                                <option value="*/15">Every 15 minutes (*/15)</option>
                                                <option value="0,30">Every 30 minutes (0,30)</option>
                                                <option value="--">-- Minutes --</option>
                                                <option value="0">:00 top of the hour (0)</option>
                                                <option value="1">:01 (1)</option>
                                                <option value="2">:02 (2)</option>
                                                <option value="3">:03 (3)</option>
                                                <option value="4">:04 (4)</option>
                                                <option value="5">:05 (5)</option>
                                                <option value="6">:06 (6)</option>
                                                <option value="7">:07 (7)</option>
                                                <option value="8">:08 (8)</option>
                                                <option value="9">:09 (9)</option>
                                                <option value="10">:10 (10)</option>
                                                <option value="11">:11 (11)</option>
                                                <option value="12">:12 (12)</option>
                                                <option value="13">:13 (13)</option>
                                                <option value="14">:14 (14)</option>
                                                <option value="15">:15 quarter past (15)</option>
                                                <option value="16">:16 (16)</option>
                                                <option value="17">:17 (17)</option>
                                                <option value="18">:18 (18)</option>
                                                <option value="19">:19 (19)</option>
                                                <option value="20">:20 (20)</option>
                                                <option value="21">:21 (21)</option>
                                                <option value="22">:22 (22)</option>
                                                <option value="23">:23 (23)</option>
                                                <option value="24">:24 (24)</option>
                                                <option value="25">:25 (25)</option>
                                                <option value="26">:26 (26)</option>
                                                <option value="27">:27 (27)</option>
                                                <option value="28">:28 (28)</option>
                                                <option value="29">:29 (29)</option>
                                                <option value="30">:30 half past (30)</option>
                                                <option value="31">:31 (31)</option>
                                                <option value="32">:32 (32)</option>
                                                <option value="33">:33 (33)</option>
                                                <option value="34">:34 (34)</option>
                                                <option value="35">:35 (35)</option>
                                                <option value="36">:36 (36)</option>
                                                <option value="37">:37 (37)</option>
                                                <option value="38">:38 (38)</option>
                                                <option value="39">:39 (39)</option>
                                                <option value="40">:40 (40)</option>
                                                <option value="41">:41 (41)</option>
                                                <option value="42">:42 (42)</option>
                                                <option value="43">:43 (43)</option>
                                                <option value="44">:44 (44)</option>
                                                <option value="45">:45 quarter til (45)</option>
                                                <option value="46">:46 (46)</option>
                                                <option value="47">:47 (47)</option>
                                                <option value="48">:48 (48)</option>
                                                <option value="49">:49 (49)</option>
                                                <option value="50">:50 (50)</option>
                                                <option value="51">:51 (51)</option>
                                                <option value="52">:52 (52)</option>
                                                <option value="53">:53 (53)</option>
                                                <option value="54">:54 (54)</option>
                                                <option value="55">:55 (55)</option>
                                                <option value="56">:56 (56)</option>
                                                <option value="57">:57 (57)</option>
                                                <option value="58">:58 (58)</option>
                                                <option value="59">:59 (59)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right" style="vertical-align:middle;">Hour:</td>
                                        <td>
                                            <input type="text" size="4" id="hour"  name="hour" />&nbsp;
                                            <select style="width: 175px" id="hourSelect" onchange="selectOption('hour', document.getElementById('hourSelect').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="*">Every hour (*)</option>
                                                <option value="*/2">Every other hour (*/2)</option>
                                                <option value="*/3">Every 3 hours (*/3)</option>
                                                <option value="*/4">Every 4 hours (*/4)</option>
                                                <option value="*/6">Every 6 hours (*/6)</option>
                                                <option value="0,12">Every 12 hours (0,12)</option>
                                                <option value="--">-- Hours --</option>
                                                <option value="0">12:00 a.m. midnight (0)</option>
                                                <option value="1">1:00 a.m. (1)</option>
                                                <option value="2">2:00 a.m. (2)</option>
                                                <option value="3">3:00 a.m. (3)</option>
                                                <option value="4">4:00 a.m. (4)</option>
                                                <option value="5">5:00 a.m. (5)</option>
                                                <option value="6">6:00 a.m. (6)</option>
                                                <option value="7">7:00 a.m. (7)</option>
                                                <option value="8">8:00 a.m. (8)</option>
                                                <option value="9">9:00 a.m. (9)</option>
                                                <option value="10">10:00 a.m. (10)</option>
                                                <option value="11">11:00 a.m. (11)</option>
                                                <option value="12">12:00 p.m. noon (12)</option>
                                                <option value="13">1:00 p.m. (13)</option>
                                                <option value="14">2:00 p.m. (14)</option>
                                                <option value="15">3:00 p.m. (15)</option>
                                                <option value="16">4:00 p.m. (16)</option>
                                                <option value="17">5:00 p.m. (17)</option>
                                                <option value="18">6:00 p.m. (18)</option>
                                                <option value="19">7:00 p.m. (19)</option>
                                                <option value="20">8:00 p.m. (20)</option>
                                                <option value="21">9:00 p.m. (21)</option>
                                                <option value="22">10:00 p.m. (22)</option>
                                                <option value="23">11:00 p.m. (23)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"  style="vertical-align:middle;">Day:</td>
                                        <td>
                                            <input type="text" size="4" id="day" name="day" />&nbsp;
                                            <select style="width: 175px" id="daySelect" onchange="selectOption('day', document.getElementById('daySelect').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="*">Every day (*)</option>
                                                <option value="*/2">Every other day (*/2)</option>
                                                <option value="1,15">1st and 15th (1,15)</option>
                                                <option value="--">-- Days --</option>
                                                <option value="1">1st (1)</option>
                                                <option value="2">2nd (2)</option>
                                                <option value="3">3rd (3)</option>
                                                <option value="4">4th (4)</option>
                                                <option value="5">5th (5)</option>
                                                <option value="6">6th (6)</option>
                                                <option value="7">7th (7)</option>
                                                <option value="8">8th (8)</option>
                                                <option value="9">9th (9)</option>
                                                <option value="10">10th (10)</option>
                                                <option value="11">11th (11)</option>
                                                <option value="12">12th (12)</option>
                                                <option value="13">13th (13)</option>
                                                <option value="14">14th (14)</option>
                                                <option value="15">15th (15)</option>
                                                <option value="16">16th (16)</option>
                                                <option value="17">17th (17)</option>
                                                <option value="18">18th (18)</option>
                                                <option value="19">19th (19)</option>
                                                <option value="20">20th (20)</option>
                                                <option value="21">21st (21)</option>
                                                <option value="22">22nd (22)</option>
                                                <option value="23">23rd (23)</option>
                                                <option value="24">24th (24)</option>
                                                <option value="25">25th (25)</option>
                                                <option value="26">26th (26)</option>
                                                <option value="27">27th (27)</option>
                                                <option value="28">28th (28)</option>
                                                <option value="29">29th (29)</option>
                                                <option value="30">30th (30)</option>
                                                <option value="31">31st (31)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"  style="vertical-align:middle;">Month:</td>
                                        <td>
                                            <input type="text" size="4" id="month"  name="month" />&nbsp;
                                            <select style="width: 175px" id="monthSelect" onchange="selectOption('month', document.getElementById('monthSelect').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="*">Every month (*)</option>
                                                <option value="*/2">Every other month (*/2)</option>
                                                <option value="*/4">Every 3 months (*/4)</option>
                                                <option value="1,7">Every 6 months (1,7)</option>
                                                <option value="--">-- Months --</option>
                                                <option value="1">January (1)</option>
                                                <option value="2">February (2)</option>
                                                <option value="3">March (3)</option>
                                                <option value="4">April (4)</option>
                                                <option value="5">May (5)</option>
                                                <option value="6">June (6)</option>
                                                <option value="7">July (7)</option>
                                                <option value="8">August (8)</option>
                                                <option value="9">September (9)</option>
                                                <option value="10">October (10)</option>
                                                <option value="11">November (11)</option>
                                                <option value="12">December (12)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"  style="vertical-align:middle;">Weekday:</td>
                                        <td>
                                            <input type="text" size="4" id="weekday"  name="weekday" />&nbsp;
                                            <select style="width: 175px" id="weekdaySelect" onchange="selectOption('weekday', document.getElementById('weekdaySelect').value)">
                                                <option value="--">-- Select an option --</option>
                                                <option value="*">Every weekday (*)</option>
                                                <option value="1-5">Mon thru Fri (1-5)</option>
                                                <option value="0,6">Sat and Sun (6,0)</option>
                                                <option value="1,3,5">Mon, Wed, Fri (1,3,5)</option>
                                                <option value="2,4">Tues, Thurs (2,4)</option>
                                                <option value="--">-- Weekdays --</option>
                                                <option value="0">Sunday (0)</option>
                                                <option value="1">Monday (1)</option>
                                                <option value="2">Tuesday (2)</option>
                                                <option value="3">Wednesday (3)</option>
                                                <option value="4">Thursday (4)</option>
                                                <option value="5">Friday (5)</option>
                                                <option value="6">Saturday (6)</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>							


                                <div class="spacer">
                                </div>
                                <br/>
                                <input type="hidden" id="add" name="add" value="add">
                                <input type="hidden" id="editid" name="editid" value="">
                                <br/>
                                <div class="spacer">
                                </div>
                                <div id="bottomDiv">
                                    <button id="save" class="smlButton" type="submit">Save</button>
                                    <button class="show_hide smlButton" type="button">Close</button>
                                    <?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
                <!-- End Content -->
                <div style="clear:both;">
                </div>
            </div>
            <!-- End Main -->
            <!-- JS script Include -->
            <script type="text/JavaScript" src="js/scheduler.js"></script>
        </div>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
        <!-- End Mainwrap -->
    </div>
</body>
</html>