$(document).ready(function () {
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxLicenseCheck.php", function (data) {

        if (data == 0) { //license not accepted

            document.getElementById("acceptLicenseChkBox").checked = false

            document.getElementById("next_a").href = '#';
            document.getElementById("next_a").style.cursor = "default"

            document.getElementById("dbinstall_a").href = '#';
            document.getElementById("dbinstall_a").style.cursor = "default"

            document.getElementById("finalcheck_a").href = '#';
            document.getElementById("finalcheck_a").style.cursor = "default"
        } else if (data == 1) {

            document.getElementById("acceptLicenseChkBox").checked = true

            document.getElementById("next_a").href = 'dbinstall.php';
            document.getElementById("next_a").style.cursor = "pointer"

            document.getElementById("dbinstall_a").href = 'dbinstall.php';
            document.getElementById("dbinstall_a").style.cursor = "pointer"

            document.getElementById("finalcheck_a").href = 'finalcheck.php';
            document.getElementById("finalcheck_a").style.cursor = "pointer"
        }

    })



});

function acceptLicense() {
    if (document.getElementById("acceptLicenseChkBox").checked = true) {
        id = 1;
    }
    $.ajaxSetup({ cache: false });
    $.getJSON("lib/ajaxHandlers/ajaxLicenseUpdate.php?id=" + id, function (data) {

        if (data == "success") {

            document.getElementById("next_a").href = 'dbinstall.php';
            document.getElementById("next_a").style.cursor = "pointer"

            document.getElementById("dbinstall_a").href = 'dbinstall.php';
            document.getElementById("dbinstall_a").style.cursor = "pointer"

            document.getElementById("finalcheck_a").href = 'finalcheck.php';
            document.getElementById("finalcheck_a").style.cursor = "pointer"

        }

    })


}
