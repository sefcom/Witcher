<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";

$result = "OK";

$Ingress_Filtering = "Disable";

if (get("x", "/device/ingress_filtering") == "1") 
{ 
	$Ingress_Filtering = "Enable"; 
}
else 
{ 
	$Ingress_Filtering = "Disable"; 
}


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetIPv6IngressFilteringResponse xmlns="http://purenetworks.com/HNAP1/">
			<Status><?=$Ingress_Filtering?></Status>
		</GetIPv6IngressFilteringResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>