#!/bin/sh
<?/* vi: set sw=4 ts=4: */
include "/htdocs/upnpinc/gena.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

foreach ("/runtime/services/upnp/inf")
{
	if (query($SERVICE."/subscription#")>0)
	{
		GENA_subscribe_cleanup("/runtime/services/upnp/inf:".$InDeX."/".$SERVICE);

		$inf_uid = query("uid");
		$p = XNODE_getpathbytarget("", "inf", "uid", $inf_uid, 0);
		$phyinf = PHYINF_getifname(query($p."/phyinf"));

		foreach ($SERVICE."/subscription")
		{
			$seq = query("seq");
			if ($seq == "") $seq = 0;
			$host = query("host");
			echo "xmldbc -P /htdocs/upnp/".$TARGET_PHP;
			echo " -V INF_UID=".$inf_uid;
			echo " -V HDR_URL=".query("uri");
			echo " -V HDR_HOST=".$host;
			echo " -V HDR_SID=".query("uuid");
			echo " -V HDR_SEQ=".$seq;
			echo " | httpc -i ".$phyinf." -d \"".$host."\" -p TCP > /dev/null\n";
			$seq++;
			set("seq", $seq);
		}
	}
}
?>
