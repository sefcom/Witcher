<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

/* Start script */
fwrite("w", $START, "#!/bin/sh\n");

$cnt = query("/route6/static/count");
foreach ("/route6/static/entry")
{
	if ($InDeX > $cnt) break;

	$en		= query("enable");
	$netid	= query("network");
	$prefix	= query("prefix");
	$gw		= query("via");
	$dev	= PHYINF_getruntimeifname(query("inf"));
	$metric = query("metric");

	if ($dev=="")
	{
		$inf = cut(query("inf"), 0, "-");
		foreach ("/runtime/inf")
		{
			if (query("inet/ipv6/mode")=="LL")
			{
				if (cut(query("uid"), 0, "-")==$inf)
					$infll = query("uid");
			}
		}
		$dev	= PHYINF_getruntimeifname($infll);
	}
	
	//if ($en=="1" && $dev!="" && $netid!="" && $prefix!="" && $gw!="")
	if ($en=="1" && $netid!="" && $prefix!="" && $gw!="")
	{
		if (ipv6networkid($netid,$prefix)==$netid) $dest = $netid."/".$prefix;
		else $dest = $netid;

		if($metric!="") $mtrcmd = " metric ".$metric;
		else		$mtrcmd = "";

		if($dev!="")	$devcmd = " dev ".$dev;
		else		$devcmd = "";
		fwrite(a, $START, "ip -6 route add ".$dest." via ".$gw.$devcmd.$mtrcmd." table STATIC\n");
	}
}
fwrite(a, $START, "exit 0\n");

/* Stop script */
fwrite(w, $STOP,
	"#!/bin/sh\n".
	"ip -6 route flush table STATIC\n".
	"exit 0\n"
	);

?>
