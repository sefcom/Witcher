$(function () {
    /* global bootbox */

    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/backups/&ext=zip", function (data) {

        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) {
                var filename = obj.filename;
                var filepath = obj.filepath;
                var filesize = obj.filesize;
                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to download" alt="click to download">' + filename + '</a>  ' + filesize + '</td>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
            });
            $('#backupsDiv tbody').html(html.join(''));
        } else {
            $('#backupsDiv tbody').append('<tr><td><font color="red">Cannot display backup files</font></td></tr>');
        }
    });
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/backups/syslogs/&ext=zip", function (data) {

        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) {
                var filename = obj.filename;
                var filepath = obj.filepath;
                var filesize = obj.filesize;

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to download" alt="click to download">' + filename + '</a>  ' + filesize + '</td>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
            });
            $('#syslogsDiv tbody').html(html.join(''));
        } else {
            $('#syslogsDiv tbody').append('<tr><td><font color="red">Cannot display system log archives</font></td></tr>');
        }
    });
});


// Open File by ajax
function createBackup() {
    $('#pleaseWait1').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxBackupFull.php", function (data) {
        if (data.success === true) {
            BackupStatusPass
            $('#pleaseWait1').hide();
            $('#BackupStatusPass').show();
            $('#refreshBackupDiv').show();
        } else {
            $('#pleaseWait1').hide();
            $('#BackupStatusFail').show();
        }
    });
}

function refreshBackupDiv(){
    window.location.reload();
}

// Open File by ajax
function createBackupSyslog() {
    $('#pleaseWait2').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxBackupSyslog.php", function (data) {

        if (data.success === true) {
            $('#pleaseWait2').hide();
            $('#syslogBackupStatusPass').show();
            $('#refreshLogDiv').show();
        } else {
            $('#pleaseWait2').hide();
            $('#syslogBackupStatusFail').show();
        }
    });
}

function refreshLogDiv(){
    window.location.reload();
}

function deleteFiles(filePath, ext) {
    bootbox.confirm({
        message: "Are you sure you want to remove all archives?",
        backdrop: false,
        size: 'small',
        title: "Notice!",
        callback: function (result) {
            if (result) {
                $('#pleaseWait').slideDown('fast');
                $.ajaxSetup({cache: false});
                $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
                    if (data.success === true) {
                        errorDialog("Archives deleted successfully");
                    } else {
                        errorDialog("Some Archives could not be deleted");
                    }
                    window.location.reload();
                });
            } else {
                window.location.reload();
            }
        }
    });
}