
$(document).ready( function() {
   var str=location.href; //取得整个地址栏
   var start=str.lastIndexOf("/");
   var page="";
   if(start>-1)
   {
  	 page=str.substring(start+1,str.length+1);
   }
   switch(page)
   {
   case "DhcpServer.html":
  $('#lang_netWorkSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_netWorkSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#netWorkSetup_menuId').removeClass("hide").addClass("show");
  $('#lidhcpsever').addClass("submenucurent");
   break;
   
   case "AdvMacBindIp.html":
  $('#lang_netWorkSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_netWorkSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#netWorkSetup_menuId').removeClass("hide").addClass("show");
  $('#liipbandmac').addClass("submenucurent");
   break;  
  
   case "Upnp.html":
  $('#lang_netWorkSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_netWorkSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#netWorkSetup_menuId').removeClass("hide").addClass("show");
  $('#liupnp').addClass("submenucurent");
   break;   
  
   case "Staticroute.html":
  $('#lang_netWorkSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_netWorkSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#netWorkSetup_menuId').removeClass("hide").addClass("show");
  $('#listaticrouter').addClass("submenucurent");
   break;    
 
   case "Ddns.html":
  $('#lang_netWorkSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_netWorkSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#netWorkSetup_menuId').removeClass("hide").addClass("show");
  $('#liddns').addClass("submenucurent");
   break; 
   
   case "Advwireless.html":
  $('#lang_wirelessSetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_wirelessSetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#wirelessSetUp_menuId').removeClass("hide").addClass("show");
  $('#liadvwlanset').addClass("submenucurent");
   break;
   
   case "AdvWlanAccess.html":
  $('#lang_wirelessSetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_wirelessSetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#wirelessSetUp_menuId').removeClass("hide").addClass("show");
  $('#liwirelesscontrol').addClass("submenucurent");
   break;   
   
   case "Guestwireless.html":
  $('#lang_wirelessSetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_wirelessSetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#wirelessSetUp_menuId').removeClass("hide").addClass("show");
  $('#liguestwlansetup').addClass("submenucurent");
   break;  
   
   case "WiFiTimer.html":
  $('#lang_wirelessSetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_wirelessSetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#wirelessSetUp_menuId').removeClass("hide").addClass("show");
  $('#liwifitimer').addClass("submenucurent");
   break;   
   
  case "SharePortSetup.html":
  $('#lang_usbSetUp').removeClass("leftmenuh3").addClass("menucurent");
   break;  
   
   case "Firewall.html":
  $('#lang_securitySetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_securitySetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#securitySetUp_menuId').removeClass("hide").addClass("show");
  $('#lifirewall').addClass("submenucurent");
   break;    
  
   case "Dmz.html":
  $('#lang_securitySetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_securitySetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#securitySetUp_menuId').removeClass("hide").addClass("show");
  $('#lidmz').addClass("submenucurent");
   break;  
 
   case "VirtualServer.html":
  $('#lang_securitySetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_securitySetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#securitySetUp_menuId').removeClass("hide").addClass("show");
  $('#liportforward').addClass("submenucurent");
   break;   
 
   case "MacFilter.html":
  $('#lang_securitySetUp').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_securitySetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#securitySetUp_menuId').removeClass("hide").addClass("show");
  $('#liipfilter').addClass("submenucurent");
   break;

   case "AccessControl.html":
       $('#lang_securitySetUp').removeClass("leftmenuh3").addClass("menucurent_open");
       $('#ic_securitySetUp').removeClass("ic-menu_down").addClass("select-ic-menu_up");
       $('#securitySetUp_menuId').removeClass("hide").addClass("show");
       $('#liaccesscontrol').addClass("submenucurent");
       break;

       case "SNTP.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
   $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#litime').addClass("submenucurent");
   break;     
 
   case "account.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#liaccount').addClass("submenucurent");
   break;  
 
   case "Backup.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#libackup').addClass("submenucurent");
   break;  
   
   case "FirmwareUpdate.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#liupgrade').addClass("submenucurent");
   break;  
   
   case "reboot.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#lireboot').addClass("submenucurent");
   break;    
 
   case "Diagnosis.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#lidiag').addClass("submenucurent");
   break; 
   
   case "RouterInfo.html":
  $('#lang_systemSetup').removeClass("leftmenuh3").addClass("menucurent_open");
  $('#ic_systemSetup').removeClass("ic-menu_down").addClass("select-ic-menu_up");
  $('#systemSetup_menuId').removeClass("hide").addClass("show");
  $('#lirouterinfo').addClass("submenucurent");
   break;   
 }

});


/*
function initialLeftMenu() {
 var a = "<div id='Menu'>";
 a+="<h3 id='lang_netWorkSetup' class='leftmenuh3' onClick='clickLeftMenu(\"netWorkSetup\");' style='margin-top:0px;'><div class='ic-menu_down' id='ic_netWorkSetup'></div><span>网络设置</span></h3>";
 a+="<ul id='netWorkSetup_menuId' class='hide'>";
 a+="<li id='lidhcpsever'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"DhcpServer\");' id='lang_dhcpsever'>局域网设置</a></li>";
 a+="<li id='liipbandmac'><a   href='javascript:void(0);' onclick='LeftMenuClickOn(\"AdvMacBindIp\");' id='lang_ipbandmac'>IP/MAC绑定</a></li>";
 a+="<li id='liupnp'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Upnp\");' id='lang_upnp'>UPnP</a></li>";
 a+="<li id='listaticrouter'><a  href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Staticroute\");' id='lang_staticrouter'>静态路由</a></li>";
 a+="<li id='liddns'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"Ddns\");' id='lang_ddns'>动态域名服务(DDNS)</a></li>";
 a+="</ul>";
 a+="<h3 id='lang_wirelessSetUp' class='leftmenuh3' onClick='clickLeftMenu(\"wirelessSetUp\");'><div class='ic-menu_down' id='ic_wirelessSetUp'></div><span>无线设置</span></h3>";
 a+="<ul id='wirelessSetUp_menuId' class='hide'>";
 a+="<li id='liadvwlanset'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"Advwireless\");' id='lang_advwlanset'>高级设置</a></li>";
 a+="<li id='liwirelesscontrol'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"AdvWlanAccess\");' id='lang_wirelesscontrol'>无线访问控制</a></li>";
 a+="<li id='liguestwlansetup'><a  href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Guestwireless\");' id='lang_guestwlansetup'>访客网络</a></li>";
 a+="</ul>";
 a+="<h3 id='lang_securitySetUp' class='leftmenuh3' onClick='clickLeftMenu(\"securitySetUp\");'><div class='ic-menu_down' id='ic_securitySetUp'></div><span>安全设置</span></h3>";
 a+="<ul id='securitySetUp_menuId' class='hide'>";
 a+="<li id='lifirewall'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Firewall\");' id='lang_firewall'>防火墙</a></li>";
 a+="<li id='lidmz'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Dmz\");' id='lang_dmz'>DMZ主机</a></li>";
 a+="<li id='liportforward'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"PortForward\");' id='lang_portforward'>虚拟服务器</a></li>";
 a+="<li id='liipfilter'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"MacFilter\");'  id='lang_ipfilter'>IP过滤</a></li>";
 a+="</ul>";
 a+="<h3 id='lang_systemSetup' class='leftmenuh3' onClick='clickLeftMenu(\"systemSetup\");'><div class='ic-menu_down' id='ic_systemSetup'></div><span>系统管理</span></h3>";
 a+="<ul id='systemSetup_menuId' class='hide'>";
 a+="<li id='litime'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"SNTP\");' id='lang_time'>网络时间</a></li>";
 a+="<li id='libackup'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Backup\");' id='lang_backup'>备份与恢复配置</a></li>";
 a+="<li id='liaccount'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"account\");' id='lang_account'>修改登录密码</a></li>";
 a+="<li id='liupgrade'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"FirmwareUpdate\");' id='lang_upgrade'>固件升级</a></li>";
 a+="<li id='lireboot'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"reboot\");' id='lang_reboot'>重启与恢复出厂</a></li>";
 a+="<li id='lidiag'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Diagnosis\");' id='lang_diag'>日志与诊断</a></li>";
 a+="<li id='lirouterinfo'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"RouterInfo\");' id='lang_routerinfo'>路由器信息</a></li>";
 
 a+="</ul></div>";
 document.getElementById("sub_menu_container").innerHTML = a;
}
*/

function initialLeftMenu() {
 var a = "<div id='Menu'>";
 a+="<h3 id='lang_netWorkSetup' class='leftmenuh3' onClick='clickLeftMenu(\"netWorkSetup\");' style='margin-top:0px;'><div class='ic-menu_down' id='ic_netWorkSetup'></div><span>"+I18N("j","Commom_Network_Settings")+"</span></h3>";
 a+="<ul id='netWorkSetup_menuId' class='hide'>";
 a+="<li id='lidhcpsever'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"DhcpServer\");' id='lang_dhcpsever'>"+I18N("j","Commom_LAN_Settings")+"</a></li>";
 a+="<li id='liipbandmac'><a   href='javascript:void(0);' onclick='LeftMenuClickOn(\"AdvMacBindIp\");' id='lang_ipbandmac'>"+I18N("j","Commom_IPMAC_Binding")+"</a></li>";
 a+="<li id='liupnp'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Upnp\");' id='lang_upnp'>UPnP</a></li>";
 a+="<li id='listaticrouter'><a  href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Staticroute\");' id='lang_staticrouter'>"+I18N("j","Commom_Static_Route")+"</a></li>";
 a+="<li id='liddns'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"Ddns\");' id='lang_ddns'>"+I18N("j","Commom_DDNS")+"</a></li>";
 a+="</ul>";
 a+="<h3 id='lang_wirelessSetUp' class='leftmenuh3' onClick='clickLeftMenu(\"wirelessSetUp\");'><div class='ic-menu_down' id='ic_wirelessSetUp'></div><span>"+I18N("j","Commom_Wireless_Settings")+"</span></h3>";
 a+="<ul id='wirelessSetUp_menuId' class='hide'>";
 a+="<li id='liadvwlanset'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"Advwireless\");' id='lang_advwlanset'>"+I18N("j","Commom_Advance_Settings")+"</a></li>";
 //a+="<li id='liwirelesscontrol'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"AdvWlanAccess\");' id='lang_wirelesscontrol'>"+I18N("j","Commom_Wireless_Access_Control")+"</a></li>";
 a+="<li id='liguestwlansetup'><a  href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Guestwireless\");' id='lang_guestwlansetup'>"+I18N("j","Commom_Guest_Network")+"</a></li>";
 a+="<li id='liwifitimer'><a  href='javascript:void(0);'  onclick='LeftMenuClickOn(\"WiFiTimer\");' id='lang_wifitimer'>"+I18N("j","WiFiTimer")+"</a></li>";

 a+="</ul>";
 
//  a+="<h3 id='lang_usbSetUp' class='leftmenuh3' onClick='LeftMenuClickOn(\"SharePortSetup\");'><span>"+I18N("j","Commom_USB_Settings")+"</span></h3>";

 a+="<h3 id='lang_securitySetUp' class='leftmenuh3' onClick='clickLeftMenu(\"securitySetUp\");'><div class='ic-menu_down' id='ic_securitySetUp'></div><span>"+I18N("j","Commom_Security_Settings")+"</span></h3>";
 a+="<ul id='securitySetUp_menuId' class='hide'>";
 a+="<li id='lifirewall'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Firewall\");' id='lang_firewall'>"+I18N("j","Commom_Firewall")+"</a></li>";
 a+="<li id='lidmz'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"Dmz\");' id='lang_dmz'>"+I18N("j","Commom_DMZ_Host")+"</a></li>";
 a+="<li id='liportforward'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"VirtualServer\");' id='lang_portforward'>"+I18N("j","Commom_Virtual_Server")+"</a></li>";
 a+="<li id='liipfilter'><a href='javascript:void(0);'  onclick='LeftMenuClickOn(\"MacFilter\");'  id='lang_ipfilter'>"+I18N("j","Commom_IP_Filter")+"</a></li>";
 a+="<li id='liaccesscontrol'><a  href='javascript:void(0);' onclick='LeftMenuClickOn(\"AccessControl\");' id='lang_accesscontrol'>"+I18N("j","AccessControl_Title")+"</a></li>";
 a+="</ul>";
 a+="<h3 id='lang_systemSetup' class='leftmenuh3' onClick='clickLeftMenu(\"systemSetup\");'><div class='ic-menu_down' id='ic_systemSetup'></div><span>"+I18N("j","Commom_System_Management")+"</span></h3>";
 a+="<ul id='systemSetup_menuId' class='hide'>";
 a+="<li id='litime'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"SNTP\");' id='lang_time'>"+I18N("j","Commom_SNTP_Time")+"</a></li>";
 a+="<li id='libackup'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Backup\");' id='lang_backup'>"+I18N("j","Commom_Backup_Recovery")+"</a></li>";
 a+="<li id='liaccount'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"account\");' id='lang_account'>"+I18N("j","Commom_Modify_Login_Password")+"</a></li>";
 a+="<li id='liupgrade'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"FirmwareUpdate\");' id='lang_upgrade'>"+I18N("j","Commom_Firmware_Update")+"</a></li>";
 a+="<li id='lireboot'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"reboot\");' id='lang_reboot'>"+I18N("j","Commom_Reboot_Factory_Reset")+"</a></li>";
 a+="<li id='lidiag'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"Diagnosis\");' id='lang_diag'>"+I18N("j","Commom_Log_Diagnosis")+"</a></li>";
 a+="<li id='lirouterinfo'><a href='javascript:void(0);' onclick='LeftMenuClickOn(\"RouterInfo\");' id='lang_routerinfo'>"+I18N("j","Commom_Router_Information")+"</a></li>";
 
 a+="</ul></div>";
 document.getElementById("sub_menu_container").innerHTML = a;
}

function LeftMenuClickOn(menu)
{
	if(!confirmExit())
	{
		return false;
	}
	
	self.location.href =menu+".html";	
}



function ClearTable(Node, Num){
		var _num  = Num || 0;
		var Table = document.getElementById(Node);
		var Tr    = Table.getElementsByTagName('tr');
		
		for(var i = Tr.length - 1; i -_num >= 0; i--){
			Table.deleteRow(i);
		}		
	}
function CreateTable(Node,Value){
		var Table = document.getElementById(Node);
		var Tbody = Table.getElementsByTagName('tbody')[0];
		
		if(Tbody == null){
			Tbody = document.createElement('tbody');
		}
	
		for(var i = 0; i < Value.length; i++){
			var Tr = [];
			Tr[i]  = document.createElement('tr');
			
	
			if(Value[i] == undefined) continue;
			for(var j = 0; j < Value[i].length; j++){
				var Td = [];
				Td[j]  = document.createElement('td');
				Td[j].innerHTML = Value[i][j];
				Td[j].align = "center";
				Td[j].id        = Node + "_" + i.toString() + j.toString();
				Tr[i].appendChild(Td[j]);
			}
			Tbody.appendChild(Tr[i]);
		}
		Table.appendChild(Tbody);
	}


var Form = {
	Checkbox: function(Id,xValue){
		var _node = document.getElementsById(Id);
		
		switch(xValue){
			case undefined : {
				return (_node.checked == true) ? 1 : 0;
			}
			case '1' : {
				_node.checked = true;
				break;
			}
			case '0' : {
				_node.checked = false;
				break;
			}
		}
		return xValue;
	},
	Radio: function(Name,xValue){
		var _node = document.getElementsByName(Name);
		if(xValue == undefined){
			for(var i = 0; i < _node.length; i++){
				if(_node[i].checked == true){
					return _node[i].value;
				}
			}
		} else {
			for(var j = 0; j < _node.length; j++){
				if(_node[j] !== undefined && _node[j].value == xValue){
					_node[j].checked = true;
				}
			}
		}
		return xValue;
	},
	Select: function(Id,xValue){
		var _node = document.getElementsById(Id);
		
		if(xValue == undefined){
			return _node.value;
		} else {
			_node.value = xValue;
		}
		return xValue;
	},
	CreateOptions: function(nodeName,optionValue,valueArray){
		var Node = document.getElementsById(nodeName),valueOptions;
		
		Node.options.length = 0;
		if(valueArray == undefined){
			valueOptions = optionValue;
		} else {
			valueOptions = valueArray;
		}
		
		for(var i = 0; i < optionValue.length; i++){
			Node.options[i] = new Option(optionValue[i]);
			Node.options[i].value = valueOptions[i];
		}
	}
}


function SetCheckBox(id, stren, strdis, ckstatus, init)
{
	var checkbox = id+"_ck";
	var now_check;
	var status;
	
	if(init)
	{
		now_check = ckstatus;
		now_check?status=false:status=true;
	}
	else
	{
		now_check = document.getElementById(checkbox).checked;
		now_check?status=true:status=false;
		save_button_changed();
	}
	
	if(status)
	{
		document.getElementById(id).className = "checkbox_off";
		document.getElementById(id).innerHTML = '<input type="checkbox" id="'+checkbox+'" name="'+checkbox+'" checked>'+strdis;
		document.getElementById(checkbox).checked = false;
	}
	else
	{
		document.getElementById(id).className = "checkbox_on";
		document.getElementById(id).innerHTML = '<input type="checkbox" id="'+checkbox+'" name="'+checkbox+'" checked>'+stren;
		document.getElementById(checkbox).checked = true;
	}
}

function CheckHTMLStatus(a) {
    document.getElementById("DetectRouterConnection").style.display = "none"
    /*if (a != "") {
        $.ajax({
            cache: false,
            url: a + ".html",
            timeout: 5000,
            type: "GET",
            error: function() {
                document.getElementById("DetectRouterConnection").style.display = "inline"
            },
            success: function(b) {
                document.getElementById("DetectRouterConnection").style.display = "none";
                self.location.href = a + ".html"
            }
        })
    } else {
        $.ajax({
            cache: false,
            url: "./js/CheckConnection",
            timeout: 5000,
            type: "GET",
            error: function() {
                document.getElementById("DetectRouterConnection").style.display = "inline"
            },
            success: function(b) {
                document.getElementById("DetectRouterConnection").style.display = "none"
            }
        })
    }*/
};
var childrenIDs=["netWorkSetup","wirelessSetUp","securitySetUp","systemSetup"];

function clickLeftMenu(objID)
{

	 for(var i=0;i<childrenIDs.length;i++){
            if(childrenIDs[i] == objID){
			
				if ($("#"+objID+"_menuId").is(":hidden")){
                    $("#"+objID+"_menuId").slideDown(500);
					if($("#lang_"+objID).hasClass("menucurent"))
					{
						$("#ic_"+objID).removeClass("select-ic-menu_down").addClass("select-ic-menu_up");
						$("#lang_"+objID).removeClass("menucurent").addClass("menucurent_open");
					}
					else
					{
						$("#ic_"+objID).removeClass("ic-menu_down").addClass("ic-menu_up");
						$("#lang_"+objID).removeClass("leftmenuh3").addClass("leftmenuh3_open");
					}
                }else{			   
				   $("#"+objID+"_menuId").slideUp(500);
					if($("#lang_"+objID).hasClass("menucurent_open"))
					{
						$("#ic_"+objID).removeClass("select-ic-menu_up").addClass("select-ic-menu_down");
						$("#lang_"+objID).removeClass("menucurent_open").addClass("menucurent");
					}
					else
					{
						$("#ic_"+objID).removeClass("ic-menu_up").addClass("ic-menu_down");
						$("#lang_"+objID).removeClass("leftmenuh3_open").addClass("leftmenuh3");
					}	
                }
            }else{
					$("#"+childrenIDs[i]+"_menuId").hide();
               		 $("#"+childrenIDs[i]+"_menuId").slideUp(500);
					
					if($("#lang_"+childrenIDs[i]).hasClass("menucurent_open"))
					{
						$("#ic_"+childrenIDs[i]).removeClass("select-ic-menu_up").addClass("select-ic-menu_down");
						$("#lang_"+childrenIDs[i]).removeClass("menucurent_open").addClass("menucurent");
					}
					else
					{
						$("#ic_"+childrenIDs[i]).removeClass("ic-menu_up").addClass("ic-menu_down");
						$("#lang_"+childrenIDs[i]).removeClass("leftmenuh3_open").addClass("leftmenuh3");
					}				

            }
        }
}

function showErr(errid,errinfoid,res){
    $("#"+errid).show();
    $("#"+errinfoid).html(res);
}
function OffErr(errid,errinfoid,res){
    $("#"+errid).hide();
    $("#"+errinfoid).html("");
}
function basiccontainerminiheigh()
{
  var height1=document.getElementById("basiccontent").offsetHeight;
  var height2=document.documentElement.clientHeight - 48-188-64;
  var height=height1>height2?height1:height2;
  document.getElementById("basiccontent").style.minHeight=height2+"px";
}

function MoreContainMiniheight()
{
	var height1=document.documentElement.clientHeight - 48-188-64;
	var meunheight=document.documentElement.clientHeight - 48-188;
	var height2=document.getElementById("sub_menu_container").offsetHeight;
	var height=height2>height1?height2:height1;
	document.getElementById("right_content").style.minHeight=height+"px";
	document.getElementById("basiccontent").style.minHeight=meunheight+"px";
}

function BeforeOnload()
{
	document.getElementById("content").style.display="none";
	document.getElementById("CreateOnloadMessage").style.display="";
}

function AfterOnload()
{
	document.getElementById("content").style.display="";
	document.getElementById("CreateOnloadMessage").style.display="none";
}
