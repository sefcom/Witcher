var G = {};
var listMax = 0;
var wanIp = "";

var staticRouterInfo;
var pageview = R.pageView({ //页面初始化
    init: function () {
        top.loginOut();
        top.$(".main-dailog").removeClass("none");
        top.$(".save-msg").addClass("none");
        /*$("#submit").on("click", function () {
            staticRouterInfo.submit();
        });*/
    }
});

var pageModel = R.pageModel({
    getUrl: "goform/GetStaticRouteCfg",
    setUrl: "goform/SetStaticRouteCfg",
    translateData: function (data) {
        var newData = {};
        newData.staticRouter = data;
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
    initEvent: initStaticEvent
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
            if ($(trArry[i]).children().eq(4).children().hasClass("delete")) {
                data += $(trArry[i]).children().eq(0).html() + ",";
                data += $(trArry[i]).children().eq(1).html() + ",";
                data += $(trArry[i]).children().eq(2).html() + ",";
                data += $(trArry[i]).children().eq(3).html() || "0.0.0.0";
                data += "~";
            }
        }
        data = data.replace(/[~]$/, "");

        if (G.action == "add") {
            if (data != "") {
                data += "~";
            }
            data += $("#network").val() + "," + $("#mask").val() + "," + ($("#gateway").val() || "0.0.0.0") + ",";

            if (top.G.wanNum == 1) {
                data += $("#singleWan").text();
            } else {
                data += $("#wanSelect").val();
            }
        }

        data = "list=" + data;
        return data;
    }
});

//模块注册
R.module("staticRouter", view, moduleModel);


function initStaticEvent() {

    $("#network").inputCorrect("ip");
    $("#gateway").inputCorrect("ip");
    $("#mask").inputCorrect("ip");
    $("#network, #mask").on("blur", function () {
        var routeArray = [];
        var maskArray = [];
        var routeStr = $("#network").val(),
            maskStr = $("#mask").val(),
            str = "";
        if ((routeStr !== "") && (maskStr !== "")) {
            maskArray = maskStr.split(".");
            routeArray = routeStr.split(".");
            if ((maskArray.length == 4) && (routeArray.length == 4)) {
                for (var index = 0; index < 4; index++) {
                    str += (maskArray[index] & routeArray[index]);
                    if (index != 3) {
                        str += '.';
                    }
                }
                $('#network').val(str);
            }
        }
    });

    checkData();
    top.initIframeHeight();
    $(".add").on("click", function () {
        G.validate.checkAll();
    });
    $("#portList").delegate(".delete", "click", function () {
        $(this).parent().parent().attr("data-target", "delete");
        //if (confirm(_("Do you want to continue?"))) {
        G.action = "delete";
        staticRouterInfo.submit();
        //}
    });
    $(".input-append ul").on("click", function (e) {
        $("#gateway")[0].value = ($(this).parents(".input-append").find("input")[0].value || "");
    });

}

function addList() {
    var str = "";

    str += "<tr>";
    str += "<td title='" + _("This route will not take effect.") + "' id='network" + (listMax + 1) + "'>" + $("#network").val() + "</td>";
    str += "<td title='" + _("This route will not take effect.") + "' id='mask" + (listMax + 1) + "'>" + $("#mask").val() + "</td>";
    str += "<td title='" + _("This route will not take effect.") + "' alt='gateway' id='gateway" + (listMax + 1) + "'>" + $("#gateway").val() + "</td>";
    //str += "<td title='" + _("This route will not take effect.") + "'>---</td>";

    if ($("#singleWan").hasClass("none")) {
        str += "<td title='" + _("This route will not take effect.") + "' id='wanPort" + (listMax + 1) + "'>" + $("#wanSelect").val() + "</td>";
    } else {
        str += "<td title='" + _("This route will not take effect.") + "'  id='wanPort" + (listMax + 1) + "'>WAN1</td>";
    }

    str += "<td><span title='" + _("Delete") + "' class='delete'></span></td></tr>";

    $("#portBody").append(str);
    $("#network").val("");
    $("#mask").val("");
    $("#gateway").val("");
    listMax++;
    top.initIframeHeight();
};


function delList() {
    $("#portBody").find("[data-target='delete']").remove();
}


function checkData() {
    G.validate = $.validate({
        custom: function () {
            var network = "",
                gateway = "",
                mask = "",
                str = "",
                i = 0,
                addNum = 0,
                wanMsg;

            network = $("#network").val();
            mask = $("#mask").val();
            gateway = $("#gateway").val();


            $("#portBody tr").each(function () {
                //存在删除图标时，即为手动添加
                if ($(this).find("td:eq(4)").children('span').hasClass("delete")) {
                    addNum++;
                }
            });

            if (addNum >= 10) {
                return _("Only a maximum of %s rules are allowed.", [10]);
            }

            if ($.validate.valid.routeCheck.all(network)) {
                $("#network").focus();
                return $.validate.valid.routeCheck.all(network);
            }
            if ($.validate.valid.mask.all(mask)) {
                $("#mask").focus();
                return $.validate.valid.mask.all(mask);
            }
            if (gateway != "") {
                if ($.validate.valid.ip.all(gateway)) {
                    $("#gateway").focus();
                    return $.validate.valid.ip.all(gateway);
                }

                /*
                //决策不需要判断同网段
                 if (top.G.wanNum == 1) {
                    wanMsg = _("WAN Port Gateway");
                    if (G.initData.wanGateway != "") {
                        if (!checkIpInSameSegment(gateway, G.initData.wanMask, G.initData.wanGateway, G.initData.wanMask)) {
                            return _("%s and %s (%s) must be in the same network segment.", [_("Gateway"), wanMsg, G.initData.wanGateway]);
                        }
                    }

                } else {
                    if ($("#wanSelect").val() == "WAN1") {
                        wanMsg = _("WAN1 Port Gateway");
                        if (G.initData.wanGateway != "") {
                            if (!checkIpInSameSegment(gateway, G.initData.wanMask, G.initData.wanGateway, G.initData.wanMask)) {
                                return _("%s and %s (%s) must be in the same network segment.", [_("Gateway"), wanMsg, G.initData.wanGateway]);
                            }
                        }
                    } else {
                        if (G.initData.wanGateway2 != "") {
                            if (!checkIpInSameSegment(gateway, G.initData.wanMask2, G.initData.wanGateway2, G.initData.wanMask2)) {
                                return _("%s and %s (%s) must be in the same network segment.", [_("Gateway"), _("WAN2 Port Gateway"), G.initData.wanGateway2]);
                            }
                        }
                    }
                }*/

            }

            /*判断目标网络是否重复*/
            var netExist = false;
            $("#portBody tr").each(function () {
                var existIP = $(this).find("td:eq(0)").html();

                if (network == existIP) {
                    netExist = true;
                    return false;
                }
            });

            if (netExist) {
                return _("The destination network exists.");
            }

        },

        success: function () {
            G.action = "add";
            staticRouterInfo.submit();
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

function initValue(obj) {
    var list = obj.routeList,
        i = 0,
        str = "";
    G.initData = obj;
    wanIp = obj.wanIp;

    for (i = 0; i < list.length; i++) {
        str += "<tr>";
        if (list[i].effective === "1") {
            str += "<td id='network" + (i + 1) + "'>" + (list[i].network || "") + "</td>";
            str += "<td id='mask" + (i + 1) + "'>" + (list[i].mask || "") + "</td>";
            str += "<td alt='gateway' id='gateway" + (i + 1) + "'>" + (list[i].gateway || "") + "</td>";
            str += "<td alt='interface' id='interface" + (i + 1) + "'>" + (list[i].ifname || "") + "</td>";
        } else {
            str += "<td title='" + _("This route will not take effect.") + "' id='network" + (i + 1) + "'>" + (list[i].network || "") + "</td>";
            str += "<td title='" + _("This route will not take effect.") + "' id='mask" + (i + 1) + "'>" + (list[i].mask || "") + "</td>";
            str += "<td title='" + _("This route will not take effect.") + "' alt='gateway' id='gateway" + (i + 1) + "'>" + (list[i].gateway || "") + "</td>";
            str += "<td title='" + _("This route will not take effect.") + "' alt='interface' id='interface" + (i + 1) + "'>" + (list[i].ifname || "") + "</td>";
        }

        if (list[i].operateType === "1") {
            str += "<td><span title='" + _("Delete") + "' class='delete'></span></td></tr>";
        } else {
            str += "<td><span>" + _("System") + "</span></td></tr>";
        }

    }
    listMax = list.length;
    $("#portBody").html(str);

    //WAN口
    if (top.G.wanNum === 1) {
        $("#singleWan").removeClass("none");
    } else {
        var str = "",
            i = 0;

        for (; i < top.G.wanNum; i++) {
            str += '<option value="WAN' + (i + 1) + '">WAN' + (i + 1) + '</option>';
        }
        $("#wanSelect").removeClass("none").html(str);
    }
    top.initIframeHeight();
    initTableHeight();
}

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
    staticRouterInfo = R.page(pageview, pageModel);
};