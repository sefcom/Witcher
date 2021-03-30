$(document).ready(function () {
    /* global bootbox */
// Open div and display error per above PHP next to input field
// Check if 'message' GET is set in URL - if it is, display form and PHP error next to field
    if (location.href.match(/\error/)) { // check url for 'message'
        $('schedulerForm').show(); // show form (which will also display error msg in php after input field)
        $(".show_hide").show(); // show show_hide class 
    } else {
        $(".schedulerForm").hide(); //hide form
        $(".show_hide").show(); // show show_hide class 
    }

    $('.show_hide').click(function () { // show_hide function on click
        $(".schedulerForm").toggle(); //speed set to 1ms so that div does not render on side of page first
    });
    // disable 'mailErrorsOnly' chkbox by default
    document.getElementById('mailErrorsOnly').disabled = true;
    document.getElementById('reportTypeSlctDiv').style.display = 'none';
    document.getElementById('reportTypeSlct').disabled = true;
    document.getElementById('snippetSlctDiv').style.display = 'none';
    document.getElementById('snippetSlct').disabled = true;
    document.getElementById('mailErrorsOnlyDiv').style.display = 'none';
    document.getElementById('mailErrorsOnly').disabled = true;
    document.getElementById('deviceSelectRadioDiv').style.display = 'none';
    document.getElementById('catSelectRadioDiv').style.display = 'none';
    document.getElementById('selectRadio').disabled = true;
    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;
    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;
});

// single row selector from rconfigFunctions.js
tblRowSelector('taskTbl');

/* Next function is to allow selection of devices or categories only select lists */
function deviceOrCatSelect() {
    chosen = "";
    len = document.taskForm.selectRadio.length;

    for (i = 0; i < len; i++) { // get values from selectRadio selection
        if (document.taskForm.selectRadio[i].checked) {
            chosen = document.taskForm.selectRadio[i].value;
        }
    }
    if (chosen === "deviceSelectRadio") { // if value = "deviceSelectRadio"
        document.taskForm.deviceSelect.disabled = false; //disable 'disabled' attr for 'deviceSelect' selection list
        document.taskForm.catSelect.disabled = true; //enable 'disabled' attr for 'catSelect' selection list
        document.getElementById("catSelect").selectedIndex = -1; // disable any selections made in the 'catSelect' selection list
    } else if (chosen === "catSelectRadio") { // if value = "catSelectRadio"
        document.taskForm.catSelect.disabled = false; //disable 'disabled' attr for 'catSelect' selection list
        document.taskForm.deviceSelect.disabled = true; //enable 'disabled' attr for 'deviceSelect' selection list
        document.getElementById("deviceSelect").selectedIndex = -1; // disable any selections made in the 'deviceSelect' selection list
    } else { // else both are disabled by default
        document.taskForm.deviceSelect.disabled = true;
        document.taskForm.catSelect.disabled = true;
    }
}

// Next action when delTask function is called from Delete button
function delTask() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Task?", 'lib/crud/scheduler.crud.php', "Please select a Task!");
}


// Next action when getTask function is called from edit button
function getTask() {
    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        //retrieve task details to display on form from getRow GET variable
        $.getJSON("lib/crud/scheduler.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {

            // get devices and list in deviceNameView DIV
            var devicesStr = '';

            $.each(data.devices, function (i, data) {
                var deviceName = data.deviceName;
                if (deviceName) {
                    devicesStr += deviceName + ', ';
                } 
                return devicesStr;
            });

            //loop through all items in the JSON array  
            var categoryNames = data.categoryName;
            var id = '';
            var taskTypeView = '';
            var taskNameView = '';
            $.each(data.rows, function (i, data) {
                var id = data.id;
                var taskTypeView = data.taskType;
                var taskNameView = data.taskname;
                var taskDescView = data.taskDescription;
                var addedByView = data.addedBy;
                var dateAddedView = data.dateAdded;
                // get crontime from DB and split to array
                var crontimeView = data.crontime;
                var n = crontimeView.split(" ");
                if (n[0] === "*") {
                    var minutesView = "Every Minute";
                } else {
                    var minutesView = n[0];
                }
                if (n[1] === "*") {
                    var hoursView = "Every Hour";
                } else {
                    var hoursView = n[1] + "hrs";
                }
                if (n[2] === "*") {
                    var dayOfMonthView = "Every Day of the Month";
                } else {
                    var dayOfMonthView = n[2];
                }
                if (n[3] === "*") {
                    var MonthView = "Every Month";
                } else {
                    var MonthView = n[3];
                }
                if (n[4] === "*") {
                    var dayOfWeekView = "Every Day";
                } else {
                    var dayOfWeekView = n[4];
                }

                if (taskTypeView === "1") {
                    var taskTypeView = "Download Configurations";
                } else if (taskTypeView === "2") {
                    var taskTypeView = "Run Report";
                } else if (taskTypeView === "3") {
                    var taskTypeView = "Schedule Config Snippet";
                }
                // display modal if taskNameView is NOT undefined or NULL
                if (taskNameView) {
                    // check if one or many categories are returned and display the categories, else this is a devices selection task and we will display the devices
                    if (categoryNames) {
                        var categoryNamesAsString = categoryNames.join(', '); // convert to string with spaces
                        bootbox.dialog({
                            title: '<h3 class="h3">Scheduled task details:</h3>',
                            size: 'large',
                            message: '' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Task Details</h4><br />' +
                                    '<hr>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task ID: </span><span id="taskIdView" name="taskIdView" style=\"float:left; font-size:12px;\">' + id + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Type: </span><span id="taskTypeView" name="taskTypeView" style=\"float: left; font-size:12px;\" >' + taskTypeView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Name: </span><span id="taskNameView" name="taskNameView" style=\"float: left; font-size:12px;\">' + taskNameView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Description: </span><span id="taskDescView" name="taskDescView" style=\"float: left; font-size:12px;\">' + taskDescView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Added By: </span><span id="addedByView" name="addedByView" style=\"float: left; font-size:12px;\">' + addedByView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Created on: </span><span id="dateAddedView" name="dateAddedView" style=\"float: left; font-size:12px;\">' + dateAddedView + '</span></p><br/>' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Schedule</h4><br />' +
                                    '<hr>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Minutes: </span><span id="minutesView" name="minutesView" style=\"float: left; font-size:12px;\">' + minutesView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Hours: </span><span id="hoursView" name="hoursView" style=\"float: left; font-size:12px;\">' + hoursView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Day of Month: </span><span id="dayOfMonthView" name="dayOfMonthView" style=\"float: left; font-size:12px;\">' + dayOfMonthView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Month: </span><span id="MonthView" name="MonthView" style=\"float: left; font-size:12px;\">' + MonthView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Day of Week: </span><span id="dayOfWeekView" name="dayOfWeekView" style=\"float: left; font-size:12px;\">' + dayOfWeekView + '</span></p><br/>' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Categories</h4><br />' +
                                    '<hr>' +
                                    '<div id="taskDetailsDiv" style="text-align: left;"><span style=\"font-size:12px;\">' + categoryNamesAsString + '</span></div>',
                            buttons: {
                                main: {
                                    label: "close",
                                    className: "btn"
                                }
                            }
                        });
                    } // end categorieNames Check
                    else { // list individual devices
                        bootbox.dialog({
                            title: '<h3 class="h3">Scheduled task details:</h3>',
                            size: 'large',
                            message: '' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Task Details</h4><br />' +
                                    '<hr>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task ID: </span><span id="taskIdView" name="taskIdView" style=\"float:left; font-size:12px;\">' + id + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Type: </span><span id="taskTypeView" name="taskTypeView" style=\"float: left; font-size:12px;\" >' + taskTypeView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Name: </span><span id="taskNameView" name="taskNameView" style=\"float: left; font-size:12px;\">' + taskNameView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Task Description: </span><span id="taskDescView" name="taskDescView" style=\"float: left; font-size:12px;\">' + taskDescView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Added By: </span><span id="addedByView" name="addedByView" style=\"float: left; font-size:12px;\">' + addedByView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Created on: </span><span id="dateAddedView" name="dateAddedView" style=\"float: left; font-size:12px;\">' + dateAddedView + '</span></p><br/>' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Schedule</h4><br />' +
                                    '<hr>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Minutes: </span><span id="minutesView" name="minutesView" style=\"float: left; font-size:12px;\">' + minutesView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Hours: </span><span id="hoursView" name="hoursView" style=\"float: left; font-size:12px;\">' + hoursView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Day of Month: </span><span id="dayOfMonthView" name="dayOfMonthView" style=\"float: left; font-size:12px;\">' + dayOfMonthView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Month: </span><span id="MonthView" name="MonthView" style=\"float: left; font-size:12px;\">' + MonthView + '</span></p><br/>' +
                                    '<p><span style=\"width: 200px; float: left; text-align: left; font-weight: bold; font-size:12px;\">Day of Week: </span><span id="dayOfWeekView" name="dayOfWeekView" style=\"float: left; font-size:12px;\">' + dayOfWeekView + '</span></p><br/>' +
                                    '<h4 class="h4" style="width: 100%; float: left; text-align: left; ">Devices</h4><br />' +
                                    '<hr>' +
                                    '<div id="taskDetailsDiv" style="text-align: left;"><span style=\"font-size:12px;\">' + devicesStr + '</span></div>',
                            buttons: {
                                main: {
                                    label: "close",
                                    className: "btn"
                                }
                            }
                        });
                    }
                } else {
                    bootbox.alert({
                        size: 'small',
                        title: "Notice!",
                        backdrop: false,
                        message: "Could not load data!"
                    });
                }
            });
        });
    } else {
        bootbox.alert({
            size: 'small',
            title: "Notice!",
            backdrop: false,
            message: "Please select a Task!"
        });
    }
} // end getTask Function

function mailErrorsChkBox() {
    var mailConnectionReportChkBox = document.getElementById('mailConnectionReport');
    if (mailConnectionReportChkBox.checked)
        document.getElementById('mailErrorsOnly').disabled = false;
    else
        document.getElementById('mailErrorsOnly').disabled = true;
}

function displayDownloadElements() {
    document.getElementById("reportTypeSlct").disabled = true;
    document.getElementById("reportTypeSlctDiv").style.display = 'none';
    document.getElementById("snippetSlct").disabled = true;
    document.getElementById("snippetSlctDiv").style.display = 'none';
    document.getElementById('mailErrorsOnlyDiv').style.display = 'block';
    document.getElementById('mailErrorsOnly').disabled = false;
    document.getElementById('deviceSelectRadioDiv').style.display = 'block';
    document.getElementById('catSelectRadioDiv').style.display = 'block';
    document.getElementById('selectRadio').disabled = false;
    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;
    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;
}

function displayReportElements() {
    document.getElementById("reportTypeSlct").disabled = false;
    document.getElementById("reportTypeSlctDiv").style.display = 'block';
    document.getElementById("snippetSlct").disabled = false;
    document.getElementById("snippetSlctDiv").style.display = 'none';
    document.getElementById('mailErrorsOnlyDiv').style.display = 'none';
    document.getElementById('mailErrorsOnly').disabled = true;
    document.getElementById('deviceSelectRadioDiv').style.display = 'none';
    document.getElementById('catSelectRadioDiv').style.display = 'none';
    document.getElementById('selectRadio').disabled = true;
    document.getElementById('chooseCatDiv').style.display = 'block';
    document.getElementById('catId').disabled = false;
    document.getElementById('catCommandDiv').style.display = 'block';
    document.getElementById('catCommand').disabled = false;
}

function displaySnippetElements() {
    document.getElementById("snippetSlct").disabled = false;
    document.getElementById("snippetSlctDiv").style.display = 'block';
    document.getElementById('reportTypeSlctDiv').style.display = 'none';
    document.getElementById('reportTypeSlct').disabled = true;
    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;
    document.getElementById('deviceSelectRadioDiv').style.display = 'block';
    document.getElementById('catSelectRadioDiv').style.display = 'block';
    document.getElementById('selectRadio').disabled = false;
    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;
}

// change list of commands in select box based on categories selection - only when run report is selected
function changeType() {
    var catCommandDiv = document.getElementById('catCommandDiv');
    var catCommandSel = document.getElementById('catCommand');
    var catIdSelect = document.getElementById('catId');
    var catId = $('#catId').val();
    var taskTypeVal = $('#taskType').val();

    catCommandDiv.style.display = catIdSelect.selectedIndex !== '' ? 'block' : 'none'; // check that anything other than '' for the catIdSelect is selected and display commands dropdown
    catCommandDiv.style.display = taskTypeVal === '3' ? 'none' : 'block'; // however, if the 'config snippet' task type is selected. Hide the Commands Div
    catCommandSel.disabled = taskTypeVal === '3' ? true : false; // however, if the 'config snippet' task type is selected. Disable the Commands Select

    if (taskTypeVal === '2') { // if taskType is Config Compare Report, then run next code to update various selects after selecting a category - this is not relevant for snippet cat select
        if (catId) { // if catId is not equal to '' i.e. catId is selected then carry on

            $.ajaxSetup({cache: false});
            $.getJSON("lib/ajaxHandlers/ajaxGetCommandsByCat.php?catId=" + catId, function (data) {
                var command = '';
                command += '<option value="">Please select</option>';
                for (var i = 0; i < data.length; i++) {
                    command += '<option value="' + data[i].command + '">' + data[i].command + '</option>'; // need to specify command as value for option as this is what will populate the grep
                }
                $("select#catCommand").html(command);
            });
        }

        if (catId) { // if catId is not equal to '' i.e. catId is selected then carry on
            $.ajaxSetup({cache: false});
            $.getJSON("lib/ajaxHandlers/ajaxGetNodesByCat.php?catId=" + catId, function (data) {
                var options = '';
                options += '<option value="">Please select</option>';
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].deviceName + '">' + data[i].deviceName + '</option>'; // need to specify deviceName as value for option as this is what will populate the grep
                }
                $("select#nodeId").html(options);
            });
        }
    } // end if taskType
}

// function to take sample cron string and populate fields
function selectSample(cronValue) {

    var array = cronValue.split(' ');
    var minute = array[0];
    var hour = array[1];
    var day = array[2];
    var month = array[3];
    var weekday = array[4];
    if (array !== '' || array !== ' ' || array !== null) {

        // foreach section below - 
        // update the relevant field with the cron value
        // switch for the value, and update the relevant fields select for the output text related i.e. */5 = '*/5 every 5 minutes'
        $('#minute').val(minute);
        switch (minute)
        {
            case minute:
                $('#minuteSelect').val(minute);
                break;
            default:
                $('#minuteSelect').val('--');
        }

        $('#hour').val(hour);
        switch (hour)
        {
            case hour:
                $('#hourSelect').val(hour);
                break;
            default:
                $('#hourSelect').val('--');
        }

        $('#day').val(day);
        switch (day)
        {
            case day:
                $('#daySelect').val(day);
                break;
            default:
                $('#daySelect').val('--');
        }

        $('#month').val(month);
        switch (month)
        {
            case month:
                $('#monthSelect').val(month);
                break;
            default:
                $('#monthSelect').val('--');
        }

        $('#weekday').val(weekday);
        switch (weekday)
        {
            case weekday:
                $('#weekdaySelect').val(weekday);
                break;
            default:
                $('#weekdaySelect').val('--');
        }
    } else {
        bootbox.alert({
            size: 'small',
            title: "Notice!",
            backdrop: false,
            message: "Cron details not found"
        });
    }
}

// function to add option value to relevant field
function selectOption(field, value) {
// chnage sample selction back to default
    $('#sampleOptions').val('--');

    if (value !== '' || value !== ' ' || value !== null) {
        $('#' + field).val(value);
    } else {
        bootbox.alert({
            size: 'small',
            title: "Notice!",
            backdrop: false,
            message: "Details not found for field" + field
        });
    }
}
