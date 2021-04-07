<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function GENA_notify_init($shell_file, $target_php, $inf_uid, $host, $uri, $sid)
{

	$inf_path = XNODE_getpathbytarget("", "inf", "uid", $inf_uid, 0);
	if ($inf_path=="")
	{
		TRACE_debug("can't find inf_path by $inf_uid=".$inf_uid."!");
		return "";
	}
	$phyinf = PHYINF_getifname(query($inf_path."/phyinf"));
	if ($phyinf == "")
	{
		TRACE_debug("can't get phyinf by $inf_uid=".$inf_uid."!");
		return "";
	}

	$upnpmsg = query("/runtime/upnpmsg");
	if ($upnpmsg == "") $upnpmsg = "/dev/null";
	fwrite(w, $shell_file,
		"#!/bin/sh\n".
		'echo "[$0] ..." > '.$upnpmsg."\n".
		"xmldbc -P ".$target_php.
			" -V INF_UID=".$inf_uid.
			" -V HDR_URL=".$uri.
			" -V HDR_HOST=".$host.
			" -V HDR_SID=".$sid.
			" -V HDR_SEQ=0".
			" | httpc -i ".$phyinf." -d \"".$host."\" -p TCP > ".$upnpmsg."\n"
	);
	fwrite(a, $shell_file, "rm -f ".$shell_file."\n");
}

/***************************************************************/
/* construct the NOTIFY request event header */
function GENA_notify_req_event_hdr($url, $host, $content_len, $sid, $seq, $outputfile)
{
	if ($outputfile!="")
	{
		fwrite("w", $outputfile, "NOTIFY ".$url." HTTP/1.1\r\n");
		fwrite("a", $outputfile, "HOST: ".$host."\r\n");
		fwrite("a", $outputfile, "CONTENT-TYPE: text/xml\r\n");
		fwrite("a", $outputfile, "CONTENT-LENGTH: ".$content_len."\r\n");
		fwrite("a", $outputfile, "NT: upnp:event\r\n");
		fwrite("a", $outputfile, "NTS: upnp:propchange\r\n");
		fwrite("a", $outputfile, "SID: ".$sid."\r\n");
		fwrite("a", $outputfile, "SEQ: ".$seq."\r\n\r\n");
	}
	else
	{
		echo "NOTIFY ".$url." HTTP/1.1\r\n";
		echo "HOST: ".$host."\r\n";
		echo "CONTENT-TYPE: text/xml\r\n";
		echo "CONTENT-LENGTH: ".$content_len."\r\n";
		echo "NT: upnp:event\r\n";
		echo "NTS: upnp:propchange\r\n";
		echo "SID: ".$sid."\r\n";
		echo "SEQ: ".$seq."\r\n\r\n";
	}
}

function GENA_subscribe_http_resp($sid, $timeout)
{
	/* Generate HTTP header */
	echo "HTTP/1.1 200 OK\r\n";
	echo "SID: ".$sid."\r\n";
	echo "TIMEOUT: ";
	if ($timeout == 0) echo "Second-infinite";
	else echo "Second-".$timeout;
	echo "\r\n\r\n";
}

function GENA_subscribe_cleanup($node_base)
{
	$curr_time = query("/runtime/device/uptime");

	anchor($node_base);
	$count = query("subscription#");
	while ($count > 0)
	{
		$tout = query("subscription:".$count."/timeout");
		if ($tout > 0 && $tout < $curr_time) del("subscription:".$count);
		$count--;
	}
}

function GENA_subscribe_new($node_base, $host, $remote, $uri, $timeout, $shell_file, $target_php, $inf_uid)
{
	anchor($node_base);
	$count = query("subscription#");
	$found = 0;
	/* find subscription index & uuid */
	foreach ("subscription")
	{
		if (query("host")==$host && query("uri")==$uri)	{$found = $InDeX; break;}
	}
	if ($found == 0)
	{
		$index = $count + 1;
		$new_uuid = "uuid:".query("/runtime/genuuid");
	}
	else
	{
		$index = $found;
		$new_uuid = query("subscription:".$index."/uuid");
	}

	/* get timeout */
	if ($timeout==0 || $timeout=="") {$timeout = 0; $new_timeout = 0;}
	else {$new_timeout = query("/runtime/device/uptime") + $timeout;}
	/* set to nodes */
	set("subscription:".$index."/remote",	$remote);
	set("subscription:".$index."/uuid",		$new_uuid);
	set("subscription:".$index."/host",		$host);
	set("subscription:".$index."/uri",		$uri);
	set("subscription:".$index."/timeout",	$new_timeout);
	set("subscription:".$index."/seq", "1");

	GENA_subscribe_http_resp($new_uuid, $timeout);
	GENA_notify_init($shell_file, $target_php, $inf_uid, $host, $uri, $new_uuid);
}

function GENA_subscribe_sid($node_base, $sid, $timeout)
{
	anchor($node_base);
	$found = 0;
	foreach ("subscription")
	{
		if (query("uuid") == $sid) {$found = $InDeX; break;}
	}
	if ($found != 0)
	{
		/* Update timeout */
		if ($timeout == 0 || $timeout == "")
		{
			$timeout = 0;
			$new_timeout = 0;
		}
		else
		{
			$new_timeout = query("/runtime/device/uptime") + $timeout;
		}
		set("subscription:".$found."/timeout", $new_timeout);

		GENA_subscribe_http_resp($sid, $timeout);
	}
	else
	{
		echo "HTTP 412 Precondition Failed\r\n";
		echo "\r\n";
	}
}

function GENA_unsubscribe($node_base, $sid)
{
	anchor($node_base);
	$found = 0;
	foreach ("subscription")
	{
		if (query("uuid")==$sid)
		{
			$found = $InDeX;
			break;
		}
	}
	if ($found > 0)
	{
		del("subscription:".$found);
		echo "HTTP/1.1 200 OK\r\n\r\n";
	}
	else
	{
		echo "HTTP/1.1 412 Precondition Failed\r\n\r\n";
	}
}
?>
