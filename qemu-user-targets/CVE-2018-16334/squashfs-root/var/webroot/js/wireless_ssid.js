/**************** Page *******************************/
var wrlBasicPage;
var G = {};
var pageview = R.pageView({ //页面初始化
	init: initPage
}); //page view

//page model
var pageModel = R.pageModel({
	getUrl: "goform/WifiBasicGet", //获取数据接口
	setUrl: "goform/WifiBasicSet", //提交数据接口
	translateData: function (data) { //数据转换
		var newData = {};
		newData.wrlBasic = data;
		return newData;
	},
	afterSubmit: function (str) { //提交数据回调
		callback(str);
	}
});

//页面逻辑初始化
function initPage() {
	$.validate.valid.ssid = {
		all: function (str) {
			var ret = this.specific(str);
			//ssid 前后不能有空格，可以输入任何字符包括中文，仅32个字节的长度
			if (ret) {
				return ret;
			}

			/*if (str.charAt(0) == " " || str.charAt(str.length - 1) == " ") {
				return _("The first and last characters of WiFi Name cannot be spaces.");
			}*/

			if (getStrByteNum(str) > 32) {
				return _("The WiFi name can contain only a maximum of %s bytes.", [32]);
			}
		},
		specific: function (str) {
			var ret = str;
			if ((null == str.match(/[^ -~]/g) ? str.length : str.length + str.match(/[^ -~]/g).length * 2) > 32) {
				return _("The WiFi name can contain only a maximum of %s bytes.", [32]);
			}
		}
	}
	$.validate.valid.ssidPwd = {
		all: function (str) {
			var ret = this.specific(str);

			if (ret) {
				return ret;
			}
			if ((/^[0-9a-fA-F]{1,}$/).test(str) && str.length == 64) { //全是16进制 且长度是64

			} else {
				if (str.length < 8 || str.length > 63) {
					return _("The password must consist of %s-%s characters.", [8, 63]);
				}
			}
			//密码不允许输入空格
			//if (str.indexOf(" ") >= 0) {
			//	return _("The WiFi password cannot contain spaces.");
			//}
			//密码前后不能有空格
			/*if (str.charAt(0) == " " || str.charAt(str.length - 1) == " ") {
				return _("The first and last characters of WiFi Password cannot be spaces.");
			}*/
		},
		specific: function (str) {
			var ret = str;
			if (/[^\x00-\x80]/.test(str)) {
				return _("Invalid characters are not allowed.");
			}
		}
	}

	$("#save").on("click", function () {
		G.validate.checkAll();
	});
}

//提交回调
function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
	if (num == 0) {
		$("#wrl_submit").blur();
		top.wrlInfo.initValue();
		top.staInfo.initValue();
	}
}

/****************** Page end ********************/

/****************** Module wireless setting *****/

var view = R.moduleView({
	initHtml: initHtml,
	initEvent: initEvent
});

var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () { //获取模块提交数据

		getCheckbox(["hideSsid", "hideSsid_5g"]);

		var dataObj = {
				"wrlEn": $('[name="wrlEn"]').val(),
				"wrlEn_5g": $('[name="wrlEn_5g"]').val(),
				"security": $("#security").val(),
				"security_5g": $("#security_5g").val(),
				"ssid": $("#ssid").val(),
				"ssid_5g": $("#ssid_5g").val(),
				"hideSsid": $("#hideSsid").val(),
				"hideSsid_5g": $("#hideSsid_5g").val(),
				"wrlPwd": $("#wrlPwd").val(),
				"wrlPwd_5g": $("#wrlPwd_5g").val()
			},
			dataStr;
		dataStr = objTostring(dataObj);
		return dataStr;
	}
});
//模块注册
R.module("wrlBasic", view, moduleModel);

//初始化页面
function initHtml() {
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
}

//事件初始化
function initEvent() {
	$('[name^="wrlEn"]').on("click", function () {
		changeWifiEn($(this));
	});

	$("select").on("change", function () {
		if ($(this).val() === "none") {
			$(this).parent().parent().next().find("input").val("").attr("disabled", true);
			$(this).parent().parent().next().find("input").removeValidateTip(true).removeClass("validatebox-invalid");
		} else {
			$(this).parent().parent().next().find("input").attr("disabled", false);
		}
	});


	top.loginOut();
	checkData();
}

//模块数据验证
function checkData() {
	G.validate = $.validate({
		custom: function () {
			//if ($("#wrlEn").hasClass("btn-on")) {
			if (($("#security").val() !== "none") && ($("#wrlPwd").val() === "")) {
				return _("Please specify your 2.4 GHz WiFi password.");
			}
			//}

			//if ($("#wrlEn_5g").hasClass("btn-on")) {
			if (($("#security_5g").val() !== "none") && ($("#wrlPwd_5g").val() === "")) {
				return _("Please specify your 5 GHz WiFi password.");
			}
			//}
		},

		success: function () {
			wrlBasicPage.submit();
		},

		error: function (msg) {
			if (msg) {
				$("#wrl_save_msg").html(msg);
				setTimeout(function () {
					$("#wrl_save_msg").html("&nbsp;");
				}, 3000);
			}
			return;
		}
	});
}

function changeWifiEn(ele) {
	var className = ele.attr("class");
	if (className == "btn-off") {
		ele.attr("class", "btn-on");
		ele.val(1);
		ele.parent().parent().nextAll().removeClass("none");
	} else {
		ele.attr("class", "btn-off");
		ele.val(0);
		ele.parent().parent().nextAll().addClass("none");
	}
	top.initIframeHeight();
}

function initEn(ele, en) {
	if (en === "on") {
		ele.attr("class", "btn-on");
		ele.val(1);
		ele.parent().parent().nextAll().removeClass("none");
	} else {
		ele.attr("class", "btn-off");
		ele.val(0);
		ele.parent().parent().nextAll().addClass("none");
	}
}

function initValue(obj) {
	inputValue(obj);
	if (obj.wrlEn === "1") {
		initEn($('[name="wrlEn"]'), "on");
	} else {
		initEn($('[name="wrlEn"]'), "off");
	}

	if (obj.wrlEn_5g === "1") {
		initEn($('[name="wrlEn_5g"]'), "on");
	} else {
		initEn($('[name="wrlEn_5g"]'), "off");
	}

	$("#wrlPwd").initPassword("", false, false);
	$("#wrlPwd_5g").initPassword("", false, false);

	//mainPageLogic.validate.checkAll("wrl-form");
	if (obj.security === "none") {
		$("#wrlPwd").val("").attr("disabled", true);
		if ($("#wrlPwd_").length > 0) {
			$("#wrlPwd_").val("").attr("disabled", true);
		}
	} else {
		$("#wrlPwd").attr("disabled", false);
		if ($("#wrlPwd_").length > 0) {
			$("#wrlPwd_").attr("disabled", false);
		}
	}
	if (obj.security_5g === "none") {
		$("#wrlPwd_5g").val("").attr("disabled", true);
		if ($("#wrlPwd_5g_").length > 0) {
			$("#wrlPwd_5g_").val("").attr("disabled", true);
		}
	} else {
		$("#wrlPwd_5g").attr("disabled", false);
		if ($("#wrlPwd_5g_").length > 0) {
			$("#wrlPwd_5g_").attr("disabled", false);
		}
	}

	if (obj.hideSsid == 1) {
		$("#hideSsid")[0].checked = true;
	} else {
		$("#hideSsid")[0].checked = false;
	}
	if (obj.hideSsid_5g == 1) {
		$("#hideSsid_5g")[0].checked = true;
	} else {
		$("#hideSsid_5g")[0].checked = false;
	}
};

/******************* Module wireless setting end ************/

window.onload = function () {
	wrlBasicPage = R.page(pageview, pageModel);
};