function SOAPStatue(){
   this.SetWanSettingsResult="";
};
function SOAPDNSSettings()
{
    this.Primary = "";
    this.Secondary = "";
}
function SOAPGetNetworkSettingsResponse(){
    this.Type = "";
    this.PppoeType  = "";
    this.Username = "";
    this.Password = "";
    this.MaxIdleTime = 0;
    this.MTU = "";
    this.HostName = "";
    this.ServiceName = "";
    this.AutoReconnect = false;
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
    this.DnsManual = "";
    this.RuntimeDNS = new SOAPDNSSettings();
    this.MacCloneEnable ="";
    this.CloneMacAddress  = "";
    this.MacCloneType   = "";
    this.WanSpeed= "";
    this.WanDuplex= "";
    this.ConfigDNS = new SOAPDNSSettings();
    this.MacAddress = "";
    this.VPNServerIPAddress = "";
    this.VPNLocalIPAddress = "";
    this.VPNLocalSubnetMask = "";
    this.VPNLocalGateway = "";
    this.PppoeStatus = "";
};

function SOAPSetWanSettings(){
    this.Type = "";
    this.PppoeType  = "";
    this.Username = "";
    this.Password = "";
    this.MaxIdleTime = 0;
    this.MTU = "";
    this.HostName = "";
    this.ServiceName = "";
    this.AutoReconnect = false;
    this.IPAddress = "";
    this.SubnetMask = "";
    this.Gateway = "";
    this.DnsManual = "";
    this.MacCloneEnable ="";
    this.CloneMacAddress  = "";
    this.MacCloneType   = "";
    this.WanSpeed= "";
    this.WanDuplex= "";
    this.ConfigDNS = new SOAPDNSSettings();
    this.MacAddress = "";
    this.VPNServerIPAddress = "";
    this.VPNLocalIPAddress = "";
    this.VPNLocalSubnetMask = "";
    this.VPNLocalGateway = "";
};

function SOAPSetNetworkWirelessSettings(){
    this.RadioID = "";
    this.Enabled = "";
    this.SSID=""
}

function SOAPLANInfoResponse(){
    this.IPAddress = "";
    this.SubnetMask = "";
    this.MACAddress = "";
};
function SOAPGuestInfoResponse(){
    this.IPAddress = "";
    this.SubnetMask = "";
};

function SOAPGetWanConnectionTypResponse(){
    this.Type = "";
}

function SOAPWanplugInfoResponse(){
    this.WanStatus="";
}

