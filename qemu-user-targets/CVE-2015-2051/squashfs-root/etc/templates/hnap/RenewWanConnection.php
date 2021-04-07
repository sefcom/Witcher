HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$nodebase="/runtime/hnap/RenewWanConnection/";
$rlt="OK";

$Action=query($nodebase."Action");
if($Action!="ReStart" && $Action!="DHCPRelease" && $Action!="DHCPRenew" 
	&& $Action!="PPPoEConnect" && $Action!="PPPoEDisconnect" && $Action!="PPPConnect" && $Action!="PPPDisconnect"
	&& $Action!="PPTPConnect" && $Action!="PPTPDisconnect" && $Action!="L2TPConnect" && $Action!="L2TPDisconnect")
{
	$rlt="ERROR";	
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
if($rlt=="OK")
{

	if($Action=="ReStart")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Restart Wan Settings\" > /dev/console\n");
		fwrite("a",$ShellPath, "service WAN restart > /dev/console\n");	
	}	
	else if($Action=="DHCPRelease")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Release Wan DHCP Settings\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.DHCP.RELEASE > /dev/console\n");
	}
	else if($Action=="DHCPRenew")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Renew Wan DHCP Settings\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.DHCP.RENEW > /dev/console\n");
	}
	else if($Action=="PPPoEConnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Connect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.DIALUP > /dev/console\n");
	}
	else if($Action=="PPPoEDisconnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Disconnect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.HANGUP > /dev/console\n");
	}
	else if($Action=="PPPConnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Connect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.COMBO.DIALUP > /dev/console\n");
	}
	else if($Action=="PPPDisconnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Disconnect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.COMBO.HANGUP > /dev/console\n");
	}
	else if($Action=="PPTPConnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Connect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.DIALUP > /dev/console\n");
	}
	else if($Action=="PPTPDisconnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Disconnect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.HANGUP > /dev/console\n");
	}	
	else if($Action=="L2TPConnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Connect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.DIALUP > /dev/console\n");
	}
	else if($Action=="L2TPDisconnect")
	{
		fwrite("a",$ShellPath, "echo \"[$0]-->Disconnect to internet\" > /dev/console\n");
		fwrite("a",$ShellPath, "event WAN-1.PPP.HANGUP > /dev/console\n");
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
    <RenewWanConnectionResponse xmlns="http://purenetworks.com/HNAP1/">
      <RenewWanConnectionResult><?=$rlt?></RenewWanConnectionResult>
    </RenewWanConnectionResponse>
  </soap:Body>
</soap:Envelope>
