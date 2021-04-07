<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_run_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1, 0);
?>
<dhcp_clients>
<?
foreach("/runtime/mydlink/userlist/entry")
{
	echo "<client>\n";
	echo "<ip_address>".query(ipv4addr)."</ip_address>\n";
	$mac = query(macaddr);
//	echo "<mac>".query(macaddr)."</mac> \n";
	echo "<mac>".toupper($mac)."</mac> \n";
	echo "<host_name>".query(hostname)."</host_name>\n";
	echo "<seconds_remaining>7200</seconds_remaining>\n";
	echo "<is_reservation>0</is_reservation>\n";
	echo "<learned_by>0</learned_by>\n";
	echo "</client>\n";
}
/*
foreach($path_run_lan1."/dhcps4/leases/entry"){
	echo "<client>\n";
	echo "<ip_address>".query(ipaddr)."</ip_address>\n";
	echo "<mac>".query(macaddr)."</mac> \n";
	echo "<host_name>".query(hostname)."</host_name>\n";
	echo "<seconds_remaining>".query(due_time)."</seconds_remaining>\n";
	echo "<is_reservation>0</is_reservation>\n";
	echo "<learned_by>0</learned_by>\n";
	echo "</client>\n";
}
*/
?></dhcp_clients>
