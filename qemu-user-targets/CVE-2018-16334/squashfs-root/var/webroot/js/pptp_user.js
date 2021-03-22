var deviceInfo;

var pageview = R.pageView({ //页面初始化
    init: function () {
        getPptpUserList();
    }
});

var pageModel = R.pageModel({});

/********online list****************/
var pptpUserView = R.moduleView({
    init: function () {}
})
var moduleModel = R.moduleModel({});

//模块注册
R.module("onlineUserList", pptpUserView, moduleModel);

function getPptpUserList() {
    $.getJSON("goform/getPptpOnlineClient?" + Math.random(), initUserList);
    setTimeout(function () {
        getPptpUserList();
    }, 5000);
}

function initUserList(obj) {
    var i = 0,
        tableData = obj.clientList,
        len = tableData.length,
        str = "",
        timeStr = "";

    if (len != 0) {
        for (i = 0; i < len; i++) {
            var timeNow = tableData[i].onlineTime,
                dayNum = parseInt(timeNow / 1440, 10),
                hourNum = parseInt((timeNow - (1440 * dayNum)) / 60, 10),
                minNum = parseInt((timeNow - (1440 * dayNum) - (60 * hourNum)), 10);

            // timeStr = dayNum + _("day(s)") + hourNum + _("hour(s)") + minNum + _("min(s)");

            timeStr = formatSeconds(tableData[i].onlineTime);

            str += "<tr class='tr-row'><td class='text-fixed' title='" + tableData[i].username + "'>" + tableData[i].username + "</td>" +
                "<td>" + tableData[i].dialIP + "</td>" +
                "<td>" + tableData[i].clientIP + "</td>" +
                "<td>" + timeStr + "</td>" +
                "</tr>";
        }
    } else {
        str = "<tr><td colspan=4>" + _("The online users list is empty.") + "</td></tr>";
    }

    $("#onlineUserList").html(str);

    top.initIframeHeight();
    initTableHeight();
}

window.onload = function () {
    deviceInfo = R.page(pageview, pageModel);
};