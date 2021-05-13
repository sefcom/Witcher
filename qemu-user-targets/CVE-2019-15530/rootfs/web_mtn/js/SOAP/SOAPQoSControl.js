/**
 * @constructor
 * 解析Qos客户端和优先级列表
 */
function SOAPGetQoSSettingResponse(){
    this.GetQoSSettingsResult = "";
    this.UploadBandwidth = "";
    this.DownloadBandwidth = "";
    this.QoSInfoList = new SOAPQoSListInfoResponse();
};

// @prototype
SOAPGetQoSSettingResponse.prototype =
{

}

function SOAPQoSListInfoResponse(){
    var qosInfo = new SOAPQoSInfoResponse();
    this.QoSInfo = $.makeArray(qosInfo);
}

function SOAPQoSInfoResponse(){
    this.Hostname = "";
    this.IPAddress = "";
    this.MACAddress = "";
    this.Priority = "";
    this.Type = "";
}

// @prototype
SOAPQoSListInfoResponse.prototype =
{
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.QoSInfo.splice(0,1);
        }
        this.QoSInfo.push(data);
        return true;
    }
};

/**
 * @constructor of set Priority of Qos
 */

function SOAPSetQoSSetting(){
    this.UploadBandwidth = "";
    this.DownloadBandwidth = "";
    this.QoSInfoData = new SOAPQoSListInfo();
};


function SOAPQoSListInfo(){
    var qosInfo = new SOAPQoSInfoResponse();
    this.QoSInfo = $.makeArray(qosInfo);
}

/**
 * @constructor
 * 手动设置速率的构造
 */
function SOAPManualSetSpeed(){
    this.UploadBandwidth = "";
    this.DownloadBandwidth = "";
}

/**
 * @constructor of switch Qos
 */
function SOAPGetQoSEnableResponse(){
    this.QoSManagementType = "";
};


function SOAPSetQoSEnableResponse(){
    this.QoSManagementType = "";
};

function SOAPSetWanSpeedTestResponse(){
    this.WanSpeedTest = true;
}

function SOAPGetWanSpeedTestResponse(){
    this.UploadBandwidth = "";
    this.DownloadBandwidth = "";
}