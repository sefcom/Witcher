<module>
	<service><?=$GETCFG_SVC?></service>
	<inf>
		<upnp><count>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
		if ($inf!="") echo query($inf."/upnp/count");
?>
		</count></upnp>
	</inf>
</module>
