<module>
	<service><?=$GETCFG_SVC?></service>
	<nat>
<?
include "/htdocs/phplib/xnode.php";
$nat = XNODE_getpathbytarget("/nat", "entry", "uid", cut($GETCFG_SVC,1,"."));
if ($nat!="")
{
	$svc = cut($GETCFG_SVC,0,".");
	if	($svc == "PFWD")		$target = "portforward";
	else if	($svc == "VSVR")		$target = "virtualserver";
	else if	($svc == "PORTT")		$target = "porttrigger";
	else if	($svc == "DMZ")			$target = "dmz";
	else					$target = "";

	if ($target!="")
	{
		echo "\t\t<entry>\n";
		echo "\t\t\t<".$target.">\n";
		echo dump(4, $nat."/".$target);
		echo "\t\t\t</".$target.">\n";
		echo "\t\t</entry>\n";
	}
}
?>	</nat>
</module>
