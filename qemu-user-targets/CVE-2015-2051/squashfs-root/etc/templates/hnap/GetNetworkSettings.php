<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result="OK";
$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$lan1_inet = get("", $path_inf_lan1."/inet");
$path_inet_lan1 = XNODE_getpathbytarget("inet", "entry", "uid", $lan1_inet, 0);
$dhcps4_lan1 = query($path_inf_lan1."/dhcps4");
if($dhcps4_lan1=="") {$dhcps4_lan1="DHCPS4-1";} //If $dhcps4_lan1 is empty, it means the dhcp server in LAN1 is disabled.
$path_dhcps4_lan1 = XNODE_getpathbytarget("dhcps4", "entry", "uid", $dhcps4_lan1, 0);
TRACE_debug("path_inf_lan1=".$path_inf_lan1);
TRACE_debug("path_inet_lan1=".$path_inet_lan1);
TRACE_debug("path_dhcps4_lan1=".$path_dhcps4_lan1);

$hostname = get("", "/device/hostname");
if(get("", $path_inf_lan1."/dns4") != "")	$dnsr = "true";
else										$dnsr = "false";

$ipaddress = get("", $path_inet_lan1."/ipv4/ipaddr");
$subnetmask = ipv4int2mask(get("", $path_inet_lan1."/ipv4/mask"));
$local_domain_name = query($path_dhcps4_lan1."/domain");
$start = get("", $path_dhcps4_lan1."/start");
$end = get("", $path_dhcps4_lan1."/end");
$leasetime = get("", $path_dhcps4_lan1."/leasetime")/60;
if(get("", $path_dhcps4_lan1."/broadcast") == "yes")		$broadcast = "true";
else																										$broadcast = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetNetworkSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetNetworkSettingsResult><?=$result?></GetNetworkSettingsResult>
		<IPAddress><?=$ipaddress?></IPAddress>
		<SubnetMask><?=$subnetmask?></SubnetMask>
		<DeviceName><?=$hostname?></DeviceName>
		<LocalDomainName><?=$local_domain_name?></LocalDomainName>
		<IPRangeStart><?=$start?></IPRangeStart>
		<IPRangeEnd><?=$end?></IPRangeEnd>
		<LeaseTime><?=$leasetime?></LeaseTime>
		<Broadcast><?=$broadcast?></Broadcast>
		<DNSRelay><?=$dnsr?></DNSRelay>
	</GetNetworkSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
