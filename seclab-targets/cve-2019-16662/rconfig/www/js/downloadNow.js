$(window).load(function () {
    $('#noticeBoard').hide();
    var rid = location.search.split('rid=')[1];
    runDownloadScript(rid);
});



function runDownloadScript(rid) {
    $.ajax({
        async: false, // prevent an async call
        url: 'lib/ajaxHandlers/ajaxDownloadNow.php?rid=' + rid,
        data: {},
        dataType: "json",
        complete: function () {
            $('#loading').fadeOut(500);
            $('#loading').remove();
            $('#noticeBoard').show();
        },
        success: function (data) {
            $.each(data, function (i, item) {
                $('#noticeBoard').append('<div class="noticeDiv">' + item + '</>');
            });
        }
    });
}