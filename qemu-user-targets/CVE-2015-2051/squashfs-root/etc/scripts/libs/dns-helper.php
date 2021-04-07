#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";

function add_target($base, $svr, $target)
{
	foreach ($base."/svrlist/server")
	{
		/* Need no routing rule if the interface is the default route. */
		if ("1" == query("defaultroute")) continue;
		/* Not the server we are looking for, try next. */
		if ($svr != query("ipaddr")) continue;

		/* The resolved IP address will be saved under
		 * /runtime/services/dnsmasq/svrlist/server/target.
		 * They are the target IP address of the domain routing rules
		 * and will be used for removing the rules from the routing table.
		 * We also save a copy under
		 * /runtime/inf:#/dnscache/target to preserve the old domain routing
		 * targets when the interface is going down and up. The routing rules
		 * for the old targets will be added in service - 'ROUTE.DOMAIN' when
		 * the interface is comming up. */
		$inf = query("inf");
		$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
		if ($infp == "") return;
		foreach ($infp."/dnscache/target")	if ($VaLuE == $target) return;
		foreach ("target")					if ($VaLuE == $target) return;

		/* We only add rules for new target and save them in both places. */
		$addrtype = query($infp."/inet/addrtype");
		if (query($infp."/inet/".$addrtype."/valid")!="1") return;
		if		($addrtype=="ipv4") $gw = query($infp."/inet/ipv4/gateway");
		else if	($addrtype=="ppp4") $gw = query($infp."/inet/ppp4/peer");
		/* Got gateway, add routing command */
		add("target", $target);
		add($infp."/dnscache/target", $target);
		echo 'echo "DOMAINROUTE: '.$target.' via '.$gw.' ('.$inf.') ..." > /dev/console\n';
		echo 'ip route add '.$target.' via '.$gw.' table DOMAIN\n';
		return;
	}
}

function flush_route($base)
{
	foreach ($base."/svrlist/server")
	{
		if ("1" == query("defaultroute")) continue;
		/* Only remove the domain routing rules which added by us (DNS service). */
		foreach ("target")
		{
			echo 'echo "DOMAINROUTE: remove route to '.$VaLuE.' ..." > /dev/console\n';
			echo 'ip route del '.$VaLuE.' table DOMAIN\n';
		}
		$i = query("target#");
		while ($i>0) { del("target"); $i--; }
	}
}

function flush_all($base)
{
	flush_route($base);
	del($base."/svrlist");
}

$dnsbase = "/runtime/services/dnsmasq";
if		($ACTION=="get_addrs")	add_target($dnsbase, $DNS, $TARGET);
else if	($ACTION=="flushroute")	flush_route($dnsbase);
else if	($ACTION=="flush")		flush_all($dnsbase);
?>
exit 0
