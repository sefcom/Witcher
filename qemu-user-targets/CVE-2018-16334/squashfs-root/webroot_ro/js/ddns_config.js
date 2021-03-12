var G = {},
	initObj = null;


var ddnsInfo;
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
	getUrl: "goform/GetDDNSCfg",
	setUrl: "goform/SetDDNSCfg",
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
		var data,
			subObj = {};

		if ($("#ddnsEn").val() == 1) {
			subObj = {
				"ddnsEn": $("#ddnsEn").val(),
				"serverName": $("#serverName").val(),
				"ddnsUser": $("#ddnsUser").val(),
				"ddnsPwd": $("#ddnsPwd").val(),
				"ddnsDomain": $("#ddnsDomain").val()
			}
		} else {
			subObj = {
				"ddnsEn": $("#ddnsEn").val(),
				"serverName": initObj.serverName,
				"ddnsUser": initObj.ddnsUser,
				"ddnsPwd": initObj.ddnsPwd,
				"ddnsDomain": initObj.ddnsDomain
			}
		}

		data = objTostring(subObj);
		return data;
	}
});

//模块注册
R.module("wrlWps", view, moduleModel);

function initEvent() {
	$("#ddnsEn").on("click", changeDdnsEn);
	$("#btnLink").on("click", function () {
		var netAddress = $("#serverName").val();
		window.open("http://" + netAddress, "");
	});
	$("#serverName").on("change", changeDdnsType);
	checkData();
	top.initIframeHeight();
}

function changeDdnsType() {
	if (($("#serverName").val() === "88ip.cn") || ($("#serverName").val() === "oray.com")) {
		$("#domainContainer").addClass("none");
	} else {
		$("#domainContainer").removeClass("none");
	}
}

function changeDdnsEn() {
	var className = $("#ddnsEn").attr("class");
	if (className == "btn-off") {
		$("#ddnsEn").attr("class", "btn-on");
		$("#ddnsEn").val(1);
		$("#ddns_set").removeClass("none");
	} else {
		$("#ddnsEn").attr("class", "btn-off");
		$("#ddnsEn").val(0);
		$("#ddns_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			ddnsInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});
}

var statusMsg = {
	"0": _("Disconnected"),
	"1": _("Connected")
};

function initValue(obj) {
	initObj = obj;
	if (obj.ddnsEn == "1") {
		$("#ddnsEn").attr("class", "btn-on");
		$("#ddnsEn").val(1);
		$("#ddns_set").removeClass("none");
	} else {
		$("#ddnsEn").attr("class", "btn-off");
		$("#ddnsEn").val(0);
		$("#ddns_set").addClass("none");
	}
	$("#serverName").val(obj.serverName);
	$("#ddnsUser").val(obj.ddnsUser);
	$("#ddnsPwd").val(obj.ddnsPwd);
	$("#ddnsDomain").val(obj.ddnsDomain);
	if (obj.ddnsStatus == "1") {
		$("#ddnsStatus").attr("class", "text-success");
	} else {
		$("#ddnsStatus").attr("class", "text-error");
	}
	$("#ddnsStatus").html(statusMsg[obj.ddnsStatus]);

	$('#ddnsPwd').initPassword(_(""), false, false);
	changeDdnsType();
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
	ddnsInfo = R.page(pageview, pageModel);
};