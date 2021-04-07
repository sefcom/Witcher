#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";

$inf_base = XNODE_getpathbytarget("/runtime", "inf", "uid", $INF, 0);
$leases_base = $inf_base."/dhcps4/leases";
echo "/usr/sbin/updateleases -a -f ".$FILE." -p ".$leases_base."\n";
?>
