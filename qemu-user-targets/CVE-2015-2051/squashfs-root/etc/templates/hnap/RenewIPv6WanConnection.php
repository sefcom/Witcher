HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$nodebase="/runtime/hnap/RenewIPv6WanConnection/";
$rlt="OK";

$Action=query($nodebase."Action");
if($Action!="DHCPv6Release" && $Action!="DHCPv6Renew" && $Action!="PPPoEDisconnect" && $Action!="PPPoEConnect")
{
	$rlt="ERROR";
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
if($rlt=="OK")
{
	if($Action=="DHCPv6Release")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Release IPv6 Wan DHCP Settings\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-4.DHCP6.RELEASE > /dev/console\n");
	}
	else if($Action=="DHCPv6Renew")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Renew IPv6 Wan DHCP Settings\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-4.DHCP6.RENEW > /dev/console\n");
	}
	else if($Action=="PPPoEConnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Connect to internet for IPv6\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-3.PPP.DIALUP > /dev/console\n");
	}
	else if($Action=="PPPoEDisconnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Disconnect to internet for IPv6\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-3.PPP.HANGUP > /dev/console\n");
	}
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
    <RenewIPv6WanConnectionResponse xmlns="http://purenetworks.com/HNAP1/">
      <RenewIPv6WanConnectionResult><?=$rlt?></RenewIPv6WanConnectionResult>
    </RenewIPv6WanConnectionResponse>
  </soap:Body>
</soap:Envelope>
