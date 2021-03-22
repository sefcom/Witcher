var G = {},
	initObj = {};

var statusUsbInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");

	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetUsbCfg",
	setUrl: "",
	translateData: function (data) {
		var newData = {};
		newData.usbStatus = data;
		return newData;
	}

});

/************************/
var view = R.moduleView({
	initEvent: initEvent
})
var moduleModel = R.moduleModel({
	initData: initValue
});

//模块注册
R.module("usbStatus", view, moduleModel);

function initEvent() {
	$("#gotoUsb").on("click", function () {
		top.closeIframe();
		top.mainPageLogic.changeMenu("system-status", "usb-setting");
	});

	$("#usbWrap").delegate(".btn-unlink", "click", unLinkUsb);
}

function unLinkUsb() {
	var devName = $(this).data("target");
	$.GetSetData.setData("goform/setUsbUnload", "deviceName=" + encodeURIComponent(devName), unLinkCallback);
}

function unLinkCallback(str) {
	var num = $.parseJSON(str).errCode;
	showErrMsg("msg-err", _("Unmounted successfully"));
	statusUsbInfo.model.update();
}

var refreshTimer = null;

function initValue(obj) {
	var devList = obj.usbList,
		len = devList.length >= 2 ? 2 : devList.length,
		i = 0,
		j = 0,
		diskList,
		$usbElem,
		usePercent,
		domStr,
		diskLen;

	var tplStr = '<div class="usb-space-content usb-target">' +
		'<div class="usb-space-row"></div>' +
		'<div>' +
		'<div class="use-space-percent"></div>' +
		'<div class="no-use-space-percent"></div>' +
		'</div>' +
		'<div class="usb-space-row text-muted usb-span-tips"></div>' +
		'</div>';

	initObj = obj;
	$("#usbWrap1").addClass("none");
	$("#usbWrap2").addClass("none");
	clearTimeout(refreshTimer);
	refreshTimer = setTimeout(function () {
		statusUsbInfo.model.update();
	}, 2000);
	if (len == 0) {
		$("#usbWrap").addClass("none");
		$("#noUsbDevice").removeClass("none");
		return;
	}

	$("#usbWrap").removeClass("none");
	$("#noUsbDevice").addClass("none");

	for (i = 0; i < len; i++) {
		$usbElem = $("#usbWrap" + (i + 1));
		$usbElem.removeClass("none");
		$("#usbDeviceName" + (i + 1)).text(devList[i].devName);
		$("#usbWrap .btn-unlink").eq(i).data("target", devList[i].devId);

		//分区
		diskList = devList[i].diskList;
		diskLen = diskList.length;
		domStr = "";
		$usbElem.find(".content-gray").html('');
		for (j = 0; j < diskLen; j++) {
			domStr = tplStr;
			$usbElem.find(".content-gray").append(domStr);
			$usbElem.find(".usb-target .usb-space-row").text(diskList[j].diskName);
			usePercent = (1 - Number(diskList[j].memoryUnUse, 10) / Number(diskList[j].memoryTotal, 10));
			usePercent = (usePercent * 100).toFixed(1) + "%";
			$usbElem.find(".usb-target .use-space-percent").css("width", usePercent);
			$usbElem.find(".usb-target .no-use-space-percent").css("width", (100 - parseFloat(usePercent, 10)) + "%");

			//处理剩余容量  小于G时显示为MB
			$usbElem.find(".usb-target .usb-span-tips").html(_("Available: %s; Total: %s", [translateCapacity(diskList[j].memoryUnUse), translateCapacity(diskList[j].memoryTotal)]));
			$usbElem.find(".usb-target").removeClass("usb-target");
		}
	}


	top.initIframeHeight();
}

window.onload = function () {
	statusUsbInfo = R.page(pageview, pageModel);
};