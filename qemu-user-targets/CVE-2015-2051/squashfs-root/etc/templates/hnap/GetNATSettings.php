<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";
$disable = get("","/device/disable_nat");

if($disable == "1")	$disable = "true";
else 				$disable = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetNATSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetNATSettingsResult><?=$result?></GetNATSettingsResult>
		<Disable><?=$disable?></Disable>
	</GetNATSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

