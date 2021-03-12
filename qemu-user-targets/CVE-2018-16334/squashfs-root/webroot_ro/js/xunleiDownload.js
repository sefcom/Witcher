/**
 * slidePage
 * 对迅雷远程下载和绑定的状态作相应的隐藏显示，及文字显示
 * @param  {Object} obj 接口数据
 * @return 
 */
function slidePage(obj) {
    if (obj.thunderEn) {
        $("#thunderEn").attr("class", "btn-on");
        $("#thunderEnable").removeClass("none");
        $("#thunderDisable").addClass("none");
    } else {
        $("#thunderEn").attr("class", "btn-off");
        $("#thunderEnable").addClass("none");
        $("#thunderDisable").removeClass("none");
    }

    if (obj.bindStatus) {
        $("#bindStatus").css("color", "#343c48").html(_("Bound"));
        $("#unbind").parent().removeClass("none");
    } else {
        $("#bindStatus").css("color", "#f00").html(_("Not bound"));
        $("#unbind").parent().addClass("none");
    }
}

/**
 * submitData
 * 设置提交的数据
 * @param  {String} subType switch 或 unbind
 * @return 
 */
function submitData(subType) {
    var subData = "";

    if (subType == "switch") {
        if ($(this).hasClass("btn-on")) {
            subData = "thunderEn=false";
        } else {
            subData = "thunderEn=true";
        }
    } else {
        subData = "action=unbind";
    }

    $.GetSetData.setData("goform/setThundercfg", subData, function(str) {
        if (!top.isTimeout(str)) {
            return;
        }

        var num = $.parseJSON(str).errCode;
        if (num == 0) {
            initHtml();
        }
    });
}

function initHtml() {
    $.GetSetData.getJson("goform/getThundercfg", function(obj) {
        slidePage(obj);

        $("#signCode").html(obj.thunderCode);
    });
}

function initEvent() {
    $("#thunderEn").on("click", function() {
        submitData("switch");
    });

    $("#unbind").on('click', function() {
        submitData("unbind");
    });
}

window.onload = function() {
    initHtml();
    initEvent();
};
