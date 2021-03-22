<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>System | Firmware Upgrade</title>
<style>html,body,form,table,tr,td,div,p,span{
	margin:0px;
	padding:0px;
}

html {
	color: #666;
}
.first{border:1px solid #CBCBCB; padding:0px; margin:0px;background: url(../images/login_bg1.jpg) repeat-x; }
.second{border:1px solid #fff;}
.third{border:1px solid #EDEDED; }
.forth{border:1px solid #E7E7E7; height:436px \9;
	 min-height:435px;
	min-width:510;
	 padding:10px;}
body {
	COLOR: #666;font-family: "宋体","Times New Roman"; padding:0px; margin:0px; background: #e5e5e5;height:100%}
#main{margin:5px 0px;margin-left:54px}
.head{margin:20px 0px 0px 0px; FONT-SIZE:16px; font-weight:bold;COLOR:#000000;HEIGHT:35px; line-height:35px;}
table {border-collapse: separate;border-spacing:0;}

.content1{  font-size:12px; margin-top:10px; line-height:30px;width:446px; margin-left:0px}
.content1 td{ padding-left:5px}

INPUT.button1 {
	BACKGROUND-COLOR: #CCCCCC;COLOR: #000000; FONT-SIZE: 14px; FONT-STYLE: normal; FONT-VARIANT: normal; FONT-WEIGHT: normal; HEIGHT: 25px; width:58px; LINE-HEIGHT: normal; cursor:pointer;background:url(../images/bg_button.gif) repeat-x center; border:1px solid #ccc;border-radius:3px/5px; margin:5px 10px; line-height:25px;
}
INPUT.button1:hover{background:url(../images/bg_button_over.gif) repeat-x center;}
.hr{ background:url(../images/line.jpg) repeat-x; height:2px; line-height:2px; width:90%; margin:0px 5px 0px 0px; position:relative;display:inline-block}
.filestyle{ background-color:#FFF;}</style>
<script type="text/javascript">FirwareVerion="<%aspTendaGetStatus("sys","sysver");%>";//升级包版本
FirwareDate="<%aspTendaGetStatus("sys","compimetime");%>";//升级日期
//system_tool.js
/**
  * @方法 init
  * @参数 （objec）f 要初始化的表单对象
  * @描述 根据f的id来确定调用不同的初始化函数。
  */
function init(f)
{
	var op_id = f.id;
	switch (op_id) {
		case 'system_upgrade':
			initSystemUpgrade(f);
			break;
		default: ;
	}
}

/**
  * @方法 preSubmit
  * @所属页面 system_reboot.asp
  * @参数 （objec）f 要操作的表单对象
  * @描述 根据f的id来确定调用不同的提交函数。
  */
function preSubmit(f)
{
	var op_id = f.id;
	switch (op_id) {
		case 'system_upgrade':
			submitSystemUpgrade(f);
			break;
		default: ;
	}
}
/**
  * @方法 initUpgradeReboot
  * @所属页面 upgrading.asp
  * @参数 （objec）f 要初始化的表单对象
  * @描述 调用父框架中reboot，显示正在重启的进度条。
  */
function initUpgradeReboot(f)
{
	var url = "http://" + lanip;
	if (upgrade_sslenable == 1) {
		url = "https://" + lanip;
	}
	window.parent.reboot(url,400,upgrade_sslenable,1);
}

/**
  * @方法 initDirectrReboot
  * @所属页面 directr_reboot.asp
  * @参数 （objec）f 要初始化的表单对象
  * @描述 调用父框架中reboot，显示正在重启的进度条。
  */
function initDirectrReboot(f)
{
	var url = "http://" + lanip;
	if (sslenable == 1) {
		url = "https://" + lanip;
	}
	window.parent.reboot(url,550,sslenable);
}

/**
  * @方法 init
  * @所属页面 system_upgrade.asp
  * @参数 （objec）f 要初始化的表单对象
  * @描述 在html元素加载完成后运行，初始化页面元素的值。
  */
function initSystemUpgrade(f){
	f.reset();
}

/**
  * @方法 systemUpgrade
  * @所属页面 system_hostname.asp
  * @参数 （objec）f 要操作的表单对象
  * @描述 验证数据，并向后台提交数据。
  */
function submitSystemUpgrade(){  
	if (document.frmSetup.upgradeFile.value == "") {
		alert("请先选择升级文件！");
		return ;
	}
	if(confirm("您确定要升级吗？")) {
	   document.getElementById("fwsubmit").disabled = true;
	   document.frmSetup.submit() ;
	} 	
}</script>
<script src="lang/b28n_async.js"></script>
<script>
B.setTextDomain(["translate"]);
</script>
</head>

<body onload="init(document.frmSetup)">
<form name="frmSetup" method="POST" id="system_upgrade" action="/cgi-bin/upgrade" enctype="multipart/form-data">
<div class="first">
<div class="second">
<div class="third">
<div class="forth">	
<div class="head">Firmware Upgrade</div><div class="hr"></div>
<div id="main">
<table class="content1" width="100%" id="MyTable" cellspacing="0">
	<tbody><tr><td width="100" align="left" id="file">文件</td><td>
	<input type="file" name="upgradeFile" size="20" class="filestyle">&nbsp;&nbsp;
	</td></tr>
	<tr><td width="100" align="left" id="currenttype">当前系统版本</td><td>
		<script>document.write( FirwareVerion+"发布日期："+FirwareDate )</script></td></tr>
	<tr><td colspan="2"></td></tr>
</tbody></table>
</div>
<div class="hr"></div>
<br>
<input id="fwsubmit" type="button" class="button1" onmouseover="style.color='#FF9933'" onmouseout="style.color='#000000'" value="升级" onclick="preSubmit(document.frmSetup)">

</div></div></div></div>
</form>		

</body></html>