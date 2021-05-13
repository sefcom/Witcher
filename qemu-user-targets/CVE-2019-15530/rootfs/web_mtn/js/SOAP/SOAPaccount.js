/**
 * Created by T-mark on 2017/8/30.
 */
/**
 * @constructor
 */
function SOAPCheckPasswdSettingsRequest(){
    this.CurrentPassword = "";
}


/**
 * @constructor
 */
function SOAPSetPasswdSettingsRequest(){
    this.NewPassword = "";
    this.ChangePassword = "true";
}