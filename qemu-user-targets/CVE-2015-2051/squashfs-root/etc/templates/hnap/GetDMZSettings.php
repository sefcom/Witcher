<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";
include "/htdocs/phplib/inet.php";

$result = "OK";
$dmz_path = "/nat/entry/dmz";
if(get("", $dmz_path."/enable")=="1")
{
	$enable = "true";
	$ipaddr = ipv4ip(get("", INET_getpathbyinf($LAN1)."/ipv4/ipaddr"), get("", INET_getpathbyinf($LAN1)."/ipv4/mask"), get("", $dmz_path."/hostid"));
}
else
{
	$enable = "false";
	$ipaddr = "";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetDMZSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetDMZSettingsResult><?=$result?></GetDMZSettingsResult>
			<Enabled><?=$enable?></Enabled>
			<IPAddress><?=$ipaddr?></IPAddress>
		</GetDMZSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>