var G = {},
	initObj = null,
	pagination = -null;

var sysLogInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetSySLogCfg",
	setUrl: "",
	translateData: function (data) {
		var newData = {};
		newData.syslog = data;
		return newData;
	}
});

/************************/
var view = R.moduleView({
	initEvent: function () {
		$("#export").on("click", exportLog);
	}
})
var moduleModel = R.moduleModel({
	initData: getValue,
	getSubmitData: function () {
		return "wpsEn=" + $("#wpsEn").val();
	}
});

//模块注册
R.module("syslog", view, moduleModel);

function exportLog() {
	//导出日志时大概需要5秒左右，与SE确认按钮禁用5s
	$("#export").attr("disabled", true);
	setTimeout(function () {
		$("#export").removeAttr("disabled");
	}, 5000);
	window.location = "cgi-bin/DownloadLog/syslog.tar";
}

function getValue(obj) {
	initObj = obj;
	obj.sort(function (a, b) {
		if (parseInt(a.index, 10) < parseInt(b.index, 10)) {
			return 1;
		} else {
			return -1;
		}
	});
	$.each(obj, function (i, item) {
		item.index = i + 1;
	});

	pagination = new Pagination({
		pageEleWrapId: "logPagination",
		dataArr: initObj,
		pageItemCount: 10,
		getDataUrl: "",
		handle: initValue, //每一页数据用户的处理函数
		getAll: true,
		param: ""
	});

	if ($(".pagination:hidden").length) {
		$("#export").parent().css("margin-top", "20px");
	} else {
		$("#export").parent().css("margin-top", "-40px");
	}
	top.initIframeHeight();
}

function initValue(obj) {
	var len = obj.length,
		i = 0,
		str = "";
	for (i = 0; i < len; i++) {
		str += "<tr>";
		str += "<td>" + obj[i].index + "</td>";
		str += "<td>" + obj[i].time + "</td>";
		str += "<td>" + obj[i].type + "</td>";
		str += "<td class='sys-log-txt fixed' title='" + obj[i].log + "'>" + obj[i].log + "</td>";
		str += "</tr>";
	}
	$("#logBody").html(str).find(".sys-log-txt").each(function (i) {
		$(this).attr("title", obj[i].log);
	});
	top.initIframeHeight();
}

window.onload = function () {
	sysLogInfo = R.page(pageview, pageModel);
};