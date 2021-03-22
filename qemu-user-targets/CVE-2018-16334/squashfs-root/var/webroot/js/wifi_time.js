var initObj = null;

var wifiTime;
var pageview = R.pageView({ //页面初始化
	init: initWifiTime
});

var pageModel = R.pageModel({
	getUrl: "goform/initSchedWifi",
	setUrl: "goform/openSchedWifi",
	translateData: function (data) {
		var newData = {};
		newData.wifiTime = data;
		return newData;
	},

	beforeSubmit: function () {
		/***************判断时间是否与智能省电冲突 add by zzc*****************/
		var schedWifiEnable = $("#schedWifiEnable").val();
		var startHour = $("#startHour").val(),
			startMin = $("#startMin").val(),
			endHour = $("#endHour").val(),
			endMin = $("#endMin").val(),
			time = startHour + ":" + startMin + "-" + endHour + ":" + endMin;
		if (schedWifiEnable == 1) {

			if (initObj.powerSaveTime) {
				var powerSaveTimeStart = parseInt(initObj.powerSaveTime.split("-")[0].replace(/[^\d]/g, ""), 10),
					powerSaveTimeEnd = parseInt(initObj.powerSaveTime.split("-")[1].replace(/[^\d]/g, ""), 10);
				if (isTimeOverlaping(powerSaveTimeStart, powerSaveTimeEnd, parseInt(startHour + "" + startMin, 10), parseInt(endHour + "" + endMin, 10))) {
					//重叠
					if (!window.confirm(_("The effective period of Sleeping Mode (%s) overlaps that of WiFi Schedule (%s).  During the overlap period, the WiFi Schedule function will be ineffective. Do you want to save the settings?", [initObj.powerSaveTime, time]))) {
						return false;
					}
				}
			}
		}
		return true;
		/***************判断时间是否与智能省电冲突 over*****************/
	},
	afterSubmit: function (str) { //提交数据回调
		callback(str);
	}
});

/*******************************/
var view = R.moduleView({
	initHtml: initHtml,
	initEvent: function () {
		$("#schedWifiEnable").on("click", function () {
			if (initObj.wl_mode == "ap") {
				changeWifiTimeEn();
			}
		});
		$("[name='timeType']").on("click", changeTimeType);
	},
	checkData: function () {
		var subObj = {},
			schedWifiEnable = $("#schedWifiEnable").val(),
			start_time = "",
			end_time = "",
			index = 0,
			i = 0;
		if (schedWifiEnable == 1) {
			start_time = $("#startHour").val() + ":" + $("#startMin").val();
			end_time = $("#endHour").val() + ":" + $("#endMin").val();

			for (i = 0; i < 7; i++) {
				if ($("#day" + (i + 1))[0].checked) {
					index++;
				}
			}


			if ($("#schedWifiEnable").val() == "1") {
				if (index == 0 && $("#thatday")[0].checked) {
					return _("Please select at least one day.");
				}

				if (start_time.replace(/[:]/g, "") == end_time.replace(/[:]/g, "")) {
					//top.mainPageLogic.validate._error(_("The start time must be earlier than the end time."));		
					return _("The start time and end time must not be the same.");
				}
			}
		}
	}
});

var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () { //获取模块提交数据
		var subObj = {},
			schedWifiEnable = $("#schedWifiEnable").val(),
			start_time = "",
			end_time = "",
			subStr = "",
			i = 0,
			dayList = "",
			timeType;

		if (schedWifiEnable == 1) {
			start_time = $("#startHour").val() + ":" + $("#startMin").val();
			end_time = $("#endHour").val() + ":" + $("#endMin").val();

			for (i = 0; i < 7; i++) {
				if ($("#day" + (i + 1))[0].checked) {
					dayList += "1,";
				} else {
					dayList += "0,";
				}
			}
			dayList = dayList.replace(/[,]$/, "");

			if ($("#everyday")[0].checked) {
				timeType = "0";
			} else {
				timeType = "1";
			}

			/***************判断时间是否与智能省电冲突 over*****************/

		} else {
			start_time = initObj.schedStartTime;
			end_time = initObj.schedEndTime;
			timeType = initObj.timeType;
			dayList = initObj.day;
		}

		subObj = {
			"schedWifiEnable": $("#schedWifiEnable").val(),
			"schedStartTime": start_time,
			"schedEndTime": end_time,
			"timeType": timeType,
			"day": dayList
		}
		subStr = objTostring(subObj);
		return subStr;
	}
});

//模块注册
R.module("wifiTime", view, moduleModel);

function initValue(obj) {
	initObj = obj;

	$("#schedWifiEnable").attr("class", obj.schedWifiEnable == "1" ? "btn-off" : "btn-on");
	changeWifiTimeEn();

	if (obj.wl_mode != "ap") {
		showErrMsg("msg-err", _("Please disable Wireless Repeating on the WiFi Settings page first."), true);
		$("#submit")[0].disabled = true;
	} else {
		(obj.timeUp == "1" ? $("#timeUpTip").addClass("none") : $("#timeUpTip").removeClass("none"));
	}

	if (obj.schedStartTime == "0" && obj.schedEndTime == "0") {
		obj.schedStartTime = "00:00";
		obj.schedEndTime = "07:00";
	}
	$("#startHour").val((obj.schedStartTime).split(":")[0]);
	$("#startMin").val((obj.schedStartTime).split(":")[1]);
	$("#endHour").val((obj.schedEndTime).split(":")[0]);
	$("#endMin").val((obj.schedEndTime).split(":")[1]);
	if (obj.timeType == "0") {
		$("[name='timeType']")[0].checked = true;
	} else {
		$("[name='timeType']")[1].checked = true;
	}

	var dayArr = obj.day.split(","),
		len = dayArr.length,
		i = 0,
		dayVal;

	for (i = 0; i < len; i++) {
		dayVal = dayArr[i];
		if (dayVal == 0) {
			$("#day" + (i + 1)).attr("checked", false);
		} else {
			$("#day" + (i + 1)).attr("checked", true);
		}
	}

	if ($("#everyday")[0].checked) {
		$("[id^='day']").attr("disabled", true);

	} else {
		$("[id^='day']").removeAttr("disabled");
	}
	top.initIframeHeight();
}

function initWifiTime() {
	$("#submit").on("click", function () {
		if (initObj.wl_mode == "ap") {
			wifiTime.submit();
		}
	});

	top.loginOut();

	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
}

function initHtml() {
	var obj = getTimeString();
	$("#startHour").html(obj.hour);
	$("#startMin").html(obj.minute);
	$("#endHour").html(obj.hour);
	$("#endMin").html(obj.minute);
}

function changeTimeType() {
	if ($("#everyday")[0].checked) {
		$("[id^='day']").attr("disabled", true).prop("checked", true);

	} else {
		$("[id^='day']").removeAttr("disabled");
		//$("#day6, #day7").prop("checked", false);
	}
	top.initIframeHeight();
}

function changeWifiTimeEn() {
	var className = $("#schedWifiEnable").attr("class");
	if (className == "btn-off") {
		$("#schedWifiEnable").attr("class", "btn-on");
		$("#schedWifiEnable").val(1);
		$("#time_set").removeClass("none");
	} else {
		$("#schedWifiEnable").attr("class", "btn-off");
		$("#schedWifiEnable").val(0);
		$("#time_set").addClass("none");
	}
	top.initIframeHeight();
}

function callback(str) {
	$("#submit").attr("disabled", false);
	if (!top.isTimeout(str)) {
		return;
	}
	top.$("#iframe-msg").html("");
	//top.$("#iframe-msg").addClass("none");

	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
	if (num == 0) {
		//getValue();	
		top.wrlInfo.initValue();
	}
}

/*************************************************/
window.onload = function () {
	wifiTime = R.page(pageview, pageModel);
};