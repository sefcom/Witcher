var G = {},
	initObj = null;
ipAllAllow = false;


var advRemoteInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		$("#submit").on("click", function () {
			G.validate.checkAll();
		});
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetRemoteWebCfg",
	setUrl: "goform/SetRemoteWebCfg",
	translateData: function (data) {
		var newData = {};
		newData.remoteWeb = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initRemoteEvent,
	checkData: function () {
		var remoteIp;
		if ($("#remoteWebEn").val() == "1") {
			remoteIp = $("#remoteIp").val();
			//SE决策：不需要判断与其他功能关系，只需满足是IP地址即可
			/*if (remoteIp != "0.0.0.0" && checkIpInSameSegment(initObj.lanIp, initObj.lanMask, remoteIp, initObj.lanMask)) {
				return _("%s and %s (%s) must not be in the same network segment.", [_("Remote IP Address"), _("LAN IP Address"), initObj.lanIp]);
			}*/

			// 决策： 访客网络网段冲突有后台处理
			/*if (initObj.wlGuestIp && checkIpInSameSegment(initObj.wlGuestIp, "255.255.255.0", subObj.remoteIp, "255.255.255.0")) {
				showErrMsg("msg-err", _("%s and %s (%s) must not be in the same network segment.", [_("Remote IP Address"),_("Guest Network IP"),initObj.wlGuestIp]));
				return false;
			}*/
		}
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		if ($("#remoteWebEn").val() == 1) {
			subObj = {
				"remoteWebEn": $("#remoteWebEn").val(),
				"remoteIp": $("#remoteIp").val(),
				"remotePort": parseInt($("#remotePort").val(), 10)
			};
		} else {
			subObj = {
				"remoteWebEn": $("#remoteWebEn").val(),
				"remoteIp": initObj.remoteIp,
				"remotePort": initObj.remotePort
			};
		}
		data = objTostring(subObj);
		return data;
	}
});

//模块注册
R.module("remoteWeb", view, moduleModel);

function initRemoteEvent() {
	var pwdFlag = window.location.href.substr(window.location.href.indexOf('&') + 1);
	if (pwdFlag === "nopwd") {
		$('#nocontent').removeClass("none");
		$('#content').addClass("none");
	} else {
		$("#remoteWebEn").on("click", changeDmzEn);

		$.validate.valid.remoteIp = function (str) {
			if (str == "0.0.0.0") return;
			return $.validate.valid.ip.all(str);
		}
		$("#remotePort").inputCorrect("num");
		$("#remoteIp").inputCorrect("ip");
		checkData();
	}

	top.initIframeHeight();
}

function changeDmzEn() {
	var className = $("#remoteWebEn").attr("class");
	if (className == "btn-off") {
		if (+initObj.syspwdflag == 0) {
			//$('#content').addClass("none");
			$("#nocontent").removeClass("none");
			return false;
		}
		$("#remoteWebEn").attr("class", "btn-on");
		$("#remoteWebEn").val(1);
		$("#remote_set").removeClass("none");
	} else {
		$("#nocontent").addClass("none");
		$("#remoteWebEn").attr("class", "btn-off");
		$("#remoteWebEn").val(0);
		$("#remote_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			advRemoteInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});
}

function initValue(obj) {
	initObj = obj;
	if (obj.remoteWebEn == "1") {
		$("#remoteWebEn").attr("class", "btn-on");
		$("#remoteWebEn").val(1);
		$("#remote_set").removeClass("none");
	} else {
		$("#remoteWebEn").attr("class", "btn-off");
		$("#remoteWebEn").val(0);
		$("#remote_set").addClass("none");
	}

	$("#remoteIp").val(obj.remoteIp);
	$("#remotePort").val(obj.remotePort);
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
		top.sysInfo.initValue();
	}
}

window.onload = function () {
	advRemoteInfo = R.page(pageview, pageModel);
};