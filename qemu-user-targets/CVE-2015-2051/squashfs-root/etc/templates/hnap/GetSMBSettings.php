<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";
if(get("", "/samba/auth")=="1")
{	$SMBSecurity = "Enable";}
else
{	$SMBSecurity = "Disable";}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSMBSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetSMBSettingsResult><?=$result?></GetSMBSettingsResult>
			<SMBSecurity><?=$SMBSecurity?></SMBSecurity>
		</GetSMBSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>