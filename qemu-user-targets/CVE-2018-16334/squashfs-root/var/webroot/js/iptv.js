var G = {};
var listMax = 0;
var initObj = {};

var iptvInfo;
var pageview = R.pageView({ //页面初始化
	init: function () {
		top.loginOut();
		top.$(".main-dailog").removeClass("none");
		top.$(".save-msg").addClass("none");
		$("#submit").on("click", function () {
			if (!this.disabled)
				G.validate.checkAll();
		});
	}
});
var pageModel = R.pageModel({
	getUrl: "goform/GetIPTVCfg",
	setUrl: "goform/SetIPTVCfg",
	translateData: function (data) {
		var newData = {};
		newData.iptv = data;
		return newData;
	},
	beforeSubmit: function () {
		var data,
			list = "",
			vlanId,
			vlanArry = $("#vlanBody").children(),
			len = vlanArry.length,
			i = 0;
		subObj = {};
		for (i = 0; i < len; i++) {
			list += $(vlanArry[i]).children().eq(1).find("input").val() + ",";
		}

		list = list.replace(/[,]$/, "");
		var $selectVlan = $("[name='selectVlan']:checked");
		if ($("#iptvType").val() == "none") {
			vlanId = "";
		} else if ($("#iptvType").val() == "manual") {
			vlanId = $selectVlan.parent().next().find("input").val();
		} else {
			list = "85,51";
			vlanId = $("[name='areaVlan']:checked").val();
		}

		subObj = {
			//"iptvEn": $("#iptvEn").val(),
			"stbEn": $("#stbEn").val(),
			"igmpEn": $("#igmpEn").val(),
			"iptvType": $("#iptvType").val(),
			"vlanId": vlanId,
			"list": list
		}
		if (subObj.stbEn == "0") {
			$.extend(subObj, {
				"iptvType": initObj.iptvType,
				"vlanId": initObj.vlanId,
				"list": initObj.list
			});
		}

		//是否要重启,只要stb相关数据改变了就重启
		G.reboot = false;

		if (initObj.stbEn != subObj.stbEn) {
			G.reboot = true;
		} else {
			if (initObj.iptvType != subObj.iptvType || initObj.vlanId != subObj.vlanId || initObj.list != subObj.list) {
				G.reboot = true;
			} else {
				G.reboot = false;
			}
		}

		if (G.reboot && !confirm(_("Please reboot the router after changing the IPTV settings. Do you want to reboot the router?"))) {
			return false;
		}

		return true;
	},
	afterSubmit: callback
});

/************************/
var view = R.moduleView({
	initEvent: initIptvEvent,
	checkData: function () {
		var $selectVlan = $("[name='selectVlan']:checked"),
			vlanId;
		if ($("#iptvType").val() == "none") {
			vlanId = "";
		} else if ($("#iptvType").val() == "manual") {
			vlanId = $selectVlan.parent().next().find("input").val();
		} else {
			list = "85,51";
			vlanId = $("[name='areaVlan']:checked").val();
		}

		if ($("#stbEn").val() == "1" && $("#iptvEn").val() == "1" && $("#iptvType").val() == "manual") {
			if (!vlanId) {
				return _("Please select a VLAN ID.");

			}
		}
	}
})

var subObj = {};
var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		return objTostring(subObj);;
	}
});

//模块注册
R.module("iptv", view, moduleModel);

function initIptvEvent() {

	$("#stbEn").on("click", function () {
		if (initObj.wl_mode == "ap")
			changeSTBEn();
	});

	$("#igmpEn").on("click", function () {
		if (initObj.wl_mode == "ap")
			changeIGMPEn();
	});

	$("#iptvType").on("change", changeType);
	$("#vlanList").delegate(".add", "click", addList);
	$("#vlanList").delegate(".del", "click", delList);

	checkData();

}

function addList() {
	var str = "";
	if ($("#vlanBody").children().length >= 8) {
		showErrMsg("msg-err", _("Only a maximum of %s VLANs are allowed.", [8]));
		return;
	}
	str += "<tr>";
	str += "<td class='fixed'><input type='radio' name='selectVlan'>" + "</td>";
	str += "<td class='fixed'><input type='text' id='vlanId" + (listMax + 1) + "' class='input-small' maxlength='4'>" + "</td>";
	str += "<td class='fixed'><span class='delete del' title='" + _("Delete") + "'></span></td>";
	str += "</tr>";
	$("#vlanBody").append(str);
	$("#vlanId" + (listMax + 1)).inputCorrect("num");
	listMax++;
	top.initIframeHeight();
}

function delList() {
	if ($(this).parents("tr").find("input[type='radio']").prop("checked")) {
		$("#vlanBody").find("input[type='radio']").eq(0).prop("checked", true);
	}
	$(this).parent().parent().remove();
	top.initIframeHeight();
}

function changeType() {
	if ($("#iptvType").val() == "none") {
		$("#vlanList").addClass("none");
		$("#area_set").addClass("none");
	} else if ($("#iptvType").val() == "manual") {
		$("#vlanList").removeClass("none");
		$("#area_set").addClass("none");
	} else {
		$("#vlanList").addClass("none");
		$("#area_set").removeClass("none");
	}
	top.initIframeHeight();
}

function changeIGMPEn() {
	var className = $("#igmpEn").attr("class");
	if (className == "btn-off") {
		$("#igmpEn").attr("class", "btn-on");
		$("#igmpEn").val(1);
	} else {
		$("#igmpEn").attr("class", "btn-off");
		$("#igmpEn").val(0);
	}
	top.initIframeHeight();
}

function changeSTBEn() {
	var className = $("#stbEn").attr("class");
	if (className == "btn-off") {
		$("#stbEn").attr("class", "btn-on");
		$("#stbEn").val(1);
		$("#vlan_set").removeClass("none");
	} else {
		$("#stbEn").attr("class", "btn-off");
		$("#stbEn").val(0);
		$("#vlan_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {
			var vlanId,
				vlanArry = $("#vlanBody").children(),
				len = vlanArry.length,
				i = 0;

			if ($("#stbEn").val() == "1" && $("#iptvType").val() == "manual") {
				var tVlanArr = [];
				for (i = 0; i < len; i++) {
					vlanId = $(vlanArry[i]).children().eq(1).find("input").val();
					if (!(/^[0-9]{1,}$/).test(vlanId)) {
						$(vlanArry[i]).children().eq(1).find("input").focus();
						return _("The VLAN ID must consist of only digits.");
					} else if (parseInt(vlanId, 10) > 4094 || parseInt(vlanId, 10) < 4) {
						$(vlanArry[i]).children().eq(1).find("input").focus();
						return _("The VLAN ID range is %s.", ["4-4094"]);
					} else if ($.inArray(vlanId, tVlanArr) != -1) {
						$(vlanArry[i]).children().eq(1).find("input").focus();
						return _("Duplicate VLAN IDs are not allowed.");
					}
					tVlanArr.push(vlanId);
				}
			}
		},

		success: function () {

			iptvInfo.submit();
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

	if (obj.wl_mode != "ap") {
		showErrMsg("msg-err", _("Please disable Wireless Repeating on the WiFi Settings page first."), true);
		$("#submit")[0].disabled = true;
	}

	$("#stbEn").attr("class", (obj.stbEn == "1" ? "btn-off" : "btn-on"));
	changeSTBEn();

	$("#igmpEn").attr("class", (obj.igmpEn == "1" ? "btn-off" : "btn-on"));
	changeIGMPEn();


	$("#iptvType").val(obj.iptvType);
	if ($("#iptvType").val() == "shanghai") {
		$("[name='areaVlan'][value='" + obj.vlanId + "']")[0].checked = true;
	}
	changeType();
	var vlanStr = "",
		vlanArry = obj.list.split(",") || "",
		checked = '',
		len = vlanArry.length,
		i = 0;
	if (len < 1) {
		len = 1;
	}
	for (i = 0; i < len; i++) {
		vlanStr += "<tr>";
		if (obj.vlanId == vlanArry[i]) {
			checked = "checked=true";
		} else {
			checked = "";
		}
		vlanStr += "<td class='fixed'><input type='radio' name='selectVlan' " + checked + ">" + "</td>";
		vlanStr += "<td class='fixed'><input alt='vlanIpt' type='text' id='vlanId" + i + "' class='input-small' maxlength='4' value='" + vlanArry[i] + "'>" + "</td>";
		if (i == 0) {
			vlanStr += "<td class='fixed'><input type='button' class='btn add btn-small btn-action' value='" + _("+New") + "'></td>";
		} else {
			vlanStr += "<td class='fixed'><span class='delete del' title='" + _("Delete") + "'></span></td>";
		}
		vlanStr += "</tr>";
	}
	$("#vlanBody").html(vlanStr);
	$("#vlanBody input[alt=vlanIpt]").inputCorrect("num");
	listMax = len;

	top.initIframeHeight();
}

function callback(str) {
	var reboot = G.reboot;
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;

	//top.showSaveMsg(num);
	if (num == 0) {
		//getValue();
		if (reboot) {
			//window.location.href = "redirect.html?3";	
			top.$.progress.showPro("reboot");
			$.get("goform/SysToolReboot?" + Math.random(), function (str) {
				//top.closeIframe(num);
				if (!top.isTimeout(str)) {
					return;
				}
			});
		} else {
			top.advInfo.initValue();
			top.showSaveMsg(num);
		}

	}
}

window.onload = function () {
	iptvInfo = R.page(pageview, pageModel);
};