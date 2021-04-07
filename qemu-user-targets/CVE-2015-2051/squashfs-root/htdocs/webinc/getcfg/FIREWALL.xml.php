<module>
	<service><?=$GETCFG_SVC?></service>
	<acl>
<?
if($GETCFG_SVC=="FIREWALL-2")
{
	$target ="firewall2";
}
else if($GETCFG_SVC=="FIREWALL-3")
{
	$target ="firewall3";
}
else
{
	$target = tolower($GETCFG_SVC);
}
echo "\t\t<".$target.">\n";
echo dump(3, "/acl/".$target);
echo "\t\t</".$target.">";
?>
	</acl>
</module>
