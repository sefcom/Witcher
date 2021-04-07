<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
$wan1_phyinf = query($path_run_inf_wan1."/phyinf");
$path_run_wan1_phyinf = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyinf, 0);
/* Get address family & IP address */
$atype = query($path_run_inf_wan1."/inet/addrtype"); 
if      ($atype=="ipv4") {$ipaddr=query($path_run_inf_wan1."/inet/ipv4/ipaddr");}
else if ($atype=="ppp4") {$ipaddr=query($path_run_inf_wan1."/inet/ppp4/local");}
else if ($atype=="ipv6") {$ipaddr=query($path_run_inf_wan1."/inet/ipv6/ipaddr");}
else if ($atype=="ppp6") {$ipaddr=query($path_run_inf_wan1."/inet/ppp6/local");} 

$rx_0 = query($path_run_wan1_phyinf."/stats/rx/bytes");
$tx_0 = query($path_run_wan1_phyinf."/stats/tx/bytes");
setattr("/runtime/sleep",  "get", "sleep 1"); 
query("/runtime/sleep");
del("/runtime/sleep");
$rx_1 = query($path_run_wan1_phyinf."/stats/rx/bytes");
$tx_1 = query($path_run_wan1_phyinf."/stats/tx/bytes");
$rx = $rx_1 - $rx_0;
$tx = $tx_1 - $tx_0;
$wan_st=query($path_run_wan1_phyinf."/linkstatus");
if($wan_st !="")
{ $wan_mode=1;}
else
{ $wan_mode=0;}
?>
<Internet>
	<Ip_address><? echo $ipaddr; ?></Ip_address> 
	<dns><? echo query($path_run_inf_wan1."/inet/ipv4/dns"); ?></dns> 
	<mac><? echo query("/runtime/devdata/wanmac"); ?></mac> 
	<wan_mode><? echo $wan_mode; ?></wan_mode> 
	<down_value><? echo  $rx; ?></down_value> 
	<up_value><? echo $tx; ?></up_value> 
</Internet>

