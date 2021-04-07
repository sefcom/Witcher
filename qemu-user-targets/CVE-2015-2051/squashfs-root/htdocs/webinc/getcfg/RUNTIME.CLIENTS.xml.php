<?
/*
 * We collect DHCP leases & wireless client list in this getcfg.
 * GUI will handle these info easier & quicker.
 */
include "/htdocs/phplib/xnode.php";

?><module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
<?
/* DHCP */
foreach ("/runtime/inf")
{
	$p = XNODE_getpathbytarget("", "inf", "uid", query("uid"), 0);
	$dhcps4 = query($p."/dhcps4");
	$dhcps6 = query($p."/dhcps6");

	if ($dhcps4!="" || $dhcps6!="")
	{
		echo "\t\t<inf>\n";
		echo "\t\t\t<uid>".query("uid")."</uid>\n";	
		echo "\t\t\t<phyinf>".query("phyinf")."</phyinf>\n";
		if ($dhcps4!="")	echo "\t\t\t<dhcps4>\n".dump(4, "dhcps4")."\t\t\t</dhcps4>\n";
		if ($dhcps6!="")	echo "\t\t\t<dhcps6>\n".dump(4, "dhcps6")."\t\t\t</dhcps6>\n";
		echo "\t\t</inf>\n";
	}
}
/* wireless client list */
foreach ("/runtime/phyinf")
{
	echo "\t\t<phyinf>\n";
	echo "\t\t\t<uid>".query("uid")."</uid>\n";
	echo "\t\t\t<type>".query("type")."</type>\n";
	if (query("type")=="wifi")
	{
		echo "\t\t\t<media>\n";
		echo "\t\t\t\t<clients>\n".dump(5, "media/clients")."\t\t\t\t</clients>\n";
		echo "\t\t\t</media>\n";
	}
	echo "\t\t\t<bridge>\n".dump(4, "bridge")."\t\t\t</bridge>\n";
	echo "\t\t</phyinf>\n";
}
/* wireless neighbor */
	echo "\t\t<neighbor>\n";
	echo "\t\t\t<wifi>\n".dump(4, "/runtime/neighbor/wifi")."\t\t\t</wifi>\n";
	echo "\t\t</neighbor>\n";
?>	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
