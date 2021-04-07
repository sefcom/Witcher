<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/inf.php";

$result = "OK";

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->VPN download\" > /dev/console\n");
if($result=="OK")
{
	$ProfileLink = "http://".INF_getcfgipaddr($LAN1)."/vpnconfig.php";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetQuickVPNProfileLinkResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetQuickVPNProfileLinkResult><?=$result?></GetQuickVPNProfileLinkResult>
		<ProfileLink><?=$ProfileLink?></ProfileLink>
	</GetQuickVPNProfileLinkResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
