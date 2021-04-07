HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";

$nodebase = "/runtime/hnap/SetStaticRouteIPv6Settings/StaticRouteIPv6List/SRIPv6Info";

$result = "REBOOT";

//+++ Jerry Kao, Default node path.
$Route_path = "/route6/static";

// Remove the original node.
del($Route_path);

// Max Rule = 10.
$max_rules = query($Route_path."/max");
if($max_rules == "") { $max_rules = 10; }

set($Route_path."/seqno", 1);
set($Route_path."/count", 0);
set($Route_path."/max", $max_rules);

$Route_entry = $Route_path."/entry";

foreach($nodebase)
{
	$uid = "SRT-".$InDeX;
	set($Route_entry.":".$InDeX."/uid", $uid);
	
	if (get("x", "Status") == "true") { set($Route_entry.":".$InDeX."/enable", "1"); }
	else                              { set($Route_entry.":".$InDeX."/enable", "0"); }
	
	set($Route_entry.":".$InDeX."/description", get("", "Name"));	
	set($Route_entry.":".$InDeX."/network",     get("", "DestNetwork"));		
	set($Route_entry.":".$InDeX."/prefix",      get("", "PrefixLen"));	
	set($Route_entry.":".$InDeX."/via",         get("", "Gateway"));	
	set($Route_entry.":".$InDeX."/metric",      get("", "Metric"));
	
	$Interface = get("", "Interface");
	if($Interface=="WAN" || $Interface=="LAN") 
	{
		$Interface = $Interface."-4";
	}
	else if( $Interface == "LAN(DHCP-PD)")
	{
		//+++ Jerry Kao, according Old UI (adv_routingv6_list.php).
		$Interface = "PD";	
	}
	
	set($Route_entry.":".$InDeX."/inf", $Interface);
	
	set($Route_path."/seqno", $InDeX+1);
	set($Route_path."/count", $InDeX);
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Static Route IPv6\" > /dev/console\n");

if($result=="REBOOT")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service ROUTE6.STATIC restart > /dev/console\n");
	/*
	The IPv6 static route should be take effect after route reboot. However D-Link wish route reboot is approved by user. 
	Web would tell user the route should reboot to activate IPv6 static route and user would decide reboot or not.
	*/
	//fwrite("a",$ShellPath, "reboot > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetStaticRouteIPv6SettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetStaticRouteIPv6SettingsResult><?=$result?></SetStaticRouteIPv6SettingsResult>
		</SetStaticRouteIPv6SettingsResponse>
	</soap:Body>
</soap:Envelope>