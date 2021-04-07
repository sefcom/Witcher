<?
include "/htdocs/phplib/xnode.php";

fwrite(w,$START,'#!/bin/sh\n');
fwrite(w,$STOP, '#!/bin/sh\n');

$cnt = query("/route/destination/count");
if ($cnt=="") $cnt=0;
foreach ("/route/destination/entry")
{
	/* entry count */
	if ($InDeX > $cnt) break;

	/* Skip if disabled */
	if (query("enable")!="1") continue;
	/* The interface must be up to add the route. */
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", query("inf"), 0);
	if ($infp == "") continue;
	/* Get the gateway, we use 'via' in the routing rule. */
	$addrtype = query($infp."/inet/addrtype");
	if (query($infp."/inet/".$addrtype."/valid")!="1") continue;
	if		($addrtype == "ipv4") $gateway = query($infp."/inet/ipv4/gateway");
	else if	($addrtype == "ppp4") $gateway = query($infp."/inet/ppp4/peer");
	else continue;
	/* Generate the rule command */
	$dest = query("network").'/'.query("mask");
	fwrite(a,$START,'ip route add '.$dest.' via '.$gateway.' table DEST\n');
}

fwrite(a,$START,'exit 0\n');

fwrite(a,$STOP, 'ip route flush table DEST\n');
fwrite(a,$STOP, 'exit 0\n');

?>
