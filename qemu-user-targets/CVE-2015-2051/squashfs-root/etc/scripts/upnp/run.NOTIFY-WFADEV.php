#!/bin/sh
<?/* vi: set sw=4 ts=4: */
include "/htdocs/upnpinc/gena.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/trace.php";

foreach ("/runtime/services/upnp/inf")
{
	if (query($SERVICE."/subscription#")>0)
	{
		GENA_subscribe_cleanup("/runtime/services/upnp/inf:".$InDeX."/".$SERVICE);

		$inf_uid = query("uid");
		$p = XNODE_getpathbytarget("", "inf", "uid", $inf_uid, 0);
		$phyinf = PHYINF_getifname(query($p."/phyinf"));
		$rootp = "/runtime/services/upnp/inf:".$InDeX;

		foreach ($SERVICE."/subscription")
		{
			$rootp = $rootp."/".$SERVICE;
			if ($REMOTE_ADDR != "" && $REMOTE_ADDR != query("remote")) continue;

			$seq = query("seq");
			if ($seq == "") $seq = 0;

			$host	= query("host");
			$uuid	= query("uuid");
			$uri	= query("uri");
			$temp_file = "/var/run/WFAWLANConfig-".$uuid."-payload";

			/* prepare the notify header. */
			GENA_notify_req_event_hdr($uri, $host, "", $uuid, $seq, $temp_file);

			echo "wfanotify";
			echo " -r ".$rootp;
			echo " -t ".$EVENT_TYPE;
			echo " -m ".$EVENT_MAC;
			echo " -f ".$EVENT_PAYLOAD;
			echo " >> ".$temp_file."\n";

			echo "cat ".$temp_file." | httpc -i ".$phyinf." -d \"".$host."\" -p TCP > /dev/null\n";
			echo "rm -f ".$temp_file." ".$EVENT_PAYLOAD."\n";
			$seq++;
			set("seq", $seq);
		}
	}
}
?>
