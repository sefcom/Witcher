<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<log>
			<sysact>
<?
$logfile = "/htdocs/web/docs/rg.log";
fwrite("w", $logfile, "[System]\n");
foreach("/runtime/log/sysact/entry")
{
	$msg = get("x", "/runtime/log/sysact/entry:".$InDeX."/message");
	$check = strstr($msg, "Web site (null)");
	if(isempty($check)==1)
	{//ignore messages which doesn't have HTTP type
	echo "\t\t\t\t<entry>\n";
	$time = get("TIME.ASCTIME", "/runtime/log/sysact/entry:".$InDeX."/time");
	
	echo "\t\t\t\t\t<time>".$time."</time>\n";
	echo "\t\t\t\t\t<message>".$msg."</message>\n";
	echo "\t\t\t\t</entry>\n";
	fwrite("a", $logfile, $time."\t");
	fwrite("a", $logfile, $msg."\n");
	}
}
?>
			</sysact>
			<attack>
<?
fwrite("a", $logfile, "\n[Attack]\n");
foreach("/runtime/log/attack/entry")
{
	echo "\t\t\t\t<entry>\n";
	$time = get("TIME.ASCTIME", "/runtime/log/attack/entry:".$InDeX."/time");
	$msg = get("x", "/runtime/log/attack/entry:".$InDeX."/message");
	echo "\t\t\t\t\t<time>".$time."</time>\n";
	echo "\t\t\t\t\t<message>".$msg."</message>\n";
	echo "\t\t\t\t</entry>\n";
	fwrite("a", $logfile, $time."\t");
	fwrite("a", $logfile, $msg."\n");
}
?>
			</attack>
			<drop>
<?
fwrite("a", $logfile, "\n[Drop]\n");
foreach("/runtime/log/drop/entry")
{
	echo "\t\t\t\t<entry>\n";
	$time = get("TIME.ASCTIME", "/runtime/log/drop/entry:".$InDeX."/time");
	$msg = get("x", "/runtime/log/drop/entry:".$InDeX."/message");
	echo "\t\t\t\t\t<time>".$time."</time>\n";
	echo "\t\t\t\t\t<message>".$msg."</message>\n";
	echo "\t\t\t\t</entry>\n";
	fwrite("a", $logfile, $time."\t");
	fwrite("a", $logfile, $msg."\n");
}
?>
			</drop>
		</log>
	</runtime>
</module>
