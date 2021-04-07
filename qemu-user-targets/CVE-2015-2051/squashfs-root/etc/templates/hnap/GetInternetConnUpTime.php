<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 


$DEBUG_HNAP = "n";	//DEBUG mode default = n.


$path_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_inetuid = get("x", $path_wan1."/inet");
$wan1_phyuid  = get("x", $path_wan1."/phyinf");
$runtime_wan1_inet =  XNODE_getpathbytarget("/runtime", "inf", "inet/uid", $wan1_inetuid, 0);
$runtime_wan1_phy  =  XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyuid, 0);

$wancable_status = 0;
$wan_network_status = 0;
$linkstatus = get("x", $runtime_wan1_phy."/linkstatus");
$linkuptime = get("x", $runtime_wan1_phy."/linkuptime");
if($linkstatus != "") { $wancable_status = 1; }
if($wancable_status == 1) { $wan_network_status = 1; }

$system_uptime = get("x", "/runtime/device/uptime");
$wan_uptime = get("x", $runtime_wan1_inet."/inet/uptime");
$wan_delta_uptime = $system_uptime - $wan_uptime;


//The WAN uptime would not be modified when the WAN connection is disconnected. The WAN connection uptime would not be refreshed for this condition.
if($linkuptime!="")
{
	//If the $wan_uptime < $linkuptime, WAN port link up recently. Else the WAN type changed to static recently.
	if($wan_uptime < $linkuptime){$wan_delta_uptime = $system_uptime - $linkuptime;}
}

$result = "OK";

if($DEBUG_HNAP == "y")
{
	TRACE_debug("$path_wan1         = ".$path_wan1);
	TRACE_debug("$wan1_inetuid      = ".$wan1_inetuid);
	TRACE_debug("$wan1_phyuid       = ".$wan1_phyuid);
	TRACE_debug("$runtime_wan1_inet = ".$runtime_wan1_inet);
	TRACE_debug("$runtime_wan1_phy  = ".$runtime_wan1_phy);

	TRACE_debug("$wancable_status    = ".$wancable_status);
	TRACE_debug("$wan_network_status = ".$wan_network_status);
	
	TRACE_debug("$system_uptime    = ".$system_uptime);
	TRACE_debug("$wan_uptime       = ".$wan_uptime);
	TRACE_debug("$wan_delta_uptime = ".$wan_delta_uptime);	
}	


if($wancable_status == 1 && 
	 $wan_delta_uptime > 0 && 
	 $wan_uptime > 0)
{
	$uptime = $wan_delta_uptime;
}
else { $uptime = 0; }


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetInternetConnUpTimeResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetInternetConnUpTimeResult><?=$result?></GetInternetConnUpTimeResult>
			<UpTime><?=$uptime?></UpTime>
		</GetInternetConnUpTimeResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>