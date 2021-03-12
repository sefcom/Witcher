var G = {},
	initObj = null;

var sleepInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/PowerSaveGet",
	setUrl: "goform/PowerSaveSet",
	translateData: function (data) {
		var newData = {};
		newData.sleep = data;
		return newData;
	},
	beforeSubmit: function () {
		var powerSavingEn = $("#powerSavingEn").val(),
			startHour = $("#startHour").val(),
			startMin = $("#startMin").val(),
			endHour = $("#endHour").val(),
			endMin = $("#endMin").val(),
			time = startHour + ":" + startMin + "-" + endHour + ":" + endMin;

		if (powerSavingEn == "1") {

			//判断时间是否与智能led冲突
			if (initObj.ledTime) {
				var ledTimeStart = parseInt(initObj.ledTime.split("-")[0].replace(/[^\d]/g, ""), 10),
					ledTimeEnd = parseInt(initObj.ledTime.split("-")[1].replace(/[^\d]/g, ""), 10);
				if (isTimeOverlaping(ledTimeStart, ledTimeEnd, parseInt(startHour + "" + startMin, 10), parseInt(endHour + "" + endMin, 10))) {
					//重叠
					if (!window.confirm(_("The effective period of Sleeping Mode (%s) overlaps that of LED Control (%s). During the overlap period, the LED Control function will be ineffective. Do you want to save the settings?", [time, initObj.ledTime]))) {
						return false;
					}
				}
			}

			//判断时间是否与wifi开关冲突
			if (initObj.wifiTime) {
				var wifiTimeStart = parseInt(initObj.wifiTime.split("-")[0].replace(/[^\d]/g, ""), 10),
					wifiTimeEnd = parseInt(initObj.wifiTime.split("-")[1].replace(/[^\d]/g, ""), 10);
				if (isTimeOverlaping(wifiTimeStart, wifiTimeEnd, parseInt(startHour + "" + startMin, 10), parseInt(endHour + "" + endMin, 10))) {
					//重叠
					if (!window.confirm(_("The effective period of Sleeping Mode (%s) overlaps that of WiFi Schedule (%s).  During the overlap period, the WiFi Schedule function will be ineffective. Do you want to save the settings?", [time, initObj.wifiTime]))) {
						return false;
					}
				}
			}
		}
		return true;
	},
	afterSubmit: callback
});


/************************/
var view = R.moduleView({
	initEvent: initEvent,
	initHtml: initHtml,
	checkData: checkData
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var subObj = {},
			startHour = $("#startHour").val(),
			startMin = $("#startMin").val(),
			endHour = $("#endHour").val(),
			endMin = $("#endMin").val(),
			time = startHour + ":" + startMin + "-" + endHour + ":" + endMin,
			subStr;

		subObj = {
			"powerSavingEn": $("#powerSavingEn").val(),
			"time": time,
			"ledCloseType": $("[name='ledCloseType']:checked").val(),
			"powerSaveDelay": $("#power_save_delay")[0].checked ? "1" : "0"
		};
		subStr = objTostring(subObj);
		return subStr;
	}
});

//模块注册
R.module("sleep", view, moduleModel);

function initHtml() {
	var obj = getTimeString();
	$("#startHour").html(obj.hour);
	$("#startMin").html(obj.minute);
	$("#endHour").html(obj.hour);
	$("#endMin").html(obj.minute);

	if (top.CONFIG_USB_MODULES == "n") {
		$("#powerTipsMsg").html(_("When it is enabled, indicators are turned off and the WiFi network enter the Sleeping mode to reduce power consumption."));
	}
}

function initEvent() {
	$("#power_submit").on("click", function () {
		sleepInfo.submit();
	});
	$("#powerSavingEn").on("click", function () {
		if (initObj.wl_mode == "ap")
			changePowerEn.call(this);
	});
	$("#startHour,#startMin,#endHour,#endMin").on("change", changeTimeSet);

	top.initIframeHeight();
}

function changePowerEn() {
	var className = $(this).attr("class");
	if (className == "btn-off") {
		$(this).attr("class", "btn-on");
		$("#power_time_set").removeClass("none");
		if (top.CONFIG_LED_CLOSE_TYPE == "y") {
			$("#power_save_led_set").removeClass("none");
		}
		$("#power_save_delay_set").removeClass("none");
		
		$("#power_notice").removeClass("none");
		$("#powerCloseTips").addClass("none");
		$(this).val(1);
	} else {
		$(this).attr("class", "btn-off");
		$("#power_time_set").addClass("none");
		$("#power_save_delay_set").addClass("none");
		$("#power_save_led_set").addClass("none");
		$("#power_notice").addClass("none");
		$("#powerCloseTips").removeClass("none");
		$(this).val(0);
	}
}

function changeTimeSet() {
	var startTimeMin = parseInt($("#startHour").val() * 60) + parseInt($("#startMin").val()),
		endTimeMin = parseInt($("#endHour").val() * 60) + parseInt($("#endMin").val()),
		totalTime = 0;

	if (startTimeMin > endTimeMin) {
		totalTime = 24 * 60 - startTimeMin + endTimeMin;
	} else {
		totalTime = endTimeMin - startTimeMin;
	}
	return totalTime; //return total min;
}

function checkData() {

	var subData,
		dataObj,
		subObj,
		callback,
		powerSavingEn = $("#powerSavingEn").val(),
		startHour = $("#startHour").val(),
		startMin = $("#startMin").val(),
		endHour = $("#endHour").val(),
		endMin = $("#endMin").val(),
		time = startHour + ":" + startMin + "-" + endHour + ":" + endMin;

	if (powerSavingEn == "1") {
		if (startHour == endHour && startMin == endMin) {
			return (_("The start time and end time must not be the same."));
		}
	}
}

function initValue(obj) {
	initObj = obj;
	(obj.timeUp == "1" ? $("#timeUpTip").addClass("none") : $("#timeUpTip").removeClass("none"));
	inputValue(obj);
	$("#powerSavingEn").attr("class", (obj.powerSavingEn == "1" ? "btn-off" : "btn-on"));
	changePowerEn.call($("#powerSavingEn")[0]);

	$("#power_save_delay")[0].checked = (obj.powerSaveDelay == "1" ? true : false);

	$("[name='ledCloseType'][value='" + obj.ledCloseType + "']")[0].checked = true;

	var time = obj.time;

	$("#startHour").val(time.split("-")[0].split(":")[0]);
	$("#startMin").val(time.split("-")[0].split(":")[1]);
	$("#endHour").val(time.split("-")[1].split(":")[0]);
	$("#endMin").val(time.split("-")[1].split(":")[1]);

	changeTimeSet();

	if (obj.wl_mode != "ap") {
		showErrMsg("msg-err", _("This function is not available if Wireless Repeating is enabled."), true);
		$("#timeUpTip").addClass("none");
		$("#power_submit")[0].disabled = true;
	} else {
		showErrMsg("msg-err", "", true);
		$("#power_submit")[0].disabled = false;
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
		top.advInfo.initValue();
	}
}

window.onload = function () {
	sleepInfo = R.page(pageview, pageModel);
};