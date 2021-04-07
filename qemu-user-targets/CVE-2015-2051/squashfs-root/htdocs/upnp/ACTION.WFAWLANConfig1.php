<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/upnp.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/soap.php";
fwrite("w", $SHELL_FILE, "#!/bin/sh\n");

$rtpath_base = UPNP_getdevpathbytype($INF_UID, $G_WFA);
if ($rtpath_base == "")
{
	SHELL_debug($SHELL_FILE, "Can not to get the runtime node path of WFA!");
	exit;
}

$act_name	= query($RTNODE_BASE."/action_name");
$svc_type	= query($rtpath_base."/devdesc/device:1/serviceList/service:1/serviceType");

SOAP_act_resp_500("501", "Action Failed");
?>
