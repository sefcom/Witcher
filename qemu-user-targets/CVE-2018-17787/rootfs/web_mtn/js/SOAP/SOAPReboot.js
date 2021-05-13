

function SOAPGetDeviceDomainNameResponse(){
    this.DefaultDomainName = "";
    this.GetDeviceDomainNameResult = "";
}

function SOAPGetAutoRebootSettingsResponse() {
    this.AutoReboot = "";
    this.StartTime = "";
    this.StopTime = "";
}

function SOAPSetAutoRebootSettings(){
    this.AutoReboot = "";
    this.StartTime = "";
}

/**
 * @constructor
 */
function SOAPGetNTPServerSettingsResponse(){
    this.Status = '';
}