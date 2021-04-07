<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$enable = get("","/upnpav/dms/active");
$devicename = get("","/upnpav/dms/name");

if($enable == "1") 	$enable = "true"; 
else 								$enable = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetDLNAResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetDLNAResult><?=$result?></GetDLNAResult>
		<Enabled><?=$enable?></Enabled>
		<DeviceName><?=$devicename?></DeviceName>
		</GetDLNAResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

