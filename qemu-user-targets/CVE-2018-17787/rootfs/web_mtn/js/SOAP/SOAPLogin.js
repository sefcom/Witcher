/**
 * @constructor
 */
function SOAPLogin()
{
	this.Action = "";
	this.Username = "Admin";
	this.LoginPassword = "";
	this.Captcha = "";
	this.PrivateLogin = "LoginPassword";
};

// @prototype
SOAPLogin.prototype = 
{

}

/**
 * @constructor
 */
function SOAPLoginResponse()
{
	this.Challenge = "";
	this.Cookie = "";
	this.PublicKey = "";

};

/**
 * @constructor
 */
function SOAPGetCAPTCHAsettingResponse()
{
	this.CaptchaUrl = "";
};
