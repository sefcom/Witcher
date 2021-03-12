$(function () {
	getValue();
	top.loginOut();

	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
});

function getValue() {
	//$.getJSON("list.txt",initValue);
	//$.getJSON("goform/initWifiMacFilter?"+Math.random(),initValue);
	$.getJSON("goform/getBlackRuleList?" + Math.random(), initValue);
}

function initValue(obj) {
	var i = 0,
		len = obj.length,
		str = "";
	if (len != 0) {
		for (i = 0; i < len; i++) {

			str += "<tr class='tr-row'><td class='dev-name fixed' title='" + obj[i].devName + "'>" + obj[i].devName + "</td>" +
				"<td title='" + obj[i].deviceId + "'>" + obj[i].deviceId.toUpperCase() + "</td>" +
				"<td><input type='button' class='btn del' value='" + _("Remove") + "'></td></tr>";
		}
	} else {
		str = "<tr><td colspan=3 >" + _("The blacklist is empty.") + "</td></tr>";
	}
	if (str == "") {
		str = "<tr><td colspan=3 >" + _("The blacklist is empty.") + "</td></tr>";
	}
	$("#list").html(str).find(".dev-name").each(function (i) {
		$(this).attr("title", obj[i].devName);
	});
	$(".del").on("click", delList);
	top.initIframeHeight();
}

function delList() {
	var mac = $(this).parents("tr").find("td").eq(1).attr("title"),
		data;

	data = "mac=" + mac;
	$.post("goform/delBlackRule", data, callback);
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num, _("Removing from the blacklist..."), 3);
	if (num == 0) {
		top.staInfo.initValue();
	}
}