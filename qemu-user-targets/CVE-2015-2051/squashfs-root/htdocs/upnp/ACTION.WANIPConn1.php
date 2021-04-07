<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/upnp.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/soap.php";

fwrite("w", $SHELL_FILE, "#!/bin/sh\n");

$dev_nodebase = UPNP_getdevpathbytype($INF_UID, $G_IGD);
if ($dev_nodebase == "")
{
	SHELL_debug($SHELL_FILE, "Can not to get the runtime node path of IGD!");
	exit;
}

$act_name	= query($ACTION_NODEBASE."/action_name");
$svc_type	= query($dev_nodebase."/devdesc/device/deviceList/device:1/deviceList/device:1/serviceList/service:2/serviceType");

$WID = 1; /* WAN port ID */
$errorCode = 401;
$SOAP_BODY="";
if (	$act_name == "AddPortMapping"
	||	$act_name == "DeletePortMapping"
	||	$act_name == "GetConnectionTypeInfo"
	||	$act_name == "GetExternalIPAddress"
	||	$act_name == "GetGenericPortMappingEntry"
	||	$act_name == "GetNATRSIPStatus"
	||	$act_name == "GetSpecificPortMappingEntry"
	||	$act_name == "GetStatusInfo"
	||	$act_name == "RequestConnection"
	||	$act_name == "ForceTermination"
	||	$act_name == "SetConnectionType")
{
	dophp("load", "/htdocs/upnpinc/ACTION.DO.".$act_name.".php");
}

/* 200 OK */
if ($errorCode == 200) {SOAP_act_resp_200(query($rtpath_base."/server"), $svc_type, $act_name, $SOAP_BODY); exit;}

/* ERROR */
if		($errorCode == 401) SOAP_act_resp_500("401", "Invalid Action");
else if ($errorCode == 402) SOAP_act_resp_500("402", "Invalid Args");
else if ($errorCode == 501) SOAP_act_resp_500("501", "Action Failed");
else if ($errorCode == 704) SOAP_act_resp_500("704", "ConnectionSetupFailed");
else if ($errorCode == 705) SOAP_act_resp_500("705", "ConnectionSetupInProgress");
else if ($errorCode == 706) SOAP_act_resp_500("706", "ConnectionNotConfigured");
else if ($errorCode == 707) SOAP_act_resp_500("707", "DisconnectInProgress");
else if ($errorCode == 708) SOAP_act_resp_500("708", "InvalidLayer2Address");
else if ($errorCode == 709) SOAP_act_resp_500("709", "InternetAccessDisabled");
else if ($errorCode == 710) SOAP_act_resp_500("710", "InvalidConnectionType");
else if ($errorCode == 711) SOAP_act_resp_500("711", "ConnectionAlreadyTerminated");
else if ($errorCode == 713) SOAP_act_resp_500("713", "SpecifiedArrayIndexInvalid");
else if ($errorCode == 714) SOAP_act_resp_500("714", "NoSuchEntryInArray");
else if ($errorCode == 715) SOAP_act_resp_500("715", "WildCardNotPermittedInSrcIP");
else if ($errorCode == 716) SOAP_act_resp_500("716", "WildCardNotPermittedInExtPort");
else if ($errorCode == 718) SOAP_act_resp_500("718", "ConflictInMappingEntry");
else if ($errorCode == 724) SOAP_act_resp_500("724", "SamePortValuesRequired");
else if ($errorCode == 725) SOAP_act_resp_500("725", "OnlyPermanentLeasesSupported");
else if ($errorCode == 726) SOAP_act_resp_500("726", "RemoteHostOnlySupportsWildcard");
else if ($errorCode == 727) SOAP_act_resp_500("727", "ExternalPortOnlySupportsWildcard");
?>
