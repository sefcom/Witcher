/**
 * Created by T-mark on 2017/8/28.
 */
/**
 * @constructor
 */
function SOAPGetnetworksResponse()
{
    this.IPAddress = "";
    this.SubnetMask = "";
    this.DHCPenable = "";
    this.IPRangeStart = "";
    this.IPRangeEnd = "";
    this.LeaseTime = "";
    this.MACAddress = "";
}


/**
 * @constructor
 */
function SOAPGetDhcpClientInfoResponse(){
    this.MacAddress = "";
    this.IPv4Address = "";
    this.DeviceName = "";
   // this.NickName = "";
}

/**
 * @constructor
 */
function SOAPDHCPClientInfoListsResponse()
{
    var clientInfo = new SOAPGetDhcpClientInfoResponse();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPGetDHCPClientInfoResponse()
{
    this.DHCPClientInfoLists =new SOAPDHCPClientInfoListsResponse();
};


/**
 * @constructor
 */
function SOAPGetStaticClientInfoResponse(){
    this.MacAddress = "";
    this.IPv4Address = "";
    this.DeviceName = "";
    //this.NickName = "";
}

/**
 * @constructor
 */
function SOAPSTATICClientInfoListsResponse()
{
    var clientInfo = new SOAPGetStaticClientInfoResponse();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPGetSTATICClientInfoResponse()
{
    this.StaticClientInfoLists =new SOAPSTATICClientInfoListsResponse();
};


/**
 * @constructor
 */
function SOAPSetStaticClientInfoRequest(){
    this.MacAddress = "";
    this.IPv4Address = "";
    this.DeviceName = "";
    //this.NickName = "";
}

/**
 * @constructor
 */
function SOAPSTATICClientInfoListsRequest()
{
    var clientInfo = new SOAPSetStaticClientInfoRequest();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPSetSTATICClientInfoRequest()
{
    this.StaticClientInfoLists =new SOAPSTATICClientInfoListsRequest();
};

// @prototype
SOAPSTATICClientInfoListsRequest.prototype ={
    _init:true,
    push : function(data){
        if(this._init)
        {
            this._init = false;
            this.ClientInfo.splice(0,1);
        }
        this.ClientInfo.push(data);
        return true;
    }
};