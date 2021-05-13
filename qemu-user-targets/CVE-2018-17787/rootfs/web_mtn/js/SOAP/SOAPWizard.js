/**
 * Created by T-mark on 2017/8/25.
 */


/**
 * @constructor
 */
function SOAPGetnetworksResponse()
{
    this.IPAddress = "";
    this.SubnetMask = "";
}

/**
 * @constructor
 */
function SOAPGetGuestouterResponse()
{
    this.IPAddress = "";
    this.SubnetMask = "";
}

/**
 * @constructor
 */
function SOAPGetWanSettingsResponse()
{
    this.IPAddress = "";
    this.SubnetMask = "";
}


/**
 * @constructor
 */
function COnfigDNS(){
    this.Primary = "";
    this.Secondary = "";
}

/**
 * @constructor
 */
function SOAPGetDeviceSettingsResponse()
{
    this.FirmwareVersion = "";
}

/**
 * @constructor
 */
function SOAPGetWLanRadioRequest(){
    this.RadioID = ""
}


/**
 * @constructor
 */
function SOAPGetWLanRadioGuestRequest(){
    this.RadioID = ""
}

/**
 * @constructor
 */
function SOAPGetWLanRadioResponse(){
    this.SSID = "";
}

/**
 * @constructor
 */
function SOAPGetWLanRadioGuestResponse(){
    this.SSID = "";
}

/**
 * @constructor
 */
function SOAPGetWanConnectionTypeResponse(){
  this.Type = "";
}

/**
 * @constructor
 */
function SOAPGetPPPoEServerStatusResponse(){
    this.Type = "";
    this.UserName = "";
    this.Password = "";
}

function SOAPSetPPPoEServerSettingsRequest(){
    this.Enabled = "";
}

/**
 * @constructor
 */
function SOAPSetWanSettingsRequest()
{
    this.Type = "";
    this.PppoeType = "";
    this.Username = "";
    this.Password = "";
    this.ServiceName = "";
    this.MTU = "";
    this.WanSpeed = "";
    this.WanDuplex = "";
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
    this.ConfigDNS = new COnfigDNS();
    this.MacCloneEnable = false;
    this.DnsManual=false;
    this.AutoReconnect = true;
}

/**
 * @constructor
 */
function SOAPSetWLanRadioSettingsRequest(){
    this.RadioID = "";
    this.SSID = "";
}

/**
 * @constructor
 */
function SOAPSetWLanRadioSecurityRequest(){
    this.RadioID = "";
    this.Enabled = "false";
    this.Key = "";
}

/**
 * @constructor
 */
function SOAPSetDeviceSettingRequest(){
    this.AdminPassword = "";
    this.ChangePassword = "true";
}

/**
 * @constructor
 */
function SOAPSetPasswdSettingsRequest(){
    this.NewPassword = "";
}

function SOAPSetAutoUpgradeFirmwareRequest(){
    this.AutoUpgrade = false;
    this.StartTime = "";
}

/**
 * @constructor
 */
function SOAPSetnetworksRequest()
{
    this.IPAddress = "";
    this.SubnetMask = "";
    this.DHCPenable = false;
    this.IPRangeStart = "";
    this.IPRangeEnd = "";
    this.LeaseTime = "";
}

/**
 * @constructor
 */
function SOAPSetnetworksResponse()
{
    this.SetNetworkSettingsResult = "";

}