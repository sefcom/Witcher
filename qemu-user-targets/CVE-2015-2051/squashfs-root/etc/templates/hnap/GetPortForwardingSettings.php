<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";

$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_nat = get("x", $path_inf_wan1."/nat");
$path_wan1_nat = XNODE_getpathbytarget("nat", "entry", "uid", $wan1_nat, 0); 
$pfwd_entry = $path_wan1_nat."/portforward/entry";

$result = "OK";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetPortForwardingSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetPortForwardingSettingsResult><?=$result?></GetPortForwardingSettingsResult>
		<PortForwardingList>
			<?
				foreach($pfwd_entry)
				{
					$en = get("x", "enable");
					if ($en == "1") { $enable = true; }
					else { $enable = false; }
					
					$description = get("x", "description");
					
					$tport_str = get("x", "tport_str");
					
					$uport_str = get("x", "uport_str");
					
					$internal_inf = get("x", "internal/inf");
					$lanip = INF_getcurripaddr($internal_inf);
					$mask = INF_getcurrmask($internal_inf);
					$hostid = get("x", "internal/hostid");
					$ipv4addr = ipv4ip($lanip, $mask, $hostid);
					
					$schedule = XNODE_getschedulename(get("x", "schedule"));
					
					echo "			<PortForwardingInfo>\n";
					echo "				<Enabled>".$enable."</Enabled>\n";
					echo "				<PortForwardingDescription>".$description."</PortForwardingDescription>\n";
					echo "				<TCPPorts>".$tport_str."</TCPPorts>\n";
					echo "				<UDPPorts>".$uport_str."</UDPPorts>\n";
					echo "				<LocalIPAddress>".$ipv4addr."</LocalIPAddress>\n";
					echo "				<ScheduleName>".$schedule."</ScheduleName>\n";
					echo "			</PortForwardingInfo>\n";
				}
			?>
		</PortForwardingList>
	</GetPortForwardingSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>