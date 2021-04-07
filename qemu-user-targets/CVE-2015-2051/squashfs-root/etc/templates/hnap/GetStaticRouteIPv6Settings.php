<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$result = "OK";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetStaticRouteIPv6SettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetStaticRouteIPv6SettingsResult><?=$result?></GetStaticRouteIPv6SettingsResult>
			<StaticRouteIPv6List> 
			<?				
				//+++ Jerry Kao, Get parameter from /route6 directly.
				$Route_entry  = "/route6/static/entry";
				
				foreach($Route_entry)
				{															
					if (query("enable") == "1") { $Status = true; }
					else                        { $Status = false; }
					
					if (query("metric") != "")  { $Metric = query("metric"); }
					else                        { $Metric = "1"; }
					
					if (query("inf") == "PD")	{ $Interface = "LAN(DHCP-PD)"; }
					else
					{ 
						if(query("inf") =="WAN-4" || query("inf") =="LAN-4") 
						{
							$Interface = substr(get("", "inf"), 0, 3);
						}
						else
						{
							$Interface = "NULL";
						}
					}
					
					$Name      = query("description");
					$IPAddress = query("network");
					$PrefixLen = query("prefix"); 
					$Gateway   = query("via");															
					
					echo "			<SRIPv6Info>\n";
					echo "				<Status>".$Status."</Status>\n";
					echo "				<Name>".$Name."</Name>\n";
					echo "				<DestNetwork>".$IPAddress."</DestNetwork>\n";
					echo "				<PrefixLen>".$PrefixLen."</PrefixLen>\n";
					echo "				<Gateway>".$Gateway."</Gateway>\n";
					echo "				<Metric>".$Metric."</Metric>\n";
					echo "				<Interface>".$Interface."</Interface>\n";
					echo "			</SRIPv6Info>\n";										
				}       				
			?>        				
			</StaticRouteIPv6List>
		</GetStaticRouteIPv6SettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>