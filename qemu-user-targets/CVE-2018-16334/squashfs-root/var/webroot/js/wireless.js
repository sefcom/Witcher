var wrlBandInfo;
var pageview = R.pageView({ //页面初始化
	init: initWrlBandPage
});

var G_data = {};
var pageModel = R.pageModel({
	getUrl: "goform/WifiRadioGet",
	setUrl: "goform/WifiRadioSet",
	translateData: function (data) {
		var newData = {};
		newData.wrlRadio = data;
		return newData;
	},
	afterSubmit: callback
});

/*************************************************************/

var view = R.moduleView({
	initEvent: function () {
		$("#adv_mode, #adv_mode_5g").on("change", getBandWidthList);

		$("#adv_band_5g").on("change", changeBandwidth);
	}
})

var moduleModel = R.moduleModel({
	initData: initValue,
	getSubmitData: function () {
		return $("#wireless").serialize();
	}
});

//模块注册
R.module("wrlRadio", view, moduleModel);

function initWrlBandPage() {
	top.loginOut();
	top.$(".main-dailog").removeClass("none");
	top.$(".save-msg").addClass("none");
	$("#submit").on("click", function () {
		wrlBandInfo.submit();
	});
}


function getBandWidthList() {
	var mode_24g = $("#adv_mode").val(),
		bang_24g_init = $("#adv_band").val(),
		bang_5g_init = $("#adv_band_5g").val();


	//2.4g 频宽受到网络模式影响 
	if (mode_24g == "bgn" || mode_24g == "n only") {
		$("#adv_band").html('<option value="20">20</option><option value="40">40</option><option value="auto">20/40</option>');
	} else {
		$("#adv_band").html('<option value="20">20</option>').val("20");
	}
	$("#adv_band").val(bang_24g_init);
	if ($("#adv_band").val() != bang_24g_init) {
		$("#adv_band").val("20");
	}

	if ("80" in G_data.channel_5g) {
		$("#adv_band_5g").html('<option value="20">20</option><option value="40">40</option><option value="80">80</option><option value="auto">20/40/80</option>');
	} else if ("40" in G_data.channel_5g) {
		$("#adv_band_5g").html('<option value="20">20</option><option value="40">40</option><option value="auto">20/40</option>');
	} else {
		$("#adv_band_5g").html('<option value="20">20</option>').val("20");
	}

	if ($("#adv_band_5g option[value=" + bang_5g_init + "]").length == 0) {
		if ("80" in G_data.channel_5g) {
			$("#adv_band_5g").val(80);
		} else if ("40" in G_data.channel_5g) {
			$("#adv_band_5g").val(40);
		}
	} else {
		$("#adv_band_5g").val(bang_5g_init);
	}

	changeBandwidth();
}

function changeBandwidth() {

	var channel,
		channel_5g,
		len_5g,
		i = 0,
		bandwidth,
		bandwidth_5g,
		str = "",
		adv_channel = $("#adv_channel").val(),
		adv_channel_5g = $("#adv_channel_5g").val();


	bandwidth = $("#adv_band").val();
	channel = G_data.channel;
	len = channel.length;
	for (i = 0; i < len; i++) {
		if (i == 0) {
			str += "<option value='0'>" + _("Auto") + "</option>";
		} else {
			//str += "<option value='" + channel[i] + "'>" + _("Channel") + " " + channel[i] + "</option>";
			str += "<option value='" + channel[i] + "'>" + _("Channel") + " " + channel[i] + "</option>";
		}
	}
	$("#adv_channel").html(str);
	if ($("#adv_channel option[value=" + adv_channel + "]").length == 0) {
		$("#adv_channel").val(0);
	} else {
		$("#adv_channel").val(adv_channel);
	}

	str = "";
	bandwidth_5g = $("#adv_band_5g").val();
	if (bandwidth_5g == "auto") {
		if ("80" in G_data.channel_5g) {
			bandwidth_5g = "80";
		} else {
			bandwidth_5g = "40";
		}
	}

	bandwidth_5g = (bandwidth_5g == "auto" ? "80" : bandwidth_5g);
	channel_5g = G_data.channel_5g[bandwidth_5g];
	len_5g = channel_5g.length;
	for (i = 0; i < len_5g; i++) {
		if (i == 0) {
			str += "<option value='0'>" + _("Auto") + "</option>";
		} else {
			//str += "<option value='" + channel_5g[i] + "'>" + _("Channel") + " " + channel_5g[i] + "</option>";
			str += "<option value='" + channel_5g[i] + "'>" + _("Channel") + " " + channel_5g[i] + "</option>";
		}
	}
	$("#adv_channel_5g").html(str);
	if ($("#adv_channel_5g option[value=" + adv_channel_5g + "]").length == 0) {
		$("#adv_channel_5g").val("0");
	} else {
		$("#adv_channel_5g").val(adv_channel_5g);
	}
}

function initValue(obj) {
	var bandWidth_5g;
	G_data = obj;

	top.$(".main-dailog").removeClass("none");
	top.$("iframe").removeClass("none");
	top.$(".loadding-page").addClass("none");

	//inputValue(obj);
	$("#adv_mode").val(obj.adv_mode);
	$("#adv_mode_5g").val(obj.adv_mode_5g);
	getBandWidthList();

	$("#adv_band").val(obj.adv_band);
	$("#adv_band_5g").val(obj.adv_band_5g);

	changeBandwidth();
	$("#adv_channel").val(obj.adv_channel);
	$("#adv_channel_5g").val(obj.adv_channel_5g);

	getBandWidthList();
}

//执行回调
function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;
	top.showSaveMsg(num);
}

/***************************************************************/


window.onload = function () {
	wrlBandInfo = R.page(pageview, pageModel);
};