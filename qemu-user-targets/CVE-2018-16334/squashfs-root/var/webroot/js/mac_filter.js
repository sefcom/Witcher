var G = {},
	MAX_LENGTH = 30,
	initObj = {},
	initWhiteLength = 0;

var macFilterInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		$("#submit").on("click", function () {
			macFilterInfo.submit();
		});
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/getMacFilterCfg",
	setUrl: "goform/setMacFilterCfg",
	translateData: function (data) {
		var newData = {};
		newData.macFilter = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initEvent,
	checkData: function () {

	}
});
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data = "",
			macFilterType = $("[name='macFilterType']:checked").val(),
			tableListId,
			trArr,
			len,
			listStr,
			i;
		data += "macFilterType=" + macFilterType;
		if (macFilterType == "white") {
			tableListId = "whiteListTable";
		} else {
			tableListId = "blackListTable";
		}

		trArr = $("#" + tableListId + " tr:gt(1)");
		len = trArr.length;
		listStr = "";
		for (i = 0; i < len; i++) {
			if (trArr.eq(i).children().length != 3) {
				continue;
			}
			listStr += encodeURIComponent(trArr.eq(i).children().eq(0).text()) + "\r" + trArr.eq(i).children().eq(1).text() + "\n";
		}
		listStr = listStr.replace(/(\n)$/, '');
		data += "&deviceList=" + listStr;
		return data;
	}
});

//模块注册
R.module("macFilter", view, moduleModel);

function initEvent() {
	$("[name='macFilterType']").on("click", changeFilterType);
	$("table").delegate(".online-link", "click", addOnlineList);
	$("table").delegate(".delete", "click", delTableList);
	$("#addWhiteMac, #addBlackMac").on("click", function () {
		if (this.id == "addWhiteMac") {
			addTableList.call(this, "whiteListTable");
		} else {
			addTableList.call(this, "blackListTable");
		}
	});

	$(".mac").inputCorrect("mac");
	clearDevNameForbidCode($("#whiteListTable .deviceName")[0]);
	clearDevNameForbidCode($("#blackListTable .deviceName")[0]);
}

//添加设备
function addTableList(tableId) {
	var deviceName = $(this).parents("tr").find(".deviceName").val(),
		mac = $(this).parents("tr").find(".mac").val().toUpperCase(),
		$tr = $(this).parents("table").find("tr:gt(1)"),
		len = $tr.length,
		i = 0,
		msg = $.validate.valid.mac.all(mac);

	if (msg) {
		showErrMsg("msg-err", msg);
		return;
	}

	//统一验证设备名称合法性
	msg = checkDevNameValidity(deviceName, true);

	if (msg) {
		showErrMsg("msg-err", msg);
		return;
	}

	if (len >= 30) {
		showErrMsg("msg-err", _("Only a maximum of %s rules are allowed.", [30]));
		return;
	}
	for (i = 0; i < len; i++) {
		if (mac == $tr.eq(i).children().eq(1).text()) {
			showErrMsg("msg-err", _("The MAC address exists. Please enter another."));
			return;
		}
	}

	//删除加入白名单操作
	if (tableId === "whiteListTable") {
		if ($("#whiteListTable").find(".online-link").length > 0) {
			$("#whiteListTable").find(".online-link").parent().parent().remove();
		}
	} else {
		if ($("#blackListTable").find(".black-link").length > 0) {
			$("#blackListTable").find(".black-link").parent().remove();
		}
	}

	createTableList([{
		devName: deviceName,
		devMac: mac
	}], tableId);

	//清空数据
	$(this).parents("tr").find(".deviceName").val("");
	$(this).parents("tr").find(".mac").val("");
}

//添加在线设备
function addOnlineList() {
	var onlineArr = initObj.onlineList,
		len = onlineArr.length,
		i = 0,
		hasInTable = false,
		maxLen,
		newOnlineArr = [];
	//删除此项
	$(this).parents("tr").remove();
	for (i = 0; i < len; i++) {
		hasInTable = false;
		//排除在线设备
		if (onlineArr[i].devMac == initObj.localhostMac) {
			continue;
		}
		$("#whiteListTable tr:gt(1)").each(function () {
			//判断MAC地址是否存在
			if ($(this).children().eq(1).html().toLowerCase() == onlineArr[i].devMac.toLowerCase()) {
				hasInTable = true;
				return false;
			}
		});

		if (hasInTable) {
			continue;
		} else {
			newOnlineArr.push(onlineArr[i]);
		}
	}
	maxLen = $("#whiteListTable tr:gt(1)").length + newOnlineArr.length;
	//在线设备超过最大条数时，取前30个
	if (maxLen > MAX_LENGTH) {
		newOnlineArr = newOnlineArr.slice(0, MAX_LENGTH - maxLen);
	}
	createTableList(newOnlineArr, "whiteListTable");
}

function delTableList() {
	$(this).parents("tr").remove();
	top.initIframeHeight();
}

//切换模式事件
function changeFilterType() {
	if ($("[name='macFilterType'][value='black']")[0].checked) {
		$("#blackListTable").removeClass("none");
		$("#whiteListTable").addClass("none");
	} else {
		$("#blackListTable").addClass("none");
		$("#whiteListTable").removeClass("none");
	}
	top.initIframeHeight();
}

function initValue(obj) {
	var localhostObj = {};
	initObj = obj;
	if ($("[name='macFilterType'][value='" + obj.macFilterType + "']").length > 0) {
		$("[name='macFilterType'][value='" + obj.macFilterType + "']")[0].checked = true;
	} else {
		$("[name='macFilterType']")[0].checked = true;
	}
	changeFilterType();
	initWhiteLength = initObj.whiteList.length;
	$("#blackListTable tbody").html("");
	createTableList(initObj.blackList, "blackListTable", "black");
	$("#whiteListTable tbody").html("");

	//排除白名单中的本机
	for (var i = 0, len = initObj.whiteList.length; i < len; i++) {
		if (initObj.whiteList[i].devMac.toUpperCase() == initObj.localhostMac.toUpperCase()) {
			localhostObj = initObj.whiteList.splice(i, 1);
			initObj.whiteList.unshift(localhostObj[0]);
			break;
		}
	}

	createTableList(initObj.whiteList, "whiteListTable", "white");

	top.initIframeHeight();

	initTableHeight();
}

//生成列表
function createTableList(dataArr, tableId, type) {
	var trStr = "",
		i = 0,
		len = dataArr.length,
		deviceName,
		deviceMac,
		maxLen;

	//黑白名单为空时  白名单排除本机之后
	if (len === 0 && type) {
		//黑名单为空时
		if (type == "black") {
			trStr = "<tr><td class='black-link' colspan='3'>" + _("The blacklist is empty.") + "</td>";
		} else {
			if (initWhiteLength === 0) {
				createTableList([{
					devName: initObj.localhostName,
					devMac: initObj.localhostMac
				}], "whiteListTable", "white");
				trStr = "<tr><td colspan='3' style='text-align: center;'><span class='online-link'>" + _("Add all online devices to the whitelist") + "</span></td></tr>";
			}
		}

		$("#" + tableId + " tbody").append(trStr);
		return;
	}

	//当设备超过30条时，只显示前30个
	maxLen = $("#" + tableId + " tr:gt(1)").length + len;
	if (maxLen > MAX_LENGTH) {
		len = len - (maxLen - MAX_LENGTH);
	}

	for (i = 0; i < len; i++) {
		deviceName = dataArr[i].devName;
		if (type) {
			deviceName = dataArr[i].devName || top.G.deviceNameSpace;
		}
		deviceMac = dataArr[i].devMac.toUpperCase();
		trStr = "<tr><td class='device-target fixed'></td><td title='" + deviceMac + "'>" + deviceMac + "</td>";

		if (type == "white" && deviceMac == initObj.localhostMac.toUpperCase()) {
			trStr += "<td><span>" + _("Local Host") + "</span></td>";
		} else {
			trStr += "<td><span class='delete' title='" + _("Delete") + "'></span></td>";
		}
		trStr += "</tr>";
		$("#" + tableId + " tbody").append(trStr);
		$("#" + tableId + " tbody .device-target").attr("title", deviceName);
		$("#" + tableId + " tbody .device-target").text(deviceName);
		$("#" + tableId + " tbody .device-target").removeClass("device-target");
	}
	top.initIframeHeight();

}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;

	if (num == 2) {
		//与lan口IP相同
		//top.$("#iframe-msg").removeClass("none");


	} else {
		top.showSaveMsg(num);
		if (num == "0") {
			//getValue();
			top.advInfo.initValue();
		}
	}
}

window.onload = function () {
	macFilterInfo = R.page(pageview, pageModel);
};