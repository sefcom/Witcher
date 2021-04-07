<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";

$path = "/route/static";
$entry = $path."/entry";
$result = "OK";

$nodebase = "/runtime/hnap/GetStaticRouteIPv4Settings";
del("/runtime/hnap/GetStaticRouteIPv4Settings");
$nodebase = $nodebase."/entry";

$i=0;

foreach ($entry)
{
	$i++;
	
	$uid = get("", "uid");
	
	if (get("", "enable") == "1") { set($nodebase.":".$i."/Enabled", true); }
	else { set($nodebase.":".$i."/Enabled", false); }
	
	set($nodebase.":".$i."/Name", get("", "name"));
	
	set($nodebase.":".$i."/IPAddress", get("", "network"));
	
	$netmask = ipv4int2mask(get("", "mask"));
	set($nodebase.":".$i."/NetMask", $netmask);
	
	set($nodebase.":".$i."/Gateway", get("", "via"));
	
	if (query("metric") != "") { set($nodebase.":".$i."/Metric", get("", "metric")); }
	else { set($nodebase.":".$i."/Metric", "1"); }
	
	/*The HNAP spec. for Get&SetStaticRouteIPv4Settings is not clearly to define the tag of Interface.
	  For our device DB settings, the interface should be WAN-1, WAN-2,... LAN-1,... and so on. However D-Link 2013 new GUI only set the tag with "WAN".
	  Now if the D-Link send "WAN" or "LAN" to our device, we use $WAN1 or $LAN1 in the htdocs/webinc/config.php. */
	if(get("", "inf") == $WAN1)		{$interface = "WAN";}
	else if(get("", "inf") == $LAN1)	{$interface = "LAN";}
	set($nodebase.":".$i."/Interface", $interface);
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetStaticRouteIPv4SettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetStaticRouteIPv4SettingsResult><?=$result?></GetStaticRouteIPv4SettingsResult>
			<StaticRouteIPv4List> 
			<?
				foreach($nodebase)
				{
					echo "				<SRIPv4Info>\n";
					echo "				<Enabled>".get("x", "Enabled")."</Enabled>\n";
					echo "				<Name>".get("x", "Name")."</Name>\n";
					echo "				<IPAddress>".get("x", "IPAddress")."</IPAddress>\n";
					echo "				<NetMask>".get("x", "NetMask")."</NetMask>\n";
					echo "				<Gateway>".get("x", "Gateway")."</Gateway>\n";
					echo "				<Metric>".get("x", "Metric")."</Metric>\n";
					echo "				<Interface>".get("x", "Interface")."</Interface>\n";
					echo "				</SRIPv4Info>\n";
				}
			?>        				
			</StaticRouteIPv4List>
		</GetStaticRouteIPv4SettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>