var G = {};
var initObj = null;
var listNum = 0;

var pptpSrvInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		$("#submit").on("click", function () {
			G.validate.checkAll();
		});
		checkData();
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetPptpServerCfg",
	setUrl: "goform/SetPptpServerCfg",
	translateData: function (data) {
		var newData = {};
		newData.pptpServer = data;
		return newData;
	},
	afterSubmit: callback
});

function getPptpServerList() {
	var trArry = $("#serverList").children(),
		len = trArry.length,
		i = 0,
		username,
		password,
		enable,
		data = "";

	for (i = 0; i < len; i++) {
		if (G.action == "delete" && $(trArry[i]).attr("data-target") == "delete") {
			continue;
		}
		username = $(trArry[i]).find("[data-role='username']").text();
		password = $(trArry[i]).find("[data-role='password']").text();
		enable = $(trArry[i]).find("[alt='pptpAction']").attr("class") == "enable" ? "0" : "1";
		data += encodeURIComponent(username) + ";";
		data += encodeURIComponent(password) + ";";
		data += enable + ";"; //禁用/启用
		data += "0;"; //netEn
		data += ";"; //serverIp
		data += ";"; //serverMask
		data += "~"; //remark encodeURIComponent(remark)
	}

	data = data.replace(/(~)$/, '');

	if (G.action == "add") {
		if (data != "") {
			data += "~";
		}
		data += encodeURIComponent($("#userName").val()) + ";";
		data += encodeURIComponent($("#password").val()) + ";";
		data += "1;"; //禁用/启用
		data += "0;"; //netEn
		data += ";"; //serverIp
		data += ";"; //serverMask
		data += ""; //remark encodeURIComponent(remark)
	}
	data = "list=" + data;
	return data;
}


/************************/
var view = R.moduleView({
	initEvent: function () {
		$('#password').initPassword(_(""), false, false);
		$("#serverEn").on("click", changeServerEn);
		$("#mppeEn").on("click", changeMppeEn);
		$("#addBtn").on("click", addRuleByHand);
		$("#serverList").delegate(".delete", "click", function () {
			$(this).parent().parent().attr("data-target", "delete");
			//if (confirm(_("Do you want to continue?"))) {
			G.action = "delete";
			setUserList(delList);
			//}
		});

		$("#serverList").delegate(".disable", "click", function () {
			var userName = $(this).parents("tr").find("[data-role='username']").text();
			//data = "action=disable&username=" + userName;
			$(this).removeClass("disable").addClass("enable").attr("title", _("Click to Enable"));

			//禁用时，连接状态变成禁用
			$(this).parent().prev().find("[alt='pptpStatus']").attr("class", "status-offline");

			G.action = "disable";
			setUserList();
		});

		$("#serverList").delegate(".enable", "click", function () {
			var userName = $(this).parents("tr").find("[data-role='username']").text();
			//data = "action=enable&username=" + userName;
			$(this).removeClass("enable").addClass("disable").attr("title", _("Click to Disable"));
			G.action = "enable";
			setUserList();
		});

		$("#startIp, #serverIp, #serverMask").inputCorrect("ip");
		$("#endIp").inputCorrect("num");

		$("#startIp").on("blur keyup", function () {
			var startIp = $("#startIp").val();
			var arry = startIp.split(".");
			var str = arry[0] + "." + arry[1] + "." + arry[2] + ".";

			if (!$.validate.valid.ip.all(startIp)) {
				$("#endNet").html(str);
			}
		});
	}
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var data,
			i = 0,
			username,
			password,
			netEn,
			serverIp,
			serverMask,
			remark,
			trArry = $("#serverList").children(),
			listEmpty = false;
		len = trArry.length;

		if ($("#serverEn").val() == 1) {
			data = "serverEn=" + $("#serverEn").val();
			data += "&startIp=" + $("#startIp").val();
			data += "&endIp=" + $("#endNet").html() + $("#endIp").val();
			data += "&mppe=" + $("#mppeEn").val();
			data += "&mppeOp=" + $("[name='mppeNum']:checked").val();
			/*data += "&list=";

			if (!listEmpty) {
				for (i = 0; i < len; i++) {
					username = $(trArry[i]).children().eq(0).find("input").val();
					password = $(trArry[i]).children().eq(1).find("input").val();

					data += encodeURIComponent(username) + ";";
					data += encodeURIComponent(password) + ";";
					data += "0;"; //netEn
					data += ";"; //serverIp
					data += ";"; //serverMask
					data += "~"; //remark encodeURIComponent(remark)
				}
			}

			data = data.replace(/[~]$/, "");*/
		} else {
			data = "serverEn=" + $("#serverEn").val();
			data += "&startIp=" + initObj[0].startIp;
			data += "&endIp=" + initObj[0].endIp;
			data += "&mppe=" + initObj[0].mppe;
			data += "&mppeOp=" + initObj[0].mppeOp;
			/*data += "&list=";

			if (!listEmpty) {
				for (i = 1; i < initObj.length; i++) {
					username = initObj[i].userName;
					password = initObj[i].password;

					data += encodeURIComponent(username) + ";";
					data += encodeURIComponent(password) + ";";
					data += "0;"; //netEn
					data += ";"; //serverIp
					data += ";"; //serverMask
					data += "~"; //remark encodeURIComponent(remark)
				}
			}

			data = data.replace(/[~]$/, "");*/
		}
		return data;
	}
});

//模块注册
R.module("pptpServer", view, moduleModel);

function changeServerEn() {
	var className = $("#serverEn").attr("class");
	if (className == "btn-off") {
		$("#serverEn").attr("class", "btn-on");
		$("#serverEn").val(1);
		$("#pptp_set").removeClass("none");
	} else {
		$("#serverEn").attr("class", "btn-off");
		$("#serverEn").val(0);
		$("#pptp_set").addClass("none");
	}
	initTableHeight();
	top.initIframeHeight();
}

function changeMppeEn() {
	var className = $("#mppeEn").attr("class");
	if (className == "btn-off") {
		$("#mppeEn").attr("class", "btn-on");
		$("#mppeEn").val(1);
		$("#mppeNumWrap").removeClass("none");
	} else {
		$("#mppeEn").attr("class", "btn-off");
		$("#mppeEn").val(0);
		$("#mppeNumWrap").addClass("none");
	}
	initTableHeight();
	top.initIframeHeight();
}

function addRuleByHand() {
	var username = $("#ruleAddTr #userName").val(),
		password = $("#ruleAddTr #password").val(),
		data = "";
	//remark = $("#ruleAddTr").children().eq(5).find("input").val();

	if ($("#serverList tr").length >= 8) {
		showErrMsg("msg-err", _("Only a maximum of %s entries are allowed.", [8]));
		return;
	}

	var ruleMsg = checkRule($("#ruleAddTr")[0]);
	if (!ruleMsg) {
		//检查用户名是否有重复
		$("#serverList tr").each(function () {
			var usernameExist = $(this).find("[data-role=username]").text();
			if (usernameExist == username) {
				ruleMsg = _("The user name already exists.");
				return false;
			}
		});
	}
	if (ruleMsg) {
		showErrMsg("msg-err", ruleMsg);
	} else {
		data = "action=add&username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
		G.action = "add";
		setUserList(handlerList);
	}
}


function setUserList(callback) {
	var data = getPptpServerList();
	$.GetSetData.setData("goform/setPptpUserList", data, function (str) {
		var num = $.parseJSON(str).errCode;
		if (num == 0) {
			if (typeof callback == "function") {
				callback.call(this);
			}
		}
	});
}

function handlerList() {
	addList({
		"userName": $("#userName").val(),
		"password": $("#password").val(),
		"enable": "1"
	});
	$("#ruleAddTr").find("input").each(function () {
		if ((this.type.toLowerCase() === "text") || (this.type.toLowerCase() === "password")) {
			this.value = "";
		} else if (this.type.toLowerCase() === "checkbox") {
			this.checked = false;
		}
	});
}

function addList(rowDataObj) {
	var str = "",
		$row = null,
		statusTxt = [_("Disconnected"), _("Connected")];

	if ($("#serverList tr").length >= 8) {
		showErrMsg("msg-err", _("Only a maximum of %s entries are allowed.", [8]));
		return;
	}
	listNum++;
	str = "<tr>";

	str += "<td><span data-role='username'></span></td>";
	str += "<td><span data-role='password'></td>";
	str += "<td><div alt='pptpStatus'></div></td>";
	str += "<td><span alt='pptpAction' title='" + _("Click to Disable") + "' class=''></span><span>&nbsp;&nbsp;&nbsp;</span><span class='delete' title='" + _("Delete") + "'></span></td></tr>";
	$row = $(str);
	$("#serverList").append($row);
	//TODO根据数据显示图标是禁用还是启用 
	if (rowDataObj) {
		if (rowDataObj.enable == "1") {
			$row.find("[alt=pptpAction]").attr("class", "disable").attr("title", _("Click to Disable"));
		} else {
			$row.find("[alt=pptpAction]").attr("class", "enable").attr("title", _("Click to Enable"));
		}

		$row.find("[alt=pptpStatus]").attr("class", rowDataObj.connsta == "1" ? "status-online" : "status-offline");
		$row.find("[data-role=username]").text(rowDataObj.userName);
		$row.find("[data-role=password]").text(rowDataObj.password);
	}
	top.initIframeHeight();
}

//删除操作
function delList() {
	$("#serverList").find("[data-target='delete']").remove();
}

//检查一条规则是否合法
function checkRule(rowEle) {
	var rel_ip = /^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/,
		rel_mask = /^(254|252|248|240|224|192)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(252|248|240|224|192|128|0))$/,

		lanIp = G.data.lanIp,
		lanMask = G.data.lanIp,
		guestIp = G.data.guestIp,

		username = $(rowEle).children().eq(0).find("input").val(),
		password = $(rowEle).children().eq(1).find("input").val();



	if (!username || username == "") {
		$("#userName").focus();
		return _("Please specify a user name.");
	} else if ($.validate.valid.pwd(username)) {
		$("#userName").focus();
		return $.validate.valid.pwd(username);
	}
	if (password == "") {
		$("#password").focus();
		return _("Please specify a password.");
	} else if ($.validate.valid.pwd(password)) {
		$("#password").focus();
		return $.validate.valid.pwd(password);
	}
}

function checkData() {
	G.validate = $.validate({
		custom: function () {
			var i = 0,
				trArry = $("#serverList").children(),
				len = trArry.length,
				lanIp = G.data.lanIp,
				lanMask = G.data.lanMask,
				serverIp = G.data.serverIp,
				vlan2Ip = G.data.vlan2Ip,
				vlan2Mask = G.data.vlan2Mask,
				guestIp = G.data.guestIp,
				guestMask = G.data.guestMask,
				wanIp = G.data.wanIp,
				wanMask = G.data.wanMask,
				wanIp2 = G.data.wanIp2 || "",
				wanMask2 = G.data.wanMask2,
				rel_ip = /^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/,
				rel_mask = /^(254|252|248|240|224|192)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(252|248|240|224|192|128|0))$/,
				startIp,
				endIp,
				msg = "";

			startIp = $("#startIp").val();
			endIp = $("#endIp").val();

			var wanMsg = _("WAN IP Address");

			if (top.G.wanNum == 2) {
				wanMsg = _("WAN1 IP Address");
			}

			if ($("#serverEn").val() == "1") {
				if (lanIp != "" && checkIpInSameSegment(startIp, lanMask, lanIp, lanMask)) {
					return _("The start IP address of IP Address Pool must not be in the same network segment as LAN IP Address (%s).", [lanIp]);
				}

				// 决策： 访客网络网段冲突有后台处理
				/*if (guestIp != "" && checkIpInSameSegment(startIp, lanMask, guestIp, guestMask)) {
					startIp = $("#startIp").val()
					return _("The start IP address of IP Address Pool must not be in the same network segment as Guest Network IP Address (%s).",[guestIp]);
				}*/

				if (wanIp != "" && checkIpInSameSegment(startIp, lanMask, wanIp, wanMask)) {
					return _("The start IP address of IP Address Pool must not be in the same network segment as %s (%s).", [wanMsg, wanIp]);
				}

				//增加WAN2 IP判断
				if (top.G.wanNum == 2) {
					if (wanIp2 != "" && checkIpInSameSegment(startIp, lanMask, wanIp2, wanMask2)) {
						return _("The start IP address of IP Address Pool must not be in the same network segment as %s (%s).", [_("WAN2 IP Address"), wanIp2]);
					}
				}

				if (serverIp != "" && checkIpInSameSegment(startIp, lanMask, serverIp, "255.255.255.0")) {
					return _("The start IP address of the address pool is in conflict with the IP address (%s) of the server connected to a WAN port. Please use another start IP address.", [serverIp]);
				}

				if (vlan2Ip != "" && checkIpInSameSegment(startIp, lanMask, vlan2Ip, vlan2Mask)) {
					return _("The start IP address of address pool is in conflict with the IP address (%s) of the WAN port connected to a server. Please use another start IP address.", [vlan2Ip]);
				}

				startIp = $("#startIp").val().split(".")[3];
				if (parseInt(startIp, 10) > parseInt(endIp, 10)) {
					return _("The end IP address must be greater than the start IP address.");
				}

				if ((startIp + "") == "1") {
					return _("The last digit of the start IP address cannot be 1.");
				}

				//todo： 判断地址池包含8个
				if ((parseInt(endIp, 10) - parseInt(startIp, 10)) < 7) {
					return _("Please ensure that the IP address pool contains at least 8 IP addresses.");
				}

				/*var ruleMsg;
				for (i = 0; i < len; i++) {
					ruleMsg = checkRule(trArry[i]);
					if (ruleMsg) {
						return ruleMsg;
					}
				}*/
			}
		},

		success: function () {
			pptpSrvInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				showErrMsg("msg-err", msg);
			}
			return;
		}
	});
}

function updateConnectStatus(data) {
	var statusTxt = [_("Disconnected"), _("Connected")],
		rowDataObj = null,
		statusClass = "";

	for (var i = 1; i < data.length; i++) {
		rowDataObj = data[i];
		$("span[data-role=username]").each(function () {
			if ($(this).text() == rowDataObj.userName) {
				statusClass = (rowDataObj.connsta == "1" ? "status-online" : "status-offline");
				$(this).parents("tr").find("[alt=pptpStatus]").attr("class", statusClass);
				return false;
			}
		});
	}
}

var checkServerStatus;

function initValue(obj) {
	var doc,
		str = "",
		len = obj.length,
		i = 1;
	G.data = obj[0];
	initObj = obj;

	$("#serverEn").attr("class", (obj[0].serverEn == "1" ? "btn-off" : "btn-on"));
	changeServerEn();
	$("#mppeEn").attr("class", (obj[0].mppe == "1" ? "btn-off" : "btn-on"));
	changeMppeEn();
	if (obj[0].mppeOp.replace(/[^\d]/g, "") != "")
		$("[name='mppeNum'][value='" + obj[0].mppeOp + "']")[0].checked = true;

	var ipArry = obj[0].endIp.split(".");
	$("#startIp").val(obj[0].startIp);
	$("#endNet").html(ipArry[0] + "." + ipArry[1] + "." + ipArry[2] + ".");
	$("#endIp").val(ipArry[3]);

	for (i = 1; i < len; i++) {
		addList(obj[i]);
	}

	//更新连接状态
	checkServerStatus = setInterval(function () {
		$.GetSetData.getJson("goform/GetPptpServerCfg?" + Math.random(), updateConnectStatus);
	}, 5000);
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
	if (num == 0) {
		clearInterval(checkServerStatus);
		top.vpnInfo.initValue();
	}
}

window.onload = function () {
	pptpSrvInfo = R.page(pageview, pageModel);
};

window.onunload = function () {
	clearInterval(checkServerStatus);
};