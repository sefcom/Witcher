<module>
	<service><?=$GETCFG_SVC?></service>
	<inet>
<?		echo dump(2, "/inet");
?>	</inet>
	<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
		if ($inf!="") echo dump(2, $inf);
?>	</inf>
</module>
