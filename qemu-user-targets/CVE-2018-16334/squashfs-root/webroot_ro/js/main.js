var G = {
        menuChanging: false,
        iframeFlag: false,
        initPage: false,
        initYunFlag: false,
        wanNum: 0,
        homePage: "http://www.tenda.com.cn",
        deviceNameSpace: "Unknown",
        deviceList: [], //存储已有手机图标列表
        noDeviceList: [], //存储设备没有手机图标列表
        browserLang: ''
    },
    mainPageLogic, staInfo, netInfo, advInfo, wrlInfo, sysInfo,
    firstIn = false;
//数据本身有indexOf方法，但IE8文档模式不支持
Array.prototype.indexOf = function (val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};
Array.prototype.remove = function (val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};

function PageModel(url, subUrl) {
    // 获取数据的方式
    this.method = 'ajax';
    this.getUrl = url;
    this.subUrl = subUrl;
    G.browserLang = getBrowserLang();
    // 获取数据
    this.pGet = function (successCallback, errorCallback) {
        $.ajax({
            url: this.getUrl,
            cache: false,
            type: "GET",
            success: successCallback,
            error: errorCallback
        });
    };

    // 提交数据
    this.pSubmit = function (obj) {
        $.ajax({
            url: obj.url,
            type: "POST",
            data: obj.data,
            success: obj.callback
                //error: errorCallback
        });
    };
}

function PageView(modules) {
    this.modules = modules;

    // 初始化 页面视图
    this.init = function () {
        var len = this.module.length,
            i;

        // 循环初始各模块
        for (i = 0; i < len; i++) {
            this.module[i].initView();
            this.module[i].initControl();
        }
    };


    //获取页面元素数据
    //==========================================================================
    // 以元素 ID 来获取提交的值
    this.getVal = function (id) {
        return $('#' + id).val();
    };

    // 获取页面要提交的值， 有值返回对象，无这返回 null
    this.getSubmitData = function (data) {
        var ret = null;
        ret = data;
        return ret;
    };


    //页面元素显示相关
    //==========================================================================

    // 初始化页面最小高度
    this.initPageMiniHeight = function () {
        $('.main-section').css('min-height', ($.viewportHeight() - 20) + 'px');
        // $('#internet-form').css('min-height', '800px');


        // IE 6 not support min-height
        // In IE 6 height the same as min-height
        if (typeof isIE6 !== "undefined") {
            $('.main-section, .nav').css('height', ($.viewportHeight() - 20) + 'px');
        }

    };

    // 总体数据验证错误时处理入口
    this.showInvalidError = function (msg) {
        $('#page-message').html(msg || ' ');
        return;
    };

    // 数据提交成功时处理入口
    this.showSuccessful = function (msg) {
        $('#page-message').html(msg);
        location.reload();
    };

    // 数据 提交失败时处理入口
    this.showError = function (msg) {
        $('#page-message').html(msg);
    };

    // 获取数据 失败时处理入口
    this.showGetError = function (msg) {
        $('#page-message').html(msg);
    };

    this.showChangeMenu = function ($active, targetTop, callBack) {
        $active.stop().animate({
            "top": targetTop
        }, 100, "easeInOutExpo", callBack);
    };

    this.showChangePage = function ($active, hei, callBack) {
        $active.stop().animate({
            "margin-top": hei
        }, 0, "easeInOutExpo", callBack);
    };

    this.scrollTo = function ($active, targetMarginTop, callBack) {
        $active.stop().animate({
            "margin-top": targetMarginTop
        }, 100, "easeInOutExpo", callBack);
    };

    this.showScrollEnd = function ($cur, hei, callBack) {
        $cur.animate({
            "margin-top": hei
        }, 300).animate({
            "margin-top": 0
        }, 300, callBack);
    }

    //页面元素事件事件监听
    //==========================================================================

    // 导航事件监听
    this.addNavHandler = function (callBack) {
        $('#main-nav a').on('click', function (e) {
            e.preventDefault();
        });

        $('#main-nav a').on('click.menu', callBack);
    };

    // 给提交按钮添加，回调函数
    this.addSubmitHandler = function (callBack) {
        $('#subBtn').on('click', function (e) {
            e.preventDefault();
            callBack.apply();
        });
    };

    //语言选择
    $(".lang-toggle").on("click", function () {
        if ($(this).next().hasClass("none")) {
            $(this).next().removeClass("none")
        } else {
            $(this).next().addClass("none")
        }
    });
    $(".lang-menu a").on("click", function () {
        $(this).parents(".lang-toggle span").html($(this).html());
        $(this).parents(".lang-menu").addClass("none")
        B.setLang($(this).attr("data-country"));
        setTimeout("location.reload()", 300);
    })
    $(document).on("click", function (e) {
        if ($(e.target).parents(".lang-set").length == 0)
            $(".lang-menu ").addClass("none")
    });

}

function PageLogic(pageView, pageModel) {

    this.modelObj = "";
    // 给页面添加全局数据验证
    this.validate = $.validate({
        custom: function () {
            var returnVal;
            if (window[mainPageLogic.modelObj].checkValidate) {
                returnVal = window[mainPageLogic.modelObj].checkValidate(); //模块数据验证
            }

            if (returnVal != true) {
                return returnVal;
            }
        },

        success: function () {
            //var data = pageView.getSubmitData();
            var subObj = window[mainPageLogic.modelObj].preSubmit(); //数据提交
            if (subObj) {
                pageModel.pSubmit(subObj);
            }
        },

        error: function (msg) {
            pageView.showInvalidError(msg);
        }
    });

    this.initModule = function (id) {
        var menus = {
            "system-status": 'staInfo',
            "internet-setting": 'netInfo',
            "wireless-setting": "wrlInfo",
            "guest-setting": "guestInfo",
            "parent-control": "parentInfo",
            "usb-setting": "usbInfo",
            "vpn-setting": "vpnInfo",
            "advance": "advInfo",
            "system": "sysInfo"
        };
        mainPageLogic.modelObj = menus[id];
        //清空错误信息
        $(".validatebox-invalid").each(function () {
            this['data-check-error'] = false;
            $(this).removeClass("validatebox-invalid");
        });
        //$(".validatebox-invalid").removeClass("validatebox-invalid");
        $(".validatebox-tip").parent().remove();
        $.validate.utils.errorNum = 0;
        $.validateTipId = 0;

        window[menus[id]].init();
        G.initPage = true;

        //点击菜单。检查是否要跳到登录页面(检查开销比较小的定时重启接口，看是否返回登录页面)
        $.get("goform/GetSysAutoRebbotCfg?" + Math.random(), function callback(str) {
            if (str.indexOf("<!DOCTYPE") != -1) {
                location.reload(true);
            }
        });
    }

    this.changeMenu = function (curId, targetId) {

        // 如果在同一菜单, 直接放回不做其他操作
        if (curId === targetId) {
            return;
        }

        var menus = ["system-status", "internet-setting", "wireless-setting", "guest-setting", "parent-control", "vpn-setting", "usb-setting", "advance", "system", "other"],
            $cur = $('#' + curId),
            $target = $('#' + targetId),
            curHeight = $cur.height(),
            targetPageTop, targetMenuTop;

        G.menuChanging = true;
        $(".lang-menu").addClass("none");
        if (CONFIG_USB_MODULES == "n") {

            menus.remove("usb-setting");
        }

        // 初始化模块
        this.initModule(targetId);

        // 向 上 切换页面
        if (menus.indexOf(curId) > menus.indexOf(targetId)) {
            $target.addClass('active');
            $target.css('margin-top', '-' + $target.height() + 'px');

            targetPageTop = 0;

            // 向 下 却换页面
        } else if (menus.indexOf(curId) < menus.indexOf(targetId)) {
            $target.addClass('active');
            $target.css('margin-top', '0px');
            targetPageTop = -curHeight;
            $target = $cur;
        }

        // 却换页面内容动画
        pageView.showChangePage($target, targetPageTop, function () {
            G.menuChanging = false;
            $cur.removeClass('active');
        });

        // 却换左边菜单样式或动画
        targetMenuTop = $("#main-nav a").eq(menus.indexOf(targetId)).offset().top + 13;
        targetMenuTop -= $("#" + curId).offset().top;
        pageView.showChangeMenu($("#main-nav-label"), targetMenuTop, function () {
            $('#main-nav li').eq(menus.indexOf(curId)).removeClass('active');
            $('#main-nav li').eq(menus.indexOf(targetId)).addClass('active');
        });
        //window.location.hash = "#-" + targetId;
    };

    this.scorllPageUptoEndNum = 0;
    this.scorllPageDowntoEndNum = 0;
    this.scorllPage = function ($active, dir, targetId) {
        var viewHeight = $.viewportHeight(),
            curMarginTop = parseInt($active.css('margin-top'), 10),
            curHeight = $active.height(),
            difHeight = curHeight - viewHeight,
            targetMarginTop;

        if ((curMarginTop === 0 && dir === 'up') ||
            (dir === 'down' && curMarginTop === -difHeight)) {

            if (this.scorllPageUptoEndNum < 2 && this.scorllPageDowntoEndNum < 2) {
                if (curMarginTop === 0 && dir === 'up') {
                    this.scorllPageUptoEndNum += 1;
                    this.scorllPageDowntoEndNum = 0;
                } else if (dir === 'down' && curMarginTop === -difHeight) {
                    this.scorllPageUptoEndNum = 0;
                    this.scorllPageDowntoEndNum += 1;
                }
            } else {
                this.scorllPageUptoEndNum = 0;
                this.scorllPageDowntoEndNum = 0;
                this.changeMenu($active.attr('id'), targetId);
            }
            return;
        }

        if (dir === 'down') {
            targetMarginTop = curMarginTop - 120;
            targetMarginTop = (difHeight + targetMarginTop) > 0 ?
                targetMarginTop : -difHeight;

        } else if (dir === 'up') {
            targetMarginTop = curMarginTop + 120;

            targetMarginTop = targetMarginTop > 0 ?
                0 : targetMarginTop;
        }

        G.menuChanging = true;
        pageView.scrollTo($active, targetMarginTop, function () {
            G.menuChanging = false;
        });
    }

    // 实现最顶端或最底端回弹效果
    this.scorllEnd = function ($active, dir) {
        G.menuChanging = true;

        if (dir === 'down') {
            pageView.showScrollEnd($active, '-15%', function () {
                G.menuChanging = false;
            });
        } else if (dir === 'up') {
            pageView.showScrollEnd($active, '15%', function () {
                G.menuChanging = false;
            });
        }
    }

    this.onMenuClick = function (e) {
        var curId = $(".nav-list.active a")[0].href.split('#')[1],
            targetId = e.target.href.split('#')[1];

        if (curId == targetId) {
            this.initModule(targetId);
            return;
        }
        if (!G.menuChanging) {
            $(".nav-list.active").removeClass('active');
            $(e.target).parent().addClass('active');

            this.changeMenu(curId, targetId);
        }
    }

    this.onMousewheel = function (e, delta) {

        // 如果菜单切换中，不响应滚轮事件
        if (G.menuChanging) {
            return;
        }
        var dir = delta > 0 ? 'up' : 'down',
            wheelSpeed = Math.abs(delta),
            $curMenu = $(".nav-list.active"),
            curId = $curMenu.find('a')[0].href.split('#')[1],
            viewHeight = $.viewportHeight(),
            $cur = $('#' + curId),
            curHeight = $cur.height(),
            isScrollEnd = false,
            $targetMenu, targetId;

        // 滚轮向 上 滚
        if (delta > 0 && curId !== 'system-status') {
            $targetMenu = $curMenu.prev();

            // 滚轮向 下 滚
        } else if (delta < 0 && curId !== 'system') {
            $targetMenu = $curMenu.next();

            // 第一页且向上滚 或 最后一页且向下滚
        } else {
            $targetMenu = $curMenu;
            isScrollEnd = true;
        }
        targetId = $targetMenu.find('a')[0].href.split('#')[1];

        // 如果视窗高度 大于 当前页面高度，则执行页面却换
        if (viewHeight >= curHeight) {

            // 如果能有页面可以切换
            if (!isScrollEnd) {
                this.changeMenu(curId, targetId);
            }

            // 如果本页面还有内容没显示，则执行页面滚动
        } else {
            this.scorllPage($cur, dir, targetId);
        }
        // 如果滚动到尽头
        if (isScrollEnd) {
            this.scorllEnd($cur, dir);
        }
    }

    // 初始化页面
    this.init = function () {
        var that = this;

        // 先执行数据的获取，获取成功后执行 页面视图的初始化
        //pageModel.pGet(pageView.init, pageView.showGetError);

        if (!G.initPage) {
            var curId = "system-status";
            //var hashId = window.location.hash.replace("#-","");
            var hashId = "system-status";

            if ($("a[href=#" + hashId + "]").length != 0) {
                curId = hashId;
            }

            $("#main-nav li, .main-section").removeClass("active");
            $("a[href=#" + curId + "]").parents("li").addClass("active");
            $("#" + curId).addClass("active").css("margin-top", "0");
            /*setTimeout(function() {
                $("#main-nav-label").css("top",$("a[href=#"+curId+"]").offset().top + 13 + "px");
            },100);*/
            $("#main-nav-label").css("top", 73 + "px");
            this.initModule(curId);
            G.initPage = true;
        }

        pageView.addSubmitHandler(function (e) {
            that.validate.checkAll();
        });

        pageView.addNavHandler(function (e) {
            that.onMenuClick(e);
        });

        pageView.initPageMiniHeight();

        /*$("body").on('mousewheel', function (e, delta) {


            if ($("#gbx_overlay").length == 0 || $("#gbx_overlay").is(":hidden")) {
                that.onMousewheel(e, delta);
            }
        });*/

        $(window).resize(function () {
            pageView.initPageMiniHeight();
            initIframeHeight();
        });

        $(".iframe-close").off("click").on("click", closeIframe);
        $("body").delegate("#gbx_overlay", "click", function () {
            if (!top.iframeload) {
                return;
            }
            $(".main-dailog").addClass("none");
            $(".main-dailog").find("iframe").attr("src", "").removeClass("none");
            $("#iframe-msg").html("");
            $("#gbx_overlay").remove();
            if (window[top.mainPageLogic.modelObj] && typeof window[top.mainPageLogic.modelObj].initValue == "function") {
                if (top.mainPageLogic.modelObj != "sysInfo") {
                    window[top.mainPageLogic.modelObj].initValue();
                }
            }

        });
        closeIframe();
    };
}

$(function () {
    var getUrl = 'index.html',
        subUrl = 'subtest',
        mainPageModel = new PageModel(getUrl, subUrl),
        mainPageView = new PageView();

    mainPageLogic = new PageLogic(mainPageView, mainPageModel);
    mainPageLogic.init();
    //loginOut();

    //获取产品型号，显示或隐藏功能
    /*$.getJSON("goform/getProduct" + "?" + Math.random(), function (obj) {
        if (obj.product == "f1203") {
            //F1203没有wifi信号增强功能
            $("#adv_power").addClass("none");
        } else {
            $("#adv_power").removeClass("none");
        }
    });*/

    //通过宏控制是否有USB模块（值为“y”表示有）,有的话显示USB相关配置
    if (CONFIG_USB_MODULES == "y") {
        $("#nav-usb, .usb-line, .status-usb").removeClass("none");

        var modulesObj = {
                "usb_samba": CONFIG_FILE_SHARE,
                "usb_dlna": CONFIG_DLNA_SERVER,
                "usb_printer": CONFIG_PRINTER_SERVER
            },
            prop;

        for (prop in modulesObj) {
            if (modulesObj[prop] == "y") {
                $("#" + prop).removeClass("none");
            }
        }
        //有USB功能时，提示语会加上USB
        $("#power_usb_notice").html(_("If the Sleeping mode is enabled, the router will work in a power saving state and the LEDs, WiFi function, and portable USB storage device will enter the Hibernation state."));
    } else {

        $("#nav-usb").remove();
    }

    var langTxt = {
        "cn": "中文",
        "en": "English",
        "zh": "繁體中文"
    };
    $(".lang-toggle span").html(langTxt[B.getLang()]);

    if (top != window) {
        top.location.reload(true);
    }
});

//网络状态，外网设置要用到的联网状态
var statusTxtObj = {
    /*
    第一位传给页面判断是否有断开操作(1,可断开2没有断开)
    第二位传给页面显示颜色(1表示错误颜色、2表示尝试颜色、3表示成功颜色)
    第三位是否连接上(0表示未连上， 1表示连上)既是否显示联网时长
    第四位表示工作模式(0表示AP,1表示WISP,2表示APClient)
    第五位表示WAN口类型(0表示DHCP,1表示static IP,2表示PPPOE)
    第六位和第七位表示错误代码编号
    */
    /***********AP*********/
    //DHCP
    "1": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
    "2": _("Disconnected"),
    "3": _("Connecting…"), //(之前在1203里面这个状态表示保存了数据但是没有连接上去的情况下提示的，保留之前的)"
    "4": _("Connected. Accessing the internet..."),
    "5": _("Disconnected. Please contact your ISP for help."),
    "6": _("Connected. Accessing the internet..."),
    "7": _("Connected. You can access the internet now."),
    "8": _("No response from the remote server."),
    "9": _("Disconnecting…"),
    //静态：
    "101": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
    "102": _("Disconnected"),
    "103": _("Detecting the internet connection..."), //(之前在1203里面这个状态表示保存了数据但是没有连接上去的情况下提示的，保留之前的)"
    "104": _("Connected. Accessing the internet..."),
    "105": _("Disconnected. Please contact your ISP for help."),
    "106": _("Connected. Accessing the internet..."),
    "107": _("Connected. You can access the internet now."),
    "108": _("Disconnecting…"),
    //PPPOE
    "201": _("Please ensure that the cable between the Internet port of the router and the modem is properly connected."),
    "202": _("Disconnected"),
    "203": _("Checking your user name and password. Please wait."),
    "204": _("Dial-up success."),
    "205": _("The user name and password are incorrect."),
    "206": _("No response from the remote server. Please check whether your computer can access the internet directly using your Modem. If no, contact your ISP for help."),
    "207": _("Disconnected. Please contact your ISP for help."),
    "208": _("Connecting…"),
    "209": _("Connected. You can access the internet now."),
    "210": _("Disconnecting…"),
    /************WISP**************/
    //DHCP 
    "1001": _("No repeating in WISP mode."),
    "1002": _("No repeating in WISP mode."),
    "1003": _("Repeating in WISP mode..."),
    "1004": _("Repeating in WISP mode succeeded. Accessing the internet..."),
    "1005": _("Disconnected. Please contact your ISP for help."),
    "1006": _("Repeating in WISP mode succeeded. Accessing the internet..."),
    "1007": _("Connected. You can access the internet now."),
    "1008": _("Disconnecting…"),

    //静态 
    "1101": _("No repeating in WISP mode."),
    "1102": _("No repeating in WISP mode."),
    "1103": _("Repeating in WISP mode..."),
    "1104": _("Repeating in WISP mode succeeded. Accessing the internet..."),
    "1105": _("Disconnected. Please contact your ISP for help."),
    "1106": _("Repeating in WISP mode succeeded. Accessing the internet..."),
    "1107": _("Connected. You can access the internet now."),
    "1108": _("Disconnecting…"),
    //APClinet
    "2001": _("No repeating in Client+AP mode."),
    "2002": _("Repeating in Client+AP mode..."),
    "2003": _("Repeating in Client+AP mode succeeded.")
};

staInfo = {
    mouseOver: false,
    loading: false,
    checkBand: false,
    initObj: {},
    statusObj: null,
    hasusb: "0",
    time: "",
    wanTargetIndex: "",
    init: function () {

        if (!staInfo.loading) {

            if (CONFIG_USB_MODULES == "n") {
                $("#usbWrap").addClass("none");
                $("#usbWrap").parent().addClass("no-usb-content");
            }
            //点击新版本事件
            $(".directupgrade").on("click", function () {
                showIframe(_("Firmware Upgrade"), "directupgrade.html", 665, 556);
                clearTimeout(staInfo.time);
            });

            $("#onlineList").on("click", function () {
                showIframe(_("Manage Device"), "online_list.html", 800, 490);
            });

            $("#statusExtender").on("click", function () {
                staInfo.getHomeLink();
                showIframe(_("Extend WiFi Signal"), "status_extender.html", 800, 490);
            });


            $("#status_wl_more").on("click", function () {
                showIframe(_("WiFi Settings"), "wireless_ssid.html", 610, 490);
            });

            //跳转到WAN口设置界面，配置单WAN
            $("#wanStatusNumber .show-info-wan").on("click", function () {
                if ($(this).data("wan.status").charAt(3) != 2) { //非apclient
                    staInfo.wanTargetIndex = $(this).attr("data-target");
                    showIframe(_("Internet Settings"), "net_set.html", 620, 450);
                }
            });

            //
            $("#wispWrap .show-info-wisp").on("click", function () {
                showIframe(_("Wireless Repeating"), "wisp.html", 700, 350);
            });

            //
            $("#statusApWrap .show-info-ap").on("click", function () {
                showIframe(_("Diagnose"), "ap_diagnosis.html", 700, 350);
            });

            /*$("#wanStatusTxtWrap").on("click", function () {
                var wanStatus = $("#wanStatus").val();
                staInfo.showWanStatusPicIframe(parseInt(wanStatus.charAt(1)) - 1);
            });*/

            $("#usbWrap").on("click", function () {
                //if (staInfo.hasusb !== "0") {
                showIframe(_("USB App"), "status_usb.html", 620, 450);
                // }
            });

            $("#routerStatus").on("click", function () {
                showIframe(_("System Status"), "system_status.html", 530, 490);
            });

            //处理错误吗后几位为206的过长的状态信息
            $("#wanStatusTxtWrap").on("mouseenter", ".wan-status-detail-btn", function () {
                $(this).next().stop().fadeIn(200);
            }).on("mouseleave", ".wan-status-detail-btn", function () {
                $(this).next().stop().fadeOut(200);
            });


            var Msg = location.search.substring(1) || "0";
            if (Msg == 1) {
                mainPageLogic.changeMenu("system-status", "advance");
                return;
            }

            staInfo.loading = true;
        }
        staInfo.initValue();

        //staInfo.getHomeLink();
    },

    getHomeLink: function () {
        if (B.getLang() == "cn") {
            $.GetSetData.getData("goform/getHomeLink", function (str) {
                var obj = $.parseJSON(str);
                G.homePage = obj.homePageLink;
            });
        } else {
            G.homePage = "http://www.tendacn.com/en/product/A9.html";
            return;
        }
    },
    showWanStatusPicIframe: function (wanStatus) {
        /*switch (parseInt(wanStatus)) {
            case 0:
                showIframe(_("There is no Ethernet cable on the Internet port."), "wan_status.html", 610, 450, "wanStatus="+wanStatus);
                break;
        }*/
    },

    initValue: function () {
        if (mainPageLogic.modelObj == "staInfo") {
            clearTimeout(staInfo.time);
            staInfo.time = setTimeout("staInfo.initValue()", 5000);
        } else {
            clearTimeout(staInfo.time);
            return;
        }

        $.GetSetData.getJson("goform/GetRouterStatus", staInfo.setImage);
    },
    /*弹出页面后，取消循环取数据 ztt*/
    cancelValue: function () {
        mainPageLogic.modelObj = "";
    },

    setUSB: function (num) {
        $("#status_usb_txt").html(num + _("  "));
    },

    setImage: function (obj) {
        var selectedOffset = 0,
            speed_unit,
            option = {},
            i = 0,
            data = [];

        $(".row").removeClass("hidden");
        //WAN口数量
        G.wanNum = obj.wanInfo.length;
        if (getBrowserLang() != "CN") {
            G.wanNum = 1;
        }
        //路由器工作模式
        G.workMode = obj.workMode; //router ap client+ap

        staInfo.setUSB(obj.usbNum);

        if (G.workMode == "router") {
            $("#routerStatus .status-text-info").html(_("Router"));
            staInfo.setRouterMode(obj);
        } else if (G.workMode == "ap") {
            $("#routerStatus .status-text-info").html(_("My Router"));
            staInfo.setAPMode(obj);
        } else if (G.workMode == "wisp") {
            $("#routerStatus .status-text-info").html(_("My Router"));
            staInfo.setWispMode(obj);
        } else {
            $("#routerStatus .status-text-info").html(_("My Router"));
            staInfo.setApclientMode(obj);
        }
        if (!staInfo.hideMenu) {
            staInfo.showWorkMode(G.workMode);
            staInfo.hideMenu = true;
        }

        //显示基本信息  实时流量 WAN口IP 版本信息
        $("#curVersion").html(obj.onlineUpgradeInfo.curVersion);

        //AP模式版本号
        $("#apCurVersion").html(obj.onlineUpgradeInfo.curVersion);
        //发现新版本
        if (obj.onlineUpgradeInfo.newVersionExist === "1") {
            $(".directupgrade").removeClass("none");
        } else {
            $(".directupgrade").addClass("none");
        }

        //在线设备
        $("#clientNum").html(obj.clientNum + _("  "));

        staInfo.setWlStatus(obj);
        //staInfo.setLineUp(obj);
        //staInfo.statusObj = obj;
    },

    showWorkMode: function (workMode) {
        var hideArr = [];
        if (workMode == "ap" || workMode == "client+ap") {
            //
            $("[href='#internet-setting']").parent().addClass("none");
            $("[href='#guest-setting']").parent().addClass("none");
            $("[href='#parent-control']").parent().addClass("none");
            $("[href='#vpn-setting']").parent().addClass("none");
            hideArr = ["adv_netcontrol", "adv_firewall", "adv_ddns", "adv_virtualServer", "adv_dmz", "adv_iptv", "adv_route", "adv_upnp", "ip_mac_bind", "sys_wan", "adv_remoteweb"];
            if (workMode == "client+ap") {
                hideArr.push("adv_sleepMode");
            }
        } else if (workMode == "wisp") {
            $("[href='#guest-setting']").parent().addClass("none");
            hideArr = ["wrl_wps", "wrl_wifi_time", "adv_sleepMode", "adv_iptv"];
        }
        $(hideArr).each(function () {
            $("#" + this).addClass("none");
        })
    },

    setRouterMode: function (obj) {
        var uploadSpeedStr = 0,
            downloadSpeedStr = 0,
            i = 0,
            connectinternetFlag = 0,
            hasIpAddressFlag = 0;

        $("#internetWrap").removeClass("none");
        $("#wanStatusNumber").removeClass("none");
        $("#wispWrap").addClass("none");
        $("#statusApWrap").addClass("none");
        $("#statusWanInfo").removeClass("none");
        $("#statusApInfo").addClass("none");

        if (G.wanNum == 1) {
            $("#wan2Wrap").addClass("none");
            $("#wanStatusNumber").removeClass("multi-wan");
            $("#statusMutilWanInfo").removeClass("multi-wan");
        } else if (G.wanNum == 2) {
            $("#statusMutilWanInfo").addClass("multi-wan");
            $("#wan2Wrap").removeClass("none");
            $("#wanStatusNumber").addClass("multi-wan");
        } else {
            $("#wan2Wrap").addClass("none");
            $("#wanStatusNumber").removeClass("multi-wan");
            $("#statusMutilWanInfo").removeClass("multi-wan");
        }

        $("#statusWan1Ip").html("");
        $("#statusWan2Ip").html("");
        for (i = 0; i < G.wanNum; i++) {
            uploadSpeedStr += Number(obj.wanInfo[i].wanUploadSpeed);
            downloadSpeedStr += Number(obj.wanInfo[i].wanDownloadSpeed);
            $("#statusWan" + (i + 1) + "Ip").html(obj.wanInfo[i].wanIp || "--");

            //获取到WAN IP地址时
            if ($("#statusWan" + (i + 1) + "Ip").html() != "--") {
                hasIpAddressFlag++
            }

            //处理连接状态
            if (obj.wanInfo[i].wanStatus.charAt(1) == "3") { //已连接
                $("#wan" + (i + 1) + "StatusWrap").addClass("none");
                connectinternetFlag++;
            } else if (obj.wanInfo[i].wanStatus.charAt(1) == "1") { //连接中
                $("#wan" + (i + 1) + "StatusWrap").removeClass("none");
            } else {
                $("#wan" + (i + 1) + "StatusWrap").removeClass("none");
            }
            $("#wan" + (i + 1) + "StatusWrap").find(".show-info-wan").data("wan.status", obj.wanInfo[i].wanStatus);
            $("#wan" + (i + 1) + "StatusWrap").prev(".show-info-wan").data("wan.status", obj.wanInfo[i].wanStatus);
        }

        if (connectinternetFlag === 0) { //未能连接到互联网
            $("#internetWrap .status-internet-disable").removeClass("none");
        } else {
            $("#internetWrap .status-internet-disable").addClass("none");
        }

        if (hasIpAddressFlag === 0) { //全部未获取IP地址时
            $("#statusUploadSpeed").parent().next().removeClass("none");
            $("#statusUploadSpeed").parent().addClass("none");
        } else {
            $("#statusUploadSpeed").parent().removeClass("none");
            $("#statusUploadSpeed").parent().next().addClass("none");
            $("#statusUploadSpeed").html(translateSpeed(uploadSpeedStr));
            $("#statusDownloadSpeed").html(translateSpeed(downloadSpeedStr));
        }

    },

    setAPMode: function (obj) {
        $("#internetWrap").addClass("none");
        $("#wanStatusNumber").addClass("none");
        $("#wispWrap").addClass("none");
        $("#statusApWrap").removeClass("none");
        $("#statusWanInfo").addClass("none");
        $("#statusApInfo").removeClass("none");

        $("#statusLanMac").html(obj.lanMAC);
        $("#statusLanIp").html(obj.lanIP);

        //TODO: 设置是否连接上级成功
        if (obj.apStatus === "2303002") {
            $("#apStatusNumber .line-status-disable").addClass("none");
        } else if (obj.apStatus === "2103001") {
            $("#apStatusNumber .line-status-disable").removeClass("none");
        } else {
            $("#apStatusNumber .line-status-disable").removeClass("none");
        }

    },

    setWispMode: function (obj) {
        var wispConnectStatus = obj.wanInfo[0].wanStatus;
        $("#internetWrap").addClass("none");
        $("#wanStatusNumber").addClass("none");
        $("#wispWrap").removeClass("none");
        $("#statusApWrap").addClass("none");

        //if (G.workMode == "wisp") { //wisp
        $("#statusWanInfo").removeClass("none");
        $("#statusApInfo").addClass("none");

        $("#statusWan1Ip").html(obj.wanInfo[0].wanIp || "--");
        $("#statusWan2Ip").html("");

        //}

        //1表示连接成功  0表示连接失败
        if (wispConnectStatus.charAt(2) == "1") { //连接成功
            $("#wispConnectStatus").addClass("none");
            $("#statusUploadSpeed").html(translateSpeed(obj.wanInfo[0].wanUploadSpeed));
            $("#statusDownloadSpeed").html(translateSpeed(obj.wanInfo[0].wanDownloadSpeed));
            $("#statusUploadSpeed").parent().removeClass("none");
            $("#statusUploadSpeed").parent().next().addClass("none");

            $("#statusWispConnect").attr("class", "status-connect-wisp show-info-wisp");
        } else {
            //连接失败时不显示实时网速
            $("#wispConnectStatus").removeClass("none");
            $("#statusUploadSpeed").parent().addClass("none");
            $("#statusUploadSpeed").parent().next().removeClass("none");
            $("#statusWispConnect").attr("class", "status-connect-wisp-disable show-info-wisp");
        }
    },

    setApclientMode: function (obj) {
        var apConnectStatus = obj.wanInfo[0].wanStatus;
        apConnectStatus = apConnectStatus.slice(-4);
        if (apConnectStatus == "2003") {
            $("#wispConnectStatus").addClass("none");
            $("#statusWispConnect").attr("class", "status-connect-wisp show-info-wisp");
        } else {
            $("#wispConnectStatus").removeClass("none");
            $("#statusWispConnect").attr("class", "status-connect-wisp-disable show-info-wisp");
        }

        $("#internetWrap").addClass("none");
        $("#wanStatusNumber").addClass("none");
        $("#wispWrap").removeClass("none");
        $("#statusApWrap").addClass("none");

        $("#statusWanInfo").addClass("none");
        $("#statusApInfo").removeClass("none");

        $("#statusLanMac").html(obj.lanMAC);
        $("#statusLanIp").html(obj.lanIP);
    },

    setWlStatus: function (obj) {

        $(".status-wl-info").removeClass("none");
        if (obj.wl24gEn == "1") {
            $("#status_wl_info_txt .wl24g-name").html(obj.wl24gName.replace(/[\s]/g, "&nbsp;")).attr("title", obj.wl24gName);
        } else {
            //$("#status_wl_info_txt .wl24g-sec").addClass("none");
            $("#status_wl_info_txt .wl24g-name").html(_("Disable"));
        }

        if (obj.wl5gEn == "1") {
            $("#status_wl_info_txt .wl5g-name").html(obj.wl5gName.replace(/[\s]/g, "&nbsp;")).attr("title", obj.wl5gName);
        } else {
            //$("#status_wl_info_txt .wl5g-sec").addClass("none");
            $("#status_wl_info_txt .wl5g-name").html(_("Disable"));
        }

        if (obj.wl5gEn == "0" && obj.wl24gEn == "0") {
            //$(".status-wl-info").addClass("none");
            $("#status_wl_more").addClass("wireless-line-disable").removeClass("wireless-line-up");
        } else {
            //$(".status-wl-info").removeClass("none");
            $("#status_wl_more").removeClass("wireless-line-disable").addClass("wireless-line-up");
        }
    }
}

function wanTypeSelect(wan_type, wanIndex) {
    var wanFlagArr = ["", "", "2"],
        wanFlag = wanFlagArr[wanIndex];

    //pptp客户端开启
    if (wanIndex == "1") {
        if (netInfo.clientFlag[wanIndex - 1] === "1") {
            if ((wan_type === "3") || (wan_type === "4")) {
                $(".select-tab").html(_("Changing the settings will disable the VPN function."));
            } else {
                $(".select-tab").html("");
            }
        } else {
            $(".select-tab").html("");
        }
    }

    switch (parseInt(wan_type)) {
    case 0:
        {
            $("#ppoe_set" + wanFlag).addClass("none");
            $("#double_access" + wanFlag).addClass("none");
            $("#dnsType" + wanFlag).removeClass("none");
            $("#static_ip" + wanFlag).addClass("none");
            if (netInfo.currentWanType[wanIndex - 1] === "0") {
                if (netInfo.currentDnsType[wanIndex - 1] === "1") {
                    $("#dnsAuto" + wanFlag).val("1");
                    $("#dnsContainer" + wanFlag).addClass("none");
                } else {
                    $("#dnsAuto" + wanFlag).val("0");
                    $("#dnsContainer" + wanFlag).removeClass("none");
                }
            } else {
                $('#dnsAuto' + wanFlag).val("1");
                $("#dnsContainer" + wanFlag).addClass("none");
            }

            break;
        }
    case 1:
        {
            $("#ppoe_set" + wanFlag).addClass("none");
            $("#double_access" + wanFlag).addClass("none");
            $("#dnsType" + wanFlag).addClass("none");
            $("#static_ip" + wanFlag).removeClass("none");
            $("#dnsContainer" + wanFlag).removeClass("none");

            break;
        }
    case 2:
        $("#ppoe_set" + wanFlag).removeClass("none");
        $("#double_access" + wanFlag).addClass("none");
        $("#dnsType" + wanFlag).removeClass("none");
        $("#static_ip" + wanFlag).addClass("none");
        if (netInfo.currentWanType[wanIndex - 1] === "2") {
            if (netInfo.currentDnsType[wanIndex - 1] === "1") {
                $("#dnsAuto" + wanFlag).val("1");
                $("#dnsContainer" + wanFlag).addClass("none");
            } else {
                $("#dnsAuto" + wanFlag).val("0");
                $("#dnsContainer" + wanFlag).removeClass("none");
            }
        } else {
            $('#dnsAuto' + wanFlag).val("1");
            $("#dnsContainer" + wanFlag).addClass("none");
        }
        break;
    case 3: //多WAN没有以下情况
        {
            $("#ppoe_set").addClass("none");
            $("#double_access").removeClass("none");
            $("#double_access #serverInfo").removeClass("none");

            if (netInfo.currentWanType[wanIndex - 1] === "3") {
                if (netInfo.currentVpnType[wanIndex - 1] === "1") {
                    $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                    $("#dnsType").removeClass("none");
                    $("#static_ip").addClass("none");
                    if (netInfo.currentDnsType[wanIndex - 1] === "1") {
                        $('#dnsAuto').val("1");
                        $("#dnsContainer").addClass("none");
                    } else {
                        $('#dnsAuto').val("0");
                        $("#dnsContainer").removeClass("none");
                    }
                } else {
                    $('[name="vpnWanType"]:eq(1)').prop("checked", true);
                    $("#dnsType").addClass("none");
                    $("#static_ip").removeClass("none");
                    $("#dnsContainer").removeClass("none");
                }
            } else {
                $("#dnsType").removeClass("none");
                $('#dnsAuto').val("1");
                $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                $("#static_ip").addClass("none");
                $("#dnsContainer").addClass("none");
            }

            break;
        }
    case 4:
        {
            $("#ppoe_set").addClass("none");
            $("#double_access").removeClass("none");
            $("#double_access #serverInfo").removeClass("none");

            if (netInfo.currentWanType[wanIndex - 1] === "4") {
                if (netInfo.currentVpnType[wanIndex - 1] === "1") {
                    $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                    $("#dnsType").removeClass("none");
                    $("#static_ip").addClass("none");
                    if (netInfo.currentDnsType[wanIndex - 1] === "1") {
                        $('#dnsAuto').val("1");
                        $("#dnsContainer").addClass("none");
                    } else {
                        $('#dnsAuto').val("0");
                        $("#dnsContainer").removeClass("none");
                    }
                } else {
                    $('[name="vpnWanType"]:eq(1)').prop("checked", true);
                    $("#dnsType").addClass("none");
                    $("#static_ip").removeClass("none");
                    $("#dnsContainer").removeClass("none");
                }
            } else {
                $("#dnsType").removeClass("none");
                $('#dnsAuto').val("1");
                $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                $("#static_ip").addClass("none");
                $("#dnsContainer").addClass("none");
            }

            break;
        }
    case 5:
        {
            $("#ppoe_set").removeClass("none");
            $("#double_access").removeClass("none");
            $("#double_access #serverInfo").addClass("none");

            if (netInfo.currentWanType[wanIndex - 1] === "5") {
                if (netInfo.currentVpnType[wanIndex - 1] === "1") {
                    $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                    $("#dnsType").removeClass("none");
                    $("#static_ip").addClass("none");
                    if (netInfo.currentDnsType[wanIndex - 1] === "1") {
                        $('#dnsAuto').val("1");
                        $("#dnsContainer").addClass("none");
                    } else {
                        $('#dnsAuto').val("0");
                        $("#dnsContainer").removeClass("none");
                    }
                } else {
                    $('[name="vpnWanType"]:eq(1)').prop("checked", true);
                    $("#dnsType").addClass("none");
                    $("#static_ip").removeClass("none");
                    $("#dnsContainer").removeClass("none");
                }
            } else {
                $("#dnsType").removeClass("none");
                $('#dnsAuto').val("1");
                $('[name="vpnWanType"]:eq(0)').prop("checked", true);
                $("#static_ip").addClass("none");
                $("#dnsContainer").addClass("none");
            }
            break;
        }
    default:
        break;
    }
}

netInfo = {
    loading: false,
    time: 0,
    isConnect: false, //是否已经连上，即按钮是连接还是断开
    hasConnTime: false, //是否有联网时长
    saveType: "connect", //操作类型，是连接（connect）还是断开（disconnect）
    currentWanType: 0,
    currentDnsType: "1",
    currentVpnType: "1",
    clientFlag: "0",
    ajaxInterval: null,
    initObj: null,
    saving: false, //保存中，连接中或断开中
    clickSaveBtn: "", //判断点击的是哪个保存
    init: function () {

        //判断是否修改了WAN参数
        netInfo.wanBtnChange = false;
        netInfo.wanBtnChange2 = false;
        if (!netInfo.loading) {
            $("#netWanType").on("change", netInfo.changeWanType);
            $("[name='vpnWanType']").on("click", netInfo.changeVpnType);
            $('#dnsAuto').on('change', netInfo.changeDnsAuto);
            $("#wan1SetWrap").delegate("input,select", "change.re", function () {
                netInfo.wanBtnChange = true;
                $("#wan_submit").val(_("Connect"));
            });

            $("#wan1SetWrap").delegate("#downSpeedLimit .dropdown-menu a", "click.re", function () {
                if (netInfo.initObj.wanInfo[0].downSpeedLimit == $(this).parent().attr("data-val")) {
                    return;
                }
                netInfo.wanBtnChange = true;
                $("#wan_submit").val(_("Connect"));
            });

            $("#wan2SetWrap").delegate("input,select", "change.re", function () {
                netInfo.wanBtnChange2 = true;
                $("#wan_submit2").val(_("Connect"));
            });

            $("#wan2SetWrap").delegate("#downSpeedLimit2 .dropdown-menu a", "click.re", function () {
                if (netInfo.initObj.wanInfo[1].downSpeedLimit == $(this).parent().attr("data-val")) {
                    return;
                }
                netInfo.wanBtnChange2 = true;
                $("#wan_submit2").val(_("Connect"));
            });

            var selectObj = {
                "initVal": "",
                "editable": "1",
                "seeAsTrans": true,
                "options": [{
                    //"0": _("Denied"),
                    "10": "10",
                    "20": "20",
                    "50": "50",
                    "100": "100",
                    ".divider": ".divider",
                    ".hand-set": _("Manual")
                }]
            };

            $("#downSpeedLimit").toSelect(selectObj);
            $("#downSpeedLimit input[type='text']").inputCorrect("num").attr("maxLength", "4").css("width", "239px");
            $("#downSpeedLimit2").toSelect(selectObj);
            $("#downSpeedLimit2 input[type='text']").inputCorrect("num").attr("maxLength", "4").css("width", "239px");

            $("#downSpeedLimit input[type='text']").on("keyup.re", function () {
                $("#downSpeedLimit").val(this.value);
            });
            $("#downSpeedLimit2 input[type='text']").on("keyup.re", function () {
                $("#downSpeedLimit2").val(this.value);
            });

            $("#wan_submit").on("click", function () {
                if (!this.disabled) {
                    netInfo.clickSaveBtn = "wan_submit";
                    mainPageLogic.validate.checkAll("wan1SetWrap");
                }
            });

            $("#all_submit").on("click", function () {
                if (!this.disabled) {
                    netInfo.clickSaveBtn = "all_submit";
                    mainPageLogic.validate.checkAll("internet-form");

                }
            });
            $("#multiWanEn").on("click", function () {
                var className = $(this).attr("class");
                if (className == "btn-off") {
                    $(this).attr("class", "btn-on");
                    $(this).val("true");
                } else {
                    $(this).attr("class", "btn-off");
                    $(this).val("false");
                }

                //判断切换双WAN开关
                if ($(this).val() != netInfo.initObj.multiWanEn) {
                    $("#wan_submit2").parent().addClass("none");
                    $("#wan_submit").parent().addClass("none");
                    $("#all_submit").parent().removeClass("none");

                    /*//从关闭到开启 主动清空带宽控制值
                    if (netInfo.initObj.multiWanEn == "false") {
                        $("#downSpeedLimit")[0].val("");
                        $("#downSpeedLimit").val("");
                        $("#downSpeedLimit").removeValidateTip(true)
                        $("#downSpeedLimit2")[0].val("");
                        $("#downSpeedLimit2").val("");
                        $("#downSpeedLimit2").removeValidateTip(true)
                    }*/

                } else {

                    $("#wan_submit2").parent().removeClass("none");
                    $("#wan_submit").parent().removeClass("none");
                    $("#all_submit").parent().addClass("none");

                }

                netInfo.changeMutilWan();
            });

            $.validate.valid.ppoe = {
                all: function (str) {
                    var ret = this.specific(str);

                    if (ret) {
                        return ret;
                    }
                },
                specific: function (str) {
                    var ret = str;
                    var rel = /[^\x00-\x80]|[~;'&"%\s]/;
                    if (rel.test(str)) {
                        return _("Can't contain ~;'&\"% and space and Chinese character.");
                    }
                }
            }

            $.validate.valid.wanmask = {
                all: function (str) {
                    var rel = /^(255|254|252|248|240|224|192|128)\.0\.0\.0$|^(255\.(254|252|248|240|224|192|128|0)\.0\.0)$|^(255\.255\.(254|252|248|240|224|192|128|0)\.0)$|^(255\.255\.255\.(255|254|252|248|240|224|192|128|0))$/;
                    if (!rel.test(str)) {
                        return _("Please enter a valid subnet mask.");
                    }

                }
            };

            $("#staticIp,#mask,#gateway,#dns1,#dns2").inputCorrect("ip");

            //WAN 2
            $("#netWanType2").on("change", netInfo.changeWan2Type);
            $('#dnsAuto2').on('change', netInfo.changeWan2DnsAuto);

            $("#wan_submit2").on("click", function () {
                if (!this.disabled) {
                    netInfo.clickSaveBtn = "wan_submit2";
                    mainPageLogic.validate.checkAll("wan2SetWrap");
                }
            });

            $("#staticIp2,#mask2,#gateway2,#dns12,#dns22").inputCorrect("ip");

            netInfo.loading = true;
        }

        $("#gateway, #gateway2").attr("data-options", '{"type":"ip","msg":"' + _("Please enter a correct gateway IP address.") + '"}');
        $("#dns1, #dns1").attr("data-options", '{"type":"ip","msg":"' + _("Please enter the IP address of the primary DNS server.") + '"}');
        $("#dns2, #dns2").attr("data-options", '{"type":"ip","msg":"' + _("Please enter the IP address of the secondary DNS server.") + '"}');

        $.GetSetData.getJson("goform/getWanParameters?" + Math.random(), function (obj) {
            netInfo.initObj = obj;
            //定时刷新器
            if (!netInfo.ajaxInterval) {
                netInfo.ajaxInterval = new AjaxInterval({
                    url: "goform/getWanParameters",
                    successFun: function (data) {
                        netInfo.setValue(data);
                    },
                    gapTime: 5000
                });
            } else {
                netInfo.ajaxInterval.startUpdate();
            }
            netInfo.wanNum = obj.wanInfo.length;

            //wisp下没有pppoe拨号
            var wanOptStr = "";
            if (obj.wl_mode !== "wisp") {
                wanOptStr += '<option value="2">' + _("PPPoE") + '</option>';
            }

            wanOptStr += '<option value="0">' + _("Dynamic IP Address") + '</option><option value="1">' + _("Static IP Address") + '</option>';
            if (obj.wl_mode !== "wisp") { //wisp 隐藏pppoe选择框

                if (G.browserLang === "RU") {
                    wanOptStr += '<option value="3">' + _("Russia PPTP") + '</option><option value="4">' + _("Russia L2TP") + '</option><option value="5">' + _("Russia PPPoE");
                }
            }

            $("#netWanType").html(wanOptStr);
            $("#netWanType").val(obj.wanInfo[0].wanType);
            inputValue(obj.wanInfo[0]);
            $("#downSpeedLimit input[type='text']").val(obj.wanInfo[0].downSpeedLimit);
            $("#downSpeedLimit")[0].val(obj.wanInfo[0].downSpeedLimit);

            //if (obj.multiWanEn == "true") {
            if (obj.wanInfo.length == 2) {
                $("#netWanType2").val(obj.wanInfo[1].wanType);
                var wan2Obj = {};
                for (var prop in obj.wanInfo[1]) {
                    wan2Obj[prop + "2"] = obj.wanInfo[1][prop]
                }
                inputValue(wan2Obj);
                $("#downSpeedLimit2 input[type='text']").val(obj.wanInfo[1].downSpeedLimit);
                $("#downSpeedLimit2")[0].val(obj.wanInfo[1].downSpeedLimit);

            }

            $('#adslUser').addPlaceholder(_("Enter the user name from your ISP."));
            $('#adslUser2').addPlaceholder(_("Enter the user name from your ISP."));
            if (firstIn === false) {
                $('#adslPwd').initPassword(_("Enter the password from your ISP."), false, false);
                $('#adslPwd2').initPassword(_("Enter the password from your ISP."), false, false);
                firstIn = true;
            }
            $('#vpnPwd').initPassword(_(""), false, false);

            //client+ap 不允许配置外网设置，隐藏配置内容
            /*if (obj.wl_mode == "apclient") {
                $("#notAllowTip").removeClass("none");
                $("#connectStatusWrap, #connect_time").addClass("none");
                $("#wan_submit, #netWanType").prop("disabled", true);
                return;
            } else {
                $("#notAllowTip").addClass("none");
                if (!netInfo.saving) {
                    $("#wan_submit, #netWanType").prop("disabled", false);
                    $("#dnsAuto, #dns1, #dns2").prop("disabled", false);
                }
            }*/

            //有双WAN 并且路由模式 并且国家代码是中国
            if (CONFIG_WAN_NUMBER == 2 && G.workMode == "router" && G.browserLang === "CN") {
                $("#mutliWanWrap").removeClass("none");
            } else {
                obj.multiWanEn = "false";
                $("#mutliWanWrap").addClass("none");
            }
            if (obj.multiWanEn == "true") {
                $("#multiWanEn").attr("class", "btn-on");
            } else {
                $("#multiWanEn").attr("class", "btn-off");
            }
            netInfo.changeMutilWan();
            netInfo.setValue(obj);
            netInfo.changeWanType();
            if (obj.wanInfo.length == 2) {
                netInfo.changeWan2Type()
            }
        });
    },
    setValue: (function () {
        var statusType = 1, //连接状态类型，1错误， 2尝试，3成功
            isConnect = 1, //是否接上（显示接入时长）0未接上 1接上 
            statusClasses = ["text-error", "text-warning", "text-success"],
            obj = [],
            wanFlagArr = ["", "2"],
            wanIndex;

        return function (dataObj) {

            //如果当前连接方式不是所选方式，不更新
            if ( /*dataObj.wanType != $("#netWanType").val() ||*/ mainPageLogic.modelObj != "netInfo" /*|| dataObj.wl_mode == "apclient"*/ ) {
                netInfo.ajaxInterval.stopUpdate();
                return;
            }
            clearTimeout(netInfo.time);
            netInfo.currentWanType = [];
            netInfo.currentDnsType = [];
            netInfo.currentVpnType = [];
            netInfo.clientFlag = [];
            netInfo.hasConnTime = [];
            netInfo.isConnect = [];

            for (var i = 0; i < dataObj.wanInfo.length; i++) {
                obj = dataObj.wanInfo[i];
                netInfo.currentWanType.push(obj["wanType"]);
                netInfo.currentDnsType.push(obj["dnsAuto"]);
                netInfo.currentVpnType.push(obj["vpnWanType"]);
                netInfo.clientFlag.push(obj["vpnClient"]);
                //netInfo.dns1 = obj["dns1"];
                // netInfo.dns2 = obj["dns2"];

                wanIndex = wanFlagArr[i];

                //是否已插网线
                if (dataObj.lineUp.split("")[i] == "1") {
                    $("#lineUpImg" + wanIndex).attr("class", "line-up");
                    $("#lineUpTips" + wanIndex).html(_("Ethernet cable connected"));
                } else {
                    $("#lineUpImg" + wanIndex).attr("class", "line-off");
                    $("#lineUpTips" + wanIndex).html(_("Ethernet cable disconnected"));
                }

                //联网状态
                $("#connectStatus" + wanIndex).html(statusTxtObj[parseInt(obj["connectStatus"].substr(obj["connectStatus"].length - 4), 10) + ""]);

                statusType = parseInt(obj["connectStatus"].charAt(1), 10);
                $("#connectStatus" + wanIndex).attr("class", statusClasses[statusType - 1]);
                $("#connectStatusWrap" + wanIndex).removeClass("none");

                //联网时长
                isConnect = parseInt(obj["connectStatus"].charAt(2), 10);
                $("#connectTime" + wanIndex).html(formatSeconds(obj["connectTime"]));
                /*setTimeout(function () {
                    $("#connectTime" + wanIndex).html(formatSeconds(parseInt(obj["connectTime"], 10) + 1))
                }, 1000);*/
                if (isConnect == 1) {
                    $("#connect_time" + wanIndex).removeClass("none");

                    if (!netInfo["wanBtnChange" + wanIndex]) {
                        $("#wan_submit" + wanIndex).val(_("Disconnect"));
                    }

                } else {
                    $("#connect_time" + wanIndex).addClass("none");
                    $("#wan_submit" + wanIndex).val(_("Connect"));
                }
                netInfo.hasConnTime.push(isConnect == 1 ? true : false);

                //状态码第一个决定按钮是连接还是断开
                netInfo.isConnect.push(parseInt(obj["connectStatus"].charAt(0)) == 1 ? true : false);

                //pptp客户端开启
                if (netInfo.clientFlag[i] === "1") {
                    if (($('#netWanType' + wanIndex).val() === "3") || ($('#netWanType' + wanIndex).val() === "4")) {
                        $(".select-tab").html(_("Changing the settings will disable the VPN function."));
                    } else {
                        $(".select-tab").html("");
                    }
                } else {
                    $(".select-tab").html("");
                }
            }

            netInfo.changeWanType();
            if (dataObj.wanInfo.length == 2 && dataObj.multiWanEn == "true") {
                netInfo.changeWan2Type();
            }
        }
    })(),
    changeMutilWan: function () {

        if (CONFIG_WAN_NUMBER != 2) {
            $("#multiWanEn").attr("class", "btn-off")
        }
        //切换双WAN之后的显示/隐藏
        if ($("#multiWanEn").hasClass("btn-on")) {
            netInfo.wanNum = 2;
            $("#wanLabelTis").html(_("WAN1 Port:"));
            $("#wan2SetWrap").removeClass("none");
            $("#downSpeedLimit").parent().parent().removeClass("none");
            $("#multiWanOffTips").addClass("none");
            $("#multiWanEnTips").removeClass("none");
        } else {
            netInfo.wanNum = 1;
            $("#wanLabelTis").html(_("WAN Port:"));
            $("#wan2SetWrap").addClass("none");
            $("#downSpeedLimit").parent().parent().addClass("none");
            $("#multiWanOffTips").removeClass("none");
            $("#multiWanEnTips").addClass("none");
        }
    },
    checkWanData: function (formWrap) {
        //internet-form/wan1SetWrap/wan2SetWrap
        //检查数据合法性
        var lanIp = netInfo.initObj.lanIp,
            lanMask = netInfo.initObj.lanMask,
            wanObj = {
                wanForm: "wan1SetWrap",
                wan_type: $("#netWanType").val(),
                vpnWanType: $("[name='vpnWanType']:checked").val(),
                wanIp: netInfo.initObj.wanInfo[0].wanIp,
                dnsAuto: $("#dnsAuto").val(),
                ip: $("#staticIp").val(),
                mask: $("#mask").val(),
                gw: $("#gateway").val(),
                dns1: $("#dns1").val(),
                dns2: $("#dns2").val(),
                ppoe_user: $("#adslUser").val(),
                ppoe_pwd: $("#adslPwd").val(),
                server: $("#vpnServer").val(),
                btn_val: $("#wan_submit").val(),
                wanNumMsg: _("WAN IP Address")
            },
            wanObj2 = {},
            errMsg = "",
            dataArr = [],
            btn_val, server, wan_type, vpnWanType, ip, mask,
            gw, dns1, dns2, ppoe_user, ppoe_pwd, dnsAuto, wanMsg,
            wanIpArr = [];

        if (netInfo.wanNum == 2) {
            wanObj.wanNumMsg = _("WAN1 IP Address");
            wanObj2 = {
                wanForm: "wan2SetWrap",
                wan_type: $("#netWanType2").val(),
                ppoe_user: $("#adslUser2").val(),
                ppoe_pwd: $("#adslPwd2").val(),
                //wanIp: netInfo.initObj.wanInfo[1].wanIp || "0.0.0.0",
                dnsAuto: $("#dnsAuto2").val(),
                ip: $("#staticIp2").val(),
                mask: $("#mask2").val(),
                gw: $("#gateway2").val(),
                dns1: $("#dns12").val(),
                dns2: $("#dns22").val(),
                btn_val: $("#wan_submit2").val(),
                wanNumMsg: _("WAN2 IP Address")
            };
            if (netInfo.initObj.wanInfo[1] && netInfo.initObj.wanInfo[1].wanIp) {
                wanObj2.wanIp = netInfo.initObj.wanInfo[1].wanIp;
            } else {
                wanObj2.wanIp = "0.0.0.0";
            }
        }

        //保存时处理
        if (formWrap == "internet-form") {
            if (wanObj2.wan_type == 1) { //static ip
                wanIpArr.push({
                    ip: wanObj2.ip,
                    msg: _("WAN2 IP Address")
                });
            } else {
                wanIpArr.push({
                    ip: wanObj2.wanIp,
                    msg: _("WAN2 IP Address")
                });
            }
            if ((wanObj.wan_type == 1) || ((wanObj.wan_type == 3) && (wanObj.vpnWanType == 0)) || ((wanObj.wan_type == 4) && (wanObj.vpnWanType == 0)) || ((wanObj.wan_type == 5) && (wanObj.vpnWanType == 0))) { //static IP
                wanIpArr.push({
                    ip: wanObj.ip,
                    msg: _("WAN1 IP Address")
                });
            } else {
                wanIpArr.push({
                    ip: wanObj.wanIp,
                    msg: _("WAN1 IP Address")
                });
            }

            dataArr.push(wanObj);
            if (netInfo.wanNum == 2) {
                dataArr.push(wanObj2);
            }
        } else if (formWrap == "wan1SetWrap") {
            wanIpArr.push({
                ip: wanObj2.wanIp,
                msg: _("WAN2 IP Address")
            });
            dataArr.push(wanObj);
        } else {
            wanIpArr.push({
                ip: wanObj.wanIp,
                msg: _("WAN1 IP Address")
            });
            dataArr.push(wanObj2);
        }

        for (var i = 0; i < dataArr.length; i++) {
            btn_val = dataArr[i].btn_val;

            server = dataArr[i].server;
            wan_type = dataArr[i].wan_type;
            vpnWanType = dataArr[i].vpnWanType;
            ip = dataArr[i].ip;
            mask = dataArr[i].mask;
            gw = dataArr[i].gw;
            dns1 = dataArr[i].dns1;
            dns2 = dataArr[i].dns2;
            ppoe_user = dataArr[i].ppoe_user;
            ppoe_pwd = dataArr[i].ppoe_pwd;

            dnsAuto = dataArr[i].dnsAuto;

            wanMsg = dataArr[i].wanNumMsg; //提示信息中WAN1 还是WAN2

            if (btn_val == _("Connect")) {
                /*PPTP/L2TP双接入时；若服务器地址为ip，且地址类型为静态。dns可为全空，且dns为空时，向后台传入dnsAuto "1",不为空，传入dnsAuto "0",除此以外的静态IP设置下，dns1不能为空*/
                if (formWrap == "internet-form" || formWrap == "wan1SetWrap") {
                    if (($("#dns1").val() === "") && (!($("#dns1").is(":hidden")))) {
                        //服务器为域名（不是ip）则首选dns不能为空。
                        if ((((!$("#vpnServer").is(":hidden"))) && (!$.validate.valid.ip.all(server)) || wan_type === "5") && (vpnWanType === "0")) {} else {
                            return _("Please specify a primary DNS server.");
                        }
                    }
                }

                if (formWrap == "internet-form" || formWrap == "wan2SetWrap") {
                    if (($("#dns12").val() === "") && (!($("#dns12").is(":hidden")))) {
                        return _("Please specify a primary DNS server.");

                    }
                }

                //可共用验证
                if ((wan_type == 1) || ((wan_type == 3) && (vpnWanType == 0)) || ((wan_type == 4) && (vpnWanType == 0)) || ((wan_type == 5) && (vpnWanType == 0))) { //static IP

                    //同网段判断
                    if (checkIpInSameSegment(ip, mask, lanIp, lanMask)) {
                        return _("%s and %s (%s) must not be in the same network segment.", [wanMsg, _("LAN IP Address"), lanIp]);
                    }

                    if (netInfo.initObj.pptpSvrIp && checkIpInSameSegment(ip, mask, netInfo.initObj.pptpSvrIp, netInfo.initObj.pptpSvrMask)) {
                        return _("%s and %s (%s) must not be in the same network segment.", [wanMsg, _("PPTP Server IP Address"), netInfo.initObj.pptpSvrIp]);
                    }

                    if (!checkIpInSameSegment(ip, mask, gw, mask)) {
                        return _("The gateway and the IP address must be in the same network segment.");
                    }

                    if (ip == gw) {
                        return _("The IP address and gateway cannot be the same.");
                    }

                    if (ip == dns1) {
                        return _("The IP address and primary DNS server cannot be the same.");
                    }

                    if (ip == dns2) {
                        return _("The IP address and secondary DNS server cannot be the same.");
                    }

                    if ((dns1 === dns2) && (dns1 !== "")) {
                        return _("The primary DNS server and secondary DNS server cannot be the same.");
                    }

                    //判断IP地址重复
                    if (ip == wanIpArr[i].ip) {
                        return _("%s cannot be the same as that of %s.", [wanMsg, wanIpArr[i].msg]);
                    }

                    errMsg = checkIsVoildIpMask(ip, mask);

                    if (errMsg) {
                        return errMsg;
                    }
                } else if (wan_type == 2) { //pppoe
                    if (ppoe_user == "" || ppoe_pwd == "") {
                        return _("Please enter your ISP user name and password.");
                    }
                    /*if (netInfo.initObj.vpnClient == "1" && netInfo.initObj.vpnClientUser == ppoe_user) {
                        return _("The PPPoE user name cannot be the same as the PPTP/L2TP client user name.");
                    }*/
                }

                if ((wan_type === "3") || (wan_type === "4")) {
                    //同网段判断
                    if (checkIpInSameSegment(server, lanMask, lanIp, lanMask)) {
                        return _("%s and %s (%s) must not be in the same network segment.", [_("Server IP Address"), _("LAN IP Address"), lanIp]);
                    }
                }

                //手动DNS时不能DNS1与DNS2不能相同
                if ((dnsAuto == "0") && (dns1 == dns2) && (dns1 != "")) {
                    return _("The primary DNS server and secondary DNS server cannot be the same.");
                }
            }
        }
    },
    preSubmit: function () {
        var subData,
            wan_type = $("#netWanType").val(),
            server = $("#vpnServer").val(),
            dns1 = $("#dns1").val(),
            btn_val = $("#wan_submit").val(),
            saveContentObj = {
                "all_submit": "internet-form",
                "wan_submit": "wan1SetWrap",
                "wan_submit2": "wan2SetWrap"
            },
            subObj;

        //subData = $("#" + saveContentObj[netInfo.clickSaveBtn]).serialize();
        //断开操作
        if ($("#" + netInfo.clickSaveBtn).val() == _("Disconnect")) {
            if (netInfo.clickSaveBtn == "wan_submit") {
                subData = "module=wan1&action=disconnect";
            } else if (netInfo.clickSaveBtn == "wan_submit2") {
                subData = "module=wan2&action=disconnect";
            }
        } else { //连接

            if (netInfo.clickSaveBtn == "all_submit") {
                subData = $("#wan1SetWrap").serialize() + "&" + $("#wan2SetWrap").serialize() + "&module=wan1wan2&downSpeedLimit=" + $("#downSpeedLimit")[0].val() + "&downSpeedLimit2=" + $("#downSpeedLimit2")[0].val() + "&multiWanEn=" + ($("#multiWanEn").hasClass("btn-on") ? "true" : "false");

                /*PPTP/L2TP双接入时；若服务器地址为ip，且地址类型为静态。dns可为全空，且dns为空时，向后台传入dnsAuto "1",不为空，传入dnsAuto "0",除此以外的静态IP设置下，dns1不能为空*/
                if (!($("#dns1").is(":hidden"))) {
                    if (dns1 === "") {
                        subData = subData.replace("dnsAuto=0", "dnsAuto=1");
                    } else {
                        subData = subData.replace("dnsAuto=1", "dnsAuto=0");
                    }
                }
                subData = subData.replace("netWanType2", "wanType2");
            } else if (netInfo.clickSaveBtn == "wan_submit") {
                subData = $("#wan1SetWrap").serialize() + "&module=wan1&downSpeedLimit=" + $("#downSpeedLimit")[0].val();

                /*PPTP/L2TP双接入时；若服务器地址为ip，且地址类型为静态。dns可为全空，且dns为空时，向后台传入dnsAuto "1",不为空，传入dnsAuto "0",除此以外的静态IP设置下，dns1不能为空*/
                if (!($("#dns1").is(":hidden"))) {
                    if (dns1 === "") {
                        subData = subData.replace("dnsAuto=0", "dnsAuto=1");
                    } else {
                        subData = subData.replace("dnsAuto=1", "dnsAuto=0");
                    }
                }
            } else {
                subData = $("#wan2SetWrap").serialize() + "&module=wan2&downSpeedLimit2=" + $("#downSpeedLimit2")[0].val();
                subData = subData.replace("netWanType2", "wanType2");
            }
            subData = subData.replace("netWanType", "wanType");


            var msg = netInfo.checkWanData(saveContentObj[netInfo.clickSaveBtn]);
            if (msg) {
                if (netInfo.clickSaveBtn == "all_submit") {
                    showErrMsg("message-err-all", msg);
                } else if (netInfo.clickSaveBtn == "wan_submit") {
                    showErrMsg("message-err", msg);
                } else {
                    showErrMsg("message-err2", msg);
                }
                return;
            }
        }

        netInfo.saving = true;
        netInfo.reboot = false;
        if (netInfo.initObj.multiWanEn != ($("#multiWanEn").hasClass("btn-on") ? "true" : "false")) {
            if (confirm(_("Your settings will take effect after the system reboots. Do you want to reboot the system?"))) {
                netInfo.reboot = true;
            } else {
                return;
            }
        }
        subObj = {
            "data": subData,
            "url": "goform/WanParameterSetting?" + Math.random(),
            "callback": netInfo.callback
        }
        return subObj;
    },
    callback: function (str) {
        if (!top.isTimeout(str)) {
            return;
        }
        netInfo.wanBtnChange = false;
        netInfo.wanBtnChange2 = false;
        var resultObj = $.parseJSON(str),
            num = resultObj.errCode,
            sleep_time = resultObj.sleep_time,
            isVpn = (sleep_time > 10 ? true : false),
            waitTime = -1, //连接或断开操作成功之后需要等待的时间
            minTime = 4; //连接或断开操作至少要花费的时间，

        //开关多WAN需要重启
        if (netInfo.reboot) {
            top.$.progress.showPro("reboot");
            return;
        }
        if (num == 0) {
            showSaveMsg(num);
            $("#wan_submit").blur();
            netInfo.init();

        } else {
            showSaveMsg(num);
        }
    },

    changeWanType: function () {
        var wan_type = $("#netWanType").val();
        /* btnTxts = [_("Connect"), _("Disconnect")],
         btnTxt = "";*/
        wanTypeSelect(wan_type, "1");

        if (netInfo.currentWanType[0] == wan_type) {
            $("#connect_message").removeClass("none");
            netInfo.ajaxInterval.startUpdate();
        } else {
            $("#connect_message").addClass("none");
            // btnTxt = btnTxts[0];
            netInfo.ajaxInterval.stopUpdate();
        }

    },

    changeVpnType: function () {
        var wan_type = $("#netWanType").val();
        if (netInfo.currentWanType[0] == wan_type) {
            if ($(this).val() !== netInfo.currentVpnType[0]) {
                //$("#wan_submit").val(btnTxts[0]);
                $("#connect_message").addClass("none");
                netInfo.ajaxInterval.stopUpdate();
            } else {
                $("#connect_message").removeClass("none");
                netInfo.ajaxInterval.startUpdate();
            }
        } else {
            $("#connect_message").addClass("none");
        }

        if ($(this).val() === "0") {
            $("#static_ip").removeClass("none");
            $("#dnsContainer").removeClass("none");
            $("#dnsType").addClass("none");
            $("#staticIp").focus();
        } else {
            $("#dnsType").removeClass("none").val("1");
            $("#dnsContainer").addClass("none");
            $("#static_ip").addClass("none");
            if ($("#dnsAuto").val() == "0") { //手动时切回自动
                $('#dnsAuto').val('1');
            }
        }

        /*$("#internet-form").find(".control-group").css("margin-bottom", '0px');
        setTimeout(function() {
            $("#internet-form").find(".control-group").css("margin-bottom", '20px');
        })*/
    },

    changeDnsAuto: function () {
        var wan_type = $("#netWanType").val();
        if (netInfo.currentWanType[0] == wan_type) {
            if ($("[name='vpnWanType']:checked").val() != netInfo.currentVpnType[0]) {
                //$("#wan_submit").val(btnTxts[0]);
                $("#connect_message").addClass("none");
                netInfo.ajaxInterval.stopUpdate();
            } else {
                if ($(this).val() !== netInfo.currentDnsType[0]) {
                    $("#connect_message").addClass("none");
                    netInfo.ajaxInterval.stopUpdate();
                } else {
                    $("#connect_message").removeClass("none");
                    netInfo.ajaxInterval.startUpdate();
                }
            }
        } else {
            $("#connect_message").addClass("none");
        }

        if ($(this).val() === "1") {
            $("#dnsContainer").addClass("none");
        } else {
            $("#dnsContainer").removeClass("none");
        }
    },
    changeWan2Type: function () {
        var wan_type = $("#netWanType2").val();
        /* btnTxts = [_("Connect"), _("Disconnect")],
         btnTxt = "";*/
        wanTypeSelect(wan_type, "2");

        if (netInfo.currentWanType[1] == wan_type) {

            $("#connect_message2").removeClass("none");
            //netInfo.ajaxInterval.startUpdate();
        } else {
            $("#connect_message2").addClass("none");
            netInfo.ajaxInterval.stopUpdate();
        }
    },
    changeWan2DnsAuto: function () {
        var wan_type = $("#netWanType2").val();
        if (netInfo.currentWanType[1] == wan_type) {

            if ($(this).val() !== netInfo.currentDnsType[1]) {
                $("#connect_message2").addClass("none");
                netInfo.ajaxInterval.stopUpdate();
            } else {
                $("#connect_message2").removeClass("none");
                netInfo.ajaxInterval.startUpdate();
            }

        } else {
            $("#connect_message2").addClass("none");
        }

        if ($(this).val() === "1") {
            $("#dnsContainer2").addClass("none");
        } else {
            $("#dnsContainer2").removeClass("none");
        }
    }
};

var signalMsg = {
    "00": _("2.4 GHz Low/5 GHz Medium"),
    "02": _("2.4 GHz Low/5 GHz High"),
    "10": _("2.4 GHz Medium/5 GHz Medium"),
    "12": _("2.4 GHz Medium/5 GHz High"),
    "20": _("2.4 GHz High/5 GHz Medium"),
    "22": _("2.4 GHz High/5 GHz High")
};

wrlInfo = {
    loading: false,
    data: null,
    init: function () {
        if (!wrlInfo.loading) {
            $("#wireless-setting .main-area").on("click", wrlInfo.getIframe);
            wrlInfo.loading = true;
        }
        wrlInfo.initValue();
    },

    getIframe: function () {
        var id = $(this).attr("id");
        switch (id) {
        case "wrl_ssid_pwd":
            showIframe(_("WiFi Name & Password"), "wireless_ssid.html", 610, 490);
            break;
        case "wrl_wifi_time":
            showIframe(_("WiFi Schedule"), "wifi_time.html", 610, 470);
            break;
        case "wrl_bridge":
            showIframe(_("Wireless Repeating"), "wisp.html", 700, 350);
            break;
        case "wrl_channel":
            showIframe(_("Channel & Bandwidth"), "wireless.html", 460, 480);
            break;
        case "wrl_signal":
            staInfo.getHomeLink();
            showIframe(_("Transmit Power"), "wifi_power.html", 520, 220);
            break;
        case "wrl_wps":
            showIframe("WPS", "wifi_wps.html", 600, 400);
            break;
        case "wrl_beamforming":
            showIframe("Beamforming+", "wifi_bf.html", 600, 400);
            break;
        case "wrl_ap_mode":
            showIframe(_("AP Mode"), "wifi_ap.html", 600, 300);
            break;
        }
    },

    initValue: function () {
        $.getJSON("goform/GetWrlStatus?" + Math.random(), wrlInfo.setValue);
    },
    setValue: function (obj) {
        wrlInfo.data = obj;
        if (obj.schedWifiEn == "1") {
            $("#wrl_wifi_time .function-status").html(_("Enable"));
        } else {
            $("#wrl_wifi_time .function-status").html(_("Disable"));
        }
        if (obj.beamforming === "1") {
            $("#wrl_beamforming .function-status").html(_("Enable"));
        } else {
            $("#wrl_beamforming .function-status").html(_("Disable"));
        }
        if (obj.apMode === "1") {
            $("#wrl_ap_mode .function-status").html(_("Enable"));
        } else {
            $("#wrl_ap_mode .function-status").html(_("Disable"));
        }
        if (obj.wispEn == 0) {
            $("#wrl_bridge .function-status").html(_("Disable"));
            //$("#sys_lan_status").removeClass("disabled");
        } else if (obj.wispEn == 2) {
            //$("#sys_lan_status").addClass("disabled");
            $("#wrl_bridge .function-status").html(_("Connected"));
        } else {
            //$("#sys_lan_status").addClass("disabled");
            $("#wrl_bridge .function-status").html(_("Enable"));
        }

        $("#wrl_ssid_pwd .function-status").html((obj.namePwd === "0") ? _("Disable") : _("Enable"));

        $("#wrl_signal .function-status").html(signalMsg[obj.signal]);

        $("#wrl_wps .function-status").html(obj.wpsEn == "1" ? _("Enable") : _("Disable"));
    }
};

guestInfo = {
    loading: false,
    initObj: null,
    init: function () {
        if (!guestInfo.loading) {
            var selectObj = {
                "initVal": _("Unlimited"),
                "editable": "1",
                "seeAsTrans": true,
                "size": "",
                "options": [{
                    "0": _("Unlimited"),
                    "2": "2",
                    "4": "4",
                    "8": "8",
                    ".divider": ".divider",
                    ".hand-set": _("Custom")
                }]
            };

            $.validate.valid.speNum = function (str, min, max) {
                if (str == "0") {

                } else {
                    return $.validate.valid.num(str, min, max);
                }
            }

            $("#shareSpeed").toSelect(selectObj);
            $("#shareSpeed input[type='text']").attr("maxLength", 4).css("width", "239px");
            $("#shareSpeed input[type='text']").inputCorrect("num").on("focus", function () {
                this.value = this.value.replace(/[^\d\.]/g, "");
            }).on("blur", function () {
                guestInfo.setIptValue.call(this);
            }).each(function () {
                guestInfo.setIptValue.call(this);
            });

            $("#guest_submit").on("click", function () {
                if (!this.disabled)
                    mainPageLogic.validate.checkAll("guest-form");
            });
            $("#guestEn").on("click", function () {
                if (guestInfo.initObj.wl_mode == "ap" && guestInfo.initObj.wl_en != "0") {
                    guestInfo.changeBtnEn.call(this);
                }
            });

            guestInfo.loading = true;
        }
        guestInfo.initValue();
    },
    setIptValue: function () {
        var val = this.value.replace(/[^\d\.]/g, "");

        val = (val == "" ? 0 : val);
        val = parseFloat(val > 1000 ? 1000 : parseFloat(val).toFixed(2));
        $(this).parent(".input-append").find("[type=hidden]").val(val);

        if (parseFloat(val, 10) === 0) {
            this.value = _("Unlimited");
        } else {
            this.value = val;
        }
    },
    changeBtnEn: function () {
        var className = $(this).attr("class");
        if (className == "btn-off") {
            $(this).attr("class", "btn-on");
            $(this).val(1);
        } else {
            $(this).attr("class", "btn-off");
            $(this).val(0);
        }
    },
    initValue: function () {
        $.getJSON("goform/WifiGuestGet?" + Math.random(), guestInfo.setValue);
    },
    setValue: function (obj) {
        guestInfo.initObj = obj;
        inputValue(obj);
        var shareSpeed = (obj.shareSpeed / 128) + "";
        $("#shareSpeed").val(shareSpeed);
        $("#shareSpeed")[0].val(shareSpeed);
        $("#shareSpeed .input-box").val((shareSpeed == "0" ? _("Unlimited") : shareSpeed));
        if ($('#guestWrlPwd_').length === 0) {
            $('#guestWrlPwd').initPassword(_("Blank means no password."), false, false);
        }

        $("#guestEn").attr("class", (obj.guestEn == "1" ? "btn-off" : "btn-on"));
        guestInfo.changeBtnEn.call($("#guestEn")[0]);

        if (obj.wl_mode != "ap" || obj.wl_en != "1") {
            $("#guest_submit")[0].disabled = true;
            if (obj.wl_mode != "ap") {
                showErrMsg("guest_save_msg", _("This function is not available if Wireless Repeating is enabled."), true);
            } else if (obj.wl_en != "1") {
                showErrMsg("guest_save_msg", _("This function is not available if Wireless Repeating is disabled."), true);
            }
        } else {
            showErrMsg("guest_save_msg", "", true);
            $("#guest_submit")[0].disabled = false;
        }
    },
    checkValidate: function () {},
    preSubmit: function () {
        var subData,
            dataObj,
            subObj,
            callback,
            guestSecurity;

        guestSecurity = $("#wrlPwd").val() != "" ? "wpapsk" : "none";

        dataObj = {
            "guestEn": $("#guestEn").val(),
            "guestEn_5g": $("#guestEn").val(),
            "guestSecurity": guestSecurity,
            "guestSecurity_5g": guestSecurity,
            "guestSsid": $("#guestSsid").val(),
            "guestSsid_5g": $("#guestSsid_5g").val(),
            "guestWrlPwd": $("#guestWrlPwd").val(),
            "guestWrlPwd_5g": $("#guestWrlPwd").val(),
            "effectiveTime": $("[name=effectiveTime]").val(),
            "shareSpeed": $("#shareSpeed")[0].val() * 128
        }
        subData = objTostring(dataObj);
        subObj = {
            "data": subData,
            "url": "goform/WifiGuestSet",
            "callback": guestInfo.callback
        };
        return subObj;
    },
    callback: function (str) {
        if (!top.isTimeout(str)) {
            return;
        }
        var num = $.parseJSON(str).errCode;
        showSaveMsg(num);
        if (num == 0) {
            $("#guest_submit").blur();
            guestInfo.initValue();
        }
    }
};

parentInfo = {
    loading: false,
    initObj: {},
    editObj: {},
    init: function () {
        if (!parentInfo.loading) {

            $("#parental_list").on("click", ".edit-new", function () {
                var deviceId = $(this).parents("tr").find("td:eq(1)").attr("title");
                var deviceName = $(this).parents("tr").find("td:eq(0)").find(".dev-name").attr("title");
                parentInfo.action = "0";
                parentInfo.editObj.deviceMac = deviceId;
                parentInfo.editObj.deviceName = deviceName;

                $("#head_title").html(_("Parental Control")).addClass("selected");
                showIframe(_("Parental Control"), "parental_control.html", 700, 200);
                parentInfo.ajaxInterval.stopUpdate();
            });
            $("#parental_list").on("click", ".add", function () {
                //增加判断超过30条时不能保存

                if ($("#parental_list").find("tr[data-set='true']").length >= 30) {
                    showErrMsg("parentErr", _("Only a maximum of %s rules are allowed.", [30]));
                    return;
                }
                parentInfo.ajaxInterval.stopUpdate();
                parentInfo.editObj.deviceMac = "";
                parentInfo.editObj.deviceName = "";
                parentInfo.action = "1";
                showIframe(_("Parental Control"), "parental_control.html", 700, 200);
            });
            $("#parental_list").on("click", ".enable", function () {
                $(this).removeClass("enable").addClass("disable").attr("title", _("Click to Disable"));
                var mac = $(this).parents("tr").find("td:eq(1)").attr("title");
                $.post("goform/parentControlEn", {
                    mac: mac,
                    isControled: "1"
                });
            });
            $("#parental_list").on("click", ".disable", function () {
                $(this).removeClass("disable").addClass("enable").attr("title", _("Click to Enable"));
                var mac = $(this).parents("tr").find("td:eq(1)").attr("title");
                $.post("goform/parentControlEn", {
                    mac: mac,
                    isControled: "0"
                });
            });
            $("#parental_list").on("click", ".delete", function () {
                var mac = $(this).parents("tr").find("td:eq(1)").attr("title");
                parentInfo.ajaxInterval.stopUpdate();
                if (!confirm(_("Do you want to continue?"))) {
                    return;
                }
                $.post("goform/delParentalRule", "mac=" + mac, function (str) {
                    if (!top.isTimeout(str)) {
                        return;
                    }
                    var num = $.parseJSON(str).errCode;

                    showSaveMsg(num);

                    parentInfo.initValue();
                });
            });

            parentInfo.loading = true;
        }
        parentInfo.initValue();
    },
    initValue: function () {
        $.getJSON("goform/GetParentCtrlList?" + Math.random(), parentInfo.setValue);
    },
    setValue: function (obj) {

        parentInfo.initObj = obj;
        parentInfo.initDeviceList(obj);
        //定时刷新器
        if (!parentInfo.ajaxInterval) {
            parentInfo.ajaxInterval = new AjaxInterval({
                url: "goform/GetParentCtrlList",
                successFun: function (data) {
                    parentInfo.updateParentList(data);
                },
                gapTime: 5000
            });
        } else {
            parentInfo.ajaxInterval.startUpdate();
        }
    },
    initDeviceList: function (obj) {
        var str = "",
            type = "",
            len = obj.length,
            i = 0,
            j = 0,
            initDataList = [],
            color,
            deviceType,
            isCtrl_btn_str;
        for (j = 0; j < len; j++) {
            initDataList[j] = obj[j];
        }

        //排序：优先按是否在线排序(在线在前离线在后)，其次按照是否配置排序，未配置在前已配置在后
        initDataList.sort((function () {
            var splitter = /^(\d)$/;
            return function (item1, item2) {
                a = item1.line.match(splitter);
                b = item2.line.match(splitter);
                c = item1.isSet.match(splitter);
                d = item2.isSet.match(splitter);
                var anum = parseInt(a[1], 10),
                    bnum = parseInt(b[1], 10);
                var cnum = parseInt(c[1], 10),
                    dnum = parseInt(d[1], 10);
                if (anum === bnum) {
                    return cnum < dnum ? -1 : cnum > dnum ? 1 : 0;
                } else {
                    return bnum - anum;
                }
            }
        })());
        $("#list").html('');
        parentInfo.createParentList(initDataList);

    },
    createParentList: function (initDataList, targetString) {
        var len = initDataList.length,
            str,
            i,
            isCtrlFlag,
            deviceType,
            onlineTime;
        targetString = targetString || "";
        for (i = 0; i < len; i++) {
            if (initDataList[i].block == 1) { //block??
                continue;
            }

            //规则存在
            if (initDataList[i].isSet == "1") {
                isCtrlFlag = "true";
            } else {
                isCtrlFlag = "false";
            }
            deviceType = translateDeviceType(initDataList[i].devType);
            str = "<tr class='tr-row' data-set='" + isCtrlFlag + "'><td><div class='device-icon'><img src='" + deviceType.src + "'>" + showDeviceLogoString(deviceType, initDataList[i].devType) + "</div><div class='online-device-content'><div class='dev-name dev-name-target text-fixed' style='padding-right: 30px;'>";

            if (initDataList[i].devName === "") {
                str += "<span>" + top.G.deviceNameSpace + "</span></div>";
            } else {
                str += "<span class='dev-name-text'></span></div>";
            }

            if (initDataList[i].ip === "") {
                str += "<div>---</div>";
            } else {
                str += "<div class='txt-help-tips font-txt-small'>" + initDataList[i].ip + "</div>";
            }

            str += "</div></td>";

            str += "<td title='" + initDataList[i].deviceId + "'>" + initDataList[i].deviceId + "</td>";


            if (initDataList[i].line === "0") {
                str += "<td class='text-muted'>" + _("Offline") + "</td>";
            } else {
                onlineTime = formatSeconds(initDataList[i].onlineTime);
                str += "<td>" + onlineTime + "</td>";
            }

            str += "<td><div class='operate'><span title='" + _("Edit") + "' class='edit-new'></span>";

            if (initDataList[i].isSet === "1") {
                if (initDataList[i].isControled === "1") {
                    str += "<span title='" + _("Click to Disable") + "' class='disable' style='margin-left:18px;'></span>";
                } else {
                    str += "<span title='" + _("Click to Enable") + "' class='enable' style='margin-left:18px;'></span>";
                }
            }

            if ((initDataList[i].line === "0") && (initDataList[i].isSet === "1")) {
                str += "<span title='" + _("Delete") + "' class='delete' style='margin-left:18px;'></span>";
            }
            str += "</div></td></tr>";

            $("#list").append(str);
            $("#list").find(".dev-name-target").parents("tr").data("target", targetString);
            $("#list").find(".dev-name-target").attr("title", initDataList[i].devName);
            $("#list").find(".dev-name-target .dev-name-text").text(initDataList[i].devName);
            $("#list").find(".dev-name-target").removeClass("dev-name-target");
        }
    },
    updateParentList: function (obj) {
        var i = 0,
            len = obj.length,
            addList = [],
            onlineTime,
            exsitTarget,
            actionStr,
            $parentList = $("#list").children(),
            randomStr = Math.random();
        if (mainPageLogic.modelObj != "parentInfo") {
            parentInfo.ajaxInterval.stopUpdate();
            return;
        }

        for (i = 0; i < len; i++) {
            exsitTarget = false;
            $parentList.each(function () {
                //当设备已存在时，更新时间
                if ($(this).children().eq(1).attr("title") == obj[i].deviceId) {
                    $(this).data("target", randomStr);
                    actionStr = "";

                    if (obj[i].line === "0") { //不在线
                        //更新时间
                        $(this).children().eq(2).attr("class", "text-muted").html(_("Offline"));
                    } else {
                        onlineTime = formatSeconds(obj[i].onlineTime);
                        $(this).children().eq(2).attr("class", "").html(onlineTime);
                    }

                    actionStr += "<div class='operate'><span title='" + _("Edit") + "' class='edit-new'></span>";


                    if (obj[i].isSet === "1") {
                        if (obj[i].isControled === "1") {
                            actionStr += "<span title='" + _("Click to Disable") + "' class='disable' style='margin-left:18px;'></span>";
                        } else {
                            actionStr += "<span title='" + _("Click to Enable") + "' class='enable' style='margin-left:18px;'></span>";
                        }
                    }

                    if ((obj[i].line === "0") && (obj[i].isSet === "1")) {
                        actionStr += "<span title='" + _("Delete") + "' class='delete' style='margin-left:18px;'></span>";
                    }

                    actionStr += "</div>";

                    //更新操作
                    $(this).children().eq(3).html(actionStr);

                    //更新ip
                    if (obj[i].ip === "") {
                        $(this).find(".online-device-content").children().eq(1).html("---").attr("class", '');
                    } else {
                        $(this).find(".online-device-content").children().eq(1).html(obj[i].ip).attr("class", 'txt-help-tips font-txt-small');
                    }
                    exsitTarget = true;
                    return false;
                }
            });

            //不存在时，表示新增选项
            if (!exsitTarget) {
                addList.push(obj[i]);
            }
        }

        //新增设备
        parentInfo.createParentList(addList, randomStr);

        $("#list").children().each(function () {
            if ($(this).data("target") != randomStr) {
                $(this).remove();
            }
        })

    }
};

usbInfo = {

    loading: false,
    data: null,
    init: function () {
        if (!usbInfo.loading) {
            $("#usb-setting .main-area").on("click", usbInfo.getIframe);
            usbInfo.loading = true;
        }
        usbInfo.initValue();
    },
    initValue: function () {
        $.getJSON("goform/GetUSBStatus?" + Math.random(), usbInfo.setValue);
    },
    setValue: function (obj) {
        usbInfo.data = obj;
        if (obj.printer == "0") {
            $("#usb_printer .function-status").html(_("Disable"));
        } else {
            $("#usb_printer .function-status").html(_("Enable"));
        }

        if (obj.dlna == "0") {
            $("#usb_dlna .function-status").html(_("Disable"));
        } else {
            $("#usb_dlna .function-status").html(_("Enable"));
        }

        if (obj.hasusb === "0") {
            $("#usb_samba .function-status").html(_("Disable"));
        } else {
            $("#usb_samba .function-status").html(_("Enable"));
        }

        /*if (obj.thunderEn === "0") {
            $("#usb_xunlei .function-status").html(_("Disable"));
        } else {
            $("#usb_xunlei .function-status").html(_("Enable"));
        }*/
    },
    getIframe: function () {
        var id = $(this).attr("id");
        switch (id) {
        case "usb_samba":
            showIframe(_("Share File"), "samba.html", 620, 450);
            break;
        case "usb_dlna":
            showIframe(_("DLNA"), "dlna.html", 620, 450);
            break;
        case "usb_printer":
            showIframe(_("Share Printer"), "printer.html", 650, 240);
            break;
        case "usb_xunlei":
            showIframe(_("Offline Download with Xunlei"), "xunleiDownload.html", 650, 240);
            break;
        }
    }

};
vpnInfo = {
    loading: false,
    data: null,
    init: function () {
        if (!vpnInfo.loading) {
            $("#vpn-setting .main-area").on("click", vpnInfo.getIframe);
            vpnInfo.loading = true;
        }
        vpnInfo.initValue();
    },
    initValue: function () {
        $.getJSON("goform/GetVpnStatus?" + Math.random(), vpnInfo.setValue);
        //定时刷新器
        if (!vpnInfo.ajaxInterval) {
            vpnInfo.ajaxInterval = new AjaxInterval({
                url: "goform/GetVpnStatus",
                successFun: function (data) {
                    vpnInfo.setValue(data);
                },
                gapTime: 5000
            });
        } else {
            vpnInfo.ajaxInterval.startUpdate();
        }
    },
    setValue: function (obj) {
        vpnInfo.data = obj;
        if (mainPageLogic.modelObj != "vpnInfo") {
            vpnInfo.ajaxInterval.stopUpdate();
            return;
        }
        if (obj.server == "0") {
            $("#vpn_server .function-status").html(_("Disable"));
        } else {
            $("#vpn_server .function-status").html(_("Enable"));
        }
        if (obj.client == "0") {
            $("#vpn_client .function-status").html(_("Disable"));
        } else {
            $("#vpn_client .function-status").html(_("Enable"));
        }

        $("#vpn_online_user .function-status").html(obj.users + _(" user(s)"));
    },
    getIframe: function () {
        var id = $(this).attr("id");
        switch (id) {
        case "vpn_server":
            showIframe(_("PPTP Server"), "pptp_server.html", 630, 510);
            break;
        case "vpn_client":
            if (vpnInfo.data.wanType == "3" || vpnInfo.data.wanType == "4") {
                alert(_("This function is not available at the moment."));
                return false;
            }
            showIframe(_("PPTP/L2TP Client"), "pptp_client.html", 560, 400);
            break;
        case "vpn_online_user":
            showIframe(_("Online PPTP Users"), "pptp_user.html", 630, 510);
            break;
        }
    }
};

advInfo = {
    loading: false,
    data: null,
    init: function () {
        if (!advInfo.loading) {
            $("#advance .main-area").on("click", advInfo.getIframe);

            advInfo.loading = true;
        }
        advInfo.initValue();
    },
    initValue: function () {
        $.getJSON("goform/GetAdvanceStatus?" + Math.random(), advInfo.setValue);
    },
    setValue: function (obj) {
        advInfo.data = obj;

        if (obj.wl_mode == "apclient") {
            $("#adv_netcontrol, #adv_parental, #adv_remoteweb, #adv_ddns, #adv_upnp, #adv_virtualServer, #adv_dmz, #adv_firewall").addClass("disabled");
        } else {
            $("#adv_netcontrol, #adv_parental, #adv_remoteweb, #adv_ddns, #adv_upnp, #adv_virtualServer, #adv_dmz").removeClass("disabled");
        }

        if (obj.netControl == 0) {
            $("#adv_netcontrol .function-status").html(_("Disable"));
        } else {
            $("#adv_netcontrol .function-status").html(_("Enable"));
        }
        if (obj.led == 0) {
            $("#adv_led .function-status").html(_("Disable"));
        } else {
            $("#adv_led .function-status").html(_("Enable"));
        }

        if (obj.cloud == 0) {
            $("#adv_cloud .function-status").html(_("Disable"));
        } else {
            $("#adv_cloud .function-status").html(_("Enable"));
        }

        if (obj.sleepMode == 1) {
            $("#adv_sleepMode .function-status").html(_("Enable"));
        } else {
            $("#adv_sleepMode .function-status").html(_("Disable"));
        }

        if (obj.ddns == 0) {
            $("#adv_ddns .function-status").html(_("Disable"));
        } else {
            $("#adv_ddns .function-status").html(_("Enable"));
        }
        if (obj.upnp == 0) {
            $("#adv_upnp .function-status").html(_("Disable"));
        } else {
            $("#adv_upnp .function-status").html(_("Enable"));
        }
        if (obj.iptv == 0) {
            $("#adv_iptv .function-status").html(_("Disable"));
        } else {
            $("#adv_iptv .function-status").html(_("Enable"));
        }
        if (obj.parentControl === "0") {
            $("#adv_parental .function-status").html(_("Not Configured"));
        } else {
            $("#adv_parental .function-status").html(_("Configured"));
        }
        if (obj.virtualServer === "0") {
            $("#adv_virtualServer .function-status").html(_("Not Configured"));
        } else {
            $("#adv_virtualServer .function-status").html(_("Configured"));
        }

        if (obj.macFilterType === "black") {
            $("#adv_macFilter .function-status").html(_("Blacklist"));
        } else {
            $("#adv_macFilter .function-status").html(_("Whitelist"));
        }

        if (obj.firewall === "0") {
            $("#adv_firewall .function-status").html(_("Disable"));
        } else {
            $("#adv_firewall .function-status").html(_("Enable"));
        }
        if (obj.staticRoute === "0") {
            $("#adv_route .function-status").html(_("Not Configured"));
        } else {
            $("#adv_route .function-status").html(_("Configured"));
        }
        if (obj.dmz === "0") {
            $("#adv_dmz .function-status").html(_("Disable"));
        } else {
            $("#adv_dmz .function-status").html(_("Enable"));
        }


    },
    getIframe: function () {
        if ($(this).hasClass("disabled")) return;

        var id = $(this).attr("id");
        switch (id) {
        case "adv_sleepMode":
            $(".fopare-ifmwrap-title").addClass("border-bottom");
            // $("#head_title2").html(_("Clients controlled")).removeClass("none");
            // $("#head_title").html(_("Sleeping Mode")).addClass("selected");
            showIframe(_("Sleeping Mode"), "sleep_mode.html", 700, 200);
            break;
        case "adv_netcontrol":
            showIframe(_("Bandwidth Control"), "net_control.html", 800, 550);
            break;
        case "adv_led":
            showIframe(_("LED Control"), "system_led.html", 560, 315);
            break;
        case "adv_cloud":
            showIframe(_("Tenda App"), "cloud_managment.html", 620, 450);
            break;
        case "adv_ddns":
            showIframe("DDNS", "ddns_config.html", 500, 410);
            break;
        case "adv_virtualServer":
            showIframe(_("Virtual Server"), "virtual_server.html", 700, 550);
            break;
        case "adv_macFilter":
            showIframe(_("Filter MAC Address"), "mac_filter.html", 700, 550);
            break;
        case "adv_dmz":
            showIframe(_("DMZ Host"), "dmz.html", 430, 350);
            break;
        case "adv_upnp":
            showIframe("UPnP", "upnp_config.html", 550, 300);
            break;
        case "adv_iptv":
            showIframe("IPTV", "iptv.html", 580, 510);
            break;
        case "adv_route":
            showIframe(_("Static Route"), "static_route.html", 700, 510);
            break;
        case "adv_firewall":
            showIframe(_("Firewall"), "firewall.html", 580, 510);
            break;
        }
        G.iframeFlag = true;
    }
};

var timeMsg = {
    "0": _("(GMT-12:00) Eniwetok Island"),
    "1": _("(GMT-11:00) Samoa"),
    "2": _("(GMT-10:00) Hawaii"),
    "3": _("(GMT-09:00) Alaska"),
    "4": _("(GMT-08:00) San Francisco"),
    "5": _("(GMT-07:00) Denver"),
    "6": _("(GMT-06:00) Mexico City, Guatemala, Costa Rica, Salvador, Nicaragua"),
    "7": _("(GMT-05:00) New York, Ottawa"),
    "8": _("(GMT-04:00) Chile, Brazil"),
    "9": _("(GMT-03:00) Buenos Aires"),
    "10": _("(GMT-02:00) Mid-Atlantic"),
    "11": _("(GMT-01:00) Cape Verde Islands"),
    "12": _("(GMT) Greenwich Mean Time"),
    "13": _("(GMT+01:00) Denmark, Germany, Norway, Hungary, France, Belgium"),
    "14": _("(GMT+02:00) Israel, Egypt, Bucharest"),
    "15": _("(GMT+03:00) Moscow"),
    "16": _("(GMT+04:00) Sultanate of Oman, Mauritania, Reunion Island"),
    "17": _("(GMT+05:00) Pakistan, Novaya Zemlya, Maldives"),
    "18": _("(GMT+06:00) Colombo"),
    "19": _("(GMT+07:00) Bangkok, Jakarta"),
    "20": _("(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi, Taipei"),
    "21": _("(GMT+09:00) Tokyo, Pyongyang"),
    "22": _("(GMT+10:00) Sydney, Guam"),
    "23": _("(GMT+11:00) Solomon Islands"),
    "24": _("(GMT+12:00) Wellington"),
    "25": _("(GMT+13:00) Nuku'alofa")
};

sysInfo = {
    loading: false,
    data: null,
    init: function () {
        if (!sysInfo.loading) {
            $("#system .main-area").on("click", sysInfo.getIframe);
            var Msg = location.search.substring(1) || "0";
            if (Msg == "1") {
                showIframe(_("LAN Settings"), "lan.html", 400, 215);
            }
            sysInfo.loading = true;
        }
        sysInfo.initValue();
    },
    initValue: function () {
        $.getJSON("goform/GetSysStatus?" + Math.random(), sysInfo.setValue);
    },
    setValue: function (obj) {
        sysInfo.data = obj;

        $("#sys_lan_status .function-status").html(obj.lan);

        if (obj.firmware == "1") {
            $("#sys_upgrade .function-status").html(_("New version detected."));
        } else {
            $("#sys_upgrade .function-status").html(obj.firmware);
        }
        $("#sys_auto .function-status").html(obj.rebootEn == 1 ? _("Enable") : _("Disable"));

        if (obj.remoteWeb == 0) {
            $("#adv_remoteweb .function-status").html(_("Disable"));
        } else {
            $("#adv_remoteweb .function-status").html(_("Enable"));
        }

        if (obj.wl_mode == "apclient" || top.G.workMode == "ap") {
            $("#sys_wan").addClass("disabled");
            $("#ip_mac_bind").addClass("disabled");
            if (obj.apClientConnect == "1") {
                $("#sys_lan_status").addClass("disabled");
            }
        }

        if (obj.ipMacBindEn === "0") {
            $("#ip_mac_bind .function-status").html(_("Not Configured"));
        } else {
            $("#ip_mac_bind .function-status").html(_("Configured"));
        }

        if (obj.syncInternetTime == "1") {
            $("#sys_time .function-status").html(_("Synchronized"));
        } else {
            $("#sys_time .function-status").html(_("Unsynchronized"));
        }

        /*   if (timeMsg[obj.timeZone] !== "") {
            $("#sys_time .function-status").html(_(timeMsg[obj.timeZone]));
        }*/
    },
    getIframe: function () {
        if ($(this).hasClass("disabled")) return;

        var id = $(this).attr("id");
        switch (id) {
        case "sys_status":
            showIframe(_("System Status"), "system_status.html", 530, 490);
            break;
        case "sys_pwd":
            showIframe(_("Login Password."), "system_password.html", 500, 310);
            break;
        case "sys_lan_status":
            showIframe(_("LAN Settings"), "lan.html", 580, 415);
            break;
        case "ip_mac_bind":
            showIframe(_("DHCP Reservation"), "ip_mac_bind.html", 780, 415);
            break;
        case "sys_wan":
            showIframe(_("WAN Settings"), "mac_clone.html", 535, 450);
            break;
        case "sys_reboot":
            showIframe(_("Reboot and Reset"), "system_reboot.html", 400, 205);
            break;
        case "adv_remoteweb":
            showIframe(_("Remote Management"), "remote_web.html", 500, 475, (sysInfo.data.nopwd === true) ? "nopwd" : "");
            break;
        case "sys_upgrade":
            showIframe(_("Firmware Upgrade"), "system_upgrade.html", 665, 556);
            break;
        case "sys_backup":
            showIframe(_("Backup/Restore"), "system_backup.html", 600, 240);
            break;
            /*case "sys_config":
                showIframe(_("Reset"), "system_config.html", 400, 205);
                break;*/
        case "sys_log":
            showIframe(_("System Log"), "system_log.html", 650, 425);
            break;
        case "sys_auto":
            showIframe(_("Automatic Maintenance"), "system_automaintain.html", 500, 205);
            break;
        case "sys_time":
            showIframe(_("Time Settings"), "system_time.html", 820, 415);
            break;
        }
    }
};

<!-- 主菜单语言选择 -->
//$('.main-content').prepend('<div class="lang" id="lang" style="color: #999; font-size: 13px; margin-top:2px; float:none; position: absolute; left: 850px; top:15px;"><a class="lang-toggle" id="langToggle"><span>中文</span><b style="border-top-color:#999" class="caret"></b></a><ul class="lang-menu none" style="top: 43px; z-index: 1000;" id="langMenu"><li><a data-country="en">English</a></li><li><a data-country="cn">中文</a></li><li><a data-country="zh">繁體中文</a></li></ul><span style="margin-left:10px;">|<a style="margin-left:10px; color: #999;" href="goform/exit">Exit</a></span></div>');

$(".main-section-title").before('<div class="lang-set"><a class="lang-toggle"><span>中文</span><b style="border-top-color:#999" class="caret"></b></a><ul class="lang-menu none" style="top: 43px; z-index: 1000;"><li><a data-country="en">English</a></li><li><a data-country="cn">中文</a></li><li><a data-country="zh">繁體中文</a></li></ul><span style="margin-left:10px;">|<a style="margin-left:10px; color: #999;" href="goform/exit">Exit</a></span></div>')