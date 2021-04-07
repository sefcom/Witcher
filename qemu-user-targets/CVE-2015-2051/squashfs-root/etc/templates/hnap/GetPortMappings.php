<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_nat = query($path_inf_wan1."/nat");
$path_wan1_nat = XNODE_getpathbytarget("nat", "entry", "uid", $wan1_nat, 0); 
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetPortMappingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetPortMappingsResult>OK</GetPortMappingsResult>
      <PortMappings>
<?
	foreach($path_wan1_nat."/virtualserver/entry")
	{

		echo "        <PortMapping>\n";
		echo "          <Enabled>".query("enable")."</Enabled>\n";
		echo "          <PortMappingDescription>".get("x","description")."</PortMappingDescription>\n";
		echo "          <InternalClient>".query("internal/hostid")."</InternalClient>\n";
		echo "          <PortMappingProtocol>".query("protocol")."</PortMappingProtocol>\n";
		echo "          <ExternalPort>".query("external/start")."</ExternalPort>\n";
		echo "          <InternalPort>".query("internal/start")."</InternalPort>\n";
		echo "        </PortMapping>\n";
	
	}
?>      </PortMappings>
    </GetPortMappingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
