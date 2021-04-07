<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";
if(get("", "/samba/enable")==0)
{	$Samba_Enable = "false";}
else
{	$Samba_Enable = "true";}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSMBStatusResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetSMBStatusResult><?=$result?></GetSMBStatusResult>
			<Enabled><?=$Samba_Enable?></Enabled>
		</GetSMBStatusResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>