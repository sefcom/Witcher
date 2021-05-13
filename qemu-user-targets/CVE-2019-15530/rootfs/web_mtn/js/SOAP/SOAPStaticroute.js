/**
 * Created by T-mark on 2017/8/29.
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
function SOAPGetWanSettingsResponse () {
    this.Type = "";
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
}


/**
 * @constructor
 */
function SOAPGetStaticRouteClientInfoResponse(){
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
}

/**
 * @constructor
 */
function SOAPStaticRouteClientInfoListsResponse()
{
    var clientInfo = new SOAPGetStaticRouteClientInfoResponse();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPGetStaticrouteResponse()
{
    this.StaticRouteClientInfoLists =new SOAPStaticRouteClientInfoListsResponse();
};


/**
 * @constructor
 */
function SOAPSetStaticRouteClientInfoRequest(){
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
    this.Interface = "";
}

/**
 * @constructor
 */
function SOAPStaticRouteClientInfoListsRequest()
{
    var clientInfo = new SOAPSetStaticRouteClientInfoRequest();

    this.ClientInfo = $.makeArray(clientInfo);
};



/**
 * @constructor
 */
function SOAPSetStaticrouteRequest()
{
    this.StaticRouteClientInfoLists =new SOAPStaticRouteClientInfoListsRequest();
};

// @prototype
SOAPStaticRouteClientInfoListsRequest.prototype ={
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
