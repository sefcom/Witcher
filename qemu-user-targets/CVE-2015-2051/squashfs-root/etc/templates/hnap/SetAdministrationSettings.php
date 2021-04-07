HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$nodebase= "/runtime/hnap/SetAdministrationSettings";
$result = "OK";

$HTTPS = query($nodebase."/HTTPS"); 
$RemoteMgt = query($nodebase."/RemoteMgt"); // enable http management or not
$RemoteMgtPort = query($nodebase."/RemoteMgtPort"); //remote port
$RemoteMgtHTTPS = query($nodebase."/RemoteMgtHTTPS"); // enable https management or not
$InboundFilter = query($nodebase."/InboundFilter"); 

//Error Check
if($RemoteMgtPort!="" && isdigit($RemoteMgtPort)==0)
{$result = "ERROR";}

if($HTTPS=="true") $HTTPS = 1; 
else $HTTPS = 0;

if($RemoteMgt=="true") $RemoteMgt = 1; 
else $RemoteMgt = 0;

if($RemoteMgtHTTPS=="true") $RemoteMgtHTTPS = 1; 
else $RemoteMgtHTTPS = 0;

if($result=="OK")
{
	if($RemoteMgtHTTPS == "1")
	{
	  set($inf_lan1."/stunnel",$HTTPS);
	  set($inf_wan1."/https_rport",$RemoteMgtPort);
	  set($inf_wan1."/inbfilter",$InboundFilter);
	  del($inf_wan1."/web",$RemoteMgtPort);
	}
	else
	{
	  set($inf_lan1."/stunnel",$HTTPS);
	  set($inf_wan1."/web",$RemoteMgtPort);
	  set($inf_wan1."/inbfilter",$InboundFilter);
	  del($inf_wan1."/https_rport",$RemoteMgtPort);
	}
}

if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service HTTP.WAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetAdministrationSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetAdministrationSettingsResult><?=$result?></SetAdministrationSettingsResult>
</SetAdministrationSettingsResponse>
</soap:Body>
</soap:Envelope>