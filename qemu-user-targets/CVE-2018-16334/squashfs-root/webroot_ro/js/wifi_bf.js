var wrlBfInfo;
var pageview = R.pageView({ //页面初始化
    init: function () {
        top.loginOut();
        top.$(".main-dailog").removeClass("none");
        top.$(".save-msg").addClass("none");
    }
});
var pageModel = R.pageModel({
    getUrl: "goform/WifiBeamformingGet",
    setUrl: "goform/WifiBeamformingSet",
    translateData: function (data) {
        var newData = {};
        newData.wrlBf = data;
        return newData;
    },
    afterSubmit: callback
});

/************************/
var view = R.moduleView({
    initEvent: initEvent
})
var moduleModel = R.moduleModel({
    initData: initValue,
    getSubmitData: function () {
        return "beamformingEn=" + $("#bfEn").val();
    }
});

//模块注册
R.module("wrlBf", view, moduleModel);

function initEvent() {
    $("#bfEn").on("click", function () {
        if ($(this).hasClass("btn-on")) {
            $(this).attr("class", "btn-off");
            $(this).val(0)
        } else {
            $(this).attr("class", "btn-on");
            $(this).val(1)
        }

        wrlBfInfo.submit();
        if ($("#bfEn").val() === "1") {
            $("#waitingTip").html(_("Enabling beamforming...")).removeClass("none");
        } else {
            $("#waitingTip").html(_("Disabling beamforming...")).removeClass("none");
        }
    });
}

function callback(str) {
    if (!top.isTimeout(str)) {
        return;
    }
    var num = $.parseJSON(str).errCode;
    //top.showSaveMsg(num);
    if (num == 0) {
        top.wrlInfo.initValue();
        setTimeout(function () {
            pageModel.update();
            $("#waitingTip").html(" ").addClass("none");
        }, 2000);
    }
}

function initValue(obj) {
    $("#bfEn").attr("class", (obj.beamformingEn === "1") ? "btn-on" : "btn-off");
}

window.onload = function () {
    wrlBfInfo = R.page(pageview, pageModel);
};