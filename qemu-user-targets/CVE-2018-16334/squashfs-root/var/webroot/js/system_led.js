var G = {};
var initObj = null;
var confirmTag = true;

var ledInfo;
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
	getUrl: "goform/GetLEDCfg",
	setUrl: "goform/SetLEDCfg",
	translateData: function (data) {
		var newData = {};
		newData.led = data;
		return newData;
	},
	beforeSubmit: function () {
		return confirmTag;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initHtml: initHtml,
	initEvent: function () {
		$("[name='ledType']").on("click", changeLedType);
		checkData();
	},
	checkData: function () {
		var data = "",
			ledType = $("[name='ledType']:checked").val(),
			startHour = $("#startHour").val(),
			startMin = $("#startMin").val(),
			endHour = $("#endHour").val(),
			endMin = $("#endMin").val(),
			time = startHour + ":" + startMin + "-" + endHour + ":" + endMin;
		confirmTag = true;
		if (ledType == "time") {
			//开始时间不能和结束时间相等
			if (startHour == endHour && startMin == endMin) {
				return _("The start time and end time must not be the same.");
			}
			//判断时间是否与智能省电冲突
			if (initObj.powerSaveTime) {
				var powerSaveTimeStart = parseInt(initObj.powerSaveTime.split("-")[0].replace(/[^\d]/g, ""), 10),
					powerSaveTimeEnd = parseInt(initObj.powerSaveTime.split("-")[1].replace(/[^\d]/g, ""), 10);
				if (isTimeOverlaping(powerSaveTimeStart, powerSaveTimeEnd, parseInt(startHour + "" + startMin, 10), parseInt(endHour + "" + endMin, 10))) {
					//重叠
					if (!window.confirm(_("The effective period of Sleeping Mode (%s) overlaps that of LED Control (%s). During the overlap period, the LED Control function will be ineffective. Do you want to save the settings?", [initObj.powerSaveTime, time]))) {
						//return false;
						confirmTag = false
					}
				}
			}
		} else {
			time = initObj.time;
		}
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data = "",
			startHour = $("#startHour").val(),
			startMin = $("#startMin").val(),
			endHour = $("#endHour").val(),
			endMin = $("#endMin").val(),
			time = startHour + ":" + startMin + "-" + endHour + ":" + endMin,
			ledType = $("[name='ledType']:checked").val();

		if (ledType == "time") {

		} else {
			time = initObj.time;
		}

		data = "ledType=" + $("[name='ledType']:checked").val() + "&time=" + time + "&ledCloseType=" + $("[name='ledCloseType']:checked").val();
		return data;
	}
});

//模块注册
R.module("led", view, moduleModel);

function initHtml() {
	var obj = getTimeString();
	$("#startHour").html(obj.hour);
	$("#startMin").html(obj.minute);
	$("#endHour").html(obj.hour);
	$("#endMin").html(obj.minute);
	
	if(top.CONFIG_LED_CLOSE_TYPE  == "y") {
		$("#closeLedType").removeClass("none");
	} else {
		$("#closeLedType").addClass("none");
	}
}

function changeLedType() {
	if ($("[name='ledType'][value='time']")[0].checked) {
		$("#time_set").removeClass("none");
	} else {
		$("#time_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {

		},

		success: function () {
			ledInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				showErrMsg("msg-err", msg);
			}
			return;
		}
	});
}

function initValue(obj) {
	initObj = obj;
	(obj.timeUp == "1" ? $("#timeUpTip").addClass("none") : $("#timeUpTip").removeClass("none"));
	$("[name='ledType'][value='" + obj.ledType + "']")[0].checked = true;
	$("[name='ledCloseType'][value='" + obj.ledCloseType + "']")[0].checked = true;
	changeLedType();
	var time = obj.time;

	$("#startHour").val(time.split("-")[0].split(":")[0]);
	$("#startMin").val(time.split("-")[0].split(":")[1]);
	$("#endHour").val(time.split("-")[1].split(":")[0]);
	$("#endMin").val(time.split("-")[1].split(":")[1]);

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
	ledInfo = R.page(pageview, pageModel);
};