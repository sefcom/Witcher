var G = {},
	initObj = null;

var lanInfo;
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
	getUrl: "goform/AdvGetLanIp",
	setUrl: "goform/AdvSetLanip",
	translateData: function (data) {
		var newData = {};
		newData.lan = data;
		return newData;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initLanEvent
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var subData,
			subObj = {};

		subObj = {
			"lanIp": $("#lanIp").val(),
			"lanMask": $("#lanMask").val(),
			"dhcpEn": $("#dhcpEn").val(),
			"startIp": $("#ipNet").html() + $("#startIp").val(),
			"endIp": $("#ipNet").html() + $("#endIp").val(),
			"leaseTime": $("#leaseTime").val(),
			"lanDnsAuto": $("#lanDnsAuto").val(),
			"lanDns1": $("#lanDns1").val(),
			"lanDns2": $("#lanDns2").val()
		};

		if ($("#dhcpEn").val() == 0) {
			subObj.startIp = initObj.startIp;
			subObj.endIp = initObj.endIp;
		}
		subData = objTostring(subObj);
		return subData;
	}
});

//模块注册
R.module("lan", view, moduleModel);

function initLanEvent() {

	$("#dhcpEn").on("click", function () {
		if (initObj.wl_mode == "apclient") {
			showErrMsg("msg-err", _("The DHCP server cannot be enabled/disabled because the WiFi network is in Client+AP mode."));
			return;
		}
		changeDhcpEn();
	});

	$('#lanDnsAuto').on('click', changeDnsEn);

	$("#startIp,#endIp").inputCorrect("num");
	$("#lanIp, #lanMask, #lanDns1, #lanDns2").inputCorrect("ip");

	$("#lanIp, #lanMask").on("blur.range", function () {
		if (!$.validate.valid.lanip.all($("#lanIp").val()) && !$.validate.valid.mask.all($("#lanMask").val())) {
			changeDhcpRange();
		}
	});
	$("#lanIp").on("blur", function () {
		var ipArr = this.value.split('.');

		if ((/^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/).test(this.value)) {
			$("#ipNet").html(ipArr[0] + "." + ipArr[1] + "." + ipArr[2] + ".");
		}
	});

	$.validate.valid.lanip = {
		all: function (str) {
			var ipArr = str.split('.'),
				ret;

			$.each(ipArr, function (i, ipPart) {
				ipArr[i] = parseInt(ipPart, 10);
			});
			str = ipArr.join(".");

			ret = this.specific(str);

			if (ret) {
				return ret;
			}
			if (!(/^([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){2}([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/).test(str)) {
				return (_("Please enter a valid IP address."));
			}
		},

		specific: function (str) {
			var ipArr = str.split('.'),
				ipHead = ipArr[0];

			if (ipArr[0] === '127') {
				return _("An IP address that begins with 127 is a loopback IP address. Adopt another value ranging from 1 through 223.");
			}
			if (ipArr[0] > 223) {
				return _("An IP address that begins with %s is invalid. Adopt another value ranging from 1 through 223.", [ipHead]);
			}
		}
	};
	checkData();
}

function changeDhcpEn() {
	var className = $("#dhcpEn").attr("class");
	if (className == "btn-off") {
		$("#dhcpEn").attr("class", "btn-on");
		$("#dhcpEn").val(1);
		$("#dhcp_set").removeClass("none");
	} else {
		$("#dhcpEn").attr("class", "btn-off");
		$("#dhcpEn").val(0);
		$("#dhcp_set").addClass("none");
	}
	top.initIframeHeight();
}

function changeDnsEn() {
	var className = $("#lanDnsAuto").attr("class");
	if (className == "btn-off") {
		$("#lanDnsAuto").attr("class", "btn-on");
		$("#lanDnsAuto").val(0);
		$("#dns_set").removeClass("none");
	} else {
		$("#lanDnsAuto").attr("class", "btn-off");
		$("#lanDnsAuto").val(1);
		$("#dns_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {
			var wanIp = G.data.wanIp,
				wanMask = G.data.wanMask,
				lanIp = $("#lanIp").val(),
				lanMask = $("#lanMask").val(),
				serverIp = G.data.serverIp,
				vlan2Ip = G.data.vlan2Ip,
				vlan2Mask = G.data.vlan2Mask,
				dns1 = $("#lanDns1").val(),
				dns2 = $("#lanDns2").val(),
				remoteIp = G.data.remoteIp,
				pptpSvrIp = G.data.pptpSvrIp,
				vpnClientIp = G.data.vpnCliIp,
				guestIp = G.data.guestIp,
				wanIp2 = G.data.wanIp2,
				wanMask2 = G.data.wanMask2,
				startIp = $("#ipNet").html() + $("#startIp").val(),
				endIp = $("#ipNet").html() + $("#endIp").val(),
				wanStr = "";


			/*if (parseInt(lanIp.split(".")[0], 10) < 192) {
				return _("Only Class-C IP addresses (192.X.X.X-223.X.X.X) are allowed.");
			}*/

			var errMsg = checkIsVoildIpMask(lanIp, lanMask);

			if (errMsg) {
				return errMsg;
			}
			if (wanIp != "" && checkIpInSameSegment(wanIp, wanMask, lanIp, lanMask)) {
				if (top.G.wanNum == 1) {
					wanStr = _("WAN IP Address");
				} else {
					wanStr = _("WAN1 IP Address");
				}
				return _("%s and %s (%s) must not be in the same network segment.", [_("LAN IP Address"), wanStr, wanIp]);
			}

			if (top.G.wanNum === 2 && wanIp2 != "" && checkIpInSameSegment(wanIp2, wanMask2, lanIp, lanMask)) {
				return _("%s and %s (%s) must not be in the same network segment.", [_("LAN IP Address"), _("WAN2 IP Address"), wanIp2]);
			}
			//SE决策：远程WEB管理IP不需要判断与其他功能关系
			/*if (remoteIp != "" && checkIpInSameSegment(remoteIp, lanMask, lanIp, lanMask)) {
				return _("%s and %s (%s) must not be in the same network segment.", [_("LAN IP Address"), _("Remote IP Address"), remoteIp]);
			}*/
			if (serverIp != "" && checkIpInSameSegment(serverIp, lanMask, lanIp, lanMask)) {
				return _("The LAN IP address is in conflict with the IP address (%s) of the server connected to a WAN port. Please use another IP address.", [serverIp]);
			}
			if (vlan2Ip != "" && checkIpInSameSegment(vlan2Ip, vlan2Mask, lanIp, lanMask)) {
				return _("The LAN IP address is in conflict with the IP address (%s) of the WAN port connected to a server. Please make a change.", [vlan2Ip]);
			}
			// 决策： 访客网络网段冲突有后台处理
			/*if (guestIp != "" && checkIpInSameSegment(guestIp, G.data.guestMask, lanIp, lanMask)) {
				return _("%s and %s (%s) must not be in the same network segment.", [_("LAN IP"),_("Guest Network IP"),guestIp]);
			}*/
			if (pptpSvrIp != "" && checkIpInSameSegment(pptpSvrIp, G.data.pptpSvrMask, lanIp, lanMask)) {
				return _("%s and %s (%s) must not be in the same network segment.", [_("LAN IP Address"), _("PPTP Server IP Address"), pptpSvrIp]);
			}

			if ($("#dhcpEn").val() == "1") {
				if (parseInt($("#startIp").val(), 10) > parseInt($("#endIp").val(), 10)) {
					return _("The end IP address must be greater than the start IP address.");
				}

				if (!checkIpInSameSegment(startIp, lanMask, lanIp, lanMask)) {
					return _("%s and %s (%s) must be in the same network segment.", [_("Start IP Address"), _("LAN IP Address"), lanIp]);
				}

				if (!checkIpInSameSegment(endIp, lanMask, lanIp, lanMask)) {
					return _("%s and %s (%s) must be in the same network segment.", [_("End IP Address"), _("LAN IP Address"), lanIp]);
				}
			}

			if ((dns1 === dns2) && ($("#lanDnsAuto").hasClass("btn-on")) && (dns1 !== "")) {
				return _("The primary DNS server and secondary DNS server cannot be the same.");
			}

			if (checkIpInSameSegment(lanIp, lanMask, vpnClientIp, lanMask)) {
				return _("The LAN IP address and PPTP/L2TP client IP address (%s) cannot be in the same network segment.", [vpnClientIp]);
			}
		},

		success: function () {

			lanInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				$("#msg-err").html(msg);
				setTimeout(function () {
					$("#msg-err").html("&nbsp;");
				}, 3000)
			}
		}
	});
}

//dhcp客户端地址池
function changeDhcpRange() {
	var lanMask = $("#lanMask").val(),
		lanip = $("#lanIp").val(),
		maskCount = Number(lanMask.replace(/[\.]/g, '')),
		netIndex,
		maskEnd,
		startIpEnd,
		endIpEnd,
		lanEnd;
	if (maskCount >= 2552552550) {
		lanEnd = lanip.split(".")[3];
		maskEnd = lanMask.split(".")[3];
		netIndex = (lanEnd & maskEnd); //获取网络号

		startIpEnd = netIndex + 1; //起始IP为网络号 +1
		endIpEnd = startIpEnd + (255 - maskEnd - 2); //结束IP为网络主机个数-2
	} else {
		startIpEnd = 1;
		endIpEnd = 254;
	}
	$("#startIp").val(startIpEnd);
	$("#endIp").val(endIpEnd);
}

function initValue(obj) {
	initObj = obj;
	inputValue(obj);
	top.$(".main-dailog").removeClass("none");
	top.$("iframe").removeClass("none");
	top.$(".loadding-page").addClass("none");
	G.data = obj;
	var Msg = top.location.search.substring(1);
	if (Msg == "1") {
		$("#msg-err").html(_("The LAN IP address is in conflict with the WAN IP address.  Please change the LAN IP address. Otherwise, you cannot access the internet."));
	}
	$("#lanIp").val(obj.lanIp);
	var ipArry = [];
	ipArry = obj.lanIp.split(".");
	if (obj.dhcpEn == "1") {
		$("#dhcpEn").attr("class", "btn-on");
		$("#dhcpEn").val(1);
		$("#dhcp_set").removeClass("none");
	} else {
		$("#dhcpEn").attr("class", "btn-off");
		$("#dhcpEn").val(0);
		$("#dhcp_set").addClass("none");
	}

	if (obj.lanDnsAuto === "0") {
		$("#lanDnsAuto").attr("class", "btn-on");
		$("#lanDnsAuto").val(0);
		$("#dns_set").removeClass("none");
	} else {
		$("#lanDnsAuto").attr("class", "btn-off");
		$("#lanDnsAuto").val(1);
		$("#dns_set").addClass("none");
	}

	$("#ipNet").html(ipArry[0] + "." + ipArry[1] + "." + ipArry[2] + ".");
	$("#startIp").val(obj.startIp.split(".")[3]);
	$("#endIp").val(obj.endIp.split(".")[3]);
	top.initIframeHeight();
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	var changeFlag = false;
	if (initObj.lanIp != $("#lanIp").val()) {
		changeFlag = true;
	}
	top.showSaveMsg(num, _("Saving..."), $("#lanIp").val(), changeFlag);
}

window.onload = function () {
	lanInfo = R.page(pageview, pageModel);
};