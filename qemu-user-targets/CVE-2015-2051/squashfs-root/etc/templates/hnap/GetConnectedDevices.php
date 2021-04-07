<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_run_wlan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $WLAN1, 0);
$path_run_wlan2 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $WLAN2, 0);
$path_run_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1, 0);

// set wireless client to tmp
$nodebase="/runtime/hnap/GetConnectedDevices";
del("/runtime/hnap/GetConnectedDevices");
$nodebase=$nodebase."/entry";
$i=0;

foreach($path_run_wlan1."/media/clients/entry")
{
	$i++;
	set($nodebase.":".$i."/time", query("time"));
	set($nodebase.":".$i."/macaddr", query("macaddr")); 
	set($nodebase.":".$i."/Wireless", "true");
	set($nodebase.":".$i."/Active", "true");
	$mode=query("band");
	if($mode=="11A" || $mode=="11a")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11a");
	}
	else if($mode=="11B" || $mode=="11b")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11b");
	}
	else if($mode=="11G" || $mode=="11g")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11g");
	}
	else if($mode=="11AN" || $mode=="11an")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11an");
	}
	else if($mode=="11BN" || $mode=="11bn")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bn");
	}
	else if($mode=="11N" || $mode=="11n")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11n");
	}	
	else if($mode=="11BG" || $mode=="11bg")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bg");
	}
	else if($mode=="11BGN" || $mode=="11bgn")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bgn");
	}
}

foreach($path_run_wlan2."/media/clients/entry")
{
		$i++;
	set($nodebase.":".$i."/time", query("time"));
	set($nodebase.":".$i."/macaddr", query("macaddr")); 
	set($nodebase.":".$i."/Wireless", "true");
	set($nodebase.":".$i."/Active", "true");
	$mode=query("band");
	if($mode=="11A" || $mode=="11a")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11a");
	}
	else if($mode=="11B" || $mode=="11b")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11b");
	}
	else if($mode=="11G" || $mode=="11g")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11g");
	}
	else if($mode=="11AN" || $mode=="11an")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11an");
	}
	else if($mode=="11BN" || $mode=="11bn")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bn");
	}
	else if($mode=="11N" || $mode=="11n")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11n");
	}	
	else if($mode=="11BG" || $mode=="11bg")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bg");
	}
	else if($mode=="11BGN" || $mode=="11bgn")
	{
		set($nodebase.":".$i."/PortName", "WLAN 802.11bgn");
	}
}

foreach($path_run_lan1."/dhcps4/leases/entry")
{
	$mac=query("macaddr");
	$hostname=query("hostname");
	$found=0;
	$j=0;
	foreach($nodebase)
	{
		$j++;
		if($mac==query("macaddr"))
		{
			set($nodebase.":".$j."/DeviceName", $hostname);
			$found=1;
		}
	}

	if($found==0)
	{
		$i++;
		set($nodebase.":".$i."/time", "");
		set($nodebase.":".$i."/macaddr", $mac);
		set($nodebase.":".$i."/DeviceName", $hostname);
		set($nodebase.":".$i."/PortName", "LAN");
		set($nodebase.":".$i."/Wireless", "false");
		set($nodebase.":".$i."/Active", "true");
	}
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetConnectedDevicesResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetConnectedDevicesResult>OK</GetConnectedDevicesResult>
      <ConnectedClients>
<?
foreach($nodebase)
{
	
	echo "        <ConnectedClient>\n";
	echo "          <ConnectTime>".query("time")."</ConnectTime>\n";
	echo "          <MacAddress>".query("macaddr")."</MacAddress>\n";
	echo "          <DeviceName>".get("x","DeviceName")."</DeviceName>\n";
	echo "          <PortName>".query("PortName")."</PortName>\n";
	echo "          <Wireless>".query("Wireless")."</Wireless>\n";
	echo "          <Active>".query("Active")."</Active>\n";
	echo "        </ConnectedClient>\n";
}

?>      </ConnectedClients>
    </GetConnectedDevicesResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
