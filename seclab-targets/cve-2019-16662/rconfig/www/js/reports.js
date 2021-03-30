$(function () {
    outputReports('connectionReports');
    outputReports('compareReports');
    outputReports('complianceReports');
    outputReports('configSnippetReports');

});

function outputReports(reportName) {
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/reports/" + reportName + "/&ext=html", function (data) {

        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var filename = obj.filename;
                var filepath = obj.filepath;

                var rowHTML = ['<div>'];
                rowHTML.push('<p><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to view" alt="click to view">' + filename + '</p>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</div>');
                html.push(rowHTML.join(''));
            });
            $('#' + reportName).html(html.join(''));
            // pagination here: http://web.enavu.com/js/jquery/jpaginate-jquery-pagination-system-plugin/
            $('#' + reportName).jPaginate({items: 10, next: '', previous: '', goto: reportName});
        } else {
            $('#' + reportName).append('<tr><td><font color="red">Cannot display reports files or folder is empty</font></td></tr>');
        }
    });
}

function deleteFiles(filePath, ext, id) {
    $('#pleaseWait' + id).slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success === true) {
            errorDialog("Reports deleted successfully");
        } else {
            errorDialog("Some Reports could not be deleted");
        }
        window.location.reload();
    });
}