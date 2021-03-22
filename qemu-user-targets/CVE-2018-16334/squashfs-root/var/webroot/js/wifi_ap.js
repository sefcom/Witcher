var wrlAPInfo,
    initObj = {};
var pageview = R.pageView({ //页面初始化
    init: function () {}
});

var pageModel = R.pageModel({
    getUrl: "goform/getApModeCfg",
    setUrl: "goform/setApModeCfg",
    translateData: function (data) {
        var newData = {};
        newData.wrlAP = data;
        return newData;
    },
    afterSubmit: callback
});

/************************/
var view = R.moduleView({
    initEvent: initEvent
});

var moduleModel = R.moduleModel({
    initData: initValue,
    getSubmitData: function () {
        return "apModeEn=" + $("#apSwitch").val();
    }
});

//模块注册
R.module("wrlAP", view, moduleModel);

function initEvent() {
    $("#apSwitch").on("click", function () {
        if ($(this).hasClass("btn-on")) {
            initHtml("false");
        } else {
            initHtml("true");
        }
    });

    $("#submit").on("click", function () {
        if ($("#apSwitch").val() != initObj.apModeEn) {
            if (!confirm(_("Your settings will take effect after the system reboots. Do you want to reboot the system?"))) {
                return;
            }
        }
        wrlAPInfo.submit();
    });
}

function onlineProgress() {
    var rebootTimer = null,
        percent = 0;
    $("#apModeWrap").addClass("none");
    $("#status_progress").removeClass("none");
    $("#progress_bar").css("width", 0);
    $("#progress_num .progressNum").text("0%");
    $("#progressMsg").html(_("Rebooting... Please wait."));

    function rebootTime(percent) {
        $("#progress_bar").css("width", percent + "%");
        $("#progress_num .progressNum").text(percent + "%");
        $("#progress_num").addClass("txt-center");
        rebootTimer = setTimeout(function () {
            rebootTime(percent);
        }, 750)

        if (percent >= 100) {
            clearTimeout(rebootTimer);
            top.jumpTo(window.location.host);
            return;
        }
        percent++;
    }

    rebootTime(0);
}

function callback(str) {
    if (!top.isTimeout(str)) {
        return;
    }
    var num = $.parseJSON(str).errCode;

    if (num == 0) {
        if ($("#apSwitch").val() != initObj.apModeEn) {
            onlineProgress();
            return;
        }
    }

    top.showSaveMsg(num);
}

function initHtml(str) {
    if (str == "true") {
        $("#apSwitch").attr("class", "btn-on");
        $("#apSwitch").val("true");

        $(".text-muted").eq(1).removeClass("none");
        $(".text-muted").eq(0).addClass("none");
    } else {
        $("#apSwitch").attr("class", "btn-off");
        $("#apSwitch").val("false");

        $(".text-muted").eq(0).removeClass("none");
        $(".text-muted").eq(1).addClass("none");
    }
}

function initValue(obj) {
    initHtml(obj.apModeEn);
    initObj = obj;
}

window.onload = function () {
    wrlAPInfo = R.page(pageview, pageModel);
};