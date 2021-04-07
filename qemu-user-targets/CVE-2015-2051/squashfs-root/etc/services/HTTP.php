<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/phyinf.php";

fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");

$httpd_conf = "/var/run/httpd.conf";

/* start script */
if ( isdir("/htdocs/widget") == 1) // For widget By Joseph
{
	foreach("/runtime/services/http/server")
	{
		if(query("mode")=="HTTP")
			set("widget",	1);
	}
	fwrite("a",$START, "xmldbc -x /runtime/widget/salt \"get:widget -s\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/widgetv2/logincheck  \"get:widget -a /var/run/password -v\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/time/date \"get:date +%m/%d/%Y\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/time/time \"get:date +%T\"\n");
}
fwrite("a",$START, "xmldbc -P /etc/services/HTTP/httpcfg.php > ".$httpd_conf."\n");
fwrite("a",$START, "event PREFWUPDATE add /etc/scripts/prefwupdate.sh\n");
fwrite("a",$START, "httpd -f ".$httpd_conf."\n");
fwrite("a",$START, "event HTTP.UP\n");
fwrite("a",$START, "exit 0\n");

/* stop script */
fwrite("a",$STOP, "killall httpd\n");
fwrite("a",$STOP, "rm -f ".$httpd_conf."\n");
fwrite("a",$STOP, "event HTTP.DOWN\n");
fwrite("a",$STOP, "exit 0\n");
?>
