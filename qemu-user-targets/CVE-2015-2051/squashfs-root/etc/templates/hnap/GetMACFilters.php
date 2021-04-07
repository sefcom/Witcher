<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$enabled="false";
$allow="false";
if(query("/acl/macctrl/policy")!="DISABLE")
{
	$enabled="true";
}
if(query("/acl/macctrl/policy")!="ACCEPT")
{
	$allow="true";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetMACFiltersResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetMACFiltersResult>OK</GetMACFiltersResult>
      <Enabled><?=$enabled?></Enabled>
      <IsAllowList><?=$allow?></IsAllowList>
      <MACList>
<?
	foreach("/acl/macctrl/entry")
	{
		echo "        <string>".query("mac")."</string>\n";
	}
?>      </MACList>
    </GetMACFiltersResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
