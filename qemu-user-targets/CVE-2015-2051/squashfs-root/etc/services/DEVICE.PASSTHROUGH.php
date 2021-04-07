<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function getphyinf($inf)
{
	$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	if ($infp == "") return "";
	$phyinf = query($infp."/phyinf");
	return PHYINF_getifname($phyinf);
}

fwrite(w, $STOP,
	'#!/bin/sh\n'.
	'echo "null,null" > /proc/pthrough/ipv6\n'.
	'echo "null,null" > /proc/pthrough/pppoe\n'
	);

fwrite(w, $START, "#!/bin/sh\n");
$wan = getphyinf("WAN-1");
$lan = getphyinf("LAN-1");
$layout = query("/runtime/device/layout");
if ($layout == "router" && $wan!="" && $lan!="")
{
	$cmd = '"'.$lan.','.$wan.'"';
	anchor("/device/passthrough");
	if (query("ipv6")=="1")  $ipv6cmd = $cmd; else $ipv6cmd = "";
	if (query("pppoe")=="1") $ppoecmd = $cmd; else $ppoecmd = "";
	fwrite(a,$START,'echo '.$ipv6cmd.' > /proc/pthrough/ipv6\n');
	fwrite(a,$START,'echo '.$ppoecmd.' > /proc/pthrough/pppoe\n');
}

/* restart IPT.LAN-{#} to add or remove the rules for VPN passthrough in the chain FWD.LAN-{#}. */
$i = 1;
while ($i>0)
{
	$ifname = "LAN-".$i;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $i=0; break; }
	fwrite("a",$_GLOBALS["START"], "service IPT.".$ifname." restart\n");
	fwrite("a",$_GLOBALS["STOP"],  "service IPT.".$ifname." restart\n");
	$i++;
}
fwrite(a, $START,'exit 0\n');
fwrite(a, $STOP, 'exit 0\n');
?>
