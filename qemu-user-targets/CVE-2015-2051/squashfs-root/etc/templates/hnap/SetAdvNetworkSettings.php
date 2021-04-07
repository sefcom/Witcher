HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/webinc/config.php";
$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$path_inf_lan3 = XNODE_getpathbytarget("", "inf", "uid", $LAN3, 0);
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_phyinf = get("x", $path_inf_wan1."/phyinf");
$path_phyinf_wan1 = XNODE_getpathbytarget("", "phyinf", "uid", $wan1_phyinf, 0);
$rgmode = get("x", "/runtime/device/layout");

$nodebase="/runtime/hnap/SetAdvNetworkSettings/";
$rlt="OK";
$UPNP=get("x", $nodebase."UPNP");
$MulticastIPv4=get("x", $nodebase."MulticastIPv4");
$MulticastIPv6=get("x", $nodebase."MulticastIPv6");
$WANPortSpeed=get("x", $nodebase."WANPortSpeed");

if($UPNP=="true")
{
	set($path_inf_lan1."/upnp/count",	"1");
	set($path_inf_lan1."/upnp/entry:1", "urn:schemas-upnp-org:device:InternetGatewayDevice:1");
	set($path_inf_lan3."/upnp/count",	"1");
	set($path_inf_lan3."/upnp/entry:1", "urn:schemas-upnp-org:device:InternetGatewayDevice:2");
}
else
{
	set($path_inf_lan1."/upnp/count",	"0");
	set($path_inf_lan1."/upnp/entry:1", "");
	set($path_inf_lan3."/upnp/count",	"0");
	set($path_inf_lan3."/upnp/entry:1", "");
}

if($MulticastIPv4=="true")
{
	set("/device/multicast/igmpproxy",	"1");
	set("/device/multicast/wifienhance", "1");
}
else
{
	set("/device/multicast/igmpproxy",	"0");
	set("/device/multicast/wifienhance", "0");	
}
if($MulticastIPv6=="true")
{
	set("/device/multicast/mldproxy",	"1");
	set("/device/multicast/wifienhance6", "1");
}
else
{
	set("/device/multicast/mldproxy",	"0");
	set("/device/multicast/wifienhance6", "0");	
}

if($WANPortSpeed == "Auto")			set($path_phyinf_wan1."/media/linktype", "AUTO");
else if($WANPortSpeed == "10Mbps")	set($path_phyinf_wan1."/media/linktype", "10F");
else if($WANPortSpeed == "100Mbps")	set($path_phyinf_wan1."/media/linktype", "100F");
else if($WANPortSpeed == "1000Mbps")	set($path_phyinf_wan1."/media/linktype", "1000F");

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Adv Network Change\" > /dev/console\n");
if($rlt=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	if($rgmode=="router")
	{
		fwrite("a",$ShellPath, "service ICMP.WAN-1 restart > /dev/console\n");
		fwrite("a",$ShellPath, "service UPNP.LAN-1 restart > /dev/console\n");
		fwrite("a",$ShellPath, "service UPNP.LAN-3 restart > /dev/console\n");
	}
	fwrite("a",$ShellPath, "service ICMP.WAN-2 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service MULTICAST restart > /dev/console\n");
	fwrite("a",$ShellPath, "service PHYINF.".$wan1_phyinf." restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");	
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetAdvNetworkSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
<SetAdvNetworkSettingsResult><?=$rlt?></SetAdvNetworkSettingsResult>
</SetAdvNetworkSettingsResponse>
</soap:Body>
</soap:Envelope>
