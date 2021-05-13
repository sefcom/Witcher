var Time_GetReciprocalTime;
var DetectSecond = new Date().getTime() / 1000;
var ReadyStartTimeout;
var timeout_Value = 0;
var timeout_Click = 0;
var timeout_Draft = 0;
var changeFlag = false;
var checkFlag = false;
var Host_Name = "";
var CurrentTime = 0;
var timeout_Standard = 300;
var timeout_Range = 10;

function checkTimeout()
{
	/*try {
              sessionStorage.setItem('timeout', timeout_Value);
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		
		 $.cookie('timeout', timeout_Value, {path: '/' }); 
			
	
	Time_GetReciprocalTime = window.clearInterval(Time_GetReciprocalTime);
	Time_GetReciprocalTime = self.setInterval("GetReciprocalTime()",1000);
}

function GetReciprocalTime()
{
	var tmpTime = new Date().getTime() / 1000;
	
	if (CurrentTime == timeout_Standard - timeout_Range)
	{
		if (checkFlag == true)
		{
			/*var HNAP = new HNAP_XML();
			var xml_request = HNAP.GetXML("GetTimeSettings");*/
			CurrentTime = parseInt(tmpTime) - parseInt(DetectSecond) - timeout_Click;
			/*try {
               sessionStorage.setItem('timeout', CurrentTime);
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   
		    $.cookie('timeout', CurrentTime, {path: '/' }); 
		
			
			DetectSecond = new Date().getTime() / 1000;
			timeout_Draft = CurrentTime;
			timeout_Click = 0;
			checkFlag = false;
		}
		else
		{
			CurrentTime = parseInt(tmpTime) - parseInt(DetectSecond) + timeout_Draft;
		
		/*try {
               sessionStorage.setItem('timeout', CurrentTime);
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		    $.cookie('timeout', CurrentTime, {path: '/' }); 
		
			
		}
	}
	else if ($.cookie('timeout') >= timeout_Standard)
	{
		//sessionStorage.removeItem('timeout');
		$.removeCookie('timeout', { path: '/' }); 
		clearInterval(Time_GetReciprocalTime);
		readyLogout();
	}
	else
	{
		CurrentTime = parseInt(tmpTime) - parseInt(DetectSecond) + timeout_Draft;
		//sessionStorage.setItem('timeout', CurrentTime);
		$.cookie('timeout', CurrentTime, {path: '/' }); 
	}
}

function GetClickTime()
{
	var tmpTime = new Date().getTime() / 1000;
	timeout_Click = parseInt(tmpTime) - parseInt(DetectSecond);
}

function readyLogout()
{
	//var HNAP = new HNAP_XML();
	var soapAction = new SOAPAction();
	var setLogin = new SOAPLogin();

	setLogin.Action = "logout";
	setLogin.Username = "Admin";
	setLogin.Captcha = "";

	/*var body = soapAction.createJsonBody("Logout", setLogin);
	var json = new StringDoc(body);
	var xml_LogoutResult = HNAP.SetXML("Logout", json);//xml_Logout
	
	var logout_Result = xml_LogoutResult.Get("LogoutResponse/LogoutResult");*/
	soapAction.sendSOAPAction("Logout",setLogin,null)
		.done(function(obj){
			if(obj.LogoutResult == "OK")
			{
				redirect_URL();
			}else
				setTimeout("redirect_URL()", 1000);
		})
		.fail(function(obj){
			setTimeout("redirect_URL()", 1000);
		})
	/*if (logout_Result == "OK")	{	redirect_URL();	}
	else	{	setTimeout("redirect_URL()", 1000);	}*/
}

function redirect_URL()	{	location.assign("/");	}

function confirmExit()
{
	//try {

			if ($.cookie('timeout') > 0)
			{
				if (changeFlag)
				{
					//if (!confirm("此页面有未保存的数据，您要放弃吗？"))	{	return false;	}
					if (!confirm(I18N("j","Commom_Data_Unsaved")))	{	return false;	}
					else	{	return true;	}
				}
			}
			
			return true;
	//}
	
	/*catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
}
