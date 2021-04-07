<?
include "/htdocs/phplib/upnp/xnode.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/gena.php";

$gena_path = XNODE_getpathbytarget($G_GENA_NODEBASE, "inf", "uid", $INF_UID, 1);
$gena_path = $gena_path."/".$SERVICE;
GENA_subscribe_cleanup($gena_path);

/* IGD services */
if		($SERVICE == "L3Forwarding1")	$php = "NOTIFY.Layer3Forwarding.1.php";
else if ($SERVICE == "OSInfo1")			$php = "NOTIFY.OSInfo.1.php";
else if ($SERVICE == "WANCommonIFC1")	$php = "NOTIFY.WANCommonInterfaceConfig.1.php";
else if ($SERVICE == "WANEthLinkC1")	$php = "NOTIFY.WANEthernetLinkConfig.1.php";
else if ($SERVICE == "WANIPConn1")		$php = "NOTIFY.WANIPConnection.1.php";
/* WFA services */
else if ($SERVICE == "WFAWLANConfig1")	$php = "NOTIFY.WFAWLANConfig.1.php";


if ($METHOD == "SUBSCRIBE")
{
	if ($SID == "")
		GENA_subscribe_new($gena_path, $HOST, $REMOTE, $URI, $TIMEOUT, $SHELL_FILE, "/htdocs/upnp/".$php, $INF_UID);
	else
		GENA_subscribe_sid($gena_path, $SID,  $TIMEOUT);
}
else if ($METHOD == "UNSUBSCRIBE")
{
	GENA_unsubscribe($gena_path, $SID);
}
?>
