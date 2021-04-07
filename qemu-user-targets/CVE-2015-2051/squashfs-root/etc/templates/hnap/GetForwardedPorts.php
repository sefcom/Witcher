<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_nat = query($path_inf_wan1."/nat");
$path_wan1_nat = XNODE_getpathbytarget("nat", "entry", "uid", $wan1_nat, 0); 
?>	
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetForwardedPortsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetForwardedPortsResult>OK</GetForwardedPortsResult>
      <ForwardedPorts>
<?
	foreach($path_wan1_nat."/portforward/entry")
	{
			echo "        <ForwardedPort>\n";
			echo "          <Enabled>".query("enable")."</Enabled>\n";
			echo "          <Name>".get("x","description")."</Name>\n";
			echo "          <PrivateIP>".query("internal/hostid")."</PrivateIP>\n";
			echo "          <Protocol>".query("protocol")."</Protocol>\n";
			echo "          <StartExternalPort>".query("external/start")."</StartExternalPort>\n";
			echo "          <EndExternalPort>".query("external/end")."</EndExternalPort>\n";			
		    echo "          <StartInternalPort>".query("internal/start")."</StartInternalPort>\n";
			echo "        </ForwardedPort>\n";
	}
?>      </ForwardedPorts>
    </GetForwardedPortsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
