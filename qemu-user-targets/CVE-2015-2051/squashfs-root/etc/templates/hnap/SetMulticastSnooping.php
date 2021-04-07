HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$enable = get("","/runtime/hnap/SetMulticastSnooping/Enabled");

if($enable == "true")
{
	set("/device/multicast/igmpproxy", 1);
	set("/device/multicast/wifienhance", 1);
}
else if($enable == "false")
{
	set("/device/multicast/igmpproxy", 0);
	set("/device/multicast/wifienhance", 0);
}
else
{
	$result = "ERROR";
}

if($result == "OK")
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service ICMP.WAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service ICMP.WAN-2 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service PHYINF.WAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service MULTICAST restart > /dev/console\n");
	fwrite("a",$ShellPath, "service UPNP.LAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service WIFI.PHYINF restart > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE restart > /dev/console\n");
	
	set("/runtime/hnap/dev_status", "ERROR");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetMulticastSnoopingResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetMulticastSnoopingResult><?=$result?></SetMulticastSnoopingResult>
</SetMulticastSnoopingResponse>
</soap:Body>
</soap:Envelope>

