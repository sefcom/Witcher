var G = {};
//网络状态，外网设置要用到的联网状态
var statusTxtObj = {
	/*
	第一位传给页面判断是否有断开操作(1,可断开2没有断开)
	第二位传给页面显示颜色(1表示错误颜色、2表示尝试颜色、3表示成功颜色)
	第三位是否连接上(0表示未连上， 1表示连上)既是否显示联网时长
	第四位表示工作模式(0表示AP,1表示WISP,2表示APClient)
	第五位表示WAN口类型(0表示DHCP,1表示static IP,2表示PPPOE)
	第六位和第七位表示错误代码编号
	*/
	/***********AP*********/
	//DHCP
	"1": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
	"2": _("Disconnected"),
	"3": _("Connecting…"), //(之前在1203里面这个状态表示保存了数据但是没有连接上去的情况下提示的，保留之前的)"
	"4": _("Connected. Accessing the internet..."),
	"5": _("Disconnected. Please contact your ISP for help."),
	"6": _("Connected. Accessing the internet..."),
	"7": _("Connected. You can access the internet now."),
	"8": _("No response from the remote server."),
	"9": _("Disconnecting…"),
	//静态：
	"101": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
	"102": _("Disconnected"),
	"103": _("Detecting the internet connection..."), //(之前在1203里面这个状态表示保存了数据但是没有连接上去的情况下提示的，保留之前的)"
	"104": _("Connected. Accessing the internet..."),
	"105": _("Disconnected. Please contact your ISP for help."),
	"106": _("Connected. Accessing the internet..."),
	"107": _("Connected. You can access the internet now."),
	//PPPOE
	"201": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
	"202": _("Disconnected"),
	"203": _("Checking your user name and password. Please wait."),
	"204": _("Dial-up success."),
	"205": _("The user name and password are incorrect."),
	"206": _("No response from the remote server. Please check whether your computer can access the internet directly using your Modem. If no, contact your ISP for help."),
	"207": _("Disconnected. Please contact your ISP for help."),
	"208": _("Connecting…"),
	"209": _("Connected. You can access the internet now."),
	/************WISP**************/
	//DHCP 
	"1001": _("No repeating in WISP mode."),
	"1002": _("No repeating in WISP mode."),
	"1003": _("Repeating in WISP mode..."),
	"1004": _("Repeating in WISP mode succeeded. Accessing the internet..."),
	"1005": _("Disconnected. Please contact your ISP for help."),
	"1006": _("Repeating in WISP mode succeeded. Accessing the internet..."),
	"1007": _("Connected. You can access the internet now."),
	//静态 
	"1101": _("No repeating in WISP mode."),
	"1102": _("No repeating in WISP mode."),
	"1103": _("Repeating in WISP mode..."),
	"1104": _("Repeating in WISP mode succeeded. Accessing the internet..."),
	"1105": _("Disconnected. Please contact your ISP for help."),
	"1106": _("Repeating in WISP mode succeeded. Accessing the internet..."),
	"1107": _("Connected. You can access the internet now."),
	//APClinet
	"2001": _("No repeating in Client+AP mode."),
	"2002": _("Repeating in Client+AP mode..."),
	"2003": _("Repeating in Client+AP mode succeeded.")
};
var wanIndex = Number(top.staInfo.wanTargetIndex);
var netInfo = {
	time: 0,
	isConnect: false, //是否已经连上，即按钮是连接还是断开
	hasConnTime: false, //是否有联网时长
	saveType: "connect", //操作类型，是连接（connect）还是断开（disconnect）
	currentWanType: 0,
	currentDnsType: "1",
	currentVpnType: "1",
	ajaxInterval: null,
	wanBtnChange: false, //修改数据
	initObj: null,
	saving: false, //保存中，连接中或断开中
	setValue: (function () {
		var statusType = 1, //连接状态类型，1错误， 2尝试，3成功
			isConnect = 1, //是否接上（显示接入时长）0未接上 1接上 
			statusClasses = ["text-error", "text-warning", "text-success"];

		return function (data) {
			var obj = data.wanInfo[wanIndex - 1];
			//如果当前连接方式不是所选方式，不更新
			if (obj.wanType != $("#netWanType").val()) {
				netInfo.ajaxInterval.stopUpdate();
				return;
			}
			clearTimeout(netInfo.time);

			netInfo.currentWanType = obj["wanType"];

			netInfo.currentDnsType = obj["dnsAuto"];
			netInfo.currentVpnType = obj["vpnWanType"];
			netInfo.clientFlag = obj["vpnClient"];
			netInfo.dns1 = obj["dns1"];
			netInfo.dns2 = obj["dns2"];

			//联网状态
			$("#connectStatus").html(statusTxtObj[parseInt(obj["connectStatus"].substr(obj["connectStatus"].length - 4), 10) + ""]);

			statusType = parseInt(obj["connectStatus"].charAt(1), 10);
			$("#connectStatus").attr("class", statusClasses[statusType - 1]);
			$("#connectStatusWrap").removeClass("none");

			//联网时长
			isConnect = parseInt(obj["connectStatus"].charAt(2), 10);
			$("#connectTime").html(formatSeconds(obj["connectTime"]));
			setTimeout(function () {
				$("#connectTime").html(formatSeconds(parseInt(obj["connectTime"], 10) + 1))
			}, 1000);
			if (isConnect == 1) {
				$("#connect_time").removeClass("none");
				if (!netInfo["wanBtnChange"]) {
					$("#wan_submit").val(_("Disconnect"));
				}
			} else {
				$("#connect_time").addClass("none");
				$("#wan_submit").val(_("Connect"));
			}
			netInfo.hasConnTime = (isConnect == 1 ? true : false);

			//状态码第一个决定按钮是连接还是断开
			netInfo.isConnect = (parseInt(obj["connectStatus"].charAt(0)) == 1 ? true : false);

			//pptp客户端开启
			if (netInfo.clientFlag === "1") {
				if (($('#netWanType').val() === "3") || ($('#netWanType').val() === "4")) {
					$(".select-tab").html(_("Changing the settings will disable the VPN function."));
				} else {
					$(".select-tab").html("");
				}
			} else {
				$(".select-tab").html("");
			}

			netInfo.changeWanType();
		}
	})(),
	checkWanData: function () {
		var wan_type = $("#netWanType").val(),
			vpnWanType = $("[name='vpnWanType']:checked").val(),
			ip = $("#staticIp").val(),
			mask = $("#mask").val(),
			gw = $("#gateway").val(),
			dnsAuto = $("#dnsAuto").val(),
			dns1 = $("#dns1").val(),
			dns2 = $("#dns2").val(),
			ppoe_user = $("#adslUser").val(),
			ppoe_pwd = $("#adslPwd").val(),
			lanIp = $("#lanIp").val(),
			lanMask = $("#lanMask").val(),
			server = $("#vpnServer").val(),
			btn_val = $("#wan_submit").val(),
			wanOtherIp = netInfo.initObj.wanInfo[2 - wanIndex].wanIp,
			wanOtherMask = netInfo.initObj.wanInfo[2 - wanIndex].mask,
			wanMsg,
			wanOtherMsg;

		if (btn_val == _("Connect")) {
			/*PPTP/L2TP双接入时；若服务器地址为ip，且地址类型为静态。dns可为全空，且dns为空时，向后台传入dnsAuto "1",不为空，传入dnsAuto "0",除此以外的静态IP设置下，dns1不能为空*/
			if ((dns1 === "") && (!($("#dns1").is(":hidden")))) {
				//服务器为域名（不是ip）则首选dns不能为空。
				if ((((!$("#vpnServer").is(":hidden"))) && (!$.validate.valid.ip.all(server)) || wan_type === "5") && (vpnWanType === "0")) {} else {
					return _("Please specify a primary DNS server.");
				}
			}

			if ((wan_type == 1) || ((wan_type == 3) && (vpnWanType == 0)) || ((wan_type == 4) && (vpnWanType == 0)) || ((wan_type == 5) && (vpnWanType == 0))) { //static IP
				if (top.G.wanNum == "1") { //单WAN时

				} else {
					if (wanIndex == 1) {
						wanMsg = _("WAN2 IP Address");
						wanOtherMsg = _("WAN1 IP Address");
					} else {
						wanMsg = _("WAN1 IP Address");
						wanOtherMsg = _("WAN2 IP Address");
					}
				}
				if (top.G.wanNum == 2) {
					//判断IP地址重复
					if (ip == wanOtherIp) {
						return _("%s cannot be the same as that of %s.", [wanOtherMsg, wanMsg]);
					}
				}

				//同网段判断
				if (checkIpInSameSegment(ip, mask, lanIp, lanMask)) {
					return _("%s and %s (%s) must not be in the same network segment.", [_("WAN IP Address"), _("LAN IP Address"), lanIp]);
				}
				if (netInfo.initObj.wanInfo[wanIndex - 1].pptpSvrIp && checkIpInSameSegment(ip, mask, netInfo.initObj.wanInfo[wanIndex - 1].pptpSvrIp, netInfo.initObj.wanInfo[wanIndex - 1].pptpSvrMask)) {
					return _("%s and %s (%s) must not be in the same network segment.", [_("WAN IP Address"), _("PPTP Server IP Address"), netInfo.initObj.wanInfo[wanIndex - 1].pptpSvrIp]);
				}

				// 决策： 访客网络网段冲突有后台处理
				/*if (netInfo.initObj.guestIp && checkIpInSameSegment(ip, mask, netInfo.initObj.guestIp, netInfo.initObj.guestMask)) {
					return _("%s and %s (%s) must not be in the same network segment.", [_("WAN IP"),_("Guest Network IP"), netInfo.initObj.guestIp]);
				}*/


				if (!checkIpInSameSegment(ip, mask, gw, mask)) {
					return _("The gateway and the IP address must be in the same network segment.");
				}
				if (ip == gw) {
					return _("The IP address and gateway cannot be the same.");
				}
				if (ip == dns1) {
					return _("The IP address and primary DNS server cannot be the same.");
				}
				if (ip == dns2) {
					return _("The IP address and secondary DNS server cannot be the same.");
				}
				if ((dns1 === dns2) && (dns1 !== "")) {
					return _("The primary DNS server and secondary DNS server cannot be the same.");
				}

				var mask_arry = mask.split("."),
					ip_arry = ip.split("."),
					mask_arry2 = [],
					maskk,
					netIndex = 0,
					netIndexl = 0,
					bIndex = 0;
				if (ip_arry[0] == 127) {
					return _("The IP address cannot begin with 127.");
				}
				if (ip_arry[0] == 0 || ip_arry[0] >= 224) {
					return _("Incorrect IP address.");
				}

				for (var i = 0; i < 4; i++) { // IP & mask
					if ((ip_arry[i] & mask_arry[i]) == 0) {
						netIndexl += 0;
					} else {
						netIndexl += 1;
					}
				}

				for (var i = 0; i < mask_arry.length; i++) {
					maskk = 255 - parseInt(mask_arry[i], 10);
					mask_arry2.push(maskk);
				}
				for (var k = 0; k < 4; k++) { // ip & 255-mask
					if ((ip_arry[k] & mask_arry2[k]) == 0) {
						netIndex += 0;
					} else {
						netIndex += 1;
					}
				}
				if (netIndex == 0 || netIndexl == 0) {
					return _("The IP address must not indicate a network segment.");
				}
				for (var j = 0; j < 4; j++) { // ip | mask
					if ((ip_arry[j] | mask_arry[j]) == 255) {
						bIndex += 0;
					} else {
						bIndex += 1;
					}
				}

				if (bIndex == 0) {
					return _("The IP address cannot be a broadcast IP address.");
				}

			} else if (wan_type == 2) { //pppoe
				if (ppoe_user == "" || ppoe_pwd == "") {
					return _("Please enter your ISP user name and password.");
				}
				/*if (netInfo.initObj.vpnClient == "1" && netInfo.initObj.vpnClientUser == ppoe_user) {
					return _("The PPPoE user name cannot be the same as the PPTP/L2TP client user name.");
				}*/
			}

			if ((wan_type === "3") || (wan_type === "4")) {
				//同网段判断
				if (checkIpInSameSegment(server, lanMask, lanIp, lanMask)) {
					return _("%s and %s (%s) must not be in the same network segment.", [_("Server IP Address"), _("LAN IP Address"), lanIp]);
				}
			}

			//手动DNS时不能DNS1与DNS2不能相同
			if ((dnsAuto == "0") && (dns1 == dns2) && (dns1 != "")) {
				return _("The primary DNS server and secondary DNS server cannot be the same.");
			}
		}
	},

	callback: function (str) {
		if (!top.isTimeout(str)) {
			return;
		}

		var resultObj = $.parseJSON(str),
			num = resultObj.errCode,
			sleep_time = resultObj.sleep_time,
			isVpn = (sleep_time > 10 ? true : false),
			waitTime = -1, //连接或断开操作成功之后需要等待的时间
			minTime = 4; //连接或断开操作至少要花费的时间，

		if (num == 0) {
			showSaveMsg(num);
			$("#wan_submit").blur();
			$("#wan_submit")[0].disabled = false;
			$("#netWanType").prop("disabled", false);
			netInfo.saving = false;
			pageModel.getData();
		} else {
			showSaveMsg(num);
		}
	},

	changeWanType: function () {
		var wan_type = $("#netWanType").val();
		/* btnTxts = [_("Connect"), _("Disconnect")],
		 btnTxt = "";*/
		wanTypeSelect(wan_type);

		if (netInfo.currentWanType == wan_type) {
			$("#connect_message").removeClass("none");
			netInfo.ajaxInterval.startUpdate();
		} else {
			$("#connect_message").addClass("none");
			// btnTxt = btnTxts[0];
			netInfo.ajaxInterval.stopUpdate();
		}



		top.initIframeHeight();
	},

	changeVpnType: function () {
		var wan_type = $("#netWanType").val();
		if (netInfo.currentWanType == wan_type) {
			if ($(this).val() !== netInfo.currentVpnType) {
				//$("#wan_submit").val(btnTxts[0]);
				$("#connect_message").addClass("none");
				netInfo.ajaxInterval.stopUpdate();
			} else {
				$("#connect_message").removeClass("none");
				netInfo.ajaxInterval.startUpdate();
			}
		} else {
			$("#connect_message").addClass("none");
		}

		if ($(this).val() === "0") {
			$("#static_ip").removeClass("none");
			$("#dnsContainer").removeClass("none");
			$("#dnsType").addClass("none");
		} else {
			$("#dnsType").removeClass("none").val("1");
			$("#dnsContainer").addClass("none");
			$("#static_ip").addClass("none");
			if ($("#dnsAuto").val() == "0") { //手动时切回自动
				$('#dnsAuto').val('1');
			}
		}
		top.initIframeHeight();
	},

	changeDnsAuto: function () {
		var wan_type = $("#netWanType").val();
		if (netInfo.currentWanType == wan_type) {
			if ($("[name='vpnWanType']:checked").val() != netInfo.currentVpnType) {
				//$("#wan_submit").val(btnTxts[0]);
				$("#connect_message").addClass("none");
				netInfo.ajaxInterval.stopUpdate();
			} else {
				if ($(this).val() !== netInfo.currentDnsType) {
					$("#connect_message").addClass("none");
					netInfo.ajaxInterval.stopUpdate();
				} else {
					$("#connect_message").removeClass("none");
					netInfo.ajaxInterval.startUpdate();
				}
			}
		} else {
			$("#connect_message").addClass("none");
		}

		if ($(this).val() === "1") {
			$("#dnsContainer").addClass("none");
		} else {
			$("#dnsContainer").removeClass("none");
		}
		top.initIframeHeight();
	}
};

var wanInfo;
var pageview = R.pageView({ //页面初始化
	init: initPage
}); //page view

function initPage() {
	top.loginOut();
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
}

var pageModel = R.pageModel({
	getUrl: "goform/getWanParameters",
	setUrl: "goform/WanParameterSetting",
	translateData: function (data) {
		var newData = {};
		newData.wanSet = data;
		return newData;
	},
	submitData: function () {
		$("#wan_submit")[0].disabled = true;
		$("#netWanType").prop("disabled", true);
		netInfo.saving = true;
	},
	afterSubmit: function (str) { //提交数据回调
		netInfo.callback(str);
	}
});

/*****************/
var viewWanInfo = R.moduleView({
	checkData: netInfo.checkWanData,
	initEvent: initWanEvent
});

var modelWanInfo = R.moduleModel({
	initData: initWan,
	getSubmitData: function () {

		var subData = getSubData();
		subData = subData.replace("netWanType", "wanType");
		var dns1 = $("#dns1").val(),
			btn_val = $("#wan_submit").val(),
			wanModule = ["", "wan1", "wan2"];

		/*PPTP/L2TP双接入时；若服务器地址为ip，且地址类型为静态。dns可为全空，且dns为空时，向后台传入dnsAuto "1",不为空，传入dnsAuto "0",除此以外的静态IP设置下，dns1不能为空*/
		if (btn_val == _("Connect")) {
			if (!($("#dns1").is(":hidden"))) {
				if (dns1 === "") {
					subData = subData.replace("dnsAuto=0", "dnsAuto=1");
				} else {
					subData = subData.replace("dnsAuto=1", "dnsAuto=0");
				}
			}
		} else {
			subData = "action=disconnect";
		}

		subData += ("&module=" + wanModule[wanIndex]);
		return subData;
	}
});

R.module("wanSet", viewWanInfo, modelWanInfo);

function getSubData() {
	var dataObj = $("#internet-form").serializeArray(),
		len = dataObj.length,
		i = 0,
		dataStr = "",
		wanArr = ["", "", "2"];
	for (i = 0; i < len; i++) {
		dataStr += dataObj[i].name + wanArr[wanIndex] + "=" + encodeURIComponent(dataObj[i].value) + "&";
	}
	dataStr = dataStr.replace(/(&)$/, "");
	return dataStr;
}

function initWanEvent() {
	$("#netWanType").on("change", netInfo.changeWanType);
	$("[name='vpnWanType']").on("click", netInfo.changeVpnType);
	$('#dnsAuto').on('change', netInfo.changeDnsAuto);

	$("#internet-form").delegate("input,select", "change.re", function () {
		netInfo.wanBtnChange = true;
		$("#wan_submit").val(_("Connect"));
	});

	$("#wan_submit").on("click", function () {
		if (!this.disabled)
			G.validate.checkAll();
	});

	$.validate.valid.ppoe = {
		all: function (str) {
			var ret = this.specific(str);

			if (ret) {
				return ret;
			}
		},
		specific: function (str) {
			var ret = str;
			var rel = /[^\x00-\x80]|[~;'&"%\s]/;
			if (rel.test(str)) {
				return _("Can't contain ~;'&\"% and space and Chinese character.");
			}
		}
	};

	$.validate.valid.wanmask = {
		all: function (str) {
			var rel = /^(255|254|252|248|240|224|192|128)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(255|254|252|248|240|224|192|128|0))$/;
			if (!rel.test(str)) {
				return _("Please enter a valid subnet mask.");
			}

		}
	};

	$("#staticIp,#mask,#gateway,#dns1,#dns2").inputCorrect("ip");
	G.validate = $.validate({
		custom: function () {},

		success: function () {
			wanInfo.submit();
		},

		error: function (msg) {
			return;
		}
	});

	$("#gateway").attr("data-options", '{"type":"ip","msg":"' + _("Please enter a correct gateway IP address.") + '"}');
	$("#dns1").attr("data-options", '{"type":"ip","msg":"' + _("Please enter the IP address of the primary DNS server.") + '"}');
	$("#dns2").attr("data-options", '{"type":"ip","msg":"' + _("Please enter the IP address of the secondary DNS server.") + '"}');
}

function initWan(obj) {
	var browserLang = getBrowserLang();
	$("#loadingTip").addClass("none");
	$("#netWrap").removeClass("none");
	//定时刷新器
	netInfo.initObj = obj;
	if (!netInfo.ajaxInterval) {
		netInfo.ajaxInterval = new AjaxInterval({
			url: "goform/getWanParameters",
			successFun: function (data) {
				netInfo.setValue(data);
			},
			gapTime: 5000
		});
	} else {
		netInfo.ajaxInterval.startUpdate();
	}

	//client+ap 不允许配置外网设置，隐藏配置内容
	if (obj.wl_mode == "apclient") {
		$("#internet-form").addClass("none");
		$("#notAllowTip").removeClass("none");
	} else {
		$("#internet-form").removeClass("none");
		$("#notAllowTip").addClass("none");
	}

	//wisp下没有pppoe拨号
	//
	var wanOptStr = "";
	if (obj.wl_mode !== "wisp") {
		wanOptStr += '<option value="2">' + _("PPPoE") + '</option>';
	}
	wanOptStr += '<option value="0">' + _("Dynamic IP Address") + '</option><option value="1">' + _("Static IP Address") + '</option>';
	if (obj.wl_mode !== "wisp") { //wisp 隐藏pppoe选择框

		if (browserLang === "RU" && wanIndex == 1) {
			wanOptStr += '<option value="3">' + _("Russia PPTP") + '</option><option value="4">' + _("Russia L2TP") + '</option><option value="5">' + _("Russia PPPoE");
		}
	}

	$("#netWanType").html(wanOptStr);
	$("#netWanType").val(obj.wanInfo[wanIndex - 1].wanType);
	inputValue(obj.wanInfo[wanIndex - 1]);
	$('#adslUser').addPlaceholder(_("Enter the user name from your ISP."));
	$('#adslPwd').initPassword(_("Enter the password from your ISP."), false, false);
	$('#vpnPwd').initPassword(_(""), false, false);
	netInfo.setValue(obj);
	netInfo.changeWanType();
	$("#lanIp").val(obj.lanIp);
	$("#lanMask").val(obj.lanMask);
}

/***********************************************************/

/************/
window.onload = function () {
	wanInfo = R.page(pageview, pageModel);
};
/*******************************************/

function wanTypeSelect(wan_type) {
	/*    if(wan_type === netInfo.currentWanType) {
	        $('#dns1').val(netInfo.dns1);
	        $('#dns2').val(netInfo.dns2);
	    } else {
	        $('#dns1').val("");
	        $('#dns2').val("");
	    }*/
	//pptp客户端开启
	if (netInfo.clientFlag === "1") {
		if ((wan_type === "3") || (wan_type === "4")) {
			$(".select-tab").html(_("Changing the settings will disable the VPN function."));
		} else {
			$(".select-tab").html("");
		}
	} else {
		$(".select-tab").html("");
	}

	switch (parseInt(wan_type)) {
	case 0:
		{
			$("#ppoe_set").addClass("none");
			$("#double_access").addClass("none");
			$("#dnsType").removeClass("none");
			$("#static_ip").addClass("none");
			if (netInfo.currentWanType === "0") {
				if (netInfo.currentDnsType === "1") {
					$("#dnsAuto").val("1");
					$("#dnsContainer").addClass("none");
				} else {
					$("#dnsAuto").val("0");
					$("#dnsContainer").removeClass("none");
				}
			} else {
				$('#dnsAuto').val("1");
				$("#dnsContainer").addClass("none");
			}

			break;
		}
	case 1:
		{
			$("#ppoe_set").addClass("none");
			$("#double_access").addClass("none");
			$("#dnsType").addClass("none");
			$("#static_ip").removeClass("none");
			$("#dnsContainer").removeClass("none");

			break;
		}
	case 2:
		$("#ppoe_set").removeClass("none");
		$("#double_access").addClass("none");
		$("#dnsType").removeClass("none");
		$("#static_ip").addClass("none");
		if (netInfo.currentWanType === "2") {
			if (netInfo.currentDnsType === "1") {
				$("#dnsAuto").val("1");
				$("#dnsContainer").addClass("none");
			} else {
				$("#dnsAuto").val("0");
				$("#dnsContainer").removeClass("none");
			}
		} else {
			$('#dnsAuto').val("1");
			$("#dnsContainer").addClass("none");
		}
		break;
	case 3:
		{
			$("#ppoe_set").addClass("none");
			$("#double_access").removeClass("none");
			$("#double_access #serverInfo").removeClass("none");

			if (netInfo.currentWanType === "3") {
				if (netInfo.currentVpnType === "1") {
					$('[name="vpnWanType"]:eq(0)').prop("checked", true);
					$("#dnsType").removeClass("none");
					$("#static_ip").addClass("none");
					if (netInfo.currentDnsType === "1") {
						$('#dnsAuto').val("1");
						$("#dnsContainer").addClass("none");
					} else {
						$('#dnsAuto').val("0");
						$("#dnsContainer").removeClass("none");
					}
				} else {
					$('[name="vpnWanType"]:eq(1)').prop("checked", true);
					$("#dnsType").addClass("none");
					$("#static_ip").removeClass("none");
					$("#dnsContainer").removeClass("none");
				}
			} else {
				$("#dnsType").removeClass("none");
				$('#dnsAuto').val("1");
				$('[name="vpnWanType"]:eq(0)').prop("checked", true);
				$("#static_ip").addClass("none");
				$("#dnsContainer").addClass("none");
			}

			break;
		}
	case 4:
		{
			$("#ppoe_set").addClass("none");
			$("#double_access").removeClass("none");
			$("#double_access #serverInfo").removeClass("none");

			if (netInfo.currentWanType === "4") {
				if (netInfo.currentVpnType === "1") {
					$('[name="vpnWanType"]:eq(0)').prop("checked", true);
					$("#dnsType").removeClass("none");
					$("#static_ip").addClass("none");
					if (netInfo.currentDnsType === "1") {
						$('#dnsAuto').val("1");
						$("#dnsContainer").addClass("none");
					} else {
						$('#dnsAuto').val("0");
						$("#dnsContainer").removeClass("none");
					}
				} else {
					$('[name="vpnWanType"]:eq(1)').prop("checked", true);
					$("#dnsType").addClass("none");
					$("#static_ip").removeClass("none");
					$("#dnsContainer").removeClass("none");
				}
			} else {
				$("#dnsType").removeClass("none");
				$('#dnsAuto').val("1");
				$('[name="vpnWanType"]:eq(0)').prop("checked", true);
				$("#static_ip").addClass("none");
				$("#dnsContainer").addClass("none");
			}

			break;
		}
	case 5:
		{
			$("#ppoe_set").removeClass("none");
			$("#double_access").removeClass("none");
			$("#double_access #serverInfo").addClass("none");

			if (netInfo.currentWanType === "5") {
				if (netInfo.currentVpnType === "1") {
					$('[name="vpnWanType"]:eq(0)').prop("checked", true);
					$("#dnsType").removeClass("none");
					$("#static_ip").addClass("none");
					if (netInfo.currentDnsType === "1") {
						$('#dnsAuto').val("1");
						$("#dnsContainer").addClass("none");
					} else {
						$('#dnsAuto').val("0");
						$("#dnsContainer").removeClass("none");
					}
				} else {
					$('[name="vpnWanType"]:eq(1)').prop("checked", true);
					$("#dnsType").addClass("none");
					$("#static_ip").removeClass("none");
					$("#dnsContainer").removeClass("none");
				}
			} else {
				$("#dnsType").removeClass("none");
				$('#dnsAuto').val("1");
				$('[name="vpnWanType"]:eq(0)').prop("checked", true);
				$("#static_ip").addClass("none");
				$("#dnsContainer").addClass("none");
			}
			break;
		}
	default:
		break;
	}
}