HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$path_run_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1);
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_inet = query($path_inf_wan1."/inet");
$path_wan1_inet = XNODE_getpathbytarget("inet", "entry", "uid", $wan1_inet, 0);

$IsAccessPoint	= query("/runtime/hnap/SetAccessPointMode/IsAccessPoint");
$Result		= "";
$NewIPAddress	= "";

if ($IsAccessPoint=="true")
{
	if (query("/device/layout")=="router")	{ $Result="REBOOT"; set("/device/layout", "bridge"); }
	else				{ $Result="OK"; /* we are already in bridge mode. */ }
}
else
{
	if (query("/device/layout")=="bridge")	{ $Result="REBOOT"; set("/device/layout", "router"); }
	else				{ $Result="OK"; /* we are already in router mode */ }
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] $1 ... > /dev/console\n");
fwrite("a",$ShellPath, "echo IsAccessPoint = ".$IsAccessPoint." > /dev/console\n");
fwrite("a",$ShellPath, "echo Result = ".$Result."\n");

if ($Result == "REBOOT")
{
	fwrite("a",$ShellPath, "service DEVICE.LAYOUT stop > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE.HOSTNAME stop > /dev/console\n");
	//fwrite("a",$ShellPath, "service PHYINF.WAN-1 stop > /dev/console\n");
	fwrite("a",$ShellPath, "service INET.BRIDGE-1 stop > /dev/console\n");
	//fwrite("a",$ShellPath, "service INET.INF stop > /dev/console\n");
	//fwrite("a",$ShellPath, "service WAN stop > /dev/console\n");
	//fwrite("a",$ShellPath, "/etc/scripts/system.sh stop\n");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE.LAYOUT start > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE.HOSTNAME start > /dev/console\n");
	//fwrite("a",$ShellPath, "service PHYINF.WAN-1 start > /dev/console\n");
	fwrite("a",$ShellPath, "service INET.BRIDGE-1 start > /dev/console\n");
	//fwrite("a",$ShellPath, "service INET.INF start > /dev/console\n");
	//fwrite("a",$ShellPath, "service WAN start > /dev/console\n");
	//fwrite("a",$ShellPath, "/etc/scripts/system.sh start\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	//set("/runtime/hnap/dev_status", "ERROR");
	

	if ($IsAccessPoint!="true")			{ $NewIPAddress=query($path_run_lan1."/inet/ipv4/ipaddr"); }
	else if (query($path_wan1_inet."/addrtype")=="ipv4" && query($path_wan1_inet."/ipv4/static")=="1")	{ $NewIPAddress=query($path_wan1_inet."/ipv4/ipaddr"); }
}
else if ($Result == "OK")
{
	fwrite("a",$ShellPath, "echo \"We are already in bridge/router mode, so we do nothing ...\"");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetAccessPointModeResponse xmlns="http://purenetworks.com/HNAP1/">
<SetAccessPointModeResult><?=$Result?></SetAccessPointModeResult>
<NewIPAddress><?=$NewIPAddress?></NewIPAddress>
</SetAccessPointModeResponse>
</soap:Body>
</soap:Envelope>
