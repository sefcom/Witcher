<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

$get_wantype = $ConnectionType;

if($get_wantype != "") $rlt = "OK";
else $rlt = "ERROR";

//TRACE_info("==[Get IPv6 WanType.php]: ".$get_wantype);
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetIPv6WanTypeResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetIPv6WanTypeResult><?=$rlt?></GetIPv6WanTypeResult>
			<IPv6_ConnectionType><?=$get_wantype?></IPv6_ConnectionType>
		</GetIPv6WanTypeResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
