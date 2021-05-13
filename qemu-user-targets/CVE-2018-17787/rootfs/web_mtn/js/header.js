function Onloadmenu()
{
	var isMobile = false;
	if(navigator.userAgent.match(/iPad|Android|webOS|iPhone|iPod|BlackBerry/i)) {
		isMobile = true;
	}else {
		isMobile = false;
	}
	if(isMobile)
	{
		document.getElementById("divphonepage").style.display="";
	}
	else
	{
		document.getElementById("divphonepage").style.display="none";
	}
	$("#divphonepage").click(function(){
		$("#phoneimg").removeClass("phoneimghover").removeClass("phoneimg").addClass("phoneimgactive");
		$("#phoneword").removeClass("bsword").addClass("bswordhover");
		location.href ="MobileIndex.html";
	});
	
	var str=location.href; //取得整个地址栏
   var start=str.lastIndexOf("/");
   var page="";
   if(start>-1)
   {
  	 page=str.substring(start+1,str.length+1);
   }
   switch(page)
   {
   case "Home.html":
  $('#want_home_id').removeClass("menulist").addClass("menulistcurrent");
   break;   
   case "Network.html":
  $('#want_internet_id').removeClass("menulist").addClass("menulistcurrent");
   break;    
   case "Wireless.html":
  $('#want_wirless_id').removeClass("menulist").addClass("menulistcurrent");
   break;   
   case "Parentcontrol.html":
   $('#want_parentcontrol_id').removeClass("menulist").addClass("menulistcurrent");
   break;    
   case "QoSControl.html":
   $('#want_qos_id').removeClass("menulist").addClass("menulistcurrent");
   break;    
   case "Devices.html":
   $('#want_guestwireless_id').removeClass("menulist").addClass("menulistcurrent");
   break;
   case "WeiManage.html":
   $('#want_weixin_id').removeClass("menulist").addClass("menulistcurrent");
   break;   
   default:
   $('#want_more_id').removeClass("menulist").addClass("menulistcurrent");
   break;    
   }	
	
}


function SOAPFirmwareVersionResponse(){
    this.FirmwareVersion = "";
}


function GetSoftWareVersion()
{
	//var dtd = $.Deferred();
    var versionInfo = new SOAPFirmwareVersionResponse();
    var soapAction = new SOAPAction();
    soapAction.sendSOAPAction("GetDeviceSettings",null,versionInfo).done(function(){
        $("#softwarevesrion").html(XMLEncode_formodelname(versionInfo.FirmwareVersion));
    })
	
}


/*function GetSoftWareVersion_1nd(result_xml)
{
	var GetSoftWareVersion_1nd = result_xml.Get("GetCurrentFirmwareVersionResponse/GetCurrentFirmwareVersionResult");
	if (GetSoftWareVersion_1nd == "OK")
	{
		var current_firmware_version = result_xml.Get("GetCurrentFirmwareVersionResponse/current_firmware_version");
		var softversionid=document.getElementById("current_version");
		document.getElementById("softwarevesrion").innerHTML = current_firmware_version;
	}
	else if (DebugMode == 1)	{	alert("[!!GetXML Error!!] Function: GetSoftWareVersion_1nd");	}
}*/


function LoadFooter()
{
	var str="<div align='center';style='height:67px; width:100%;min-width:1160px;'>";
	 str+="<div class='moreline2'></div>";
	 str+="<div id='foot' style='height:65px; line-height:65px;width:1160px;text-align:right;font-size: 14px;letter-spacing: 2px;'>";
	 str+="<span style='color: #595757'>技术支持电话：</span><span style='color: #9E9E9F;'>400-629-6688</span>&nbsp;&nbsp;&nbsp;&nbsp;<span style='color: #595757'>COPYRIGHT</span><span style='color: #9E9E9F;'> © 2017 D-Link</span>";
	 str+="</div></div>";
	document.getElementById("footer").innerHTML = str;
}

function LoadDetectRouter()
{
		var str="<div  class='AlertPopRect'>";
		 str+="<table style='width:426px; margin-top:20px;' align='center'><tbody><tr><td align='center' style='font-size:14px;color:#666666; '>"+I18N("j","Commom_Router_not_Connected")+"</td></tr>";
		 str+="<tr style='height:15px; '></tr>";
		 str+="<tr> <td align='center'><input type='button' class='styled_button_ss' id='Save_edit_pop_btn' value='' onclick='CheckHTMLStatus(\"\")'></td></tr>";
		 str+="</tbody></table></div>";
		document.getElementById("DetectRouterConnection").innerHTML = str;
		document.getElementById("Save_edit_pop_btn").value = I18N("j","Commom_Retry");
}

function LoadSaveMessage()
{
	var str="<div class='AlertPopRect'>";
	str+="<div id='waitSettingFinish'>";
	str+="<div class='pull-left' style='margin-left:48px;margin-top:37px;'>";
	str+="<img src='/image/D.gif' width='48' height='48' />";
	str+="</div>";
	str+="<div class='pull-left' style='margin-left:30px;font-size:14px;color:#666666;'>";
	str+="<table style='height:126px;width:250px;' align='center'>";
	str+="<tr align='left'>";
	str+="<td id='dialog_msg_black2'>"+I18N("j","Commom_Save_Config")+"</td>";
	str+="</tr></table>";
	str+="</div>";
	str+="</div></div>";
	document.getElementById("CreatePopAlertMessage").innerHTML = str;
	
}



function LoadHeader()
{
	var str="<div align='center' style='height:119px;width:100%;min-width:1200px;border-bottom: 1px solid #008DA8;' class='clearboth' id='top_nav'>";
	str+=" <div style='width:1200px;' class='clearboth' id='nav_title'>";
	str+="<div style='margin-top:65px;margin-left: 10px;' class='pull-left'><img src='image/logo.png' width='152' height='30'> </div>";
	str+="<div class='pull-left' style='margin-top:76px;margin-left:12px;font-family: 'Roboto'; color:#C8C9CA; font-size:18px;height:20px;'><span>&nbsp;DIR-823G&nbsp;&nbsp;</span><span id='HardWareVesrion'>HW:A1&nbsp;&nbsp;</span><span>FW:</span><span id='softwarevesrion'>1.0.1</span></div>";
	str+=" <div class='pull-right'>";
	str+=" <div style='margin-top:76px;cursor: pointer;margin-right: 10px;' class='pull-left' onClick='Logout();' id='divlogout' onmouseover='Logoutmouseover();' onmouseout='Logoutmouseout();'> ";

	str+="<div class='bsword' style='padding-top: 0px;' id='logoutword'>"+I18N("j","Footer_Logout")+"<span class='logout-right'></span></div>";
	str+=" </div>";
	str+=" <div style=' margin-top:10px; margin-left:36px;cursor: pointer;' class='pull-left' id='divphonepage' onmouseover='Phonemouseover();' onmouseout='Phonemouseout();' onclick='GotoPhonePage();'>";
	str+=" <div class='phoneimg' id='phoneimg'></div>";

	str+="<div class='bsword' id='phoneword'>"+I18N("j","Footer_Mobile")+"</div>";
	str+=" </div>";
	str+=" </div>";
	str+="</div> </div>";
	str+="<div align='center' style='width: 1180px;margin: 0 auto; min-width:1180px;' class='menu_top'>";
	str+="<div style='width:100%;height:94px;position:relative;' class='clearboth'>";
	str+="<div class='nav-bar'>";
	str+=" <div id='home' onclick='clickMainMenu(\"\Home\");'><div class='wrap-icon'><div class='menulist' id='want_home_id'><div class='nav-title'><span>"+I18N("j","Commom_Home")+"</span><label style='margin-left:60px;line-height: 91px;color: #666666;font-weight:normal'>|</label></div> </div></div></div>";
	str+="<div id='internet' onclick='clickMainMenu(\"\Network\");'><div class='wrap-icon'><div class='menulist' id='want_internet_id'><div class='nav-title'><span>"+I18N("j","Commom_Internet")+"</span></div></div></div></div>";
	str+=" <div id='wirless' onclick='clickMainMenu(\"\Wireless\");'><div class='wrap-icon'><div class='menulist' id='want_wirless_id'><div class='nav-title'><span>"+I18N("j","Commom_Wireless")+"</span></div></div></div></div>";
	str+="<div id='parentcontrol' onclick='clickMainMenu(\"\Parentcontrol\");'><div class='wrap-icon'><div class='menulist' id='want_parentcontrol_id'><div class='nav-title'><span>"+I18N("j","Commom_Parent_Control")+"</span></div></div></div></div>";
	str+="<div id='qos' onclick='clickMainMenu(\"\QoSControl\");'><div class='wrap-icon'><div class='menulist' id='want_qos_id'><div class='nav-title'><span>"+I18N("j","Commom_QoS_Control")+"</span></div></div></div></div>";
	str+="<div id='guestwireless' onclick='clickMainMenu(\"\Devices\");'><div class='wrap-icon'><div class='menulist' id='want_guestwireless_id'><div class='nav-title'><span>"+I18N("j","Commom_Device_Users")+"</span></div></div></div></div>";
	/*str+="<div id='weixin' onclick='clickMainMenu(\"\WeiManage\");'><div class='wrap-icon'><div class='menulist' id='want_weixin_id'><div class='nav-title'><span>"+I18N("j","Commom_Weinxin_Control")+"</span></div></div></div></div>";*/
	str+="<div id='more' onclick='clickMainMenu(\"\DhcpServer\");'><div class='wrap-icon'><div class='menulist' style='width:130px;' id='want_more_id'><div class='nav-title'><span>"+I18N("j","Commom_More")+"...</span></div></div></div></div>";
	str+="</div></div>";
	str+="<div class='headline'></div>";
	str+="</div>";
	document.getElementById("header").innerHTML = str;
	document.title=I18N("j","Login_Router_Name");
}

function GotoPhonePage()
{
	$("#phoneimg").removeClass("phoneimghover").removeClass("phoneimg").addClass("phoneimgactive");
	$("#phoneword").removeClass("bsword").addClass("bswordhover");
	location.href ="MobileIndex.html";
}

function Logoutmouseover()
{
	$("#logoutimg").removeClass("logout").addClass("logouthover");
	$("#logoutword").removeClass("bsword").addClass("bswordhover");	
	
}

function Logoutmouseout()
{
	$("#logoutimg").removeClass("logouthover").addClass("logout");
	$("#logoutword").removeClass("bswordhover").addClass("bsword");	
}

function Phonemouseover()
{
	$("#phoneimg").removeClass("phoneimg").addClass("phoneimghover");
	$("#phoneword").removeClass("bsword").addClass("bswordhover");
}

function Phonemouseout()
{
	$("#phoneimg").removeClass("phoneimghover").addClass("phoneimg");
	$("#phoneword").removeClass("bswordhover").addClass("bsword");	
}

function clickMainMenu(menu)
{
	
	if(!confirmExit())
	{
		return false;
	}
	
	window.location.href =menu+".html";
}

function Logout()
{
	$("#logoutimg").removeClass("logout").removeClass("logouthover").addClass("logoutactive");
	$("#logoutword").removeClass("bsword").addClass("bswordhover");	
	readyLogout();
}

function Initial()
{
	LoadHeader();
	Onloadmenu();
	LoadFooter();
	LoadDetectRouter();
	LoadSaveMessage();
	GetSoftWareVersion();
}