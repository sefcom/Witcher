function SOAPStatue(){
   this.SetWLanRadioSettingsResult="";
}
//页面刷新赋值模板
function SOAPGetAPWireless24gSettingsResponse(){
      this.RadioID = "";
      this.Enabled = "";
      this.Mode = "";
      this.SSID = "";
      this.SSIDBroadcast = "";
      this.ChannelWidth = "";
      this.Channel="";
      this.SecondaryChannel = "";
      this.QoS = "";
      this.ScheduleName = "";
      this.TXPower = "";

};
function SOAPGetAPWireless5gSettingsResponse(){
    this.RadioID = "";
    this.Enabled = "";
    this.Mode = "";
    this.SSID = "";
    this.SSIDBroadcast = "";
    this.ChannelWidth = "";
    this.Channel="";
    this.SecondaryChannel = "";
    this.QoS = "";
    this.ScheduleName = "";
    this.TXPower = "";

}
function SOAPGetAPWireless24gPasswordResponse(){
    this.Key = "";
};
function SOAPGetAPWireless5gPasswordResponse(){
    this.Key = "";
};





function SOAPSetAPWirelessPassword(){
    this.Key = "";
    this.RadioID = "";
}
//获取2.4g
function SOAPGet24GResponse(){
    this.RadioID = "";
}
//获取5g
function SOAPGet5GResponse(){
    this.RadioID = "";
}
function SOAPSetAPWirelessSettings(){
    this.RadioID = "";
    this.Enabled = "";
    this.SSID=""
}


function SOAPRadioInfo(){
    this.RadioID = "";
    this.Frequency = 2;
    this.SupportedModes = new Array();
    this.Channels = new Array();
}

function SOAPRadioInfos(){
    var radioInfo = new SOAPRadioInfo();
    this.RadioInfo = $.makeArray(radioInfo);  //把类数组的对像转化为数组
}

function SOAPGetWLanRadiosResponse(){
    this.RadioInfos = new SOAPRadioInfos();
}

//guest
function SOAPGetWGuestRadioSettingsResponse(){
    this.Enabled = "";
    this.SSID = "";
};