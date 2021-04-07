<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$stsp = "/runtime/wifi_tmpnode/sitesurvey/entry:".$INDEX;
if($ACTION=="ADD_ENTRY" || $ACTION=="ADD_ENTRY_HIDDENSSID")
{
	set($stsp."/channel",	$CHANNEL);
	set($stsp."/ssid",		$SSID);
	set($stsp."/macaddr",	$BSSID);
	set($stsp."/rssi",		$SIGNAL);
	set($stsp."/wlmode",	$WLMODE);
	set($stsp."/authtype",	$AUTHTYPE);
	set($stsp."/encrtype",	$ENCRTYPE);
} 
else if ($ACTION=="CLEAN")
{
	del("/runtime/wifi_tmpnode/sitesurvey");
}
else 
{
	TRACE_error("sitesurvey.php :  UNKNOWN COMMAND !!");
}

?>
