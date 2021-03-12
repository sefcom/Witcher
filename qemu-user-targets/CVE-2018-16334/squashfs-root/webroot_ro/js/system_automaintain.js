var G = {};
var sysMaintainInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");

		$("#submit").on("click", function () {
			G.validate.checkAll();
		});
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetSysAutoRebbotCfg",
	setUrl: "goform/SetSysAutoRebbotCfg",
	translateData: function (data) {
		var newData = {};
		newData.autoReboot = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: function () {
		initHtml();
		$("#autoRebootEn").on("click", changeRebootEn);
		checkData();
	}
});
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data = "autoRebootEn=" + $("#autoRebootEn").val();
		data += "&delayRebootEn=" + ($("#delayRebootEn")[0].checked ? "true" : "false");
		data += "&rebootTime=" + $("#rebootHour").val() + ":" + $("#rebootMin").val();
		return data;
	}
});

//模块注册
R.module("autoReboot", view, moduleModel);


function translateTime(index) {
	return (index + 100 + "").slice(1);
}

function initHtml() {
	var hourStr = "",
		minStr = "",
		i = 0;
	for (i = 0; i < 24; i++) {
		hourStr += "<option value='" + translateTime(i) + "'>" + translateTime(i) + "</option>"
	}
	$("#rebootHour").html(hourStr);

	for (i = 0; i < 60; i++) {
		if (i % 5 == 0) {
			minStr += "<option value='" + translateTime(i) + "'>" + translateTime(i) + "</option>";
		}
	}
	$("#rebootMin").html(minStr);
}

function changeRebootEn() {
	var className = $("#autoRebootEn").attr("class");
	if (className == "btn-off") {
		$("#autoRebootEn").attr("class", "btn-on");
		$("#autoRebootEn").val(1);

		$("#rebootSetWrap").removeClass("none");
	} else {
		$("#autoRebootEn").attr("class", "btn-off");
		$("#autoRebootEn").val(0);

		$("#rebootSetWrap").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			sysMaintainInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});
}

function initValue(obj) {
	(obj.timeUp == "1" ? $("#timeUpTip").addClass("none") : $("#timeUpTip").removeClass("none"));
	if (obj.autoRebootEn == "1") {
		$("#autoRebootEn").attr("class", "btn-on");
		$("#autoRebootEn").val(1);

		$("#rebootSetWrap").removeClass("none");
	} else {
		$("#autoRebootEn").attr("class", "btn-off");
		$("#autoRebootEn").val(0);

		$("#rebootSetWrap").addClass("none");
	}

	if (obj.delayRebootEn == "true") {
		$("#delayRebootEn").prop("checked", true);
	} else {
		$("#delayRebootEn").prop("checked", false);
	}

	$("#rebootHour").val(obj.rebootTime.split(":")[0]);
	$("#rebootMin").val(obj.rebootTime.split(":")[1]);
	top.initIframeHeight();
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
	if (num == 0) {
		top.sysInfo.initValue();
	}

}

window.onload = function () {
	sysMaintainInfo = R.page(pageview, pageModel);
};