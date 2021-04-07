<module>
	<service><?=$GETCFG_SVC?></service>
	<ddns4>
<?		echo dump(2, "/ddns4");
?>	</ddns4>
	<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
		if($inf != "")	echo dump(2, $inf);
?>	</inf>
</module>
