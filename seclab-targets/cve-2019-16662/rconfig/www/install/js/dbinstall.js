$(function () {

});

function getServerStatus() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxDbServerReachable.php?server=" + server + "&port=" + port + "", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#dbServerPortTest').html(phpVersion);

        } else {
            $('#dbServerPortTest').append('<font color="#F7492E">Cannot get PHP version or is not installed</font>');
        }
    })
}

function getStatus() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;
    var dbName = document.getElementById('dbName').value;
    var dbUsername = document.getElementById('dbUsername').value;
    var dbPassword = document.getElementById('dbPassword').value;
    $.ajaxSetup({ cache: false });
    $.ajax({
        type: "POST",
        url: "lib/ajaxHandlers/ajaxDbTests.php",
        data: {server : server, port: port, dbName: dbName, dbUsername: dbUsername, dbPassword: dbPassword},
        cache: false,
        success: function(data){
            if ($.isEmptyObject(data) != true) {
                var result = JSON.parse(data);
                
                var connTest = result.connTest
                var credTest = result.credTest
                var dbTest = result.dbTest
                var siteUrl = result.siteUrl
                var installDir = result.installDir

                $('#dbServerPortTest').html(connTest);
                $('#dbNameTest').html(credTest);
                $('#dbCredTest').html(dbTest);

            } else {
                $('#dbServerPortTest').append('<font class="bad">Unable to test</font>');
            }
        }
      });
}

function installConfig() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;
    var dbName = document.getElementById('dbName').value;
    var dbUsername = document.getElementById('dbUsername').value;
    var dbPassword = document.getElementById('dbPassword').value;
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxDbInstall.php?server=" + server + "&port=" + port + "&dbName=" + dbName + "&dbUsername=" + dbUsername + "&dbPassword=" + dbPassword + "", function (data) {
        if ($.isEmptyObject(data) != true) {

            var error = data.error
            var success = data.success

            $('#msg').html(error);
            $('#msg').html(success);

        } else {
            $('#msg').append('<font class="bad">A problem occurred!/font>');
        }
    })
}