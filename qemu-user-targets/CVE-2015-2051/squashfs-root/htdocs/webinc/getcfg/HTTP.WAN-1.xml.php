<module><?
include "/htdocs/phplib/xnode.php";
$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
?>
	<service><?=$GETCFG_SVC?></service>
	<inf>
		<web><?echo query($infp."/web");?></web>
		<weballow>
<?			echo dump(3, $infp."/weballow");
?>		</weballow>
	</inf>
	<ACTIVATE>ignore</ACTIVATE>
</module>
