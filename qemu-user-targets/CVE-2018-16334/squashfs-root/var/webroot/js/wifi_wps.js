var wrlWpsInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/WifiWpsGet",
	setUrl: "goform/WifiWpsSet",
	translateData: function (data) {
		var newData = {};
		newData.wrlWps = data;
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
		return "wpsEn=" + $("#wpsEn").val();
	}
});

//模块注册
R.module("wrlWps", view, moduleModel);

function initEvent() {
	$("#wpsSubmit").on("click", function () {
		if (!this.disabled)
			$.post("goform/WifiWpsStart", "action=wps", callback);
	});
	$("#wpsEn").on("click", function () {
		if (initObj.wl_mode != "ap" || initObj.wl_en == "0") {
			return;
		}
		changeWpsEn();
		$("#wpsMethod").addClass("none");
		wrlWpsInfo.submit();
		if ($("#wpsEn").val() == "1") {
			$("#waitingTip").html(_("Enabling WPS...")).removeClass("none");
		} else {
			$("#waitingTip").html(_("Disabling WPS...")).removeClass("none");
		}
	});
}

function changeWpsEn() {
	if ($("#wpsEn")[0].className == "btn-off") {
		$("#wpsEn").attr("class", "btn-on");
		$("#wpsEn").val(1);
	} else {
		$("#wpsEn").attr("class", "btn-off");
		$("#wpsEn").val(0);
	}
	top.initIframeHeight();
}

function initValue(obj) {
	initObj = obj;
	$("#pinCode").html(obj.pinCode);
	$("#waitingTip").html(" ").addClass("none");
	if (obj.wl_mode != "ap" || obj.wl_en == "0") {
		if (obj.wl_mode != "ap")
			showErrMsg("msg-err", _("Please disable Wireless Repeating on the WiFi Settings page first."), true);
		if (obj.wl_en == "0")
			showErrMsg("msg-err", _("The WiFi function is disabled. Please enable it first."), true);
		$("#wpsSubmit")[0].disabled = true;
		//$("#submit")[0].disabled = true;
	}
	$("#wpsEn").attr("class", (obj.wpsEn == "1" ? "btn-off" : "btn-on"));
	changeWpsEn();
	if (obj.wpsEn == "1") {
		$("#wpsMethod").removeClass("none");
	} else {
		$("#wpsMethod").addClass("none");
	}
	top.initIframeHeight();
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


window.onload = function () {
	wrlWpsInfo = R.page(pageview, pageModel);
};