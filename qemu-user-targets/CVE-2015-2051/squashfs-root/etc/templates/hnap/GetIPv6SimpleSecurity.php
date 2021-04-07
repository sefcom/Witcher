<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";

$result = "OK";

$Simple_Security = "Disable";

if (get("x", "/device/simple_security") == "1") 
{ 
	$Simple_Security = "Enable"; 
}
else 
{ 
	$Simple_Security = "Disable"; 
}


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetIPv6SimpleSecurityResponse xmlns="http://purenetworks.com/HNAP1/">
			<Status><?=$Simple_Security?></Status>
		</GetIPv6SimpleSecurityResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>