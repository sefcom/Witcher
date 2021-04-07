<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/trace.php";

$list_entry = "/runtime/mydlink/userlist/entry";

$result = "OK";

$ip_addr = $_SERVER["REMOTE_ADDR"];
$mac_addr = INET_ARP($ip_addr);

TRACE_debug("$_SERVER=".$_SERVER["REMOTE_ADDR"]);
TRACE_debug("ip_addr=".$ip_addr);
TRACE_debug("mac_addr=".$mac_addr);

foreach($list_entry)
{
	$list_addr = get("", "ipv4addr");
	if ($ip_addr == $list_addr)
	{
		$hostname = get("", "hostname");
	}
	else { $hostname = ""; }
}

?>

<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetLocalHostInfoResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetLocalHostInfoResult><?=$result?></GetLocalHostInfoResult>
		<Hostname><?=$hostname?></Hostname>
		<IPAddress><?=$ip_addr?></IPAddress>
		<MACAddress><?=$mac_addr?></MACAddress>
	</GetLocalHostInfoResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
