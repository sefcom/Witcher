$(function () {
    $('#failDiv').hide();
    $('#passDiv').hide();
});

function finalCheck() {
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxFinalCheck.php", function (data) {
        
        if (data) {
            console.log(data)

            var configFileMsg = data.configFileMsg
            var dbReadMsg = data.dbReadMsg
            var dbWriteMsg = data.dbWriteMsg
            var appFileReadMsg = data.appFileReadMsg
            var appFileWriteMsg = data.appFileWriteMsg
            var backupFileWriteMsg = data.backupFileWriteMsg
            var backupFileReadMsg = data.backupFileReadMsg
            var tmpFileWriteMsg = data.tmpFileWriteMsg
            var tmpFileReadMsg = data.tmpFileReadMsg

            $('#configFile').html(configFileMsg);
            $('#dbRead').html(dbReadMsg);
            $('#dbWrite').html(dbWriteMsg);
            $('#appDirRead').html(appFileReadMsg);
            $('#appDirWrite').html(appFileWriteMsg);
            $('#backupDirWrite').html(backupFileWriteMsg);
            $('#backupDirRead').html(backupFileReadMsg);
            $('#tmpDirWrite').html(tmpFileWriteMsg);
            $('#tmpDirRead').html(tmpFileReadMsg);

            // iterate over all json returned. Make sure all vals have 
            // '<strong><font class="Good">Pass</strong></font><br/>' a pass text
            var all_pass = true;

            for (var i in data) {
                if (data[i] != '<strong><font class="Good">Pass</strong></font><br/>') {
                    var all_pass = false;
                    break;
                }
            }
            ;

            if (all_pass == true) {
                $('#passDiv').show();
                $('#failDiv').hide();
            } else
            if (all_pass == false) {
                $('#failDiv').show();
                $('#passDiv').hide();
            }

        } else {
            $('#dbServerPortTest').append('<font class="bad">Could not get info. Something went wrong!</font>');
        }
    })
}
