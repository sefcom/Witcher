// JavaScript Document
var initObj = {};

var cloudTimeout;

$(function () {

	$("#submit").on("click", function () {
		$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
			if (obj.wan_sta == 0) {
				showErrMsg("err-msg", _("Failed to access the internet. Please check the internet connection."));

			} else {
				preSubmit();
			}
		});
	});
	$("#cloudEnable").on("click", changeCloudEn);
	$("#btn_sn").on("click", getSNData);
	$("#btn_acc").on("click", function () {
		$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
			if (obj.wan_sta == 0) {
				showErrMsg("err-msg", _("Failed to access the internet. Please check the internet connection."));
				$("#cloud_img").addClass("none");
				$("#account").parent().addClass("none");
				$("#btn_acc_wrap").removeClass("none");
			} else {
				getCloudAccount();
			}
		});
	});
	getValue();
	top.loginOut();
});


function onlineErrCode(num) {
	var result = "";
	switch (num) {
	case 0:
		break;
	case 1:
		result = _("Unknown error.");
		break;
	case 2:
		result = _("JSON data is too long.");
		break;
	case 3:
		result = _("Failed to allocate memory. The available memory is not enough.");
		break;
	case 4:
		result = _("Connection failure.");
		break;
	case 5:
	case 6:
		result = _("Failed to connect to the socket.");
		break;
	case 7:
	case 8:
		result = _("Failed to run the command.");
		break;
	case 9:
		result = _("Invalid command.");
		break;
	case 10:
	case 11:
	case 12:
		result = _("Failed to analyze, package, or detect data.");
		break;
	case 13:
	case 14:
	case 17:
		result = _("Failed to connect to the server.");
		break;
	case 15:
		result = _("Authentication failure.");
		break;
	case 16:
		result = _("The Tenda App feature is disabled.");
		break;
	case 18:
		result = _("The cloud server is busy updating data or testing speeds.");
		break;
	}

	return result;

}

function getValue() {
	$.getJSON("goform/cloudv2?module=manage&opt=querybasic&rand=" + Math.random(), initValue);
}

function initValue(obj) {

	var result = onlineErrCode(obj.err_code);
	initObj = obj;
	if (result == "") {

		//type: 1
		top.$(".main-dailog").removeClass("none");
		top.$("iframe").removeClass("none");
		top.$(".loadding-page").addClass("none");


		if (typeof obj.enable != "undefined") {

			if (obj.enable == "1") {
				$("#cloudEnable").removeClass().addClass("btn-on");
				$("#cloudEnable").val(1);
				$("#cloud_account_set").removeClass("none");
				$(".cloud_help").css("display", "none");

				$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
					if (obj.wan_sta == 0) {
						showErrMsg("err-msg", _("Failed to access the internet. Please check the internet connection."));
						$("#cloud_img").addClass("none");
						$("#account").parent().addClass("none");
						$("#btn_acc_wrap").removeClass("none");
					} else {
						getCloudAccount();
					}
				});


			} else {
				$("#cloudEnable").removeClass().addClass("btn-off");
				$("#cloudEnable").val(0);
				$("#cloud_account_set").addClass("none");
				$(".cloud_help").css("display", "block");
			}
			if (obj.sn == "" && obj.enable == "1") {
				//重复取数据
				$("#sn, #btn_sn_wrap").addClass("none");
				$("#sn_img").removeClass("none");
				setTimeout("getSNData()", 5000);

			} else {
				$("#sn").html(obj.sn).removeClass("none");

				$("#sn_img, #btn_sn_wrap").addClass("none");
			}

		}
	} else {
		showErrMsg("err-msg", result);
		$("#btn_sn_wrap").removeClass("none");

		$("#sn_img, #sn").addClass("none");
	}
	top.initIframeHeight();
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
}

function getCloudAccount() {
	$("#cloud_img").removeClass("none");
	$("#account").parent().addClass("none");
	$("#btn_acc_wrap").addClass("none");
	clearTimeout(cloudTimeout);

	$.getJSON("goform/cloudv2?module=manage&opt=queryaccount&rand=" + Math.random(), function (obj) {

		var result = onlineErrCode(obj.err_code);
		if (obj.err_code == "19" && $("#cloudEnable").hasClass("btn-on")) {
			cloudTimeout = setTimeout(function () {
				getCloudAccount();
			}, 3000);
			return;
		}
		if (result == "" && obj.err_code != 19) {

			initObj.list = obj.list;
			$("#cloud_img, #btn_acc_wrap").addClass("none");
			$("#account").parent().removeClass("none");
			$("#account").val(obj.list).addPlaceholder(_("Registered Mobile No. or Email Address"));
		} else {
			showErrMsg("err-msg", result);
			$("#cloud_img").addClass("none");
			$("#account").parent().addClass("none");
			$("#btn_acc_wrap").removeClass("none");

		}

	});
}

function changeCloudEn() {
	var className = $("#cloudEnable").attr("class"),
		data;

	if (className == "btn-off") {
		$("#cloudEnable").removeClass().addClass("btn-on");
		$("#cloudEnable").val(1);
		$("#cloud_account_set").removeClass("none");
		$(".cloud_help").css("display", "none");

		$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
			if (obj.wan_sta == 0) {
				showErrMsg("err-msg", _("Failed to access the internet. Please check the internet connection."));
				$("#cloud_img").addClass("none");
				$("#account").parent().addClass("none");
				$("#btn_acc_wrap").removeClass("none");
			} else {
				getCloudAccount();
			}
		});

		if (initObj.sn == "") {
			//重复取数据

			//setTimeout("getSNData()", 5000);
			getSNData();

		} else {
			$("#sn").html(initObj.sn).removeClass("none");

			$("#sn_img, #btn_sn_wrap").addClass("none");
		}
	} else {
		$("#cloudEnable").removeClass().addClass("btn-off");
		$("#cloudEnable").val(0);
		$("#cloud_account_set").addClass("none");
		$(".cloud_help").css("display", "block");

	}

	top.initIframeHeight();

}



function getSNData() {
	$("#sn, #btn_sn_wrap").addClass("none");
	$("#sn_img").removeClass("none");
	$.getJSON("goform/cloudv2?module=manage&opt=querybasic&rand=" + new Date().toTimeString(), function (obj) {
		var result = onlineErrCode(obj.err_code);

		if (result == "") {
			if (obj.enable == "1" && obj.sn == "") {

				setTimeout("getSNData()", 3000);
			} else {
				initObj.sn = obj.sn;
				$("#sn").html(obj.sn).removeClass("none");
				$("#sn_img, #btn_sn_wrap").addClass("none");
			}
		} else {
			showErrMsg("err-msg", result);
			$("#btn_sn_wrap").removeClass("none");
			$("#sn_img, #sn").addClass("none");
		}
	});

}


function verifyEmail(str) {
	if (str.indexOf("@") < 0) {
		return false;
	}

	var email_frag = str.split("@");
	if (email_frag.length != 2) {
		return false;
	} else if (email_frag[0].length > 64) {
		return false;
	} else {
		var domain_frag = email_frag[1].split(".");
		if (domain_frag < 2) {
			return false;
		} else if (hasEmptyDomain(domain_frag)) {
			return false;
		} else {
			return /^[a-zA-Z0-9.!#$%&*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/ig.test(str);
		}
	}
}

function hasEmptyDomain(arr) {
	var result = false;
	for (var domain in arr) {
		if (domain == "") {
			result = true;
			break;
		}
	}
	return result;
}

function preSubmit() {



	var subObj = {},
		cloudEnable = $("#cloudEnable").val(),

		account = $("#account").val(),
		subStr = "";



	if (cloudEnable == 1) {

		if ($("#account").parent().hasClass("none")) {
			showErrMsg("err-msg", _("Change Failed. Please obtain a cloud account first."));
			return;
		}

		//除中文之外都不支持手机号码
		if (account != "" && !verifyEmail(account)) {
			if (B.getLang() != "cn" || !(/^((13[0-9])|(15[^4,\D])|(14[57])|(17[0678])|(18[0,0-9]))\d{8}$/g.test(account))) {
				showErrMsg("err-msg", _("This account is invalid. Please specify a correct account. "));
				return;
			}
		}
		showLoading();
		$.post("goform/cloudv2?module=manage&opt=setaccount&rand=" + Math.random(), "list=" + account, callback);
	} else {
		//account = initObj.list;
		//开关关闭状态，仅保存开关，不保存帐号
		showLoading();
		saveEnable();
	}



}

function saveEnable() {
	var className = $("#cloudEnable").attr("class"),
		data;
	var enable = (className == "btn-on" ? "1" : "0");
	$.post("goform/cloudv2?module=manage&opt=setbasic&rand=" + Math.random(), "enable=" + enable, function (obj) {
		obj = $.parseJSON(obj);

		var result = onlineErrCode(obj.err_code);

		if (result == "") {
			setTimeout(function () {
				hideLoading(false);

				top.advInfo.initValue();
			}, 1000);

		} else {
			hideLoading(true, result);
		}
	});
}

function showLoading() {
	if (top.$(".main-dailog").hasClass("none")) {
		return;
	}
	top.$("#gbx_overlay").remove();
	top.$("<div id='gbx_overlay'></div>").appendTo("body");
	top.$("#page-message").html(_("Saving..."));
	top.$(".save-msg").removeClass("none");
	top.$(".main-dailog").addClass("none");
}

function hideLoading(error, result) {
	if (error == true) {
		top.$(".save-msg").addClass("none");
		top.$("#gbx_overlay").remove();
		top.$(".main-dailog").removeClass("none");
		showErrMsg("err-msg", result);
	} else {
		top.$(".save-msg").addClass("none");
		top.$("#gbx_overlay").remove();
	}
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var obj = $.parseJSON(str);
	var num = obj.err_code;

	if (num == "19") {
		setTimeout(function () {
			preSubmit();
		}, 3000);

		return;
	}

	if (num == "0") {
		saveEnable();

	} else {
		hideLoading(true, onlineErrCode(num));
	}

}