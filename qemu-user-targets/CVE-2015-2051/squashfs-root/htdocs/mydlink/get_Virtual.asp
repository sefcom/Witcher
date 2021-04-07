<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$wan1_nat = query($path_inf_wan1."/nat");
$path_wan1_nat = XNODE_getpathbytarget("nat", "entry", "uid", $wan1_nat, 0);
$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1, 0);

$lan_ip = query($path_run_inf_lan1."/inet/ipv4/ipaddr");
$mask = query($path_run_inf_lan1."/inet/ipv4/mask");

?>
<portforwording>
<?
foreach($path_wan1_nat."/virtualserver/entry")
{
	echo "        <client>\n";
	echo "          <name>".get("x","description")."</name>\n";
	$hostid	= query("internal/hostid");
	$ip		= ipv4ip($lan_ip, $mask, $hostid);
	echo "          <ip>".$ip."</ip>\n";
	echo "          <lan_port>".query("internal/start")."</lan_port>\n";
    echo "          <wan_port>".query("external/start")."</wan_port>\n";
	$protocol = map("protocol","TCP","6","UDP","17","*","257");
    echo "          <protocol>".$protocol."</protocol>\n";
    echo "          <on-off>".query("enable")."</on-off>\n";
	echo "        </client>\n";
}
?></portforwording>
