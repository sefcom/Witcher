
/**
 * @constructor
 */
function SOAPGetFirmwareStatusResponse()
{
	this.CurrentFWVersion = 0;
	this.LatestFWVersion = 0;
	this.LatestFWVersionDate = "";
	this.FWDownloadUrl = "";
	this.FWUploadUrl = "";
	this.ReleaseNote = "";
};

// @prototype
/*SOAPGetFirmwareStatusResponse.prototype =
{
	get CurrentFWVersion(){
		return this._CurrentFWVersion;
	},

	set CurrentFWVersion(val){
		this._CurrentFWVersion = parseFloat(val);
	},

	get LatestFWVersion(){
		return this._LatestFWVersion;
	},

	set LatestFWVersion(val){
		this._LatestFWVersion = parseFloat(val);
	}
}*/

/**
 * @constructor
 */
function SOAPPollingFirmwareDownloadResponse()
{
	this.DownloadPercentage = 0;
};

// @prototype
SOAPPollingFirmwareDownloadResponse.prototype = 
{
	get DownloadPercentage(){
		return this._DownloadPercentage;
	},

	set DownloadPercentage(val){
		this._DownloadPercentage = parseInt(val);
	}
}


/**
 * @constructor
 */
function SOAPGetFirmwareValidationResponse()
{
	this.IsValid = false;
	this.CountDown = "";
};

/**
 * @constructor
 */
function SOAPGetConfigFileValidationResponse()
{
	this.IsValid = false;
	this.CountDown = "";
};

/**
 * @constructor
 */
function SOAPGetAutoUpgradeFirmwareResponse(){
    this.AutoUpgrade = true;
    this.StartTime = '';
    this.StopTime = '';
};

/**
 * @constructor
 */
function SOAPGetNTPServerSettingsResponse(){
    this.Status = '';
}


/**
 * @constructor
 */
function SOAPSetAutoUpgradeFirmware(){
    this.AutoUpgrade = false;
    this.StartTime = '';
    this.StopTime = '';
};