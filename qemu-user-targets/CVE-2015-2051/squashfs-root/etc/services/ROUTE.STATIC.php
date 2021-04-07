<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

$cnt = query("/route/static/count");
foreach ("/route/static/entry")
{
	if ($InDeX > $cnt) break;

	$en	  = query("enable");
	$netid= query("network");
	$mask = query("mask");
	$gw   = query("via");
	$dev  = PHYINF_getruntimeifname(query("inf"));
	$metric = query("metric");

	if ($en=="1" && $dev!="" && $netid!="" && $mask!="" )
	{
		if (ipv4networkid($netid,$mask)==$netid) $dest=$netid."/".$mask;
		else $dest=$netid;
		if ( $gw == "" )
		{

			$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", query("inf"), 0);
			if ($infp == "") {
			continue;
			}
			/* Get the gateway, we use 'via' in the routing rule. */
			$addrtype = query($infp."/inet/addrtype");
			if (query($infp."/inet/".$addrtype."/valid")!="1"){ 
			continue;}
			if		($addrtype == "ipv4") $gw = query($infp."/inet/ipv4/gateway");
			else if	($addrtype == "ppp4") $gw = query($infp."/inet/ppp4/peer");
			else 
			{
			  continue;
			}
		/*	fwrite("a", $START, "ip route add ".$dest." dev ".$dev." table STATIC\n");*/
		}

		if( $metric!="" )
		{
			fwrite("a", $START, "ip route add ".$dest." via ".$gw." dev ".$dev." metric ".$metric." table STATIC\n");
		}
		else
		{
			fwrite("a", $START, "ip route add ".$dest." via ".$gw." dev ".$dev." table STATIC\n");
		}
	}
}
fwrite("a", $START, 'exit 0\n');

fwrite("a", $STOP, 'ip route flush table STATIC\n');
fwrite("a", $STOP, 'exit 0\n');
?>
