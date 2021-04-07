<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

$enable_rip = query("/route6/dynamic/rip");
$conf_base = "/var/run";
$zebra_conf = $conf_base."/zebra.conf";
$ripng_conf = $conf_base."/ripng.conf";
$log_base = "/var/log";
$zebra_log = $log_base."/zebra.log";
$ripng_log = $log_base."/ripng.log";

/* Create zebra config file */
$hostname = query("/device/hostname");
fwrite("w", $zebra_conf,
		"hostname ".$hostname."\n!\n");

/* Create ripng config file */
fwrite("w", $ripng_conf,
		"hostname ".$hostname."\n!\n".
		"router ripng\n");

foreach ("/runtime/inf")
{
	$uid = query("uid");
	$addrtype = query("inet/addrtype");
	if ($addrtype == "ipv6")
	{
		$dev = PHYINF_getruntimeifname($uid);
		$ipaddr = query("inet/ipv6/ipaddr");
		$prefix = query("inet/ipv6/prefix");
		if ($dev == "" || $ipaddr == "" || $prefix == "") continue;

		$isLL = tolower(cut($ipaddr, 0, ':'));
		if ($isLL == "fe80") continue;

		/* Add dev info. into zebra config file */
		fwrite("a", $zebra_conf,
				"interface ".$dev."\n".
				"link-detect\n".
				"ipv6 address ".$ipaddr."/".$prefix."\n".
				"ipv6 nd suppress-ra\n!\n");

		/* Add dev info. into ripng config file */
		fwrite("a", $ripng_conf,
				"network ".$dev."\n");
	}
}

fwrite("a", $zebra_conf,
		"interface lo\n".
		"ipv6 forwarding\n"
	  );

fwrite("a", $ripng_conf,
		"redistribute connected\n".
		"redistribute static\n".
		"redistribute kernel\n"
	  );

/* Start script */
fwrite("w", $START, "#!/bin/sh\n");
if ($enable_rip == 1)
{
	fwrite("a", $START,
			"if [ -f ".$zebra_conf." ]; then\n".
			"	zebra -f ".$zebra_conf." -d;\n".
			"fi\n"
		  );
	fwrite("a", $START,
			"if [ -f ".$ripng_conf." ]; then\n".
			"	ripngd -f ".$ripng_conf." -d;\n".
			"fi\n"
		  );
}
fwrite("a", $START, "exit 0\n");

/* Stop script */
fwrite("w", $STOP, "#!/bin/sh\n");
fwrite("a", $STOP,
		"/etc/scripts/killpid.sh /var/run/zebra.pid\n".
		"if [ -f ".$zebra_conf." ]; then\n".
		"	rm -f ".$zebra_conf.";\n".
		"fi\n".
		"/etc/scripts/killpid.sh /var/run/ripngd.pid\n".
		"if [ -f ".$ripng_conf." ]; then\n".
		"	rm -f ".$ripng_conf.";\n".
		"fi\n".
		"exit 0\n"
	  );
?>
