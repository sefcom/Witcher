function doLogin(ifLogin_Password,ifLogin_Captcha)
{
	var PrivateKey = null;
	
	var loginObj = $.Deferred();
	
	var soapAction = new SOAPAction();
	var setLogin = new SOAPLogin();
	var getLogin = new SOAPLoginResponse();
	setLogin.Action = "request";
	setLogin.Username = "Admin";
	setLogin.Captcha = ifLogin_Captcha;

	// Login request
	soapAction.sendSOAPAction("Login", setLogin, getLogin).done(function(obj)
	{
		if (obj.Challenge != null || obj.Cookie != null || obj.PublicKey != null)
		{
			PrivateKey = hex_hmac_md5(obj.PublicKey + ifLogin_Password, obj.Challenge);
			PrivateKey = PrivateKey.toUpperCase();
			// Set Cookie
			$.cookie('uid', obj.Cookie, { path: '/' });
			// Storage data in DOM
		/*try {
               localStorage.setItem("PrivateKey", PrivateKey);
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   $.cookie('PrivateKey', PrivateKey, {path: '/' }); 
			

			var Login_Passwd = hex_hmac_md5(PrivateKey, obj.Challenge);
			Login_Passwd = Login_Passwd.toUpperCase();
			
			//rewrite login request
			setLogin.Action = "login";
			setLogin.LoginPassword = Login_Passwd;//Login_Passwd;
			setLogin.Captcha = ifLogin_Captcha;
			
			// Do Login to DUT
			var soapAction2 = new SOAPAction();
			soapAction2.sendSOAPAction("Login", setLogin, null).done(function(obj2)
			{
				//for compatibility
				if(obj2.LoginResult == "FAILED")
				{
					loginObj.reject();
				}
				else
				{
					loginObj.resolve();
				}
			})
			.fail(function(){
				loginObj.reject();
			});
		}
		else
		{
			loginObj.reject();
		}
	})
	.fail(function(){
		loginObj.reject();
	});
	return loginObj.promise();
}