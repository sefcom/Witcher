<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$type = fread("e", "/etc/config/nat");
$cnt = query("/nat/count");
foreach ("/nat/entry")
{
	if ($InDeX>$cnt) break;

	$UID = query("uid");
	$netsniper = query("netsniper/enable");
	if ($netsniper == 1)
	{
		fwrite("a",$START,"echo 1024 > /proc/sys/net/ipv4/ip_personality_sport\n");
		fwrite("a",$START,"echo 1 > /proc/sys/net/ipv4/ip_personality_enable\n");
	}
	else if($netsniper == 0)
	{
		fwrite("a",$START,"echo 0 > /proc/sys/net/ipv4/ip_personality_sport\n");
		fwrite("a",$START,"echo 0 > /proc/sys/net/ipv4/ip_personality_enable\n");
	}
	if ($type=="Daniel's NAT")
	{
		fwrite("a",$START,
			"iptables -t nat -F PRE.MASQ.".$UID."\n".
			"iptables -t nat -F PST.MASQ.".$UID."\n"
			);
		fwrite("a",$STOP,
			"iptables -t nat -F PRE.MASQ.".$UID."\n".
			"iptables -t nat -F PST.MASQ.".$UID."\n"
			);

		/* Select Full/Restricted Cone type by DNAT. */
		$CMD  = "iptables -t nat -A PRE.MASQ.".$UID." -j DNAT --to-destination ";
		$TYPE = query("type");
		if ($TYPE=="RESTRICTED") fwrite("a",$START,$CMD."0.0.0.0\n");
		else if ($TYPE=="FULL")  fwrite("a",$START,$CMD."255.255.255.255\n");

		/* The default NAT is Port Restricted Cone NAT. */
		if ($netsniper == 1)
		{
			$MASQ_PORT = " --to-ports 1024-65535";
			fwrite("a",$START,"iptables -t nat -A PST.MASQ.".$UID." -p tcp -j MASQUERADE".$MASQ_PORT."\n");
			fwrite("a",$START,"iptables -t nat -A PST.MASQ.".$UID." -p udp -j MASQUERADE".$MASQ_PORT."\n");
		}
		fwrite("a",$START,"iptables -t nat -A PST.MASQ.".$UID." -j MASQUERADE\n");
	}
	else
	{
		fwrite("a",$START, "iptables -t nat -F MASQ.".$UID."\n");
		fwrite("a",$STOP,  "iptables -t nat -F MASQ.".$UID."\n");

		$TYPE = query("type");
		if		($TYPE=="MASQUERADE")		$target = "MASQUERADE";
		else if	($TYPE=="PORTRESTRICTED")	$target = "STUN --type 1";
		else if ($TYPE=="RESTRICTED")		$target = "STUN --type 2";
		else if ($TYPE=="FULL")				$target = "STUN --type 3";
		else if	($TYPE=="SYMMETRIC")		$target = "STUN --type 4";
		else
		{
			fwrite("a",$START, "# Unsupported Cone : ".$TYPE."\n");
			fwrite("a",$START, "exit 9\n");
			exit;
		}
		if ($netsniper == 1)
		{
			$MASQ_PORT = ":1024-65535";
			fwrite("a",$START,"iptables -t nat -A MASQ.".$UID." -p tcp -j ".$target.$MASQ_PORT."\n");
			fwrite("a",$START,"iptables -t nat -A MASQ.".$UID." -p udp -j ".$target.$MASQ_PORT."\n");
		}
		fwrite("a",$START,"iptables -t nat -A MASQ.".$UID." -j ".$target."\n");
	}
}
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP, "exit 0\n");
?>
