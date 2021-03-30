$(document).ready(function () {
    /* global deviceIpAddr */
    /* global connPort */
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    $("#throbber").show();

    // custom - ss - hide bottom show and hide and top buttons if in a div called $("#bottomButtons").show();
    // can be show if show all button is click on a given page
    $("#bottomButtons").hide();
    $("#throbber").hide();

    $('#pleaseWait').slideDown('fast');
    setTimeout(function () {
        $('#hostStatus').load('lib/ajaxHandlers/ajaxDeviceStatus.php?deviceIpAddr=' + deviceIpAddr + '&connPort=' + connPort);
    }, 1000);
});

// Open File by ajax
function openFile(filePath) {

    if (filePath) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            writeConsole(data.join('<br/>'), filePath);
        });
    } else {
        errorDialog('File not Selected!');
    }
}

function expandAll() {
    // Hide all subfolders at startup
    $(".php-file-tree").find("UL").show();
    $("#bottomButtons").show();
}

function hideAll() {
    // Hide all subfolders at startup
    $(".php-file-tree").find("UL").hide();
    $("#bottomButtons").hide();
}

function manualDownload(rid) {
    //this function really only opens the downloadNow popup
    window.open('downloadNow.php?rid=' + rid,
            'Edit Account',
            'width=600, \
            height=500, \
            top=200, \
            left=600, \
            directories=no, \
            location=no, \
            menubar=no, \
            resizable=no, \
            scrollbars=yes, \
            status=no, \
            toolbar=no');
    return false;
}

function configDevice(rid) {
    //this function really only opens the configDevice popup
    window.open('configDevice.php?rid=' + rid,
            'Edit Account',
            'width=600, \
            height=500, \
            top=200, \
            left=600, \
            directories=no, \
            location=no, \
            menubar=no, \
            resizable=no, \
            scrollbars=yes, \
            status=no, \
            toolbar=no');
    return false;
}

function editDevice(rid) {
    window.location = 'devices.php?deviceid='+rid;
}