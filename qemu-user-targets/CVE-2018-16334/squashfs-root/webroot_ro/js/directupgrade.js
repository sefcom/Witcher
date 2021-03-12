var language = B.getLang();
var G = {};

function initDirectUpgrade() {
	$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
		if (obj.wan_sta == 0) {
			$("#status_checking").html(_("Failed to access the internet. Please check the internet connection."));
		} else {
			$.getJSON("goform/cloudv2?module=olupgrade&opt=queryversion&rand=" + new Date().toTimeString(), onlineQueryVersion);
		}
	});
}

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
	case 19:
		result = _("Connecting to the server...");
		break;
	}

	return result;

}

function onlineQueryVersion(obj) {
	var ver_info = obj.ver_info,
		descriptionArr = [];

	var result = onlineErrCode(ver_info.err_code);
	if (ver_info.err_code == 19) {
		$("#status_checking").removeClass("none").html(result);
		clearTimeout(G.onlineTimer);
		G.onlineTimer = setTimeout(function () {
			$.getJSON("goform/cloudv2?module=olupgrade&opt=queryversion&rand=" + new Date().toTimeString(), onlineQueryVersion);
		}, 2000);
		return;
	}

	if (result == "") {

		switch (ver_info.resp_type) {
		case 0:
			//获取到新版本，显示版本信息，根据当前语言来显示
			$("#status_checking, #status_progress, #download_note, #upgrade_err").addClass("none");
			$("#status_checked, #download_soft").removeClass("none");
			//显示信息
			$("#new_fw_ver").html(ver_info.detail.newest_ver);
			$("#new_fw_date").html(ver_info.detail.update_date);
			var description = ver_info.detail.description;
			if (language == "en") {
				description = ver_info.detail.description_en;
			} else if (language == "cn") {
				description = ver_info.detail.description;
			} else if (language == "zh") {
				description = ver_info.detail.description_zh_tw;
			}
			if (description) {
				descriptionArr = description.join("").split("\n");
			} else {
				descriptionArr = ver_info.detail.description[0].split("\n");
			}
			$("#releaseNote").html("");
			for (var i = 0; i < descriptionArr.length; i++) {
				$("#releaseNote").append("<li>" + descriptionArr[i] + "</li>");
			}

			break;
		case 1:
			//没有新版本
			$("#status_checking").html(_("No later version is available."));
			$("#download_soft").addClass("none");
			break;

		}
	} else {
		$("#status_checking").html(result);
	}

	top.initIframeHeight();
}

function dowloadSoft() {

	//if (window.confirm(_("When download is complete, the router starts the upgrade automatically. Keep the router powered on during the upgrade to prevent damaging the router."))) {

	$("#download_soft").attr("disabled", true);
	$('input[name="upgradeType"]').attr("disabled", true);

	$("#download_soft, #status_progress, #download_note, #status_checked").addClass("none");

	$("#status_checking").removeClass("none").html(_("Downloading..."));
	//不允许跳转页面
	top.$(".iframe-close").addClass("none");

	$.getJSON("goform/cloudv2?module=wansta&opt=query&rand=" + Math.random(), function (obj) {
		if (obj.wan_sta == 0) {

			$("#status_checking").html(_("Failed to access the internet. Please check the internet connection."));
			$("#download_soft").attr("disabled", false);
			$('input[name="upgradeType"]').attr("disabled", false);
			top.$(".iframe-close").removeClass("none");
		} else {

			$.getJSON("goform/cloudv2?module=olupgrade&opt=queryupgrade&rand=" + new Date().toTimeString(), queryUpgradeStatus);
		}
	});
	//}
}


function queryUpgradeStatus(obj) {

	var num = 0;

	if (typeof obj == "string" && obj.indexOf("!DOCTYPE html") >= 0) {
		//被重置了页面，说明升级有问题
		num = -1;
	}
	if (num == 0) {

		clearTimeout(G.time);
		showOnlineUp(obj);
	} else {
		$("#download_soft").attr("disabled", false);
		top.$(".iframe-close").removeClass("none");
		top.$("#page-message").html(_("Upgrade error. Please check the internet connection status."));
		setTimeout(function () {
			top.$(".main-dailog").removeClass("none");
			//显示页面，隐藏保存进度条
			top.$(".save-msg").addClass("none");
		}, 1000);
	}
}

function showOnlineUp(obj) {
	var update_info = {},
		wait_info = {},
		width_bg = parseInt($("#progress_bg").css("width"), 10),
		width_tip = parseInt($("#progress_num").css("width"), 10);
	width_bar = parseInt($("#progress_bar").css("width"), 10);


	up_info = obj.up_info;

	var result = onlineErrCode(up_info.err_code);

	if (up_info.err_code == 19) {
		$("#status_checking").removeClass("none").html(result);

		checkingStatus(2000);
		return;
	}

	if (result == "") {
		$("#upgrade_err").addClass("none").html("");
		switch (up_info.resp_type) {
		case 0:
			//正在询问升级服务器，即正在准备下载
			$('input[name="upgradeType"]').attr("disabled", true);

			$("#download_soft, #status_progress, #download_note, #status_checked").addClass("none");


			//$("#status_checking").removeClass("none").html(_("Downloading..."));
			$("#status_checking").html("");
			$("#status_progress").removeClass("none");
			//继续检测
			checkingStatus(5000);
			break;
		case 1:
			//内存不足
			top.$(".iframe-close").removeClass("none");
			$('input[name="upgradeType"]').attr("disabled", false);
			$("#status_checking, #status_progress, #download_note, #download_soft").addClass("none");
			$("#status_checked").removeClass("none");
			$("#upgrade_err").html(_("The available memory is not enough. Please reboot the router before downloading data.")).removeClass("none");
			break;
		case 2:
			//路由器在排队升级
			$('input[name="upgradeType"]').attr("disabled", true);
			$("#download_soft, #status_progress , #download_note, #status_checked").addClass("none");

			wait_info = up_info.detail;
			$("#status_checking").removeClass("none").html(_("%s user(s) is/are queuing. You may need to wait %s seconds.", [wait_info.pos, wait_info.time]));
			checkingStatus(2000);
			break;
		case 3:
			//路由器正在下载固件
			$('input[name="upgradeType"]').attr("disabled", true);
			$("#status_checking,#download_soft, #status_checked").addClass("none");

			$("#status_progress, #download_note").removeClass("none");
			var progress = parseInt(up_info.detail.recved / up_info.detail.fw_size * 100, 10);

			$("#progress_bar").css("width", progress + "%");
			$("#progress_num .progressNum").html(progress + "%");
			$("#remainingTime").text(formatSeconds(up_info.detail.sec_left));

			//正在下载时去掉关闭操作
			top.$("body").undelegate("#gbx_overlay", "click");
			top.$(".iframe-close").addClass("none");
			checkingStatus(2000);
			break;
		case 4:
			//路由器正在烧写固件
			$('input[name="upgradeType"]').attr("disabled", true);
			//下载完成后，隐藏更新内容，显示正在准备升级
			$("#status_checked, #download_soft, #download_note").addClass("none");
			$("#status_checking").addClass("none");
			$("#remainingTime").parent().addClass("none");
			$("#remainingTime").addClass("none");
			//$("#status_checking").removeClass("none").html(_("Preparing for the upgrade... Please wait."));
			$("#status_progress").removeClass("none");
			top.$(".iframe-close").removeClass("none");
			//top.$(".main-dailog").addClass("none");
			onlineProgress();
			break;

		default:
			top.$(".iframe-close").removeClass("none");
			$('input[name="upgradeType"]').attr("disabled", false);
			top.$("#page-message").html(_("Upgrade failure."));
			setTimeout(function () {
				top.$(".main-dailog").removeClass("none");
				//显示页面，隐藏保存进度条

			}, 1000);
			break;
		}
	} else {
		$("#status_checking, #status_progress, #download_note, #download_soft").addClass("none");
		$("#status_checked").removeClass("none");
		$("#upgrade_err").removeClass("none").html(result);
		top.$(".iframe-close").removeClass("none");
	}

	top.initIframeHeight();
}

function onlineProgress() {
	var rebootTimer = null,
		percent = 0;
	$("#progress_bar").css("width", 0);
	$("#progress_num .progressNum").text("0%");
	$("#progressMsg").html(_("Upgrading... Please wait."));
	$("#download_note").removeClass("none");

	top.$("body").undelegate("#gbx_overlay", "click");
	top.$(".iframe-close").addClass("none");

	function rebootTime(percent) {
		$("#progress_bar").css("width", percent + "%");
		$("#progress_num .progressNum").text(percent + "%");
		$("#progress_num").addClass("txt-center");
		rebootTimer = setTimeout(function () {
			rebootTime(percent);
		}, 1450);

		if (percent >= 100) {
			clearTimeout(rebootTimer);
			top.jumpTo(window.location.host);
			return;
		} else if (percent >= 80) {
			$("#progressMsg").html(_("Rebooting... Please wait."));
		}
		percent++;
	}

	rebootTime(0);
}

function checkingStatus(time) {
	clearTimeout(G.time);
	G.time = setTimeout(function () {
		$.getJSON("goform/cloudv2?module=olupgrade&opt=queryupgrade&rand=" + new Date().toTimeString(), showOnlineUp);
	}, time);
}

function changeUpgradeType() {
	if ($("#local_upgrade").prop("checked")) {
		$("#local_upgrade_wrap").removeClass("none");
		$("#online_upgrade_wrap").addClass("none");

	} else {
		initDirectUpgrade();
		$("#online_upgrade_wrap").removeClass("none");
		$("#local_upgrade_wrap").addClass("none");
	}
	top.initIframeHeight();
}

function callback(obj) {
	$("#cur_fw_ver").html(obj.cur_fw_ver);
}

function noUpgradeFirm() {
	var dataStr = "";
	if ($("#noUpdateTips").length > 0 && $("#noUpdateTips")[0].checked) {
		dataStr = "action=1&newVersion=" + $("#new_fw_ver").html();
		$.GetSetData.setData("goform/setNotUpgrade", dataStr, function () {
			top.closeIframe();
		});
	} else {
		top.closeIframe();
	}
}

function initEvent() {
	$("[name='upgradeType']").on("click", changeUpgradeType);
	$("#sys_upgrade").on("click", function () {
		if ($("#upgradeFile").val() == "") {
			//if($("#cur_fw_ver").html() == $("#new_fw_ver").html()) {
			showErrMsg("msg-err", _("Please select a firmware file for upgrading the router."));
			G.index = 1;
			return false;
			//}
			//$.post("goform/SysToolSetUpgrade", "action=0",callbackUpgrade)
		} else {
			document.forms[0].submit();
			$("#sys_upgrade").attr("disabled", true);
		}
	});

	$("#noUpdate").on("click", noUpgradeFirm);

	$("#download_soft").on("click", dowloadSoft);

	//重新定义关闭弹出框事件
	top.$(".iframe-close").off("click");
	top.$(".iframe-close").on("click", function () {
		top.$(".iframe-close").off("click").on("click", top.closeIframe);
		noUpgradeFirm();
	});

	$.getJSON("goform/SysToolGetUpgrade?" + Math.random(), callback);
}

function initUpgrade() {
	var msg = location.search.substring(1) || "0";
	//1001 格式错误
	//1002 CRC校验失败
	//1003 文件大小错误
	//1004 升级失败
	//1005 内存不足，请重启路由器
	if ($("#local_upgrade").length > 0) {
		$("#local_upgrade").prop("checked", true);
		$("#local_upgrade_wrap").removeClass("none");
		$("#online_upgrade_wrap").addClass("none");
	}

	if (msg == "1001") {
		$("#msg-err").html(_("Format error!"));
	} else if (msg == "1002") {
		$("#msg-err").html(_("CRC check Failure"));
	} else if (msg == "1003") {
		$("#msg-err").html(_("File size error"));
	} else if (msg == "1004") {
		$("#msg-err").html(_("Fail to upgrade it!"));
	} else if (msg == "1005") {
		$("#msg-err").html(_("Internal memory is not enough. Please reboot the router before upgrading."));
	} else {
		if ($("#online_upgrade").length > 0) {
			$("#online_upgrade").prop("checked", true);
			$("#online_upgrade_wrap").removeClass("none");
			$("#local_upgrade_wrap").addClass("none");
		}
		initDirectUpgrade();
	}
	$("#sys_upgrade").removeAttr("disabled");
	top.$(".main-dailog").removeClass("none");
}

window.onload = function () {
	initUpgrade();
	initEvent();
}
window.onunload = function () {
	top.$(".iframe-close").off("click").on("click", top.closeIframe);
}