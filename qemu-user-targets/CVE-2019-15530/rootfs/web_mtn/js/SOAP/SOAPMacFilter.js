//获取
function SOAPGetMACFilterSettingResponse(){
    this.IPv4_FirewallStatus="";
    this.HistoryStatus="";
    this.IPv4FirewallRuleLists = new SOAPMacFilterListInfo();
};
// @prototype
SOAPGetMACFilterSettingResponse.prototype ={

};
function SOAPMacFilterListInfo(){
    var ForwardInfo = new SOAPMACFilterInfoResponse();
    this.IPv4FirewallRule = $.makeArray(ForwardInfo);
};
function SOAPMACFilterInfoResponse(){
    this.Name = "";
    this.Status = "";
    this.Schedule = "";
    this.SrcInterface = "";
    this.SrcIPv4AddressRangeStart= "";
    this.SrcIPv4AddressRangeEnd = "";
    this.DestInterface = "";
    this.DestIPv4AddressRangeStart = "";
    this.DestIPv4AddressRangeEnd = "";
    this.Protocol = "";
    this.ProtocolRangeStart = "";
    this.ProtocolRangeEnd = "";
    this.ProtocolSrcRangeEnd = "";
    this.ProtocolSrcRangeStart = "";
};
// @prototype
SOAPMacFilterListInfo.prototype ={
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.IPv4FirewallRule.splice(0,1);
        }
        this.IPv4FirewallRule.push(data);
        return true;
    }
};



//配置
function SOAPSettMACFilterSettingResponse(){
    this.IPv4_FirewallStatus="";
    this.HistoryStatus="";
    this.IPv4FirewallRuleLists = new SOAPMACFilterListInfoResponse();
};
// @prototype
SOAPSettMACFilterSettingResponse.prototype ={

};
function SOAPMACFilterListInfoResponse(){
    var ForwardInfo = new SOAPMACFilterInfo();
    this.IPv4FirewallRule = $.makeArray(ForwardInfo);
};
function SOAPMACFilterInfo(){
    this.Name = "";
    this.Status = "";
    this.Schedule = "";
    this.SrcInterface = "";
    this.SrcIPv4AddressRangeStart= "";
    this.SrcIPv4AddressRangeEnd = "";
    this.DestInterface = "";
    this.DestIPv4AddressRangeStart = "";
    this.DestIPv4AddressRangeEnd = "";
    this.Protocol = "";
    this.ProtocolRangeStart = "";
    this.ProtocolRangeEnd = "";
    this.ProtocolSrcRangeEnd = "";
    this.ProtocolSrcRangeStart = "";
};
// @prototype
SOAPMACFilterListInfoResponse.prototype ={
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.IPv4FirewallRule.splice(0,1);
        }
        this.IPv4FirewallRule.push(data);
        return true;
    }
};
//没有数据时，只发状态
function SOAPSettMACFilterStatueSettingResponse(){
    this.IPv4_FirewallStatus="";
    this.HistoryStatus="";
    this.IPv4FirewallRuleLists="";
};


//配置结果
function SOAPMACFilterStatue(){
    this.SetIPv4FirewallSettingsResult="";
};


