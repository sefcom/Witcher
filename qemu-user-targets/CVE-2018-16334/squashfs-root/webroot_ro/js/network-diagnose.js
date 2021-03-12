var lang = B.getLang();

function init() {
    var errMsg = {
        //DHCP
        "0001": _("No Ethernet cable is connected to the Internet port.  Please connect an Ethernet cable with internet connectivity to the Internet port."),
        "0002": _("Disconnected. Please click Save on the Internet Settings page to reconnect to the internet."),
        "0003": _("Connecting…"),
        "0004": _("Connected. Accessing the internet..."),
        "0005": _("No response from the remote server. Please check whether your computer can access the internet directly without the router. If no, contact your ISP for help."),
        "0006": _("Connected. Accessing the internet..."),
        "0007": _("Connected. You can access the internet now."),
        //静态：
        "0101": _("No Ethernet cable is connected to the Internet port.  Please connect an Ethernet cable with internet connectivity to the Internet port."),
        "0102": _("Disconnected. Please click Save on the Internet Settings page to reconnect to the internet."),
        "0103": _("Detecting the internet connection..."),
        "0104": _("Connected. Accessing the internet..."),
        "0105": _("No response from the remote server. Please check whether your computer can access the internet directly without the router. If no, contact your ISP for help."),
        "0106": _("Connected. Accessing the internet..."),
        "0107": _("Connected. You can access the internet now."),
        //PPPOE
        "0201": _("No Ethernet cable is connected to the Internet port.  Please connect an Ethernet cable with internet connectivity to the Internet port."),
        "0202": _("Disconnected. Please click Save on the Internet Settings page to reconnect to the internet."),
        "0203": _("Checking your user name and password. Please wait."),
        "0204": _("Dial-up success."),
        "0205": _("Failed to authenticate the user name and password from your ISP.  Please verify your user name and password and try again."),
        "0206": _("No response from the remote server. Please check whether your computer can access the internet directly without the router. If no, contact your ISP for help."),
        "0207": _("Disconnected. Please contact your ISP for help."),
        "0208": _("Connecting…"),
        "0209": _("Connected. You can access the internet now."),
        /************WISP**************/
        //DHCP 
        "1001": _("No repeating in WISP mode."),
        "1002": _("No repeating in WISP mode."),
        "1003": _("Repeating in WISP mode..."),
        "1004": _("Repeating in WISP mode succeeded. Accessing the internet..."),
        "1005": _("The router is connected to the upstream router but cannot access the internet.  Please verify that the upstream router can access the internet."),
        "1006": _("Repeating in WISP mode succeeded. Accessing the internet..."),
        "1007": _("Connected. You can access the internet now."),
        //静态 
        "1101": _("No repeating in WISP mode."),
        "1102": _("No repeating in WISP mode."),
        "1103": _("Repeating in WISP mode..."),
        "1104": _("Repeating in WISP mode succeeded. Accessing the internet..."),
        "1105": _("The router is connected to the upstream router but cannot access the internet.  Please verify that the upstream router can access the internet."),
        "1106": _("Repeating in WISP mode succeeded. Accessing the internet..."),
        "1107": _("Connected. You can access the internet now."),
        //APClinet
        "2001": _("No repeating in Client+AP mode."),
        "2002": _("Repeating in Client+AP mode..."),
        "2003": _("Repeating in Client+AP mode succeeded.")
    };

    var netState = {
        "connected": ["0007", "0107", "0209", "1007", "2003"],
        "connectting": ["0003", "0004", "0006", "0103", "0104", "0106", "0203", "0204", "0208", "1003", "1004", "1006", "1103", "1104", "1106"],
        "disconnect": ["0001", "0005", "0101", "0102", "0105", "0201", "0202", "0205", "0206", "0207", "1001", "1002", "1101", "1102", "1005", "1105", "2001"]
    }

    var initObj;

    function updateData(obj) {
        initObj = obj;

        var host = location.host;
        /*提取浏览器的输入框域名：如果不是www.tendawifi.com、tendawifi.com和故障检测IP，页面跳转到故障检测IP*/
        if ((obj.diagnoseIp !== host) && ('tendawifi.com' !== host)) {
            window.location.href = "http://" + obj.diagnoseIp;
            return;
        }

        var lastFourChar = obj.errcode.substr(1);
        $(".row").removeClass("none");
        $("#loadContainer").addClass("none");

        //Connected/connecting/disconnect display different image.
        if ($.inArray(lastFourChar, netState.connected) > -1) {
            $('#pic-container span').css("background-position", "0px 0px");
            $('#failToAccess').hide();
        } else if ($.inArray(lastFourChar, netState.disconnect) > -1) {
            $('#pic-container span').css("background-position", "0px 100px");
        } else if ($.inArray(lastFourChar, netState.connectting) > -1) {
            $('#pic-container span').css("background-position", "0px 50px");
        }

        $(".reason").html(errMsg[lastFourChar]);
        if (obj.errcode.charAt(0) === "1") {
            $("#errCode").html(_("Error Code:") + (parseInt(obj.errcode.charAt(1)) + 1) + obj.errcode.substr(2) + " ");
        } else {
            $("#errCode").html("");
        }
    }

    $.GetSetData.getJson("goform/GetNetErrInfo?" + Math.random(), updateData);

    ajaxInterval = new AjaxInterval({
        url: "goform/GetNetErrInfo",
        successFun: updateData,
        gapTime: 5000
    });

    $("#login").on("click", function () {
        window.location.href = initObj.loginurl + "?" + Math.random();
    });

    $("#no-more").on("click", function () {
        $.post('goform/NoNotify', "no-notify=true");
        $('#no-more').css("color", "#333");
        $('#no-more').attr("disabled", true);
    });
}

if (lang != B.options.defaultLang) {
    $.ajax({
        "type": "get",
        "url": "/lang/" + lang + "/translate.json" + "?" + Math.random(),
        "async": true,
        "cache": false,
        "dataType": "text",
        "success": function (data) {
            B.setMsg($.parseJSON(data));
            B.translatePage();
            init();
        }
    })
} else {
    document.documentElement.style.display = '';
    init();
}