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
    this.DHCPenable = "";
    this.IPRangeStart = "";
    this.IPRangeEnd = "";
    this.LeaseTime = "";
    this.MACAddress = "";
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
function SOAPGetDhcpClientInfoResponse(){
    this.MacAddress = "";
    this.IPv4Address = "";
    this.DeviceName = "";
    //this.NickName = "";
    this.LeaseTime = "";
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
function SOAPGetWanSettingsResponse()
{
    this.IPAddress = "";
    this.SubnetMask = "";
}


/**
 * @constructor
 */
function SOAPSetnetworksRequest()
{
    this.IPAddress = "";
    this.SubnetMask = "";
    this.DHCPenable = "";
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