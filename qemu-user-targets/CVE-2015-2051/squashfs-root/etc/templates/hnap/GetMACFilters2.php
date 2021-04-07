<? include "/htdocs/phplib/html.php";
include "/htdocs/phplib/xnode.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$enabled="false";
$allow="false";
if(query("/acl/macctrl/policy")!="DISABLE")
{
	$enabled="true";
}
if(query("/acl/macctrl/policy")=="ACCEPT" || query("/acl/macctrl/allow")=="true")
{
	$allow="true";
}

//$allow = "";//Sammy, workaround, prevent QRS crash
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetMACFilters2Response xmlns="http://purenetworks.com/HNAP1/">
<GetMACFilters2Result>OK</GetMACFilters2Result>
<Enabled><?=$enabled?></Enabled>
<IsAllowList><?=$allow?></IsAllowList>
<MACList>
<?
	foreach("/acl/macctrl/entry")
	{
		$Status = XNODE_getschedule2013checktime(get("x","schedule"));
		if($Status == 1)	{$Status = "true";}
		else				{$Status = "false";}
		echo "<MACInfo>\n";
		echo "\t<MacAddress>".query("mac")."</MacAddress>\n";
		echo "\t<DeviceName>".get("x","description")."</DeviceName>\n";
		echo "\t<ScheduleName>".XNODE_getschedulename(get("x","schedule"))."</ScheduleName>\n";
		echo "\t<Status>".$Status."</Status>\n";
		echo "</MACInfo>\n";
	}
?>
</MACList>
</GetMACFilters2Response>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
