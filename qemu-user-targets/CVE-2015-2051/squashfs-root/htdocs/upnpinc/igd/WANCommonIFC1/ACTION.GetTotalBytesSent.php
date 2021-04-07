<NewTotalBytesSent><?
include "/htdocs/phplib/xnode.php";

if (query("/runtime/device/layout")=="router")
{
	$phyinf = query(XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0)."/phyinf");
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	echo map($p."/stats/tx/bytes", "", "0");
}
else
{
	echo "0";
}
?></NewTotalBytesSent>
