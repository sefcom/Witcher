var G = {};
var upnpInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetUpnpCfg",
	setUrl: "goform/SetUpnpCfg",
	translateData: function (data) {
		var newData = {};
		newData.upnp = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: function () {
		$("#upnpEn").on("click", changeUpnpEn);
		top.initIframeHeight();
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data = "upnpEn=" + $("#upnpEn").val();
		return data;
	}
});

//模块注册
R.module("upnp", view, moduleModel);

function changeUpnpEn() {
	var className = $("#upnpEn").attr("class");
	if (className == "btn-off") {
		$("#upnpEn").attr("class", "btn-on");
		$("#upnpEn").val(1);
		$("#upnpList").removeClass("none");
	} else {
		$("#upnpEn").attr("class", "btn-off");
		$("#upnpEn").val(0);
		$("#upnpList").addClass("none");
	}
	upnpInfo.submit();
	top.initIframeHeight();
}

function initValue(obj) {
	if (obj[0].upnpEn == "1") {
		$("#upnpEn").attr("class", "btn-on");
		$("#upnpEn").val(1);
		$("#upnpList").removeClass("none");
	} else {
		$("#upnpEn").attr("class", "btn-off");
		$("#upnpEn").val(0);
		$("#upnpList").addClass("none");
	}
	var str = "",
		len = obj.length,
		i = 1;
	for (i = 1; i < len; i++) {
		str += "<tr>";
		str += "<td>" + obj[i].remoteHost + "</td>";
		str += "<td>" + obj[i].outPort + "</td>";
		str += "<td>" + obj[i].host + "</td>";
		str += "<td>" + obj[i].inPort + "</td>";
		str += "<td>" + obj[i].protocol + "</td>";
		str += "</tr>";
	}

	$("#upnpBody").html(str);
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
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
	upnpInfo = R.page(pageview, pageModel);
};