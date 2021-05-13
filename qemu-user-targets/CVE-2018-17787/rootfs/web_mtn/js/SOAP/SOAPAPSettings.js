
function SOAPGetAPDMZSettingsResponse()
{
	this.lanip = "";
	this.dmz_enable = "";
	this.dmz_ipaddr = "";
	this.GetMultipleHNAPsResult = "";
};

function SOAPSetAPDMZSettings(){
    this.lanip = "";
    this.dmz_enable = "";
    this.dmz_ipaddr = "";
    this.GetMultipleHNAPsResult = "";
 }



 function SOAPSetStatue(){
     this.statue = "";
 }
/**
 * @constructor
 */
function SOAPSetHostIPv6Settings()
{
	this.IPv6Mode = "";

};

/**
 * @constructor
 */
function SOAPGetHostIPv6StaticSettingsResponse()
{
	this.StaticAddress = "";
	this.StaticPrefixLength = "";
	this.StaticDefaultGateway = "";
	this.StaticDNS1 = "";
	this.StaticDNS2 = "";

};

// @prototype
SOAPGetHostIPv6StaticSettingsResponse.prototype =
{
	
}

function SOAPSetHostIPv6StaticSettings()
{
	this.StaticAddress = "";
	this.StaticPrefixLength = "";
	this.StaticDefaultGateway = "";
	this.StaticDNS1 = "";
	this.StaticDNS2 = "";

};

