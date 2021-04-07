//common
function CommonDeviceInfo()
{
	this.bridgeMode = false;
	this.featureVPN = false;

	this.featureSharePort = true;
	this.featureDLNA = false;
	this.featureUPNPAV = true;
	this.featureSmartConnect = false;
	
	this.helpVer = "";
}

//Solve the problem the JSON.stringify(currentDevice) could not deal with the prototype of currentDevice.
function flatten(obj) {
    var result = Object.create(obj);
    for(var key in result) {
        result[key] = result[key];
    }
    return result;
}

DeviceInfo.prototype = new CommonDeviceInfo();
var currentDevice = new DeviceInfo();
	
sessionStorage.setItem('currentDevice', JSON.stringify(flatten(currentDevice)));

