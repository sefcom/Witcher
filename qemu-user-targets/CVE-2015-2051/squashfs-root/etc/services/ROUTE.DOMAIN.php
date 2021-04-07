<?
/* This service is for domain name routing.
 * This function is not implemented in service - DNS (DNSMASQ).
 * Since event UPDATERESOLV will restart the service DNS,
 * so we do nothing here. */
fwrite(w,$START,"#!/bin/sh\n");
fwrite(w,$STOP, "#!/bin/sh\n");

foreach ("/runtime/inf")
{
	$addrtype = query("inet/addrtype");
	if (query("inet/".$addrtype."/valid")!="1") continue;
	if		($addrtype=="ipv4") $gw = query("inet/ipv4/gateway");
	else if	($addrtype=="ppp4") $gw = query("inet/ppp4/peer");
	else continue;

	/* We save a copy of the DNS cache under
	 * '/runtime/inf:#/dnscache/target'.
	 * There are the preserved old domain routing targets. The user's
	 * browser may cache the DNS and won't send the DNS request again,
	 * so the preserved rules must be loaded for the domain routing.
	 * The routing rules are added here when the interface is comming up
	 * and will be removed when the interface is going down.
	 * The routing target of the runtime DNS query will be added/removed
	 * by the DNS service. */
	foreach ("dnscache/target")
		fwrite(a, $START, 'ip route add '.$VaLuE.' via '.$gw.' table DOMAIN\n');
}

fwrite(a,$START,"exit 0\n");

fwrite(a,$STOP, "ip route flush table DOMAIN\n");
fwrite(a,$STOP, "exit 0\n");
?>
