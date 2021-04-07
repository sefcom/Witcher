HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$nodebase = "/runtime/hnap/SetStaticRouteIPv4Settings/StaticRouteIPv4Data";
$node_info = $nodebase."/SRIPv4Info";
$path = "/route/static";
$entry = $path."/entry";
$result = "OK";

$max_rules = query($path."/max");
if($max_rules == "") { set($path."/max", "32"); }

set("/runtime/hnap/dummy", "");
movc($path, "/runtime/hnap/dummy"); //Remove the children nodes of /route/static
del("/runtime/hnap/dummy");

set($path."/seqno", "1");
set($path."/max", "32"); 
set($path."/count", "0");

foreach($node_info)
{
	$uid = "SRT-".$InDeX;
	set($entry.":".$InDeX."/uid", $uid);
	set($path."/seqno", $InDeX+1);
	set($path."/count", $InDeX);
	
	if (get("", "Enabled") == "true") { set($entry.":".$InDeX."/enable", "1"); }
	else { set($entry.":".$InDeX."/enable", "0"); }
	
	set($entry.":".$InDeX."/name",get("", "Name"));
	
	$netmask = ipv4mask2int(get("", "NetMask"));
	set($entry.":".$InDeX."/mask", $netmask);
	
	$networkid = ipv4networkid(get("", "IPAddress"), $netmask);
	set($entry.":".$InDeX."/network", $networkid);
	
	set($entry.":".$InDeX."/via", get("", "Gateway"));
	
	set($entry.":".$InDeX."/metric", get("", "Metric"));
	
	/*The HNAP spec. for Get&SetStaticRouteIPv4Settings is not clearly to define the tag of Interface.
	  For our device DB settings, the interface should be WAN-1, WAN-2,... LAN-1,... and so on. However D-Link 2013 new GUI only set the tag with "WAN".
	  Now if the D-Link send "WAN" or "LAN" to our device, we use $WAN1 or $LAN1 in the htdocs/webinc/config.php. */
	if(get("", "Interface") == "WAN")		{$interface = $WAN1;}
	else if(get("", "Interface") == "LAN")	{$interface = $LAN1;}
	set($entry.":".$InDeX."/inf", $interface);
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Static Route IPv4\" > /dev/console\n");

if($result == "OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service ROUTE.STATIC restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	<SetStaticRouteIPv4SettingsResponse xmlns="http://purenetworks.com/HNAP1/">
		<SetStaticRouteIPv4SettingsResult><?=$result?></SetStaticRouteIPv4SettingsResult>
	</SetStaticRouteIPv4SettingsResponse>
	</soap:Body>
</soap:Envelope>