<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
<?
include "/htdocs/phplib/xnode.php";
$inf = cut($GETCFG_SVC,2,".");
$path = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
if ($path!="")
{
	/* del dhcp clients that expire or update expire time */
	$curr_uptime = query("/runtime/device/uptime");
	$entry_path = $path."/dhcps4/leases/entry";
	$cnt = query($entry_path."#");
	while ($cnt > 0) 
	{
		$due_time = query($entry_path.":".$cnt."/due_time" );
		if ( $due_time != "" )
		{
			/* real expire */
			if ( $due_time <= $curr_uptime )
			{
				del($entry_path.":".$cnt); 
			}
			/* update expire time for web reference */
			else
			{
				set($entry_path.":".$cnt."/expire", $due_time - $curr_uptime );
			}
		}
		$cnt--; 
	}	
	
	echo "\t\t<inf>\n";
	echo dump(3, $path);
	echo "\t\t</inf>\n";
}
?>	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
