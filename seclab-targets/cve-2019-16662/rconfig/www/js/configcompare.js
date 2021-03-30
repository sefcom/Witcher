$(document).ready(function () {

    $('.tooltip-right').tooltip({placement: 'right'});

    // hide datepickers until selected
    $('#firstdatepickerDiv').hide();
    $('#seconddatepickerDiv').hide();

    // resets device input to white on select
    resetDeviceInputBgroud('firstdevice');
    resetDeviceInputBgroud('seconddevice');

    // device input auto complete
    deviceinputAutoComplete('first');
    deviceinputAutoComplete('second');

    // populate selects for commands
//    cmdSelectPopulate('firstdevice', 'firstCommandSelect');
//    cmdSelectPopulate('seconddevice', 'secondCommandSelect');

    // get dates for selected command
    datepickerSetup('firstdevice', 'firstCommandSelect', 'firstdatepickerDiv', 'firstdatepickerSelect');
    datepickerSetup('seconddevice', 'secondCommandSelect', 'seconddatepickerDiv', 'seconddatepickerSelect');
});


// reset device input text box to white on click - assumes the user wants to chnage the selected device
function resetDeviceInputBgroud(inputname) {
    $("input[name=" + inputname + "]").click(function () {
        $("input[name=" + inputname + "]").css({'background': '#FFF'});
    });
}

// autocomplete for device fields
function deviceinputAutoComplete(number) {
    var inputname = number + 'device';
    var selectname = number + 'CommandSelect';
    $("input[name=" + inputname + "]").autocomplete({
        source: "lib/ajaxHandlers/ajaxCompareDeviceSearchAuto.php",
        async: false,
        minLength: 1,
        create: function (e) {
            $(this).prev('.ui-helper-hidden-accessible').remove();
        },
        select: function (event, ui) {
            $("input[name=" + inputname + "]").attr('id', ui.item.id);
            $("input[name=" + inputname + "]").val(ui.item.abbrev);
            $("input[name=" + inputname + "]").css({'background': '#D0E4F4'});
            cmdSelectPopulate(inputname, selectname);
        }
    });
}

// populate commands select dropdown based on device selection
function cmdSelectPopulate(inputname, selectname) {
        // color the input field after enter on the keyboard
        var id = $("input[name=" + inputname + "]").attr('id');
        //Clear out the old values
        $("#" + selectname + "").empty();
        $.getJSON("lib/ajaxHandlers/ajaxCompareSelectCmds.php?term=" + id, function (data) {
            //Add the input items back in
            var html = '';
            var len = data.length;
            html = '<option value="">-- Select a command--</option>';
            for (var i = 0; i < len; i++) {
                html += '<option value="' + data[i].value + '">' + data[i].value + '</option>';
            }
            $("#" + selectname + "").append(html);
        });
}

// get dates for selected command
function datepickerSetup(inputname, selectname, datepickerDivName, datepickername) {
    $("#" + selectname + "").on('change', function () {
        firstId = $("input[name=" + inputname + "]").attr('id');
        command = $("#" + selectname + " option:selected").attr('value');
        command = command.replace(/\s/g, ''); // remove whitespace
        if (command !== '') {
            $("#" + datepickerDivName + "").show();
            $.getJSON("lib/ajaxHandlers/ajaxCompareGetCmdDates.php?deviceId=" + firstId + "&command=" + command, function (data) {

                $("#" + datepickername + "").datepicker("destroy");
                $("#" + datepickername + "").val('');


                $("#" + datepickername + "").datepicker({
                    dateFormat: 'dd-mm-yy',
                    beforeShowDay: enableSpecificDates
                });
                function enableSpecificDates(date) {
                    var month = date.getMonth();
                    var day = date.getDate();
                    var year = date.getFullYear();
                    for (i = 0; i < data.length; i++) {
                        if ($.inArray((month + 1) + '-' + day + '-' + year, data) !== -1) {
                            return [true, 'ui-state-active'];
                        }
                    }
                    return [false];
                }
            });
        } else {
            $("#" + datepickerDivName + "").hide();
        }
    });
}

function compare() {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    var linepadding = $('#linepadding').val();
    if (linepadding === '') {
        linepadding = 'null';
    }

    // get device names
    var firstDeviceName = $("input[name=firstdevice]").val();
    var secondDeviceName = $("input[name=seconddevice]").val();

    // get config name and remove space
    var firstConfigName = $('#firstCommandSelect').find(":selected").text();
    var firstCommand = firstConfigName.replace(/\s/g, ''); // remove whitespace
    var secondConfigName = $('#secondCommandSelect').find(":selected").text();
    var secondCommand = secondConfigName.replace(/\s/g, ''); // remove whitespace

    // get dates
    var firstDate = $("#firstdatepickerSelect").val();
    var secondDate = $("#seconddatepickerSelect").val();

    // Check is any vars are NOT empty, false, undefined or null etc..
    if (firstDeviceName && secondDeviceName && firstCommand && secondCommand && firstDate && secondDate) {
        // first get the actual paths for comparison
        var firstpath = getPaths(firstDeviceName, firstCommand, firstDate);
        var secondpath = getPaths(secondDeviceName, secondCommand, secondDate);
        // now remove forward slashes from json encoding beofer passing to GET
//        var firstpath = firstpath.replace(/\//g, '');
//        var secondpath = secondpath.replace(/\//g, '');
        if (firstpath && secondpath) {
            $('#resultsDiv').load("lib/crud/configcompare.crud.php?path_a=" + firstpath + "&path_b=" + secondpath + "&linepadding=" + linepadding);
        } else {
            errorDialog("Something went wrong! Could not retrieve configs for both devices");
        }

    } else {
        errorDialog("Please complete all fields!");
    }
}

// function to getpaths for inputted data. Syncronous call
function getPaths(deviceName, command, date) {
    var value = $.ajax({
        url: "lib/ajaxHandlers/ajaxCompareGetPaths.php?deviceName=" + deviceName + "&command=" + command + "&date=" + date,
        async: false
    }).responseText;
    return value;
}

function reloadPage() {
    window.location.reload(true); // true to reload with out POSTBACK warning in browser
}

