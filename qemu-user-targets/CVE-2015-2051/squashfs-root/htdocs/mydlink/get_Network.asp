<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1, 0);
$path_run_wlan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $WLAN1, 0);
$mask = query($path_run_inf_lan1."/inet/ipv4/mask");
$dhcp_enbled="0";
if (query($path_inf_lan1."/dhcps4") != "")
{
    $dhcp_enbled="1";
}
?>
<network>
	<Router_name><? echo query("/device/gw_name");?></Router_name> 
	<Router_model><? echo query("/runtime/device/modelname");?></Router_model> 
	<Router_ip><? echo query($path_run_inf_lan1."/inet/ipv4/ipaddr"); ?></Router_ip> 
	<Dhcp_sta><? echo $dhcp_enbled; ?></Dhcp_sta> 
	<Router_MAC><?echo query("/runtime/devdata/lanmac");?></Router_MAC> 
	<Router_ver><? echo query("/runtime/device/firmwareversion");?></Router_ver> 
	<Router_time><? echo query("/runtime/device/firmwarebuilddate");?></Router_time>
	<Router_lan><? echo query($path_run_inf_lan1."/dhcps4/leases/entry#"); ?></Router_lan>
	<Router_wireless><? echo query($path_run_wlan1."/media/clients/entry#"); ?></Router_wireless>
</network>

