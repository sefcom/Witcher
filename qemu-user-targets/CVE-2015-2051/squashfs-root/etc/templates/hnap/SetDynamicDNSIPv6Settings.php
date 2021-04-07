HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$nodebase="/runtime/hnap/SetDynamicDNSIPv6Settings/DynamicDNSIPv6List/DDNSIPv6Info";
$rlt="OK";

//Remove the Original entry first.
$entry_n = get("", "/ddns6/entry#");
if(isdigit($entry_n)==0) $entry_n=0;
$i=1;
while($i <= $entry_n)
{
	del("/ddns6/entry:1");
	$i++;
}	

foreach($nodebase)
{
	if($InDeX <= get("x", "/ddns6/max"))
	{
		if(get("x", "Status") == "Enabled")	$Status="1";
		else 								$Status="0";
		set("/ddns6/entry:".$InDeX."/enable", $Status);
		set("/ddns6/entry:".$InDeX."/v6addr", get("x", "IPv6Address"));
		set("/ddns6/entry:".$InDeX."/hostname", get("x", "Hostname"));
		set("/ddns6/cnt", $InDeX);
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Dynamic IPv6 DNS Change\" > /dev/console\n");
if($rlt=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service DDNS6.WAN-1 restart > /dev/console\n");
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
<SetDynamicDNSIPv6SettingsResponse xmlns="http://purenetworks.com/HNAP1/">
<SetDynamicDNSIPv6SettingsResult><?=$rlt?></SetDynamicDNSIPv6SettingsResult>
</SetDynamicDNSIPv6SettingsResponse>
</soap:Body>
</soap:Envelope>
