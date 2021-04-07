<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";
$enable = get("","/device/features/smartconnect");
$gz_enable = get("","/device/features/smartconnect_gz");

if($enable == "1") 	$enable = "true";
else 				$enable = "false";
if($gz_enable == "1") 	$gz_enable = "true";
else 				$gz_enable = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetSmartconnectSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetSmartconnectSettingsResult><?=$result?></GetSmartconnectSettingsResult>
		<Enabled><?=$enable?></Enabled>
		<GZ_Enabled><?=$gz_enable?></GZ_Enabled>
	</GetSmartconnectSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

