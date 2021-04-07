<?
if (query("/runtime/device/layout")!="router")
{
	$_GLOBALS["errorCode"]=501;
}
else
{
	anchor($_GLOBALS["ACTION_NODEBASE"]."/GetGenericPortMappingEntry");
	if (query("NewPortMappingIndex") >= query("/runtime/upnpigd/portmapping/entry#"))
	{
		$_GLOBALS["errorCode"] = 713;
	}
	else
	{
		$_GLOBALS["errorCode"] = 200;
		$SOAP_BODY="ACTION.GetGenericPortMappingEntry.php";
	}
}
?>
