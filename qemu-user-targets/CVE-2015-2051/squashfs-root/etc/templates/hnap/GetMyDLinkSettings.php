<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$Result = OK;
if ("1" == query("/mydlink/register_st"))
{
	$register_st = true;
}
else
{
	$register_st = false;
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetMyDLinkSettingsResponse xmlns="http://purenetworks.com/HNAP1/"> 
	<GetMyDLinkSettingsResult><?=$Result?></GetMyDLinkSettingsResult> 
		<Enabled>true</Enabled> 
		<Email><?echo query("/mydlink/regemail");?></Email>  
		<Password></Password> 
		<LastName></LastName> 
		<FirstName></FirstName> 
		<DeviceUserName><?echo query("/device/account/entry/name");?></DeviceUserName> 
		<DevicePassword></DevicePassword> 
		<AccountStatus><?=$register_st?></AccountStatus> 
</GetMyDLinkSettingsResponse> 
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
