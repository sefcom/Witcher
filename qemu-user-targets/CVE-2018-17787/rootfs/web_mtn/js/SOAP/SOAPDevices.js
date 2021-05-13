/**
 * @constructor
 */
function SOAPClientInfoResponse()
{
	this.MacAddress = "";
	this.IPv4Address = "";
	//this.IPv6Address = "";
	this.Type = "";
	this.DeviceName = "";
	this.LinkTime = "";
	this.OnlineTime = "";
	this.NickName = "";
	this.WireAndWireless = "";
	//this.ReserveIP = "";
};
function SOAPClientInfoRequest()
{
	this.MacAddress = "";
	//this.IPv4Address = "";
	//this.IPv6Address = "";
	//this.Type = "";
	this.DeviceName = "";
	//this.NickName = "";
	//this.ReserveIP = "";
	this.Enable = "";
};
/**
 * @constructor
 */
function SOAPClientInfoListsResponse()
{
	var clientInfo = new SOAPClientInfoResponse();

	this.ClientInfo = $.makeArray(clientInfo);
};


/**
 * @constructor
 */
function SOAPClientInfo()
{
	this.MacAddress = "";
	this.NickName = "";
	this.ReserveIP = "";
};

/**
 * @constructor
 */
function SOAPClientInfoLists()
{
	var clientInfo = new SOAPClientInfo();

	this.ClientInfo = $.makeArray(clientInfo);
};

// @prototype
SOAPClientInfoLists.prototype = 
{
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


/**
 * @constructor
 */
function SOAPGetClientInfoResponse()
{
	this.GetClientInfoResult = "";
	this.ClientInfoLists =new SOAPClientInfoListsResponse();
};

/**
 * @constructor
 */
function SOAPSetClientInfoResponse()
{
	this.SetClientInfoResult = "";
}

/**
 * @constructor
 */
function SOAPUpdateClientInfoResponse()
{
	this.UpdateClientInfoResult = "";
}

/**
 * @constructor
 */
function SOAPSetClientInfo()
{
	this.ClientInfoLists = new SOAPClientInfoLists();
};


/**
 * @constructor
 */
function SOAPMacInfo()
{
	this.MacAddress = "";
	this.DeviceName = "";
	this.ScheduleName = "";
	this.Status = "";
}

/**
 * @constructor
 */
function SOAPMACList()
{
	var macinfo = new SOAPMacInfo();

	this.MACInfo = $.makeArray(macinfo);
};

// @prototype
SOAPMACList.prototype = 
{
	_init:true,
	push : function(data){
		if(this._init)
		{
			this._init = false;
			this.MACInfo.splice(0,1);
		}
		this.MACInfo.push(data);
		return true;
	}
};


/**
 * @constructor
 */
function SOAPSetMACFilters2()
{
	this.Enabled = true;
	this.IsAllowList = false;
	this.MACList = new SOAPMACList();
} 

/**
 * @constructor
 */
function SOAPGetMACFilters2Response()
{
	this.Enabled = false;
	this.IsAllowList = false;
	this.MACList = new SOAPMACList();
}

/**
 * @constructor
 */
function SOAPDeleteClientInfoListsRequest()
{
	this.MacAddress = "";
	this.Enable = "";
}

/**
 * @constructor
 */
function SOAPDeleteClientInfoListsResponse()
{
	this.DelStationSettingsResult = "";
}