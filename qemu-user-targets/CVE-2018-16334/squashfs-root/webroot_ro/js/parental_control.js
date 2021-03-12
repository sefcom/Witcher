// JavaScript Document
var G_current_operate; //"1":add   "0":edit
var submitData = {}; //提交数据
var parentCtrl;
var pageview = R.pageView({ //页面初始化
    init: function () {
        top.loginOut();
        top.$(".main-dailog").removeClass("none");
        top.$(".save-msg").addClass("none");
        $("#save").on("click", function () {
            parentCtrl.submit();
        });
        //$("#cancel").on("click", cancelParentConrolInfo);
    }
});
var pageModel = R.pageModel({
    getUrl: "",
    setUrl: "goform/saveParentControlInfo",
    translateData: function (data) {
        var newData = {};
        newData.parentControl = data;
        return newData;
    },
    afterSubmit: parent_callback
});

/************************/
var view = R.moduleView({
    initEvent: initEvent,
    checkData: checkParentData
})
var moduleModel = R.moduleModel({
    getSubmitData: function () {
        return objTostring(submitData);;
    }
});

//模块注册
R.module("parentControl", view, moduleModel);

function initEvent() {
    top.$("#head_title").off("click").on("click", showParentDeviceWrap);
    top.$("#head_title2").off("click").on("click", showRuleList);

    $("#whiteEnable").on("click", changeWhiteEn);
    $("[name='timeType']").on("click", changeTimeType);
    $("[name='limitType']").on("click", changeLimitType);


    $("#device_edit, #device_save").on("click", function () {
        if (this.id == "device_edit") {
            editDevice("edit");
        } else {
            editDevice("save");
        }

    });

    $('#parent_urls').addPlaceholder(_("Please enter keywords of websites.")).on("keyup blur", function () {
        if (/[A-Z]/.test(this.value)) {
            this.value = this.value.toLowerCase();
            showErrMsg("msg-err", _("Case-insensitive"));
        }
    });
    $('#deviceName').addPlaceholder(_("Optional"));
    clearDevNameForbidCode($('#deviceName')[0]);
    $('#deviceMac').addPlaceholder("00:00:00:00:00:00");

    getParentControl();
}

function checkParentData() {
    var subObj = {},
        start_time = "",
        end_time = "",
        subStr = "",
        i = 0,
        dayList = "",
        index = 0,
        timeType,
        nameSubObj,
        editDeviceName,
        deviceName = $("#deviceName").val();

    if ($("#parentcontrolEnable").val() == "1") {
        start_time = $("#startHour").val() + ":" + $("#startMin").val();
        end_time = $("#endHour").val() + ":" + $("#endMin").val();

        for (i = 0; i < 7; i++) {
            if ($("#day" + (i))[0].checked) {
                dayList += "1,";
                index++;
            } else {
                dayList += "0,";
            }
        }
        dayList = dayList.replace(/[,]$/, "");

        if (index == 0 && $("#thatday")[0].checked) {
            return _("Please select at least one day.");
        }

        var time = start_time + "-" + end_time;
        if (start_time.replace(/[:]/g, "") == end_time.replace(/[:]/g, "")) {
            return _("The start time and end time must not be the same.");
        }

        var urls = "";
        if ($("#whiteEnable").val() == "1") {
            urls = $("#parent_urls").val();
            //TODO:验证URLS
            if (urls == "") {
                if ($('[name="limitType"]:checked').val() === "1") {
                    return _("After enabling the whitelist, you must specify whitelisted URLs.");
                } else {
                    return _("After enabling the blacklist, you must specify blacklisted websites.");
                };
            }
            var arr = urls.split(","),
                len = arr.length,
                dic = {};

            if (len > 10) {
                if ($('[name="limitType"]:checked').val() === "1") {
                    return _("Only a maximum of %s whitelisted URLs are allowed.", [10]);
                } else {
                    return _("Only a maximum of %s blacklisted URLs are allowed.", [10]);
                }
                return;
            }
            result = [];
            for (var i = 0; i < len; i++) {
                if (/^[-.a-z0-9]{2,31}$/ig.test(arr[i])) {
                    if (typeof dic[arr[i]] == "undefined") {
                        dic[arr[i]] = arr[i];
                        result.push(arr[i]);
                    }

                } else {

                    return _("One URL can consist of only 2-31 characters, including digits, letters, hyphens (-), and dots (.).");
                }
            }
            urls = result.join(",").toLowerCase();
        }

        if (G_current_operate === "1") {
            if ($('#deviceMac').val() === "") {
                return _("Please specify a MAC address.");
            }

            if ($('#deviceMac').val() === "00:00:00:00:00:00") {
                return _("The MAC address cannot be 00:00:00:00:00:00.");
            }

            if ($('#deviceMac').val().charAt(1) && parseInt($('#deviceMac').val().charAt(1), 16) % 2 !== 0) {
                return _("The second character in the MAC address must be an even number.");
            }

            if (!(/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/).test($('#deviceMac').val())) {
                return _("Please enter a valid MAC address.");
            }

            var msg = checkDevNameValidity(deviceName, true);

            if (msg) {
                return msg;
            }


            subObj = {
                "deviceId": $('#deviceMac').val().toLowerCase(),
                "deviceName": $('#deviceName').val(),
                "enable": $("#parentcontrolEnable").val(),
                "time": time,
                "url_enable": $("#whiteEnable").val(),
                "urls": urls,
                "day": dayList,
                "limit_type": $('[name="limitType"]:checked').val()
            }
        } else {
            //编辑保存时，若设备名称处于编辑状态，则走保存设备名称流程
            if (!$("#device_save").hasClass("none")) {
                editDeviceName = $("#devName").val();

                var msg = checkDevNameValidity(editDeviceName);

                if (msg) {
                    return msg;
                }

                nameSubObj = "devName=" + encodeURIComponent($("#devName").val()) + "&mac=" + $("#device_mac").html().toLowerCase();
                $.post("goform/SetOnlineDevName", nameSubObj);
            }

            subObj = {
                "deviceId": $("#device_mac").html().toLowerCase(),
                "enable": $("#parentcontrolEnable").val(),
                "time": time,
                "url_enable": $("#whiteEnable").val(),
                "urls": urls,
                "day": dayList,
                "limit_type": $('[name="limitType"]:checked').val()
            }
        }



    } else {
        //禁用表示仅保存enable或disable和MAC地址
        subObj = {
            "deviceId": $("#device_mac").html().toLowerCase(),
            "enable": $("#parentcontrolEnable").val()
        }

    }
    submitData = subObj;
}

function getParentControl() {
    G_current_operate = top.parentInfo.action;
    var deviceId = top.parentInfo.editObj.deviceMac;
    var deviceName = top.parentInfo.editObj.deviceName;

    initHtml();
    $("#device_name").text(deviceName).attr("title", deviceName);
    $("#device_mac").html(deviceId.toUpperCase());
    //显示大写，传输数据小写
    var data = "mac=" + deviceId + "&random=" + Math.random();
    if (deviceId != "") {
        $.getJSON("goform/GetParentControlInfo?" + data, initParentControl);
    } else {
        $("#parental_wrap").removeClass("none");
        $('#addName').removeClass("none");
        $('#editName').addClass("none");
        initParentControl();
    }

}

function initHtml() {
    var obj = getTimeString();
    $("#startHour").html(obj.hour);
    $("#startMin").html(obj.minute);
    $("#endHour").html(obj.hour);
    $("#endMin").html(obj.minute);
}

function initParentControl(obj) {
    $('#deviceMac').inputCorrect("mac");
    //{"enable":1,"mac":"aa:aa:aa:aa:aa:aa", "url_enable":1, "urls":"abcd,abcde", "time":"0:0-0:0", "day":"1,1,1,1,1,1,0"}
    //星期天开始
    if (G_current_operate === "0") {
        $('#addName').addClass("none");
        $('#editName').removeClass("none");
    }
    var defaultObj = {
        enable: 1,
        mac: "",
        url_enable: 1,
        urls: "",
        time: "19:00-21:00",
        day: "1,1,1,1,1,1,1",
        limit_type: 0
    };

    obj = $.extend(defaultObj, obj);
    if (typeof obj.enable == "undefined") {
        //说明现在没有这条规则
        obj.enable = 0;
        obj.url_enable = 0;
        obj.urls = "";
        obj.time = "19:00-21:00";
        obj.day = "1,1,1,1,1,1,1";
    }

    $("#parentcontrolEnable").attr("class", "btn-on").val(1);

    if (obj.url_enable == 1) {
        $("#whiteEnable").attr("class", "btn-on").val(1);
        $("#web_limit").removeClass("none").val(obj.urls);
    } else {
        $("#whiteEnable").attr("class", "btn-off").val(0);
        $("#web_limit").addClass("none");
    }

    if (obj.limit_type == 1) {
        $("[name='limitType']")[1].checked = true;
    } else {
        $("[name='limitType']")[0].checked = true;
    }
    changeLimitType();


    $("#parent_urls").val(obj.urls);
    $('#parent_urls').addPlaceholder(_("Please enter keywords of websites."));
    if (obj.time == "00:00-24:00") {
        obj.time = "00:00-00:00";
    }

    var start_time = obj.time.split("-")[0],
        end_time = obj.time.split("-")[1];
    $("#startHour").val(start_time.split(":")[0]);
    $("#startMin").val(start_time.split(":")[1]);
    $("#endHour").val(end_time.split(":")[0]);
    $("#endMin").val(end_time.split(":")[1]);

    if (obj.day == "1,1,1,1,1,1,1") {
        $("[name='timeType']")[0].checked = true;
    } else {
        $("[name='timeType']")[1].checked = true;
    }

    changeTimeType();
    var dayArr = obj.day.split(","),
        len = dayArr.length,
        i = 0,
        dayVal;

    for (i = 0; i < len; i++) {
        dayVal = dayArr[i];
        if (dayVal == 0) {
            $("#day" + (i)).attr("checked", false);
        } else {
            $("#day" + (i)).attr("checked", true);
        }
    }

    showParentalSet();
    top.initIframeHeight();
}

function initRuleList(obj) {
    var str = "",
        type = "",
        len = obj.length,
        i = 0,
        color,
        btn_str;
    str = "";
    for (i = 0; i < len; i++) {

        str += "<tr class='tr-row'><td class='fixed' title='" + obj[i].devName + "'>" + obj[i].devName + "</td>" +
            "<td title='" + obj[i].mac + "'>" + _("MAC address:") + obj[i].mac.toUpperCase() + "</td>";
        if (obj[i].enable == 1) {
            btn_str = _("Enable");
        } else {
            btn_str = _("Disable");
        }
        str += "<td>" + btn_str + "</td><td><input type='button' value='" + _("Delete") + "' class='btn btn-mini del'></td></tr>";
    }

    if (len == 0) {
        str = "<td colspan=4>" + _("The controlled devices list is empty.") + "</td>";
    }

    $("#rule_list #list2").html(str);
}

function showParentDeviceWrap() {
    //top.$("#head_title").addClass("selected");
    //top.$("#head_title2").removeClass("selected");
    $("#parental_list").removeClass("none");
    $("#parental_wrap, #rule_list").addClass("none");
    top.initIframeHeight();
    pageModel.update();
}

function showRuleList() {
    top.$("#head_title").removeClass("selected");
    top.$("#head_title2").addClass("selected");
    $("#rule_list").removeClass("none");
    $("#parental_list, #parental_wrap").addClass("none");

    $.getJSON("goform/getParentalRuleList?" + Math.random(), initRuleList);
    top.initIframeHeight();
}


function showParentalSet() {
    $("#device_edit").val(_("Edit"));
    $("#parental_wrap").removeClass("none");
    $("#parental_list, #rule_list").addClass("none");
    top.initIframeHeight();
}

function changeTimeType() {
    if ($("#everyday")[0].checked) {
        $("[id^='day']").attr("disabled", true).prop("checked", true);
    } else {
        $("[id^='day']").removeAttr("disabled");
    }
}

function changeLimitType() {
    if ($("#black")[0].checked) {
        $("#limit_label").html(_("Blocked Websites:"));
        $(".help-block").html(_("Enter website keywords separated by a comma. For example, eHow,google indicates that the eHow and Google websites are inaccessible."));
    } else {
        $("#limit_label").html(_("Unblocked Websites:"));
        $(".help-block").html(_("Enter website keywords separated by a comma. For example, eHow,google indicates that only the eHow and Google websites are accessible."));
    }
}

function changeWhiteEn() {
    var className = $("#whiteEnable").attr("class");
    if (className == "btn-off") {
        $("#whiteEnable").attr("class", "btn-on").val(1);
        $("#web_limit").removeClass("none");
    } else {
        $("#whiteEnable").attr("class", "btn-off").val(0);
        $("#web_limit").addClass("none");
    }
    top.initIframeHeight();
}

function parent_callback(str) {
    if (!top.isTimeout(str)) {
        return;
    }

    var num = $.parseJSON(str).errCode;
    top.$("#iframe-msg").removeClass("text-success red");
    //top.$("#iframe-msg").removeClass("none");
    if (num == 0) {
        top.showSaveMsg(num);
        top.parentInfo.initValue();
    } else if (num == 1) {
        showErrMsg("msg-err", _("Only a maximum of %s rules are allowed.", [30]));
        setTimeout(function () {
            top.$("#iframe-msg").html("&nbsp;");
        }, 2000);
        return;
    } else {
        top.$("#iframe-msg").addClass("red").html(_("Configuration failed."));
        setTimeout(function () {
            top.$("#iframe-msg").html("&nbsp;");
        }, 2000);
        return;
    }
    setTimeout(function () {
        top.$("#iframe-msg").html("");
        top.$("#iframe-msg").removeClass("text-success").addClass("red");
        //top.$("#iframe-msg").addClass("none");
        //showParentDeviceWrap();
    }, 800);



    /*if(num != 0) {
		top.location.reload(true);
	}*/
}

function editDevice(action) {
    var deviceName = $("#device_name").text(),
        str,
        data;
    if (action == "edit") {
        str = "<input type='text' class='input-medium' id='devName' maxlength='20'>";
        $("#device_name").html(str);
        $("#devName").val(deviceName);
        $("#device_edit").addClass("none");
        $("#device_save").removeClass("none");

        clearDevNameForbidCode($("#devName")[0]);

    } else {

        data = "devName=" + encodeURIComponent($("#devName").val()) + "&mac=" + $("#device_mac").html().toLowerCase();

        deviceName = $("#devName").val();

        //统一验证设备名称合法性
        var msg = checkDevNameValidity(deviceName);

        if (msg) {
            showErrMsg("msg-err", msg);
            return false;
        }

        $.post("goform/SetOnlineDevName", data, handDeviceName);

        $("#device_edit").removeClass("none");
        $("#device_save").addClass("none");
    }
}



function handDeviceName(data) {


    var num = $.parseJSON(data).errCode;

    //top.$("#iframe-msg").removeClass("none");
    top.$("#iframe-msg").removeClass("text-success red");
    if (num == 0) {
        $("#device_name").text($("#devName").val());
        top.$("#iframe-msg").addClass("text-success").html(_("Configuration succeeded."));

    } else {
        top.$("#iframe-msg").addClass("red").html(_("Configuration failed."));
    }
    setTimeout(function () {
        top.$("#iframe-msg").removeClass("text-success").addClass("red");
        top.$("#iframe-msg").html("");

    }, 800);
}

window.onload = function () {
    parentCtrl = R.page(pageview, pageModel);
};
