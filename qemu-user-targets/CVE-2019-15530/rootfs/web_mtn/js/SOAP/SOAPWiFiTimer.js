/**
 * @constructor
 */
function SOAPGetWifiDownSettingsResponse(){
    this.ControlMode = "";
    var controlListItem = new ControlRuleList();
    this.ControlRule = $.makeArray(controlListItem);
};


function ControlRuleList(){
    this.Enable = "";
    this.StartTime = "";
    this.EndTime = "";
    this.Week = "";
}

function SOAPSetWifiDownSettings(){
    this.ControlMode = "";
    var controlListItem = new ControlRuleList();
    this.ControlRule = $.makeArray(controlListItem);
}

function SOAPGetNTPServerSettingsResponse(){
    this.Status = '';
}
