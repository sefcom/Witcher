HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$lan1_inet = query($path_inf_lan1."/inet");
$path_lan1_inet = XNODE_getpathbytarget("inet", "entry", "uid", $lan1_inet, 0);

$nodebase="/runtime/hnap/SetRouterLanSettings/";
$DHCPServerEnabled=query($nodebase."DHCPServerEnabled");
$result = "OK";

$path_run_lan1 =  XNODE_getpathbytarget("/runtime", "inf", "inet/uid", $lan1_inet, 0);
$run_ipaddr = query($path_run_lan1."/inet/ipv4/ipaddr");
$run_mask = query($path_run_lan1."/inet/ipv4/mask");
$RSipaddr = query($nodebase."RouterIPAddress");
$RSmask = query($nodebase."RouterSubnetMask");
$rsmask = ipv4mask2int($RSmask);

if($run_ipaddr!=$RSipaddr || $run_mask!=$rsmask)
{
	set($path_lan1_inet."/ipv4/ipaddr", query($nodebase."RouterIPAddress"));
	set($path_lan1_inet."/ipv4/mask", ipv4mask2int($RSmask));
	$result = "REBOOT";
}
if($DHCPServerEnabled=="true")
{
	set($path_inf_lan1."/dhcps4", "DHCPS4-1");
}
else if($DHCPServerEnabled=="false")
{
	set($path_inf_lan1."/dhcps4", "");
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Lan Change\" > /dev/console\n");
fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
if($result == "OK")
{
	fwrite("a",$ShellPath, "service DEVICE.HOSTNAME restart > /dev/console\n");
	fwrite("a",$ShellPath, "service INET.LAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service DHCPS4.LAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service RUNTIME.INF.LAN-1 restart > /dev/console\n");
}
fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
set("/runtime/hnap/dev_status", "ERROR");
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetRouterLanSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetRouterLanSettingsResult><?=$result?></SetRouterLanSettingsResult>
    </SetRouterLanSettingsResponse>
  </soap:Body>
</soap:Envelope>
