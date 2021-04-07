<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$path_inf_wan2 = XNODE_getpathbytarget("", "inf", "uid", $WAN2, 0);
$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
$wan1_inet = query($path_inf_wan1."/inet"); 

$dhcp_path = $path_run_inf_wan1 . "/inet/ipv4";

$wan1_phyinf = query($path_inf_wan1."/phyinf");
$wan2_inet = query($path_inf_wan2."/inet");
$path_wan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
$path_wan1_phyinf = XNODE_getpathbytarget("", "phyinf", "uid", $wan1_phyinf, 0);
$path_wan2_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan2_inet, 0); 

// define the variable we are going to send back.
$Hostname="";
$Unicasting="";
$PrimaryDNS="";
$SecondaryDNS="";
$MTU="";
$MACAddress="";
$result="";

if(query($path_run_inf_wan1."/inet/ipv4/valid") == 1)
{
	$mode=query($path_wan1_inet."/addrtype");
	if($mode == "ipv4")
	{
		if(query($path_wan1_inet."/ipv4in6/mode") == "dslite")	//-----DS-Lite
		{
			$result = "ERROR";
		}
		else if (query($path_wan1_inet."/static") == 1) //-----Static
		{
			$result = "ERROR";
		}
		else	//-----DHCP
		{
			$result = "OK";
			$Hostname=query("/device/hostname");
			$MACAddress=query("/runtime/devdata/wanmac");
			$MTU=query($path_run_inf_wan1."/inet/ipv4/mtu");
			
			if (query("/dhcpc4/unicast") == "no")	//broadcasting
			{
				$Unicasting = "FALSE";
			}
			else // Unicasting
			{
				$Unicasting = "TRUE";
			}
			$PrimaryDNS = query ($path_run_inf_wan1."/inet/ipv4/dns");
		}
	}
}
else
{
	$result = "ERROR";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetWANDHCPSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetWANDHCPSettingsResult><? echo $result; ?></GetWANDHCPSettingsResult>
			<Hostname><? echo escape("x",$Hostname); ?></Hostname>
			<Unicasting><? echo escape("x",$Unicasting); ?></Unicasting>
			<PrimaryDNS><? echo escape("x",$PrimaryDNS); ?></PrimaryDNS>
			<SecondaryDNS><? echo escape("x",$SecondaryDNS); ?></SecondaryDNS>
			<MTU><? echo $MTU; ?></MTU>
			<MACAddress><? echo $MACAddress; ?></MACAddress>
		</GetWANDHCPSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
