<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";

$WAN1_INF = INF_getinfpath($WAN1);
if(query($WAN1_INF."/open_dns/type")=="") $EnableOpenDNS=false;
else $EnableOpenDNS=true;
$DeviceID = query($WAN1_INF."/open_dns/deviceid");
if(query($WAN1_INF."/open_dns/type")=="parent") $OpenDNSMode="Parental"; 
else if(query($WAN1_INF."/open_dns/type")=="family") $OpenDNSMode="FamilyShield";
else $OpenDNSMode="Advanced";
$OpenDNSMode = query($WAN1_INF."/open_dns/type");
$DeviceKey = query($WAN1_INF."/open_dns/nonce");

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetOpenDNSResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetOpenDNSResult>OK</GetOpenDNSResult>
			<EnableOpenDNS><?=$EnableOpenDNS?></EnableOpenDNS>
			<OpenDNSDeviceID><?=$DeviceID?></OpenDNSDeviceID>
			<OpenDNSMode><?=$OpenDNSMode?></OpenDNSMode>
			<OpenDNSDeviceKey><?=$DeviceKey?></OpenDNSDeviceKey>
		</GetOpenDNSResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>