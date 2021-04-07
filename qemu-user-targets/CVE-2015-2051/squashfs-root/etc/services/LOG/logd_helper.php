<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/dumplog.php";
include "/htdocs/phplib/trace.php";

/* Check <sys/syslog.h> & <elbox/include/asyslog.h> for the definition of facility. */

//if (query("/device/log/email/to")!="" && query("/device/log/email/from")!="" &&	query("/device/log/email/smtp/server")!="")
if (query("/device/log/email/logfull")=="1")
{
	$archive = "/runtime/logfull";
}
else
{
	$archive = "";
}

if ($FACILITY==23) // mydlink event
{
	$base   = "/runtime/log/mydlink";
	$type   = "mydlink";
	$max    = 50;
}	
else if ($FACILITY==26)	// attack
{
	$base	= "/runtime/log/attack";
	$type	= "attack";
	$max	= 50;
}
else if ($FACILITY==27)	// drop
{
	$base	= "/runtime/log/drop";
	$type	= "drop";
	$max	= 50;
}
else
{
	$base	= "/runtime/log/sysact";
	//if (query("/device/log/email/to")!="")	$archive = "/runtime/logarchive";
	//else									$archive = "";
	$type	= "sysact";
	$max	= 400;
}

$cnt = query($base."/entry#");
if ($cnt=="") $cnt=0;
if ($cnt >= $max)
{
	if ($archive != "")
	{
		set($archive."/type", $type);
		$archive = $archive."/".$type;	
		del($archive);
		set($archive, "");
		movc($base, $archive);
		event("LOGFULL");
	}
	else
	{
		while ($cnt >= $max)
		{
			$cnt--;
			del($base."/entry:1");
		}
	}
}
$cnt = query($base."/entry#");
if ($cnt=="") $cnt=0;
$cnt++;
$runtime_node = "/runtime/services/globals";
set($base."/entry:".$cnt."/time", $TIME);
set($base."/entry:".$cnt."/message", $TEXT);

$HOSTIP = query("/device/log/remote/ipv4/ipaddr");
/*set($runtime_node."/name",SYSLOG_NODE);
set($runtime_node."/value",$TEXT);i*/
$enable = query("/device/log/remote/enable");
if ($enable =="1")
{
	fwrite("w+","/var/run/syslog_msg.sh","#!/bin/sh\n");
	fwrite("a","/var/run/syslog_msg.sh","/usr/sbin/syslog_msg \"".$HOSTIP."\" \"".$TEXT."\" \n");
	event("SYSLOG_MSG");
}
?>
