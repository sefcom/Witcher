<module>
	<service><?=$GETCFG_SVC?></service>
<?
foreach ("/phyinf") echo "\t<phyinf>\n".dump(2, "/phyinf:".$InDeX)."\t</phyinf>\n";
?>	<inf>
<?
include "/htdocs/phplib/xnode.php";
$inf = XNODE_getpathbytarget("", "inf", "uid", cut($GETCFG_SVC,1,"."), 0);
if ($inf != "") echo dump(2,$inf);
?>	</inf>
</module>
