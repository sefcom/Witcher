<?
	/* This is an include file, used by IPTPFWD.php and IPTPORTT.php.
	 * The file add the VSVR, PFWD, and PORTT  chains to PFWD.$UID, 
	 * So that the stream destined to the WAN's IP address from the LAN hosts can be correctly redirected to the targeted LAN host, 
	 * when there are rules in Virtual Server, Port Forward, or Port Trigger.
	 */
	$CHAIN="DNAT.VSVR.".$UID;
	if (XNODE_get_var($CHAIN.".USED")>0)
		fwrite("a", $START, "iptables -t nat -A PFWD.".$UID." -j ".$CHAIN."\n");
	$CHAIN="DNAT.PFWD.".$UID;
	if (XNODE_get_var($CHAIN.".USED")>0)
		fwrite("a", $START, "iptables -t nat -A PFWD.".$UID." -j ".$CHAIN."\n");
	$CHAIN="DNAT.PORTT.".$UID;
	if (XNODE_get_var("PORTT.".$UID.".USED")>0)
		fwrite("a", $START, "iptables -t nat -A PFWD.".$UID." -j ".$CHAIN."\n");
?>
