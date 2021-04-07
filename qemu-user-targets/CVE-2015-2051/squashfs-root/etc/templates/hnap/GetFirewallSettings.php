<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";

$alg_path = "/device/passthrough";
$result = "OK";

if (get("x", "/acl/spi/enable") == "1") { $spi_ipv4 = true; }
else { $spi_ipv4 = false; }

if (get("x", "/acl/anti_spoof/enable") == "1") { $anti_spoof = true; }
else { $anti_spoof = false; }

if (get("x", $alg_path."/pptp") == "1") { $alg_pptp = true; }
else { $alg_pptp = false; }

if (get("x", $alg_path."/ipsec") == "1") { $alg_ipsec = true; }
else { $alg_ipsec = false; }

if (get("x", $alg_path."/rtsp") == "1") { $alg_rtsp = true; }
else { $alg_rtsp = false; }

if (get("x", $alg_path."/sip") == "1") { $alg_sip = true; }
else { $alg_sip = false; }

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetFirewallSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetFirewallSettingsResult><?=$result?></GetFirewallSettingsResult>
			<SPIIPv4><?=$spi_ipv4?></SPIIPv4>
			<AntiSpoof><?=$anti_spoof?></AntiSpoof>
			<ALGPPTP><?=$alg_pptp?></ALGPPTP>
			<ALGIPSec><?=$alg_ipsec?></ALGIPSec>
			<ALGRTSP><?=$alg_rtsp?></ALGRTSP>
			<ALGSIP><?=$alg_sip?></ALGSIP>
		</GetFirewallSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>