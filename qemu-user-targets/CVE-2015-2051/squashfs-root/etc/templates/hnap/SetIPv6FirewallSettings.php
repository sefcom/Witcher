HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/config.php";

$nodebase = "/runtime/hnap/SetIPv6FirewallSettings";
$node_rule = $nodebase."/IPv6FirewallRuleLists/IPv6FirewallRule";

$result = "OK";

//Clear the all the rules in the /acl6/firewall
$i = get("x", "/acl6/firewall/entry#");
while($i > 0)
{
	del("/acl6/firewall/entry");
	$i--;
}

if(get("", $nodebase."/IPv6_FirewallStatus")=="Enable_BlackList")		set("/acl6/firewall/policy", "ACCEPT");
else if(get("", $nodebase."/IPv6_FirewallStatus")=="Enable_WhiteList")	set("/acl6/firewall/policy", "DROP");
else																	set("/acl6/firewall/policy", "DISABLE");

foreach($node_rule)
{
	if($InDeX > get("", "/acl6/firewall/max")) {break;}
	
	set("/acl6/firewall/seqno", $InDeX+1);
	set("/acl6/firewall/count", $InDeX);
	
	set("/acl6/firewall/entry:".$InDeX."/description", get("", "Name"));
	
	if(get("x", "Status") == "Enable")	{set("/acl6/firewall/entry:".$InDeX."/enable", "1");}
	else								{set("/acl6/firewall/entry:".$InDeX."/enable", "0");}
	
	set("/acl6/firewall/entry:".$InDeX."/schedule", XNODE_getscheduleuid(get("", "Schedule")));
	
	if(get("", "SrcInterface")=="WAN")	{set("/acl6/firewall/entry:".$InDeX."/src/inf", "WAN-4");}
	else 								{set("/acl6/firewall/entry:".$InDeX."/src/inf", "LAN-4");}
	$SrcIPv6AddressRangeStart = get("", "SrcIPv6AddressRangeStart");
	$SrcIPv6AddressRangeEnd = get("", "SrcIPv6AddressRangeEnd");
	if(ipv6addrcmp($SrcIPv6AddressRangeStart, $SrcIPv6AddressRangeEnd)=="1")
	{
		$SrcIPv6AddressRangeStart = get("", "SrcIPv6AddressRangeEnd");
		$SrcIPv6AddressRangeEnd = get("", "SrcIPv6AddressRangeStart");
	}
	set("/acl6/firewall/entry:".$InDeX."/src/host/start",	$SrcIPv6AddressRangeStart);
	set("/acl6/firewall/entry:".$InDeX."/src/host/end",		$SrcIPv6AddressRangeEnd);

	if(get("", "DestInterface")=="WAN")	{set("/acl6/firewall/entry:".$InDeX."/dst/inf", "WAN-4");}
	else 								{set("/acl6/firewall/entry:".$InDeX."/dst/inf", "LAN-4");}
	$DestIPv6AddressRangeStart = get("", "DestIPv6AddressRangeStart");
	$DestIPv6AddressRangeEnd = get("", "DestIPv6AddressRangeEnd");
	if(ipv6addrcmp($DestIPv6AddressRangeStart, $DestIPv6AddressRangeEnd)=="1")
	{
		$DestIPv6AddressRangeStart = get("", "DestIPv6AddressRangeEnd");
		$DestIPv6AddressRangeEnd = get("", "DestIPv6AddressRangeStart");
	}
	$PortRangeStart = get("", "PortRangeStart");
	$PortRangeEnd = get("", "PortRangeEnd");
	if($PortRangeStart > $PortRangeEnd)
	{
		$PortRangeStart = get("", "PortRangeEnd");
		$PortRangeEnd = get("", "PortRangeStart");
	}
	set("/acl6/firewall/entry:".$InDeX."/dst/host/start",	$DestIPv6AddressRangeStart);
	set("/acl6/firewall/entry:".$InDeX."/dst/host/end",		$DestIPv6AddressRangeEnd);
	set("/acl6/firewall/entry:".$InDeX."/dst/port/start",	$PortRangeStart);
	set("/acl6/firewall/entry:".$InDeX."/dst/port/end",		$PortRangeEnd);
	
	if(get("x", "Protocol")=="TCP")		{set("/acl6/firewall/entry:".$InDeX."/protocol", "TCP");}
	else if(get("x", "Protocol")=="UDP"){set("/acl6/firewall/entry:".$InDeX."/protocol", "UDP");}
	else 								{set("/acl6/firewall/entry:".$InDeX."/protocol", "ALL");}

	//It is neccesary to run FIREWALL6 service and build /var/servd/IP6TFIREWALL_start.sh
	set("/acl6/firewall/entry:".$InDeX."/policy", "ACCEPT");
	
	// Check the UID of this entry, it should not be empty, and must be unique.  Refer FIREWALL6 fatlady. 
	set("/acl6/firewall/entry:".$InDeX."/uid",	"FWL-".get("", "/acl6/firewall/seqno"));
}

if($result == "OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service FIREWALL6 restart > /dev/console\n");
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
    <SetIPv6FirewallSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetIPv6FirewallSettingsResult><?=$result?></SetIPv6FirewallSettingsResult>
    </SetIPv6FirewallSettingsResponse>
  </soap:Body>
</soap:Envelope>