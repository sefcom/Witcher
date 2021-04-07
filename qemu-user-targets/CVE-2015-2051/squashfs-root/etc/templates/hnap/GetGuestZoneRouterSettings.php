<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result="OK";
$path_inf_lan2 = XNODE_getpathbytarget("", "inf", "uid", $LAN2, 0);
$lan2_inet = get("", $path_inf_lan2."/inet");
$path_inet_lan2 = XNODE_getpathbytarget("inet", "entry", "uid", $lan2_inet, 0);
TRACE_debug("path_inf_lan2=".$path_inf_lan2);
TRACE_debug("path_inet_lan2=".$path_inet_lan2);

$ipaddr = get("", $path_inet_lan2."/ipv4/ipaddr");
$mask = ipv4int2mask(get("", $path_inet_lan2."/ipv4/mask"));
$path_dhcps4_lan2 = XNODE_getpathbytarget("dhcps4", "entry", "uid", "DHCPS4-2", 0);
$start = get("", $path_dhcps4_lan2."/start");
$end = get("", $path_dhcps4_lan2."/end");
$leasetime = get("", $path_dhcps4_lan2."/leasetime")/60;
if(get("", $path_inf_lan2."/dhcps4") != "")		$en_dhcp = "true";
else																					$en_dhcp = "false";


//$InetAcs = "TRUE"; //to do, sammy

$en_btn_zone1 = get("", "/acl/obfilter/policy");
$en_btn_zone2 = get("", "/acl/obfilter2/policy");

if ($en_btn_zone1=="DISABLE" && $en_btn_zone2=="DISABLE") $InetAcs = "false";
else																											$InetAcs = "true";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetGuestZoneRouterSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetGuestZoneRouterSettingsResult><?=$result?></GetGuestZoneRouterSettingsResult>
		<InternetAccessOnly><?=$InetAcs?></InternetAccessOnly>
		<IPAddress><?=$ipaddr?></IPAddress>
		<SubnetMask><?=$mask?></SubnetMask>
		<DHCPServer><?=$en_dhcp?></DHCPServer>
		<DHCPRangeStart><?=$start?></DHCPRangeStart>
		<DHCPRangeEnd><?=$end?></DHCPRangeEnd>
		<DHCPLeaseTime><?=$leasetime?></DHCPLeaseTime>
	</GetGuestZoneRouterSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
