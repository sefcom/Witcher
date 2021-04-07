<module>
	<service><?=$GETCFG_SVC?></service>
	<dns4>
<?		echo dump(2, "/dns4");
?>	</dns4>
	<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
		echo dump(2, $inf);
?>	</inf>
</module>
