<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
anchor($path_inf_wan1);
$remoteMng = query("web");
if( $remoteMng != "" && $remoteMng != "0" ) { $remoteMngStr = "true"; } else { $remoteMngStr = "false"; }
$remotePort = query("web");
?>
<divide><? echo $remoteMngStr; ?><divide><? echo $remotePort; ?><option>
