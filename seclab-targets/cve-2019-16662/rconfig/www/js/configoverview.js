$(function () {
    var ajax_load = "<img src='images/ajax_loader.gif' alt='loading...' />";
    // call getLog function on page load  from rconfigFunctions.js		
    getLog(10);
    // this function Gets the log field output and displays in the div - more to be done here
    $("#refreshLog").click(function (evt) {
        $("#logDiv").html(ajax_load).load(getLog(10));
    });
});

// Open File by ajax
function openFile(filePath) {
    if (filePath) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            if (data === "Failed") {
                errorDialog('Could not open log file');
            } else {
                data.reverse(); // sort lines by most recent
                writeConsole(data.join('<br/>'), filePath); //writeConsole located in rconfigFunctions.js
            }
        });
    } else {
        errorDialog('File not Selected!');
    }
}

function getLog(value) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxGetLogFile.php?logType=Conn&value=" + value, function (data) {

        if (data === "Failed") {
            $('#logDivError').show();
        } else {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var file = obj.line;
                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td style="font-size:9px;">' + file + '</td>');
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
            });
            $('#logDiv').html(html.join(''));
        }
    });
}

function purge(value) {
    purgeDays = document.getElementById('purgeDays').value;
    var intRegex = /^\d+$/;

    if (purgeDays === null || purgeDays === 0 || !intRegex.test(purgeDays)) {
        errorDialog('Please enter a numerical value or a value greater than 0');
        return; //stop the execution of function 
    } else {
        bootbox.confirm({
            message: "Are you sure you purge all configuration files older than " + purgeDays + " days?",
            backdrop: false,
            size: 'small',
            title: "Notice!",
            callback: function (result) {
                if (result) {
                    $('#purgeBtn').hide();
                    $('#pleaseWait').slideDown('fast');
                    $.ajaxSetup({cache: false});
                    $.getJSON("lib/ajaxHandlers/ajaxPurgeConfigs.php?purgeDays=" + purgeDays, function (data) {
                        if (data === null) {
                            errorDialog('Nothing was deleted');
                        } else {
                            var response = data.response;
                            errorDialog(response);
                        }
                        $('#purgeBtn').show();
                        $('#pleaseWait').hide();
                    });
                } else {
                    window.location.reload();
                }
            }
        });
    }
}

