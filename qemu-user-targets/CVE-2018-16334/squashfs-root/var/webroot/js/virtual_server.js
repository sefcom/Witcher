var G = {};
var listMax = 0;
var lanIp = "";
var lanMask = "";

var virtualInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
		/*$("#submit").on("click", function () {
			virtualInfo.submit();
		});*/
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetVirtualServerCfg",
	setUrl: "goform/SetVirtualServerCfg",
	translateData: function (data) {
		var newData = {};
		newData.virtual = data;
		return newData;
	},
	beforeSubmit: function () {
		$("#msg-err").html("&nbsp;");
		return true;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initEvent
})
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		var trArry = $("#portBody").children(),
			len = trArry.length,
			i = 0,
			data = "";
		for (i = 0; i < len; i++) {
			if (G.action == "delete" && $(trArry[i]).attr("data-target") == "delete") {
				continue;
			}
			data += $(trArry[i]).children().eq(0).html() + ",";
			data += $(trArry[i]).children().eq(1).html() + ",";
			data += $(trArry[i]).children().eq(2).html() + ",";
			if ($(trArry[i]).children().eq(3).text() === "TCP&UDP") {
				data += "0";
			} else {
				data += protocolMsg[$(trArry[i]).children().eq(3).html()];
			}
			data += "~";
		}
		data = data.replace(/[~]$/, "");

		if (G.action == "add") {
			if (data != "") {
				data += "~";
			}
			data += $("#ip").val() + "," + $("#inPort")[0].val() + "," + $("#outPort").val() + ",";
			if ($("#protocol").val() === "TCP&UDP") {
				data += "0";
			} else {
				data += protocolMsg[$("#protocol").val()];
			}
		}
		data = "list=" + data;
		return data;
	}
});

//模块注册
R.module("virtual", view, moduleModel);

function initEvent() {


	selectObj.initVal = "21";
	$("#inPort").toSelect(selectObj);

	$("#ip").inputCorrect("ip");
	$("#inPort input, #outPort").inputCorrect("num");
	$("#inPort input").attr("maxlength", 5);
	checkData();
	top.initIframeHeight();
	$(".add").on("click", function () {
		G.validate.checkAll();
	});
	$("#portList").delegate(".del", "click", function () {
		//G.delTrElem = $(this).parent().parent();
		$(this).parent().parent().attr("data-target", "delete");
		//if (confirm(_("Do you want to continue?"))) {
		G.action = "delete";
		virtualInfo.submit();
		//}
	});
	$(".input-append ul").on("click", function (e) {
		$("#outPort")[0].value = ($(this).parents(".input-append").find("input")[0].value || "");
	});
}

function addList() {
	var str = "";

	str += "<tr>";
	str += "<td id='ip" + (listMax + 1) + "'>" + $("#ip").val() + "</td>";
	str += "<td alt='inPort' id='inPort" + (listMax + 1) + "'>" + $("#inPort")[0].val() + "</td>";
	str += "<td alt='outPort' id='outPort" + (listMax + 1) + "'>" + $("#outPort").val() + "</td>";
	str += "<td  alt='protocol' id='protocol" + (listMax + 1) + "'>" + $("#protocol").val() + "</td>";
	str += "<td><span class='delete del' title='" + _("Delete") + "'></span></td></tr>";

	$("#portBody").append(str);
	$("#ip").val("");
	$("#inPort").val("");
	$("#outPort").val("");
	listMax++;
	top.initIframeHeight();
};

function delList() {
	$("#portBody").find("[data-target='delete']").remove();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {
			var inPort = "",
				outPort = "",
				ip = "",
				str = "",
				i = 0,
				errMsg;

			ip = $("#ip").val();
			inPort = $("#inPort")[0].val();
			outPort = $("#outPort").val();

			if ($("#portBody").children().length >= 16) {
				return _("Only a maximum of %s rules are allowed.", [16]);
			}

			if ($.validate.valid.ip.all(ip)) {
				$("#ip").focus();
				return $.validate.valid.ip.all(ip);
			}
			errMsg = checkIsVoildIpMask(ip, lanMask);
			if (errMsg) {
				$("#ip").focus();
				return errMsg;
			}

			if (!checkIpInSameSegment(ip, lanMask, lanIp, lanMask)) {
				$("#ip").focus();
				return _("The LAN IP address must be in the same network segment as the login IP address (%s) of the router.", [lanIp]);
			}

			if (ip === lanIp) {
				$("#ip").focus();
				return _("The LAN IP address must not be the same as the login IP address (%s) of the router.", [lanIp]);
			}

			if (!(/^[0-9]{1,}$/).test(inPort)) {
				$("#inPort").find(".input-box").focus();
				return _("The LAN port number must consist of digits.");
			} else {
				if (parseInt(inPort, 10) > 65535 || parseInt(inPort, 10) < 1) {
					$("#inPort").find(".input-box").focus();
					return _("LAN port range: 1-65535");
				}
			}

			if (!(/^[0-9]{1,}$/).test(outPort)) {
				$("#outPort").focus();
				return _("The WAN port must consist of digits.");
			} else {
				if (parseInt(outPort, 10) > 65535 || parseInt(outPort, 10) < 1) {
					$("#outPort").focus();
					return _("WAN port range: 1-65535");

				}
			}


			/*判断外网端口是否重复*/
			var outPort = $("#outPort").val(),
				outPortRepeat = false;

			$("#portBody tr").each(function () {
				var rowoutPort = $(this).find("td[alt=outPort]").html();

				if (rowoutPort == outPort) {
					outPortRepeat = true;
					return false;
				}
			});

			if (outPortRepeat) {
				return _("WAN port conflict. One WAN port can be mapped only once.")
			}

		},

		success: function () {
			G.action = "add";
			virtualInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				$("#msg-err").html(msg);
				setTimeout(function () {
					$("#msg-err").html("&nbsp;");
				}, 3000);
			}
			return;
		}
	});
}
var selectObj = {
	"initVal": "",
	"editable": "1",
	"size": "small",
	"options": [{
		"21": "21 (FTP)",
		"23": "23 (TELNET)",
		"25": "25 (SMTP)",
		"53": "53 (DNS)",
		"80": "80 (HTTP)",
		"110": "110 (pop3)",
		"1723": "1723 (PPTP)",
		"3389": _("3389 (remote desktop)"),
		"9000": "9000",
		".divider": ".divider",
		".hand-set": _("Manual")
	}]
};

var protocolReceiveMsg = {
	"0": "TCP&UDP",
	"1": "TCP",
	"2": "UDP"
};

function initValue(obj) {
	var list = obj.virtualList,
		i = 0,
		str = "";

	lanIp = obj.lanIp;
	lanMask = obj.lanMask;

	for (i = 0; i < list.length; i++) {
		str += "<tr>";
		str += "<td id='ip" + (i + 1) + "'>" + (list[i].ip || "") + "</td>";
		str += "<td alt='inPort' id='inPort" + (i + 1) + "'>" + (list[i].inPort || "") + "</td>";
		str += "<td alt='outPort' id='outPort" + (i + 1) + "'>" + (list[i].outPort || "") + "</td>";
		str += "<td id='protocol" + (i + 1) + "'>" + (protocolReceiveMsg[list[i].protocol] || "") + "</td>";
		str += "<td><span class='delete del' title='" + _("Delete") + "'></span></td></tr>";
	}
	listMax = list.length;
	$("#portBody").html(str);
	top.initIframeHeight();
	initTableHeight();
}

var protocolMsg = {
	"TCP": "1",
	"UDP": "2"
};

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;

	//top.showSaveMsg(num);
	if (num == 0) {
		if (G.action == "add") {
			addList();
		} else {
			delList();
		}
	}
}

window.onload = function () {
	virtualInfo = R.page(pageview, pageModel);
};