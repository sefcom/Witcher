var G = {},
	initObj = {};

var dmzInfo;
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
	getUrl: "goform/GetDMZCfg",
	setUrl: "goform/SetDMZCfg",
	translateData: function (data) {
		var newData = {};
		newData.dmz = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initEvent,
	checkData: function () {
		var dmzIp,
			dmzEn,
			data,
			hostIp;
		var rel = /^[0-9]{1,}$/,
			hostIp = parseInt($("#hostIp").val(), 10),
			dmzEn = $("#dmzEn").val(),
			dmzIp = (dmzEn == 1 ? $("#dmz_net").html() + hostIp : initObj.dmzIp),
			errMsg;
		if (dmzEn == 1) {
			errMsg = checkIsVoildIpMask(dmzIp, initObj.lanMask);
			if (errMsg) {
				return errMsg;
			}

			if (!checkIpInSameSegment(dmzIp, initObj.lanMask, initObj.lanIp, initObj.lanMask)) {
				return _("%s and %s (%s) must be in the same network segment.", [_("DMZ Host IP Address"), _("LAN IP Address"), initObj.lanIp]);
			}

			if (dmzIp == initObj.lanIp) {
				return _("DMZ Host IP Address cannot be the same as LAN IP Address.");
			}
		}
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var dmzIp,
			dmzEn,
			data,
			hostIp = parseInt($("#hostIp").val(), 10),
			dmzEn = $("#dmzEn").val(),
			dmzIp = (dmzEn == 1 ? $("#dmz_net").html() + hostIp : initObj.dmzIp);

		data = "dmzEn=" + dmzEn + "&dmzIp=" + dmzIp;
		return data;
	}
});

//模块注册
R.module("dmz", view, moduleModel);

function initEvent() {
	$("#dmzEn").on("click", changeDmzEn);
	checkData();
	$("#hostIp").inputCorrect("num");
}

function changeDmzEn() {
	var className = $("#dmzEn").attr("class");
	if (className == "btn-off") {
		$("#dmzEn").attr("class", "btn-on");
		$("#dmzEn").val(1);
		$("#dmz_set").removeClass("none");
	} else {
		$("#dmzEn").attr("class", "btn-off");
		$("#dmzEn").val(0);
		$("#dmz_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			dmzInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});
}

function initValue(obj) {
	initObj = obj;


	var net_arry = obj["dmzIp"].split(".");
	var lan_arry = obj["lanIp"].split(".");

	top.$(".main-dailog").removeClass("none");
	top.$("iframe").removeClass("none");
	top.$(".loadding-page").addClass("none");

	if (obj["dmzEn"] == "0") {
		$("#dmzEn").attr("class", "btn-off");
		$("#dmzEn").val(0);
		$("#dmz_set").addClass("none");
	} else {
		$("#dmzEn").attr("class", "btn-on");
		$("#dmzEn").val(1);
		$("#dmz_set").removeClass("none");
	}
	$("#dmz_net").html(lan_arry[0] + "." + lan_arry[1] + "." + lan_arry[2] + ".");
	if (obj["dmzIp"] != "") {
		$("#hostIp").val(net_arry[3]);
	} else {
		$("#hostIp").val("");
	}
	top.initIframeHeight();
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;

	if (num == 2) {
		//与lan口IP相同
		//top.$("#iframe-msg").removeClass("none");
		top.$("#iframe-msg").html(_("DMZ Host IP Address cannot be the same as LAN IP Address."));
		setTimeout(function () {
			top.$("#iframe-msg").html("");
		}, 800);
	} else {
		top.showSaveMsg(num);
		if (num == 0) {
			//getValue();
			top.advInfo.initValue();
		}
	}
}

window.onload = function () {
	dmzInfo = R.page(pageview, pageModel);
};