$(function () {

//check license file and disable menu items if license not agreed to
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxLicenseCheck.php", function (data) {

        if (data == 0) { //license not accepted

            document.getElementById("dbinstall_a").href = '#';
            document.getElementById("dbinstall_a").style.cursor = "default"

            document.getElementById("finalcheck_a").href = '#';
            document.getElementById("finalcheck_a").style.cursor = "default"
        } else if (data == 1) {


            document.getElementById("dbinstall_a").href = 'dbinstall.php';
            document.getElementById("dbinstall_a").style.cursor = "pointer"

            document.getElementById("finalcheck_a").href = 'finalcheck.php';
            document.getElementById("finalcheck_a").style.cursor = "pointer"
        }

    })

    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxGetPhpVer.php", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#phpVersion').html(phpVersion);

        } else {
            $('#phpVersion').append('<font color="#F7492E">Cannot get PHP version or is not installed</font>');
        }
    })
    
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxGetMysqlVer.php", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#mysqlVersion').html(phpVersion);

        } else {
            $('#mysqlVersion').append('<font color="#F7492E">Cannot get MYSQL version or is not installed</font>');
        }
    })
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxGetHttpdVer.php", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#httpdVersion').html(phpVersion);

        } else {
            $('#httpdVersion').append('<font color="#F7492E">Cannot get Apache version or is not installed</font>');
        }
    })

    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxPhpCliCheck.php", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#phpCliCheck').html(phpVersion);

        } else {
            $('#phpCliCheck').append('<font color="#F7492E">Cannot get PHP CLI Status or is not installed</font>');
        }
    })

});