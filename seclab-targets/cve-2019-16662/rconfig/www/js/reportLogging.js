$(function () {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";

    // call getLog function on page load  from rconfigFunctions.js		
    getLog(10);

    // this function Gets the log field output and displays in the div - more to be done here
    $("#refreshLog").click(function (evt) {
        $("#logDiv").html(ajax_load).load(getLog(10));
    });

    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/logs/&ext=log", function (data) {

        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var filename = obj.filename;
                switch (filename) {
                    case "Conn-default.log":
                        displayFileName = "Connection Log";
                        break;
                    case "All-default.log":
                        displayFileName = "<font color=\"red\">Complete Log</font>";
                        break;
                    case "Fatal-default.log":
                        displayFileName = "Fatal Errors";
                        break;
                    case "Info-default.log":
                        displayFileName = "Information Log";
                        break;
                    case "Warn-default.log":
                        displayFileName = "Warning Log";
                        break;
                    default:
                        displayFileName = "";
                }
                var filepath = obj.filepath;

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="#noLink" title="click to view" alt="click to view" onclick="javascript:openFile(\'' + filepath + '\');">' + displayFileName + '</td>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
            });
            $('#logFiles tbody').html(html.join(''));
        } else {
            $('#logFiles tbody').append('<tr><td><font color="red">No Logs files to display</font></td></tr>');
        }
    });

    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/logs/archive/&ext=zip", function (data) {

        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) {
                var filename = obj.filename;
                var filepath = obj.filepath;

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to view" alt="click to view">' + filename + '</td>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
            });
            $('#archiveLogFiles tbody').html(html.join(''));
        } else {
            $('#archiveLogFiles tbody').append('<tr><td><font color="red">Archive folder Empty</font></td></tr>');
        }
    });
});


function getLog(value) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxGetLogFile.php?logType=All&value=" + value, function (data) {

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

// Open File by ajax
function openFile(filePath) {

    if (filePath) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            data.reverse(); // sort lines by most recent
            writeConsole(data.join('<br/>'), filePath); //writeConsole located in rconfigFunctions.js
        });
    } else {
        errorDialog('File not Selected!');
    }
}

function deleteDebugFiles(filePath, ext) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success === true) {
            errorDialog("Log files deleted successfully");
        } else {
            errorDialog("Some files could not be deleted");
        }
        window.location.reload();
    });
}

function archiveFiles(filePath, ext) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxArchiveFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success === true) {
            errorDialog("Log files archived");
        } else {
            errorDialog("Some files could not be archived");
        }
        window.location.reload();
    });
}