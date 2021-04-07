<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}

/****************************************************************/

function ipv6_child($child)
{
	/* Get the config */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $child, 0);
	if ($infp=="") { echo "# ".$child." is not found !!!\n"; return; }
	$phyinf = query($infp."/phyinf");
	$defrt	= query($infp."/defaultroute");

	/* Create the runtime nodes. */
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $child, 1);
	set($stsp."/phyinf", $phyinf);
	set($stsp."/defaultroute", $defrt);

	/* Get the config. */
	$ipaddr = XNODE_get_var($child."_IPADDR");
	$prefix = XNODE_get_var($child."_PREFIX");
	$devnam = PHYINF_getphyinf($child);
	$phyinfv = XNODE_get_var($child."_PHYINF");
	if($phyinfv!="")
	{
		$phyinf = $phyinfv;
		$devnam = PHYINF_getifname($phyinf);
		set($stsp."/phyinf", $phyinf);
	}

	/* Get dhcp-pd config. */
	$pdnetwork = XNODE_get_var($child."_PDNETWORK");
	$pdprefix = XNODE_get_var($child."_PDPREFIX");
	//$enablepd = query($stsp."/dhcps6/pd/enable");
	echo "# pdnetwork :".$pdnetwork."\n";
	echo "# pdprefix :".$pdprefix."\n";

	if($pdnetwork!="" && $pdprefix!="")
	{
		set($stsp."/dhcps6/pd/network", $pdnetwork);
		set($stsp."/dhcps6/pd/prefix", $pdprefix);
	}

	$pdplft = XNODE_get_var($child."_PDPLFT");
	$pdvlft = XNODE_get_var($child."_PDVLFT");
	echo "# pdplft :".$pdplft."\n";
	echo "# pdvlft :".$pdvlft."\n";

	if($pdplft!="")
	{
		set($stsp."/dhcps6/pd/preferlft", $pdplft);
	}
	if($pdvlft!="")
	{
		set($stsp."/dhcps6/pd/validlft",  $pdvlft);
	}

	/* Clear the variables. */
/*
	XNODE_del_var($child."_IPADDR");
	XNODE_del_var($child."_PREFIX");
	XNODE_del_var($child."_ADDRTYPE");
	XNODE_del_var($child."_PHYINF");
	XNODE_del_var($child."_PDNETWORK");
	XNODE_del_var($child."_PDPREFIX");
	XNODE_del_var($child."_PDPLFT");
	XNODE_del_var($child."_PDVLFT");
*/
	/* enable IPv6 */
	fwrite(w, "/proc/sys/net/ipv6/conf/".$devnam."/disable_ipv6", 0);
	
	
	$path_eth = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$val_eth = query($path_eth. "/ipv6/link/ipaddr");
	
	if ($path_eth=="")	return;
			
	else
	{	
	  if ($val_eth!="")
    {
  	 stopcmd( "phpsh /etc/scripts/IPV6.INET.php ACTION=DETACH INF=".$child);
     startcmd("phpsh /etc/scripts/IPV6.INET.php ACTION=ATTACH INF=".$child.
			" MODE=CHILD DEVNAM=".$devnam." IPADDR=".$ipaddr." PREFIX=".$prefix);
    }
    else 
    return 0;
	    
  }
	

	/* Start/Stop scripts */
	//stopcmd( "phpsh /etc/scripts/IPV6.INET.php ACTION=DETACH INF=".$child);
	//startcmd("phpsh /etc/scripts/IPV6.INET.php ACTION=ATTACH INF=".$child.
	//			" MODE=CHILD DEVNAM=".$devnam." IPADDR=".$ipaddr." PREFIX=".$prefix);
	
	/* record */
	stopcmd( "rm -f /var/run/CHILD.".$child.".UP");
	startcmd("echo 1 > /var/run/CHILD.".$child.".UP");
	
	/* delay 2s to wait ipv6 address take effect before HTTP service */
	//startcmd("sleep 2");
}

/****************************************************************/
startcmd("# CHILD_INFNAME = [".$CHILD_INFNAME."]");
stopcmd( "# CHILD_INFNAME = [".$CHILD_INFNAME."]");

/* These parameter should be valid. */
$inf = $CHILD_INFNAME;
$addrtype = XNODE_get_var($inf."_ADDRTYPE");
if ($addrtype=="ipv6") ipv6_child($inf);
else
{
	startcmd("# INET.CHILD only support IPv6 only, ".$addrtype." is not supported now.");
	stopcmd( "# INET.CHILD only support IPv6 only, ".$addrtype." is not supported now.");
}
?>
