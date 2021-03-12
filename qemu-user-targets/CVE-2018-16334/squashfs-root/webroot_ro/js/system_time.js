var G = {};
var initObj = null;

var sysTimeInfo;
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
	getUrl: "goform/GetSysTimeCfg",
	setUrl: "goform/SetSysTimeCfg",
	translateData: function (data) {
		var newData = {};
		newData.sysTime = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: checkData
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data,
			subObj = {};
		subObj = {
			//"timeType": $("[name='timeType']:checked").val(),
			//"timePeriod": $("#timePeriod").val(initObj.timePeriod),
			//"ntpServer": $("#ntpServer").val(initObj.ntpServer),
			"timePeriod": initObj.timePeriod,
			"ntpServer": initObj.ntpServer,
			"timeZone": $("#timeZone").val()
				//"time": $("#time").val()
		};
		data = objTostring(subObj);
		return data;
	}
});

//模块注册
R.module("sysTime", view, moduleModel);

function checkData() {
	G.validate = $.validate({
		custom: function () {

			/*if ($("#ntpServer").val() == "") {
				$("#ntpServer").focus();
				return _("Please enter a valid NTP server IP address.");
			}
			if (!(/^[ -~]+$/g).test($("#ntpServer").val())) {
				$("#ntpServer").focus();
				return _("Please enter a valid NTP server IP address.");
			}*/
		},
		success: function () {
			sysTimeInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				$("#msg-err").html(msg);
			}
			return;
		}
	});
}

function initValue(obj) {
	initObj = obj;
	//$("[name='timeType'][value='" + obj.timeType + "']")[0].checked = true;
	$("#timeZone").val(obj.timeZone);
	$("#sysTime").text(obj.time);
	if (obj.isSyncInternetTime == "true") {
		$("#syncInternetTips").text(_("(synchronized with internet time)"));
	} else {
		$("#syncInternetTips").text(_("(unsynchronized with internet time)"));
	}
	/*$("#ntpServer").val(obj.ntpServer);
	$("#timePeriod").val(obj.timePeriod);*/
	top.initIframeHeight();
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;

	top.showSaveMsg(num);
	if (num == 0) {
		top.advInfo.initValue();
	}
}


window.onload = function () {
	sysTimeInfo = R.page(pageview, pageModel);
};