$(function () {
	getValue();
	$("#submit").on("click", preSubmit);
	$("#dhcpEn").on("click", changeDhcpEn);
	top.loginOut();
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
});

function changeDhcpEn() {
	var className = $("#dhcpEn").attr("class");
	if (className == "btn-off") {
		$("#dhcpEn").attr("class", "btn-on");
		$("#dhcpEn").val(1);
		$("#dhcp_set").removeClass("hidden");
	} else {
		$("#dhcpEn").attr("class", "btn-off");
		$("#dhcpEn").val(0);
		$("#dhcp_set").addClass("hidden");
	}
}

function getValue() {
	$.getJSON("goform/GetDhcpServer?" + Math.random(), initValue);
}



function initValue(obj) {
	var net_arry = obj["lanIp"].split(".");

	top.$(".main-dailog").removeClass("none");
	top.$("iframe").removeClass("none");
	top.$(".loadding-page").addClass("none");

	if (obj["dhcpEn"] == "0") {
		$("#dhcpEn").attr("class", "btn-off");
		$("#dhcpEn").val(0);
		$("#dhcp_set").addClass("hidden");
	} else {
		$("#dhcpEn").attr("class", "btn-on");
		$("#dhcpEn").val(1);
		$("#dhcp_set").removeClass("hidden");
	}
	$("#dhcp_net").html(net_arry[0] + "." + net_arry[1] + "." + net_arry[2] + ".");
	$("#sip").val(obj["startIp"].split(".")[3]);
	$("#eip").val(obj["endIp"].split(".")[3]);
	$("#lease").val(obj["lease"]);
}

function preSubmit() {
	var subData,
		dhcpEn = $("#dhcpEn").val(),
		net = $("#dhcp_net").html(),
		sip = $("#sip").val(),
		eip = $("#eip").val();
	var rel = /^[0-9]{1,}$/;
	if (sip == "" || eip == "") {
		showErrMsg("msg-err", _("Please specify an IP address range."));
		return;
	}
	if (!rel.test(sip) || !rel.test(eip)) {
		showErrMsg("msg-err", _("Only digits are allowed."));
		return;
	}

	if (parseInt(sip, 10) > parseInt(eip, 10)) {
		showErrMsg("msg-err", _("The start IP address cannot be greater than the end IP address."));
		return;
	}
	if (parseInt(sip, 10) <= 0 || parseInt(eip, 10) >= 255) {
		showErrMsg("msg-err", _("The last fields of the start and end IP addresses must range from 1 through 254."));
		return;
	}

	subData = "dhcpEn=" + dhcpEn + "&startIp=" + net + parseInt(sip, 10) + "&endIp=" + net + parseInt(eip, 10) + "&lease=" + $("#lease").val();
	$.post("goform/DhcpSetSer", subData, callback);
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
	if (num == 0) {
		//getValue();
		top.sysinfo.initValue();
	}
}