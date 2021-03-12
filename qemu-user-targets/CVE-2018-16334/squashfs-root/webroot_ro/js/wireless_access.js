$(function () {
	getValue();
	top.loginOut();

	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
});

function getValue() {
	//$.getJSON("list.txt",initValue);
	$.getJSON("goform/initWifiMacFilter?" + Math.random(), initValue);
}

function initValue(obj) {
	var i = 0,
		len = obj.filterList.length,
		str = "";
	if (len != 0) {
		for (i = 0; i < len; i++) {
			str += "<tr class='tr-row'><td class='none'>" + obj.filterList[i].index + "</td><td>" + obj.filterList[i].deviceName + "</td>" +
				"<td>" + _("MAC Address:") + obj.filterList[i].deviceMac.toUpperCase() + "</td>" +
				"<td><input type='button' class='btn btn-mini del' value='" +_("Allow Wireless Access")+ "'></td></tr>";
		}
	} else {
		str = "<tr><td colspan=4 >" + _("Wireless Access List is empty.") + "</td></tr>";
	}
	$("#list").html(str);
	$(".del").on("click", delList);
}

function delList() {
	var index = $(this).parent().parent().children().eq(0).html(),
		data;

	data = "index=" + index;
	$.post("goform/delWifiMacFilter", data, callback);
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num, _("Allowing wireless access..."), 3);
}