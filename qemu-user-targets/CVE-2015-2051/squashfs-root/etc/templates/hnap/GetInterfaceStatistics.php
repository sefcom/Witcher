<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$Interface = get("h","/runtime/hnap/GetInterfaceStatistics/Interface");

$result = "OK";

$path_tx = "/stats/tx/bytes";
$path_rx = "/stats/rx/bytes";
$path_tx_pkts = "/stats/tx/packets";
$path_rx_pkts = "/stats/rx/packets";
$path_tx_drop = "/stats/tx/drop";
$path_rx_drop = "/stats/rx/drop";
$path_tx_error = "/stats/tx/error";
$path_rx_error = "/stats/rx/error";

$tx = "N/A";
$rx = "N/A";
$tx_pkts = "N/A";
$rx_pkts = "N/A";
$tx_drop = "N/A";
$error = "N/A";

function Hex2DecLL($HexStr) //Hex To Dec Long Long
{
	$HexStrL =  "0x".substr($HexStr, 10, 8);
	$HexStrH =  "0x".substr($HexStr, 2, 8);
	$DecL = strtoul($HexStrL,16);
	$DecH = strtoul($HexStrH,16);
	$DecH = $DecH * 4294967296;
	$Dec = $DecH + $DecL;
	return $Dec;
}

function get_runtime_eth_path($uid)
{
	$p = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
	if($p == "") return $p;

	return XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($p."/phyinf"));
}

//db node doesn't have Rx info
function get_wifiRxframePacket($uid)
{ 
	if($uid == "BAND24G-1.1")	
	{
		$wifiuid = "wifig0";
	
		$cmd = "wl -i ".$wifiuid." counters | grep rxframe";
		setattr("/runtime/".$wifiuid."/tmp_info", "get", $cmd);
		$tmp_rx_info = get("", "/runtime/".$wifiuid."/tmp_info");
		$rxframe = cut($tmp_rx_info, 9, " "); //get rxframe
		set("/runtime/".$wifiuid."/rxframe", $rxframe);
	}
	else if($uid == "BAND5G-1.1")	//5G low + 5G high
	{
		$wifiuid = "wifia0";
		
		//5G low
		$cmd = "wl -i wifia0 counters | grep rxframe";
		setattr("/runtime/wifia0/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia0/tmp_info");
		$rxframe_low = cut($tmp_rx_info, 9, " "); //get 5G low rxframe
		
		//5G high
		$cmd = "wl -i wifia1 counters | grep rxframe";
		setattr("/runtime/wifia1/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia1/tmp_info");
		$rxframe_high = cut($tmp_rx_info, 9, " "); //get 5G high rxframe

		$rxframe += $rxframe_low + $rxframe_high;
		set("/runtime/".$wifiuid."/rxframe", $rxframe);
	}
	return $rxframe;
}

function get_wifiRxbytePacket($uid)
{ 
	if($uid == "BAND24G-1.1")	
	{
		$wifiuid = "wifig0";
	
		$cmd = "wl -i ".$wifiuid." counters | grep rxbyte";
		setattr("/runtime/".$wifiuid."/tmp_info", "get", $cmd);
		$tmp_rx_info = get("", "/runtime/".$wifiuid."/tmp_info");
		$rxbyte = cut($tmp_rx_info, 11, " "); //get rxbyte
		set("/runtime/".$wifiuid."/rxbyte", $rxbyte);
	}
	else if($uid == "BAND5G-1.1")	//5G low + 5G high
	{
		$wifiuid = "wifia0";
		
		//5G low
		$cmd = "wl -i wifia0 counters | grep rxbyte";
		setattr("/runtime/wifia0/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia0/tmp_info");
		$rxbyte_low = cut($tmp_rx_info, 11, " "); //get 5G low rxbyte
		
		//5G high
		$cmd = "wl -i wifia1 counters | grep rxbyte";
		setattr("/runtime/wifia1/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia1/tmp_info");
		$rxbyte_high = cut($tmp_rx_info, 11, " "); //get 5G high rxbyte

		$rxbyte += $rxbyte_low + $rxbyte_high;
		set("/runtime/".$wifiuid."/rxbyte", $rxbyte);
	}
	return $rxbyte;
}

function get_wifiRxerrorPacket($uid)
{ 
	if($uid == "BAND24G-1.1")	
	{
		$wifiuid = "wifig0";
	
		$cmd = "wl -i ".$wifiuid." counters | grep rxerror";
		setattr("/runtime/".$wifiuid."/tmp_info", "get", $cmd);
		$tmp_rx_info = get("", "/runtime/".$wifiuid."/tmp_info");
		$rxerror = cut($tmp_rx_info, 13, " "); //get rxerror
		set("/runtime/".$wifiuid."/rxerror", $rxerror);
	}
	else if($uid == "BAND5G-1.1")	//5G low + 5G high
	{
		$wifiuid = "wifia0";
		
		//5G low
		$cmd = "wl -i wifia0 counters | grep rxerror";
		setattr("/runtime/wifia0/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia0/tmp_info");
		$rxerror_low = cut($tmp_rx_info, 13, " "); //get 5G low rxerror
		
		//5G high
		$cmd = "wl -i wifia1 counters | grep rxerror";
		setattr("/runtime/wifia1/tmp_info", "get", $cmd);	
		$tmp_rx_info = get("", "/runtime/wifia1/tmp_info");
		$rxerror_high = cut($tmp_rx_info, 13, " "); //get 5G high rxerror

		$rxerror += $rxerror_low + $rxerror_high;
		set("/runtime/".$wifiuid."/rxerror", $rxerror);
	}
	return $rxerror;
}

if ($Interface == "WAN")
{
	$path_wan1 = get_runtime_eth_path($WAN1);
	
	//longlay 20131216, fix packets count display problem when enable Broadcom CTF module
	if(query($path_wan1.$path_tx."4")=="")
	{
		$tx = query($path_wan1.$path_tx);
		$rx = query($path_wan1.$path_rx);
		$tx_pkts = query($path_wan1.$path_tx_pkts);
		$rx_pkts = query($path_wan1.$path_rx_pkts);
	}
	else
	{
		$tx = Hex2DecLL(query($path_wan1.$path_tx."4"));
		$rx = Hex2DecLL(query($path_wan1.$path_rx."4"));
		$tx_pkts = Hex2DecLL(query($path_wan1.$path_tx_pkts."4"));
		$rx_pktsu = Hex2DecLL(query($path_wan1.$path_rx_pkts."4u"));
		$rx_pktsm = Hex2DecLL(query($path_wan1.$path_rx_pkts."4m"));
		$rx_pktsb = Hex2DecLL(query($path_wan1.$path_rx_pkts."4b"));
		$rx_pkts = $rx_pktsu+$rx_pktsm+$rx_pktsb;
	}
	$tx_drop = query($path_wan1.$path_tx_drop);						$rx_drop = query($path_wan1.$path_rx_drop);
	$tx_error = query($path_wan1.$path_tx_error);					$rx_error = query($path_wan1.$path_rx_error);
	$error = $tx_error+$rx_error;
}
else if ($Interface == "LAN")
{
	$path_lan1 = get_runtime_eth_path($LAN1);
	
	//longlay 20131216, fix packets count display problem when enable Broadcom CTF module
	if(query($path_lan1.$path_tx."0")=="")
	{
		$tx = query($path_lan1.$path_tx);
		$rx = query($path_lan1.$path_rx);
		$tx_pkts = query($path_lan1.$path_tx_pkts);
		$rx_pkts = query($path_lan1.$path_rx_pkts);
	}
	else
	{
		$tx0 = Hex2DecLL(query($path_lan1.$path_tx."0"));
		$tx1 = Hex2DecLL(query($path_lan1.$path_tx."1"));
		$tx2 = Hex2DecLL(query($path_lan1.$path_tx."2"));
		$tx3 = Hex2DecLL(query($path_lan1.$path_tx."3"));
		$tx = $tx0+$tx1+$tx2+$tx3;
		
		$rx0 = Hex2DecLL(query($path_lan1.$path_rx."0"));
		$rx1 = Hex2DecLL(query($path_lan1.$path_rx."1"));
		$rx2 = Hex2DecLL(query($path_lan1.$path_rx."2"));
		$rx3 = Hex2DecLL(query($path_lan1.$path_rx."3"));
		$rx = $rx0+$rx1+$rx2+$rx3;
	
		$tx_pkts0 = Hex2DecLL(query($path_lan1.$path_tx_pkts."0"));
		$tx_pkts1 = Hex2DecLL(query($path_lan1.$path_tx_pkts."1"));
		$tx_pkts2 = Hex2DecLL(query($path_lan1.$path_tx_pkts."2"));
		$tx_pkts3 = Hex2DecLL(query($path_lan1.$path_tx_pkts."3"));
		$tx_pkts = $tx_pkts0+$tx_pkts1+$tx_pkts2+$tx_pkts3;
		
		$rx_pkts0u = Hex2DecLL(query($path_lan1.$path_rx_pkts."0u"));
		$rx_pkts0m = Hex2DecLL(query($path_lan1.$path_rx_pkts."0m"));
		$rx_pkts0b = Hex2DecLL(query($path_lan1.$path_rx_pkts."0b"));
		$rx_pkts1u = Hex2DecLL(query($path_lan1.$path_rx_pkts."1u"));
		$rx_pkts1m = Hex2DecLL(query($path_lan1.$path_rx_pkts."1m"));
		$rx_pkts1b = Hex2DecLL(query($path_lan1.$path_rx_pkts."1b"));
		$rx_pkts2u = Hex2DecLL(query($path_lan1.$path_rx_pkts."2u"));
		$rx_pkts2m = Hex2DecLL(query($path_lan1.$path_rx_pkts."2m"));
		$rx_pkts2b = Hex2DecLL(query($path_lan1.$path_rx_pkts."2b"));
		$rx_pkts3u = Hex2DecLL(query($path_lan1.$path_rx_pkts."3u"));
		$rx_pkts3m = Hex2DecLL(query($path_lan1.$path_rx_pkts."3m"));
		$rx_pkts3b = Hex2DecLL(query($path_lan1.$path_rx_pkts."3b"));
		$rx_pkts = $rx_pkts0u+$rx_pkts0m+$rx_pkts0b+$rx_pkts1u+$rx_pkts1m+$rx_pkts1b+
			$rx_pkts2u+$rx_pkts2m+$rx_pkts2b+$rx_pkts3u+$rx_pkts3m+$rx_pkts3b;
	}
	$tx_drop = query($path_lan1.$path_tx_drop);						$rx_drop = query($path_lan1.$path_rx_drop);
	$tx_error = query($path_lan1.$path_tx_error);					$rx_error = query($path_lan1.$path_rx_error);
	$error = $tx_error+$rx_error;
}
else if ($Interface == "WLAN2.4G" || $Interface == "WLAN5G")
{
	foreach ("/phyinf")
	{
		if ($Interface == "WLAN2.4G") 
		{
			$band_uid = $WLAN1;
			$freq = "2.4";
		}
		else
		{ 
			$band_uid = $WLAN2;
			$freq = "5";
		}
		
		if (get ("","media/freq") != $freq ) { continue; }
		
		$uid = query("uid");
		if($uid==$band_uid && $band_uid=="BAND24G-1.1") //2.4G
		{
			$path_wifi = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $band_uid);
		
			if ($path_wifi == "") continue;
			if (isdigit($tx) == 0)
			{
				$tx = 0;								$rx = 0;
				$tx_pkts = 0;						$rx_pkts = 0;
				$tx_drop = 0;						$rx_drop = 0;
				$error = 0;
			}

			$tx += query($path_wifi.$path_tx);											$rx += get_wifiRxbytePacket($band_uid);									//$rx += query($path_wifi.$path_rx);
			$tx_pkts += query($path_wifi.$path_tx_pkts);						$rx_pkts += get_wifiRxframePacket($band_uid);						//$rx_pkts += query($path_wifi.$path_rx_pkts);
			$tx_drop += query($path_wifi.$path_tx_drop);						$rx_drop += query("/runtime/".$wifiuid."/rx_drop");			//$rx_drop += query($path_wifi.$path_rx_drop);
			$tx_error = query($path_wifi.$path_tx_error);					  $rx_error = get_wifiRxerrorPacket($band_uid);						//$rx_error = query($path_wifi.$path_rx_error);
			$error += $tx_error+$rx_error;
		}
		else if($uid==$band_uid && $band_uid=="BAND5G-1.1") //5G
		{
			$path_wifi = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $band_uid); //5G low
			$path_wifi_high = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "BAND5G-2.1"); //5G high
		
			if ($path_wifi == "" && $path_wifi_high == "") continue;
			if (isdigit($tx) == 0)
			{
				$tx = 0;								$rx = 0;
				$tx_pkts = 0;						$rx_pkts = 0;
				$tx_drop = 0;						$rx_drop = 0;
				$error = 0;
			}

			$tx += query($path_wifi.$path_tx) + query($path_wifi_high.$path_tx);											$rx += get_wifiRxbytePacket($band_uid);
			$tx_pkts += query($path_wifi.$path_tx_pkts) + query($path_wifi_high.$path_tx_pkts);				$rx_pkts += get_wifiRxframePacket($band_uid);
			$tx_drop += query($path_wifi.$path_tx_drop) + query($path_wifi_high.$path_tx_drop);				$rx_drop += query("/runtime/".$wifiuid."/rx_drop");
			$tx_error += query($path_wifi.$path_tx_error) + query($path_wifi_high.$path_tx_error);	  $rx_error = get_wifiRxerrorPacket($band_uid);
			$error += $tx_error+$rx_error;
		}
	}
}

/* Internet session */
function getSessionNumber($ip)
{
	if ($ip == "")
	{ return 0; }
	
	$cmd = "grep ". $ip ." /proc/net/nf_conntrack | grep -v \"UNREPLIED\" | wc -l";
	setattr("/runtime/device/conntrace_number", "get", $cmd);
	$session = get("htm", "/runtime/device/conntrace_number");
	del("/runtime/device/conntrace_number");
	return $session;
}

$wan_runtime_path = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
$wan_type = get ("", $wan_runtime_path."/inet/addrtype");

if ($wan_type == "ipv4")
{ $wan_ip = get("", $wan_runtime_path."/inet/ipv4/ipaddr"); }
else if ($wan_type == "ppp4")
{ $wan_ip = get("", $wan_runtime_path."/inet/ppp4/local"); }

$session = getSessionNumber($wan_ip);
/* Internet session end*/

// Reset the statistic value after using ClearStatistics HNAP.
$LastesClearPath = XNODE_getpathbytarget("/runtime/hnap/LastStatisticsClear", "entry", "Interface", $Interface, 0);
$last_tx			= map($LastesClearPath."/Sent", "", 0, "*", get("", $LastesClearPath."/Sent"));
$last_rx			= map($LastesClearPath."/Received", "", 0, "*", get("", $LastesClearPath."/Received"));
$last_tx_pkts	= map($LastesClearPath."/TXPackets", "", 0, "*", get("", $LastesClearPath."/TXPackets"));
$last_rx_pkts	= map($LastesClearPath."/RXPackets", "", 0, "*", get("", $LastesClearPath."/RXPackets"));
$last_tx_drop	= map($LastesClearPath."/TXDropped", "", 0, "*", get("", $LastesClearPath."/TXDropped"));
$last_rx_drop	= map($LastesClearPath."/RXDropped", "", 0, "*", get("", $LastesClearPath."/RXDropped"));
$last_error		= map($LastesClearPath."/Errors", "", 0, "*", get("", $LastesClearPath."/Errors"));
$tx_final		= $tx - $last_tx;
$rx_final		= $rx - $last_rx;
$tx_pkts_final	= $tx_pkts - $last_tx_pkts;
$rx_pkts_final	= $rx_pkts - $last_rx_pkts;
$tx_drop_final	= $tx_drop - $last_tx_drop;
$rx_drop_final	= $rx_drop - $last_rx_drop;
$error_final		= $error - $last_error;

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetInterfaceStatisticsResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetInterfaceStatisticsResult><?=$result?></GetInterfaceStatisticsResult>
			<Interface><?=$Interface?></Interface>
			<InterfaceStatistics>
				<StatisticInfo>
					<Sent><?=$tx_final?></Sent>
					<Received><?=$rx_final?></Received>
					<TXPackets><?=$tx_pkts_final?></TXPackets>
					<RXPackets><?=$rx_pkts_final?></RXPackets>
					<TXDropped><?=$tx_drop_final?></TXDropped>
					<RXDropped><?=$rx_drop_final?></RXDropped>
					<Session><?=$session?></Session>
					<Errors><?=$error_final?></Errors>
				</StatisticInfo>
			</InterfaceStatistics>
	</GetInterfaceStatisticsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>