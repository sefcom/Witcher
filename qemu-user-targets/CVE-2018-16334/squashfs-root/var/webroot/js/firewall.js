var firewallInfo;
var pageview = R.pageView({ //页面初始化
    init: function () {
        top.loginOut();
        top.$(".main-dailog").removeClass("none");
        top.$(".save-msg").addClass("none");
        $("#submit").on("click", function () {
            firewallInfo.submit();
        });
    }
});
var pageModel = R.pageModel({
    getUrl: "goform/GetFirewallCfg",
    setUrl: "goform/SetFirewallCfg",
    translateData: function (data) {
        var newData = {};
        newData.firewall = data;
        return newData;
    },
    afterSubmit: callback
});

/************************/
var view = R.moduleView({
    initEvent: initFirewallEvent
})
var moduleModel = R.moduleModel({
    initData: initValue,
    getSubmitData: function () {
        var data = "firewallEn=" + (($("#icmpEn").attr("class") === "btn-on") ? 1 : 0) +
            (($("#tcpEn").attr("class") === "btn-on") ? 1 : 0) +
            (($("#udpEn").attr("class") === "btn-on") ? 1 : 0) +
            (($("#wanEn").attr("class") === "btn-on") ? 1 : 0);
        return data;
    }
});

//模块注册
R.module("firewall", view, moduleModel);

function initFirewallEvent() {

    $("#firewall").on('click', '.btn-on', function () {
        $(this).attr("class", "btn-off");
    });

    $("#firewall").on('click', '.btn-off', function () {
        $(this).attr("class", "btn-on");
    });
}

function initValue(obj) {
    top.$(".main-dailog").removeClass("none");
    top.$("iframe").removeClass("none");
    top.$(".loadding-page").addClass("none");

    $("#icmpEn").attr("class", (obj["firewallEn"].charAt(0) === "1") ? "btn-on" : "btn-off");
    $("#tcpEn").attr("class", (obj["firewallEn"].charAt(1) === "1") ? "btn-on" : "btn-off");
    $("#udpEn").attr("class", (obj["firewallEn"].charAt(2) === "1") ? "btn-on" : "btn-off");
    $("#wanEn").attr("class", (obj["firewallEn"].charAt(3) === "1") ? "btn-on" : "btn-off");
    top.initIframeHeight();
}

function callback(str) {
    if (!top.isTimeout(str)) {
        return;
    }
    var num = $.parseJSON(str).errCode;
    top.showSaveMsg(num);
    if (num == 0) {
        //getValue();
        top.advInfo.initValue();
    }
}

window.onload = function () {
    firewallInfo = R.page(pageview, pageModel);
};