$(function () {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";

    $('#installMsgs').hide();
    $('#installNotice').hide();

});

function updateFn() {
    $('#pleaseWait').show();
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxInstallUpdate.php", function (data) {

        if (data.noUpdateFile) {
            errorDialog(data.noUpdateFile);
            window.location.reload();
        } else {
            $('#pleaseWait').hide();
            jQuery.each(data, function (i, val) {
                $("#installMsgs").append('<span>&nbsp;' + val + '&nbsp;<span><br />');
            });
            $('#installMsgs').show();
            $('#installNotice').show();
        }
    });
}