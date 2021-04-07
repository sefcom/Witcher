<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/config.php";

$result = "OK";

$default_language = query ("/device/features/language");

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetLanguageDefaultSettingResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetLanguageDefaultSettingResult><? echo $result; ?></GetLanguageDefaultSettingResult>
			<GetLanguageDefaultSetting><? echo $default_language;  ?></GetLanguageDefaultSetting>
		</GetLanguageDefaultSettingResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
