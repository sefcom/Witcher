<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$mcast = get("","/device/multicast/igmpproxy");

if($mcast == "1") 	$enable = "true"; 
else 								$enable = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetMulticastSnoopingResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetMulticastSnoopingResult><?=$result?></GetMulticastSnoopingResult>
	<Enabled><?=$enable?></Enabled>
</GetMulticastSnoopingResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

