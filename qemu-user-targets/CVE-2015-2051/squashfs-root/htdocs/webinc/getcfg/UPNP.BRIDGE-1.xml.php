<module>
	<service><?=$GETCFG_SVC?></service>
	<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
		if ($inf!="") echo dump(2, $inf);  
?>
	</inf>
</module>
