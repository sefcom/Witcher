<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_phyinf = get("x", $path_inf_wan1."/phyinf");
$path_phyinf_wan1 = XNODE_getpathbytarget("", "phyinf", "uid", $wan1_phyinf, 0);

if(get("x", $path_inf_lan1."/upnp/count") == "1")	$UPNP = true;
else	$UPNP = false;

if(get("x", "/device/multicast/igmpproxy")=="1")	$MulticastIPv4 = true;
else	$MulticastIPv4 = false;
if(get("x", "/device/multicast/mldproxy")=="1")	$MulticastIPv6 = true;
else	$MulticastIPv6 = false;

$speed = get("x", $path_phyinf_wan1."/media/linktype");
if($speed == "AUTO")		$WANPortSpeed = "Auto";
else if($speed == "10F")	$WANPortSpeed = "10Mbps";
else if($speed == "100F")	$WANPortSpeed = "100Mbps";
else if($speed == "1000F")	$WANPortSpeed = "1000Mbps";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetAdvNetworkSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetAdvNetworkSettingsResult>OK</GetAdvNetworkSettingsResult>
			<UPNP><?=$UPNP?></UPNP>
			<MulticastIPv4><?=$MulticastIPv4?></MulticastIPv4> 
			<MulticastIPv6><?=$MulticastIPv6?></MulticastIPv6> 
			<WANPortSpeed><?=$WANPortSpeed?></WANPortSpeed>
		</GetAdvNetworkSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
