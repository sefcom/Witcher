<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php"; 
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/phyinf.php";

$layout = query("/device/layout");
if($layout=="router")
{
	$INF = $LAN1;
}
else
{
	$INF = $BR1;
}

$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $INF, 0);
$mask = query($path_run_inf_lan1."/inet/ipv4/mask");
$dhcp_enbled="false";
if (query($path_inf_lan1."/dhcps4") != "")
{
	$dhcp_enbled="true";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetRouterLanSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetRouterLanSettingsResult>OK</GetRouterLanSettingsResult>
      <RouterIPAddress><? echo query($path_run_inf_lan1."/inet/ipv4/ipaddr"); ?></RouterIPAddress>
      <RouterSubnetMask><? echo ipv4int2mask($mask); ?></RouterSubnetMask>
      <DHCPServerEnabled><?=$dhcp_enbled?></DHCPServerEnabled>
      <RouterMACAddress><? echo PHYINF_getphymac($INF); ?></RouterMACAddress>
    </GetRouterLanSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
