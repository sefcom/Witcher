/**
 * Created by T-mark on 2017/8/30.
 */

/**
 * @constructor
 */
function SOAPGetNTPServerSettingsResonse(){
    this.Time = "";
    this.Status = "";
    this.TimeZone = "";
}

/**
 * @constructor
 */
function SOAPSetNTPServerSettingsRequest(){
    this.TimeZone = "";
}