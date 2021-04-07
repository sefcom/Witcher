<module>
	<service><?=$GETCFG_SVC?></service>
	<dhcps4>
<?		echo dump(2, "/dhcps4");
?>	</dhcps4>
	<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
		if ($inf!="") { echo dump(2, $inf); }

?>	</inf>
	<inet>
		<entry>
<?
			$inet = XNODE_getpathbytarget("/inet","entry","uid",query($inf."/inet"),0);
			if ($inet!="") { echo dump(3, $inet); }

?>		</entry>
	</inet>
</module>
