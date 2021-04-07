<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";

$result = "OK";

if(get("", "/acl6/firewall/policy")=="ACCEPT")		$IPv6_FirewallStatus = "Enable_BlackList";
else if(get("", "/acl6/firewall/policy")=="DROP")	$IPv6_FirewallStatus = "Enable_WhiteList";
else												$IPv6_FirewallStatus = "Disable";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetIPv6FirewallSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetIPv6FirewallSettingsResult><?=$result?></GetIPv6FirewallSettingsResult>
		<IPv6_FirewallStatus><?=$IPv6_FirewallStatus?></IPv6_FirewallStatus>
		<IPv6FirewallRuleLists>
			<?
				foreach("/acl6/firewall/entry")
				{
					$Name = get("", "description");	
					if (get("x", "enable") == "1") { $Status = "Enable"; }
					else { $Status = "Disable"; }
					$Schedule = XNODE_getschedulename(get("x", "schedule"));
					$SrcInterface = substr(get("x", "src/inf"), 0, 3);
					$SrcIPv6AddressRangeStart = get("x", "src/host/start");
					$SrcIPv6AddressRangeEnd = get("x", "src/host/end");
					$DestInterface = substr(get("x", "dst/inf"), 0, 3);
					$DestIPv6AddressRangeStart = get("x", "dst/host/start");
					$DestIPv6AddressRangeEnd = get("x", "dst/host/end");
					if(get("x", "protocol")=="TCP") {$Protocol = "TCP";}
					else if(get("x", "protocol")=="UDP") {$Protocol = "UDP";}
					else {$Protocol = "Any";}
					$PortRangeStart = get("x", "dst/port/start");
					$PortRangeEnd = get("x", "dst/port/end");
					
					echo "			<IPv6FirewallRule>\n";
					echo "				<Name>".$Name."</Name>\n";					
					echo "				<Status>".$Status."</Status>\n";
					echo "				<Schedule>".$Schedule."</Schedule>\n";					
					echo "				<SrcInterface>".$SrcInterface."</SrcInterface>\n";
					echo "				<SrcIPv6AddressRangeStart>".$SrcIPv6AddressRangeStart."</SrcIPv6AddressRangeStart>\n";
					echo "				<SrcIPv6AddressRangeEnd>".$SrcIPv6AddressRangeEnd."</SrcIPv6AddressRangeEnd>\n";
					echo "				<DestInterface>".$DestInterface."</DestInterface>\n";
					echo "				<DestIPv6AddressRangeStart>".$DestIPv6AddressRangeStart."</DestIPv6AddressRangeStart>\n";
					echo "				<DestIPv6AddressRangeEnd>".$DestIPv6AddressRangeEnd."</DestIPv6AddressRangeEnd>\n";
					echo "				<Protocol>".$Protocol."</Protocol>\n";
					echo "				<PortRangeStart>".$PortRangeStart."</PortRangeStart>\n";
					echo "				<PortRangeEnd>".$PortRangeEnd."</PortRangeEnd>\n";
					echo "			</IPv6FirewallRule>\n";
				}
			?>
		</IPv6FirewallRuleLists>
	</GetIPv6FirewallSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>