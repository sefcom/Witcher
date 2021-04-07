<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
anchor($_GLOBALS["ACTION_NODEBASE"]."/GetSpecificPortMappingEntry");
$NewRemoteHost		= query("NewRemoteHost");
$NewExternalPort	= query("NewExternalPort");
$NewProtocol		= query("NewProtocol");

if (query("/runtime/device/layout")!="router")
{
	$_GLOBALS["errorCode"] = 501;
}
else
{
	$target = 0;
	foreach ("/runtime/upnpigd/portmapping/entry")
	{
		if ($NewProtocol == "TCP")	$proto = "TCP";
		else						$proto = "UDP";
		if ($NewRemoteHost		== query("remotehost") &&
			$NewExternalPort	== query("externalport") &&
			$proto				== query("protocol"))
		{
			$target = $InDeX;
			break;
		}
	}
	if ($target != 0)
	{
		$_GLOBALS["errorCode"]=200;
		/* The node ($_GLOBALS["ACTION_NODEBASE"]."/target") is a temporal node for 
		 * 'ACTION.GetSpecificPortMappingEntry.php' using, so I put it under $_GLOBALS["ACTION_NODEBASE"].
		 * It will be deleted when this SOAP action is done.
		 */
		set($_GLOBALS["ACTION_NODEBASE"]."/target", $target);
		$SOAP_BODY="ACTION.GetSpecificPortMappingEntry.php";
	}
	else
	{
		$_GLOBALS["errorCode"] = 714;
	}
}
?>
