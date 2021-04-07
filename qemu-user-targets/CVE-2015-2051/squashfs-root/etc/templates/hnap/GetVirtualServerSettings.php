<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";

$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_nat = get("x", $path_inf_wan1."/nat");
$path_wan1_nat = XNODE_getpathbytarget("nat", "entry", "uid", $wan1_nat, 0); 
$vsvr_entry = $path_wan1_nat."/virtualserver/entry";

$result = "OK";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetVirtualServerSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetVirtualServerSettingsResult><?=$result?></GetVirtualServerSettingsResult>
		<VirtualServerList>
			<?
				foreach($vsvr_entry)
				{
					$en = get("x", "enable");
					if ($en == "1") { $enable = true; }
					else { $enable = false; }
					
					$description = get("x", "description");
					
					$external = get("x", "external/start");
					
					$internal = get("x", "internal/start");
					
					$protocol = get("x", "protocol");
					if($protocol=="TCP+UDP") $protocol = "Both";
					
					$protocolnum = get("x", "protocolnum");
					
					$internal_inf = get("x", "internal/inf");
					$lanip = INF_getcurripaddr($internal_inf);
					$mask = INF_getcurrmask($internal_inf);
					$hostid = get("x", "internal/hostid");
					$ipv4addr = ipv4ip($lanip, $mask, $hostid);
					
					$schedule = XNODE_getschedulename(get("x", "schedule"));
					
					echo "			<VirtualServerInfo>\n";
					echo "				<Enabled>".$enable."</Enabled>\n";
					echo "				<VirtualServerDescription>".$description."</VirtualServerDescription>\n";
					echo "				<ExternalPort>".$external."</ExternalPort>\n";
					echo "				<InternalPort>".$internal."</InternalPort>\n";
					echo "				<ProtocolType>".$protocol."</ProtocolType>\n";
					echo "				<ProtocolNumber>".$protocolnum."</ProtocolNumber>\n";
					echo "				<LocalIPAddress>".$ipv4addr."</LocalIPAddress>\n";
					echo "				<ScheduleName>".$schedule."</ScheduleName>\n";
					echo "			</VirtualServerInfo>\n";
				}
			?>
		</VirtualServerList>
	</GetVirtualServerSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>