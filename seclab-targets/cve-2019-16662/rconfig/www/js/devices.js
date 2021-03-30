$(document).ready(function () {
    
    if (getParameter('deviceid')) {
        editDevice('deviceMgmtPage');
    }

    // tooltip for editing a devices name
    $("#deviceName").click(function () {
        if ($("#deviceName").val() !== '') {
            $('.tooltip-right').tooltip({placement: 'right'});
        }
    });

    if (location.href.match(/\error/)) {
        $("#dialog-category-Switch-Error").hide();
        $('.mainformDiv').show();
        $(".show_hide").show();
    } else {
        $("#dialog-category-Switch-Error").hide();
        $(".mainformDiv").hide();
        $(".bulkImportDiv").hide();
        $(".show_hide").show();
    }
    $('.show_hide').click(function () {
        $(".dialog-category-Switch-Error").hide();
        $(".mainformDiv").toggle();
    });

    $("#deviceModel").autocomplete({
        source: "lib/ajaxHandlers/ajaxDeviceModelAutoComplete.php",
        minLength: 1,
        select: function (event, ui) {
            $('#id').val(ui.item.id);
            $('#modelName').val(ui.item.abbrev);
        }
    });
});

function checkPwInput(form) {
    // alert if password fields and empty, but give the user to proceed anyway if they agree as PWs are not mandatory
    if($('#devicePassword').val() == "") {
      return confirm('Device password is missing, and is not mandatory. Do you really want to submit the form?');
    }
    if($('#deviceEnablePassword').val() == "") {
      return confirm('Device Enable password is missing, and is not mandatory. Do you really want to submit the form?');
        return false;
    }
}

function searchValidateForm() {
// simple input text box check for search form. if nothing in the field throw and alert box
    var x = document.forms["searchForm"]["searchField"].value;
    if (x === null || x === "" || x === " ") {
        errorDialog("Please enter a search term!");
        return false;
    }
}

// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "devices.php";
}

// single row selector from rconfigFunctions.js
tblRowSelector('devicesTbl');

// Next action when delDevice function is called from Delete button
function delDevice() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Device?", 'lib/crud/devices.crud.php', "Please select a device!");
}


// Next action when editDevice function is called from edit button
function editDevice(invoc) {
    $.ajaxSetup({cache: false});
    var getRow = "getRow";
    if (getParameter('deviceid') && invoc === 'deviceMgmtPage') {
        var rowid = getParameter('deviceid');
    } else if (invoc === 'button') {
        var rowid = $('[name="tablecheckbox"]:checkbox:checked').attr("id");
    }
    if (rowid) {
        $.ajaxSetup({cache: false});
        //retrieve vendor details to display on form from getRow GET variable
        $.getJSON("lib/crud/devices.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var deviceName = data.deviceName;
                if (deviceName) {
                    // open form if not opened
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    //output data to fields
                    $('input[name="deviceName"]').val(deviceName);
                    $('input[name="deviceName"]').focus(function (e) {
                        $(this).blur();
                        $(this).css({'background-color': '#DFD8D1'});
                    });
                    $('input[name="deviceIpAddr"]').val(data.deviceIpAddr);
                    $('input[name="devicePrompt"]').val(data.devicePrompt);
                    $('input[name="deviceEnablePrompt"]').val(data.deviceEnablePrompt);
                    $("#vendorId").val(data.vendorId);
                    $('input[name="deviceModel"]').val(data.model);
                    if (data.defaultCreds === "1") {
                        $('#defaultCreds').attr('checked', 'checked');
                    }
                    $('input[name="deviceUsername"]').val(data.deviceUsername);
                    $('input[name="devicePassword"]').val(data.devicePassword);
                    $('input[name="deviceEnablePassword"]').val(data.deviceEnablePassword);
                    $('#templateId').val(data.templateId);
                    $("#catId").val(data.catId);
                    $('input[name="editid"]').val(rowid); // used to populate id input so that edit script will insert
                    $('input[name="editModeOn"]').val(1) // used to populate id input so that edit script will insert

                    // check if data has any 'custom_' keys
                    for (var key in data) {
                        // check string contains 'custom_' https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/String/indexOf
                        if (key.indexOf('custom_') !== -1) {
                            if (data[key] !== null) {
                                //alert('key name: ' + key + ' value: ' + data[key]);
                                $("#" + key).val(data[key]);
                            }
                        } else {
                            continue;
                        }
                    } // for
                } else {
                    errorDialog("Could not load data!");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Device to edit!");
    }
}

function resolveDevice(host) {
    if (host === '' || host === ' ' || host === null) {
        errorDialog("You must enter a Device Name");
    } else {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetIpByDevName.php?hostname=" + host, function (data) {
            if (data !== '' || data !== ' ' || data !== null) {
                $('input[name="deviceIpAddr"]').val(data);
            } else {
                errorDialog("Could not resolve hostname - Please check spelling or add domain name to Device Name");
            }
        });
    }
}

function getDefaultUserPass(cb) {
    if ($(cb).is(":checked")) {
        $.ajaxSetup({cache: false});
        $.getJSON('lib/ajaxHandlers/ajaxGetDefaultUserPass.php', function (data) {
            $.each(data, function (i, item) {
                if(item.defaultNodeUsername == '' || item.defaultNodePassword == '') {
                    errorDialog("Default credentials not configured in settings page!");
                }
                $('#deviceUsername').val(item.defaultNodeUsername);
                $('#devicePassword').val(item.defaultNodePassword);
                $('#deviceEnablePassword').val(item.defaultNodeEnable);
            });
        });
    } else {
        $.ajaxSetup({cache: false});
        $.getJSON('lib/ajaxHandlers/ajaxGetDefaultUserPass.php', function (data) {
            $.each(data, function (i, item) {
                $('#deviceUsername').val("");
                $('#devicePassword').val("");
                $('#deviceEnablePassword').val("");
            });
        });
    }
}

function changeCatAlert(editModeOn) {
    if (editModeOn !== '' || editModeOn !== NULL) {
        errorDialog("By changing devices Category, you may end up removing it from Scheduled Tasks where it was chosen as a specific device for that task.<br />" +
                "If you do change this devices Category, you should check to make sure all Scheduled tasks are updated that are associated with this specific device. <br/>" +
                "The change to this device will only take effect once you click 'save'");
    }
}