<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$nodebase = "/runtime/hnap/GetQoSSettings";
del("/runtime/hnap/GetQoSSettings");
$nodebase = $nodebase."/entry";
$result = "OK";

$entry_bwc = "/bwc/entry:1/rules/entry";
$entry_bwcf = "/bwc/bwcf/entry";

$TYPE = get("x", "/bwc/entry:1/flag");

$i=0;
foreach($entry_bwcf)
{
	if(get("", "mac")!="")
	{
		$i++;
		set($nodebase.":".$i."/IPAddress", get("", "ipv4/start"));
		set($nodebase.":".$i."/MACAddress", get("", "mac"));
		$bwcf_uid = get("", "uid");
		$bwc_entry1_rules_path = XNODE_getpathbytarget("/bwc/entry:1/rules", "entry", "bwcf", $bwcf_uid, 0);
		set($nodebase.":".$i."/Hostname", get("", $bwc_entry1_rules_path."/description"));
		$bwcqd = get("", $bwc_entry1_rules_path."/bwcqd");
		if ($bwcqd == "BWCQD-1") { $PRIORITY = "3"; } //Higest
		else if ($bwcqd == "BWCQD-2") { $PRIORITY = "2"; } //Higher
		else if ($bwcqd == "BWCQD-3") { $PRIORITY = "1"; } //Normal
		else if ($bwcqd == "BWCQD-4") { $PRIORITY = "0"; } //Best effort
				set($nodebase.":".$i."/Priority", $PRIORITY);

		$TYPE = "TC_SPQ"; //only available TC_SPQ_2013GUI for new UI QoS now
		set($nodebase.":".$i."/Type", $TYPE);
	}
}


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetQoSSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetQoSSettingsResult><?=$result?></GetQoSSettingsResult>
			<QoSInfoList>
				<?
					foreach($nodebase)
					{
						echo "				<QoSInfo>\n";
						echo "					<Hostname>".get("x", "Hostname")."</Hostname>\n";
						echo "					<IPAddress>".get("x", "IPAddress")."</IPAddress>\n";
						echo "					<MACAddress>".get("x", "MACAddress")."</MACAddress>\n";
						echo "					<Priority>".get("x", "Priority")."</Priority>\n";
						echo "					<Type>".get("x", "Type")."</Type>\n";
						echo "				</QoSInfo>\n";
					}
				?>
			</QoSInfoList>
		</GetQoSSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
