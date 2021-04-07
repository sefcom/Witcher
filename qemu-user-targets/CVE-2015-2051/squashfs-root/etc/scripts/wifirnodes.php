#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";

$p		= XNODE_getpathbytarget("","phyinf", "uid", $UID, 0);
$w		= XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
$stsp	= XNODE_getpathbytarget("/runtime", "phyinf", "uid", $UID, 0);

if ($p!="" && $stsp!="" || $w!="")
{
	set($stsp."/media/wifi/uid",		query($w."/uid"));
	set($stsp."/media/wifi/ssid",		query($w."/ssid"));
	set($stsp."/media/wifi/authtype",	query($w."/authtype"));
	set($stsp."/media/wifi/encrtype",	query($w."/encrtype"));
	set($stsp."/media/wps/enable",		query($w."/wps/enable"));
	set($stsp."/media/wps/configured",	query($w."/wps/configured"));
}
?>
exit 0
