<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";	
			
$result = "OK";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetDynamicDNSIPv6SettingsResponse xmlns="http://purenetworks.com/HNAP1/"> 
			<GetDynamicDNSIPv6SettingsResult><?=$result?></GetDynamicDNSIPv6SettingsResult> 
			<DynamicDNSIPv6List>				
<?
foreach("/ddns6/entry")
{
	echo "\t\t\t\t<DDNSIPv6Info>\n";
	if (get("x","enable")=="1")	$Status = "Enabled";
	else 						$Status = "Disabled";
	$IPv6Address = get("x","v6addr"); 
	$Hostname = get("x","hostname");
	echo "\t\t\t\t\t<Status>".$Status."</Status>\n";
	echo "\t\t\t\t\t<IPv6Address>".$IPv6Address."</IPv6Address>\n";
	echo "\t\t\t\t\t<Hostname>".$Hostname."</Hostname>\n";
	echo "\t\t\t\t</DDNSIPv6Info>\n";
}                  
?>
			</DynamicDNSIPv6List> 
		</GetDynamicDNSIPv6SettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
