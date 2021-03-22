var G = {},
	initObj = null;

var printerInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
		$("#submit").on("click", function () {
			//G.validate.checkAll();
		});
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetPrinterCfg",
	setUrl: "goform/SetPrinterCfg",
	translateData: function (data) {
		var newData = {};
		newData.printer = data;
		return newData;
	},
	afterSubmit: callback
});


/************************/
var view = R.moduleView({
	initEvent: function () {
		$("#printerEn").on("click", changePrinterEn);
		checkData();
		top.initIframeHeight();
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data = "printerEn=" + $("#printerEn").val();
		return data;
	}
});

//模块注册
R.module("printer", view, moduleModel);

function changePrinterEn() {
	var className = $("#printerEn").attr("class");
	if (className == "btn-off") {
		$("#printerEn").attr("class", "btn-on");
		$("#printerEn").val(1);
		$("#printerWrap").removeClass("none");
	} else {
		$("#printerEn").attr("class", "btn-off");
		$("#printerEn").val(0);
		$("#printerWrap").addClass("none");
	}
	printerInfo.submit();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			printerInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});
}

function initValue(obj) {

	if (obj.printerEn == "1") {
		$("#printerEn").attr("class", "btn-on");
		$("#printerEn").val(1);
		$("#printerWrap").removeClass("none");
	} else {
		$("#printerEn").attr("class", "btn-off");
		$("#printerEn").val(0);
		$("#printerWrap").addClass("none");
	}

	$("#printerName").text(obj.printerName);

	if (typeof obj.connectPrinter != "undefined" && obj.connectPrinter == "1") {
		$("#printer_notice").removeClass("none");
	} else {
		$("#printer_notice").addClass("none");
	}

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
		top.usbInfo.initValue();
	}
}

window.onload = function () {
	printerInfo = R.page(pageview, pageModel);
};