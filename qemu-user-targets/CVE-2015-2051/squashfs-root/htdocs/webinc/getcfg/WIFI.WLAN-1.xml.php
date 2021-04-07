<module>
	<service><?=$GETCFG_SVC?></service>
	<wifi>
<?		echo dump(2, "/wifi");
?>	</wifi>
	<phyinf>
<?
		include "/htdocs/phplib/xnode.php";
		$inf = XNODE_getpathbytarget("", "phyinf", "uid", cut($GETCFG_SVC,1,"."), 0);
		if ($inf!="") echo dump(2, $inf);
?>	</phyinf>
<?
foreach ("/phyinf")
{
    if (cut(query("/phyinf:".$InDeX."/uid"), 0, "-") == "WDS")
	    {
	        echo "\t<phyinf>\n".dump(2, "/phyinf:".$InDeX)."\t</phyinf>\n";
	    }
}
?>
</module>
