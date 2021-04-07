<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";

$result = "OK";

/*
Ref: /etc/services/IPTFIREWALL.php
For Black list, the /acl/firewall/policy is ACCEPT and the /acl/firewall/entry/policy is DROP then the IP table would drop the setting IP or accept them.
For White list, the /acl/firewall/policy is DROP and the /acl/firewall/entry/policy is ACCEPT then the IP table would accept the setting IP or drop them.
*/
if(get("", "/acl/firewall/policy")=="ACCEPT")		$IPv4_FirewallStatus = "Enable_BlackList";
else if(get("", "/acl/firewall/policy")=="DROP")	$IPv4_FirewallStatus = "Enable_WhiteList";
else												$IPv4_FirewallStatus = "Disable";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetIPv4FirewallSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetIPv4FirewallSettingsResult><?=$result?></GetIPv4FirewallSettingsResult>
		<IPv4_FirewallStatus><?=$IPv4_FirewallStatus?></IPv4_FirewallStatus>
		<IPv4FirewallRuleLists>
			<?
				foreach("/acl/firewall/entry")
				{
					$Name = get("", "description");	
					if (get("x", "enable") == "1") { $Status = "Enable"; }
					else { $Status = "Disable"; }
					$Schedule = XNODE_getschedulename(get("x", "schedule"));
					$SrcInterface = substr(get("x", "src/inf"), 0, 3);
					$SrcIPv4AddressRangeStart = get("x", "src/host/start");
					$SrcIPv4AddressRangeEnd = get("x", "src/host/end");
					$DestInterface = substr(get("x", "dst/inf"), 0, 3);
					$DestIPv4AddressRangeStart = get("x", "dst/host/start");
					$DestIPv4AddressRangeEnd = get("x", "dst/host/end");
					if(get("x", "protocol")=="TCP") {$Protocol = "TCP";}
					else if(get("x", "protocol")=="UDP") {$Protocol = "UDP";}
					else {$Protocol = "Any";}
					$PortRangeStart = get("x", "dst/port/start");
					$PortRangeEnd = get("x", "dst/port/end");
					
					echo "			<IPv4FirewallRule>\n";
					echo "				<Name>".$Name."</Name>\n";					
					echo "				<Status>".$Status."</Status>\n";
					echo "				<Schedule>".$Schedule."</Schedule>\n";					
					echo "				<SrcInterface>".$SrcInterface."</SrcInterface>\n";
					echo "				<SrcIPv4AddressRangeStart>".$SrcIPv4AddressRangeStart."</SrcIPv4AddressRangeStart>\n";
					echo "				<SrcIPv4AddressRangeEnd>".$SrcIPv4AddressRangeEnd."</SrcIPv4AddressRangeEnd>\n";
					echo "				<DestInterface>".$DestInterface."</DestInterface>\n";
					echo "				<DestIPv4AddressRangeStart>".$DestIPv4AddressRangeStart."</DestIPv4AddressRangeStart>\n";
					echo "				<DestIPv4AddressRangeEnd>".$DestIPv4AddressRangeEnd."</DestIPv4AddressRangeEnd>\n";
					echo "				<Protocol>".$Protocol."</Protocol>\n";
					echo "				<PortRangeStart>".$PortRangeStart."</PortRangeStart>\n";
					echo "				<PortRangeEnd>".$PortRangeEnd."</PortRangeEnd>\n";
					echo "			</IPv4FirewallRule>\n";
				}
			?>
		</IPv4FirewallRuleLists>
	</GetIPv4FirewallSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>