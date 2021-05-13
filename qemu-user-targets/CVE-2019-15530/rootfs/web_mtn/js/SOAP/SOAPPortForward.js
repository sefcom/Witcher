//获取
function SOAPGetForwardSettingResponse(){
    this.VirtualServerList = new SOAPForwardListInfo();
};
// @prototype
SOAPGetForwardSettingResponse.prototype ={

};
function SOAPForwardListInfo(){
    var ForwardInfo = new SOAPForwardInfoResponse();
    this.VirtualServerInfo = $.makeArray(ForwardInfo);
};
function SOAPForwardInfoResponse(){
    this.Enabled = "";
    this.VirtualServerDescription = "";
    this.ExternalPort = "";
    this.InternalPort = "";
    this.ProtocolType= "";
    this.ProtocolNumber = "";
    this.LocalIPAddress = "";
    this.ScheduleName = "";
};
// @prototype
SOAPForwardListInfo.prototype ={
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.VirtualServerInfo.splice(0,1);
        }
        this.VirtualServerInfo.push(data);
        return true;
    }
};



//配置
function SOAPSettForwardSettingResponse(){
    this.VirtualServerList = new SOAPForwardListInfoResponse();
};
// @prototype
SOAPSettForwardSettingResponse.prototype ={

};
function SOAPForwardListInfoResponse(){
    var ForwardInfo = new SOAPForwardInfo();
    this.VirtualServerInfo = $.makeArray(ForwardInfo);
};
function SOAPForwardInfo(){
    this.Enabled = "";
    this.VirtualServerDescription = "";
    this.ExternalPort = "";
    this.InternalPort = "";
    this.ProtocolType= "";
    this.ProtocolNumber = "";
    this.LocalIPAddress = "";
    this.ScheduleName = "";
};
// @prototype
SOAPForwardListInfoResponse.prototype ={
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.VirtualServerInfo.splice(0,1);
        }
        this.VirtualServerInfo.push(data);
        return true;
    }
};
function SOAPLANInfoResponse(){
    this.IPAddress = "";
    this.SubnetMask = "";
};


//配置返回
function SOAPSetResponse(){
    this.SetVirtualServerSettingsResult = "";
};





