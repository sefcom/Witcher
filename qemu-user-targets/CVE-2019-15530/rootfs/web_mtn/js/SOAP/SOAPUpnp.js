/**
 * Created by T-mark on 2017/8/29.
 */
/**
 * @constructor
 */
function SOAPGetUpnpClientInfoResponse(){
    this.Protocol = "";
    this.ApplicationName = "";
    this.ClientIP = "";
    this.InternalPort = "";
    this.ExternalPort = "";
}

/**
 * @constructor
 */
function SOAPUpnpClientInfoListsResponse()
{
    var clientInfo = new SOAPGetUpnpClientInfoResponse();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPGetUpnpResponse()
{
    this.UpnpClientInfoLists =new SOAPUpnpClientInfoListsResponse();
    this.Enable = "";
};


/**
 * @constructor
 */
function SOAPSetUpnpResponse()
{
    this.Enable = "";
};
