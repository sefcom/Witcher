<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
<?
include "/htdocs/phplib/xnode.php";
$inf  = cut($GETCFG_SVC, 2, ".");
$path = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $inf, 0);
if ($path!="")
{
	echo "\t\t<phyinf>\n";
	echo dump(3, $path);
	echo "\t\t</phyinf>\n";
}
?>	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
	<FATLADY>ignore</FATLADY>
</module>
