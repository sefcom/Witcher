<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$WebFilterMethod = get("","/acl/accessctrl/webfilter/policy");

if($WebFilterMethod == "ACCEPT")			{$WebFilterMethod = "ALLOW";}
else if($WebFilterMethod == "DROP") 	{$WebFilterMethod = "DENY";}
else 																	{$result = "ERROR";}

function print_WebFilterURLs()
{
	echo "<WebFilterURLs>";
	foreach("/acl/accessctrl/webfilter/entry")
	{
		echo "<string>".get("x","url")."</string>";
	}
	echo "</WebFilterURLs>";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetWebFilterSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetWebFilterSettingsResult><?=$result?></GetWebFilterSettingsResult>
	<WebFilterMethod><?=$WebFilterMethod?></WebFilterMethod>
	<?print_WebFilterURLs();?>
</GetWebFilterSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

