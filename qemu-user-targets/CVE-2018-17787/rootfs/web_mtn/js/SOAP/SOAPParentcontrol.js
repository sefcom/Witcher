/**
 * @constructor
 */
function SOAPGetWLanRadioSettingsResponse(){
    this.SSIDBroadcast = "";
    this.ChannelWidth = "";
    this.Channel = "";
    this.Mode = "";
    this.GuardInt = "";
    this.TXPower = "";
    this.WMM = "";
    this.SSID = "";
};


function SOAPSetWLanRadioSettings(){
    this.RadioID = "";
    this.SSIDBroadcast = "";
    this.ChannelWidth = "";
    this.Channel = "";
    this.Mode = "";
    this.GuardInt = "";
    this.TXPower = "";
    this.WMM = "";
    this.Enabled = "";
    this.SSID = "";
}

/**
 * @constructor
 */
function SOAPGetNetworkSettingsResponse(){
    this.IPAddress = "";
    this.SubnetMask = "";
}

/**
 * @constructor
 */
function SOAPGetParentControlInfoResponse(){
    var parentControlItem = new ParentControlItem();
    this.UsersInfo = $.makeArray(parentControlItem);
}

SOAPGetParentControlInfoResponse.prototype = {
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.UsersInfo.splice(0,1);
        }
        this.UsersInfo.push(data);
        return true;
    }
}

function ParentControlItem(){
    this.GroupId = "";
    this.HostName = "";
    this.Mac = "";
    this.IndexEnable = "";
    this.StartTime = "";
    this.EndTime = "";
    this.Week = "";
    this.UrlEnable = "";
    this.URL1 = "";
    this.URL2 = "";
    this.URL3 = "";
    this.URL4 = "";
    this.URL5 = "";
    this.URL6 = "";
    this.URL7 = "";
    this.URL8 = "";
}

/**
 * @constructor
 */
function SOAPSetParentControlInfo(){
    var parentControlItem = new ParentControlItem();
    this.UsersInfo = $.makeArray(parentControlItem);
}