<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";

$result="OK";

if(isfile("/mydlink/version")=="1")
{
	$SupportMyDLink = "true";
}
else
{
	$SupportMyDLink = "false";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetMyDLinkSupportStatusResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetMyDLinkSupportStatusResult><?=$result?></GetMyDLinkSupportStatusResult>
	<SupportMyDLink><?=$SupportMyDLink?></SupportMyDLink>
</GetMyDLinkSupportStatusResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
