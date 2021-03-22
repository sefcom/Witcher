var G = {};
var initObj = null;

var sambaInfo;
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
	getUrl: "goform/GetSambaCfg",
	setUrl: "goform/SetSambaCfg",
	translateData: function (data) {
		var newData = {};
		newData.samba = data;
		return newData;
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
		var data,
			subObj = {},
			internetPort = ($("#premitEn").val() == 1 ? $("#internetPort").val() : initObj.internetPort);

		subObj = {
			"fileCode": $("#fileCode").val(),
			"password": $("#password").val(),
			"premitEn": $("#premitEn").val(),
			"guestpwd": $("#guestpwd").val() || "",
			"guestuser": $("#guestuser").val() || "",
			"guestaccess": $("#guestaccess").val() || "",
			"internetPort": internetPort
		};
		data = objTostring(subObj);
		return data;
	}
});

//模块注册
R.module("samba", view, moduleModel);


function initEvent() {
	var pwdFlag = window.location.href.substr(window.location.href.indexOf('&') + 1);
	if (pwdFlag === "nousb") {
		$('#nocontent').removeClass("none");
		$('#content').addClass("none");
	} else {
		$("#premitEn").on("click", changeDmzEn);
		//$(".edit").on("click", editPort);
		$("#ftpLink").on("click", function () {
			var net = $(this).html();
			window.open(net, "");
		});

		$("#internetPort").inputCorrect("num");
		$("#internetLink").on("click", function () {
			var internetPort = parseInt($("#internetPort").val(), 10);
			if (internetPort > 0 && internetPort < 65536) {
				var net = $(this).html() + ":" + internetPort;
				window.open(net, "");
			}
		});

	}
	/*$(".add").on("click", function () {
		$("#upnpBody").append('<tr>' +
			'<td>' + _("Guest") + '</td>' +
			'<td><input type="text" class="input-mini" required="required" maxlength="15" id="guestuser" value=' + (initObj.guestuser || "guest") +
			'></td>' +
			'<td><input type="password" class="input-mini" id="guestpwd" maxlength="32" required="required" data-options="{&quot;type&quot;:&quot;pwd&quot;, &quot;args&quot;:[5,32]}" value=' + (initObj.guestpwd || 'guest') +
			'></td>' +
			'<td>' +
			'<select id="guestaccess" class="input-small">' +
			'<option value="rw">' + _("Read/Write") + '</option>' +
			'<option value="r" selected>' + _("Read") + '</option>' +
			'</select>' +
			'</td>' +
			'<td class="none"><input type="button" value="' + _("Delete") + '" class="btn del btn-small"></td>' +
			'</tr>');
		checkData();
		$('#guestpwd').initPassword(_(""), false, false);
		if (initObj.guestaccess === "wr") {
			$("#guestaccess").find('[value="rw"]').attr("selected", true);
		}
		$(this).hide();
	});*/
	top.initIframeHeight();
}

function changeDmzEn() {
	var className = $("#premitEn").attr("class");
	if (className == "btn-off") {
		$("#premitEn").attr("class", "btn-on");
		$("#premitEn").val(1);
		if (top.G.workMode == "router" || top.G.workMode == "wisp") {
			if (initObj.internetLink != "") {
				$("#internet_set").removeClass("none");
			} else {
				$("#internet_set").addClass("none");
				showErrMsg("msg-err", _("Failed to access the internet. Please check the WAN settings."), true);
			}
		} else {
			$("#internet_set").addClass("none");
		}
	} else {
		$("#premitEn").attr("class", "btn-off");
		$("#premitEn").val(0);
		showErrMsg("msg-err", " ", true);
		$("#internet_set").addClass("none");
	}
	top.initIframeHeight();
}

function checkData() {
	G.validate = $.validate({
		custom: function () {
			var guestName = $('#guestuser').val();
			var guestPwd = $('#guestpwd').val();

			if ($("#upnpBody tr").length === 2) {
				if (guestName === "admin") {
					return _("The user name of the guest cannot be admin.");
				}
			}

		},

		success: function () {
			sambaInfo.submit();
		},

		error: function (msg) {
			if (msg) {
				$("#guest-user-err").html(msg);
				setTimeout(function () {
					$("#guest-user-err").html("&nbsp;");
				}, 3000)
			}
		}
	});
}

function initValue(obj) {

	initObj = obj;
	//$("#usbList").html(str);
	$("#password").val(obj.password);
	$("#fileCode").val(obj.fileCode);
	if (obj.guestuser !== "") {
		$("#guestuser").val(obj.guestuser);
		$("#guestpwd").val(obj.guestpwd);
		$("#guestaccess").val(obj.guestaccess);
	}

	if (!G.validate) {
		$('#password').initPassword(_(""), false, false);
		$('#guestpwd').initPassword(_(""), false, false);
		checkData();
	}

	if (obj.ftpLink == "") {
		$("#ftpLink").parent().parent().parent().addClass("none");
		$("#localLink").parent().parent().parent().addClass("none");
		$("#localMacLink").parent().parent().parent().addClass("none");
	}
	$("#ftpLink").html("ftp://" + obj.ftpLink + ":21");
	$("#localLink").html("\\\\" + obj.ftpLink);
	$("#localMacLink").html("smb://" + obj.ftpLink);

	if (obj.internetLink != "") {
		$("#internetLink").html("ftp://" + obj.internetLink);
	} else {
		$("#internet_set").addClass("none");
	}
	$("#premitEn").attr("class", (obj.premitEn == "1" ? "btn-off" : "btn-on"));
	changeDmzEn();

	if ((obj.wl_mode && obj.wl_mode == "apclient") || top.G.workMode == "ap") {
		$("#internet_set_wrap").addClass("none");
	} else {
		$("#internet_set_wrap").removeClass("none");
	}
	//$("#outPort").html(obj.outPort);
	$("#internetPort").val(obj.internetPort);
	top.initIframeHeight();
}

function callback(str) {
	if (!top.isTimeout(str)) {
		return;
	}
	var num = $.parseJSON(str).errCode;


	top.showSaveMsg(num);
	if (num == 0) {
		//getValue();
		top.usbInfo.initValue();
	}
}

function flashChecke() {
	var hasFlash = 0; //是否安装了flash
	var flashVersion = 0; //flash版本
	try {
		if (document.all) {
			var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
			if (swf) {
				hasFlash = 1;
				VSwf = swf.GetVariable("$version");
				flashVersion = parseInt(VSwf.split(" ")[1].split(",")[0]);
			}
		} else {
			if (navigator.plugins && navigator.plugins.length > 0) {
				var swf = navigator.plugins["Shockwave Flash"];
				if (swf) {
					hasFlash = 1;
					var words = swf.description.split(" ");
					for (var i = 0; i < words.length; ++i) {
						if (isNaN(parseInt(words[i]))) continue;
						flashVersion = parseInt(words[i]);
					}
				}
			}
		}
	} catch (e) {
		hasFlash = 1; //是否安装了flash
		flashVersion = 1; //flash版本
	}

	return (hasFlash == 1);
}

window.onload = function () {
	sambaInfo = R.page(pageview, pageModel);
};