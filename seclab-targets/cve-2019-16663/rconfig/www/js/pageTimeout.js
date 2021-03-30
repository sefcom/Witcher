/* global bootbox
 * Here is a simple script that handles mousemove and keypress events. If the time expires throw and alert, then after 1 monute logout the user.
 */
// get the timeoutSetting value from ajax script and load to var
var timeoutSetting = {};
$.ajax({
    url: "lib/ajaxHandlers/ajaxGetPageTimeout.php",
    async: true,
    dataType: 'json',
    success: function (data) {
        timeoutSetting = data;
    }
});
// if the timeout value is less than 120seconds then default the value to 120, as a minimum accepted timeout value
if (timeoutSetting <= 120) {
    var timeoutSetting = 120;
}
var IDLE_TIMEOUT = timeoutSetting; //seconds
var NOTICE_TIMEOUT = IDLE_TIMEOUT - 60; // set the notification time to 1 minute before idle timeout
var _idleSecondsCounter = 0;
document.onclick = function () {
    _idleSecondsCounter = 0;
};
document.onmousemove = function () {
    _idleSecondsCounter = 0;
};
document.onkeypress = function () {
    _idleSecondsCounter = 0;
};
window.setInterval(CheckIdleTime, 1000);

function CheckIdleTime() {
    _idleSecondsCounter++;
    var oPanel = document.getElementById("SecondsUntilExpire");
    if (oPanel) {
        oPanel.innerHTML = (IDLE_TIMEOUT - _idleSecondsCounter) + "";
    }
    // if the _idleSecondsCounter matches the NOTICE_TIMEOUT then show the dialog. The assumption is if the user clicks ok, them the _idleSecondsCounter resets to zero. 
    // if not then the script will hit the next 'if' and then log the user out using userprocess.php
    if (_idleSecondsCounter === NOTICE_TIMEOUT) {
        bootbox.dialog({
            message: "Due to inactivity, you will be logged out of rConfig in 1 minute!<br /> Click OK to continue working",
            title: "Notice!",
            buttons: {
                main: {
                    label: "Ok!",
                    className: "btn-primary"
                }
            }
        });
    }
    if (_idleSecondsCounter >= IDLE_TIMEOUT) {
        document.location.href = "lib/crud/userprocess.php";
    }
}

$(document).ready(function () {
    CheckIdleTime();
});