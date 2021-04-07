<module>
	<service><?=$GETCFG_SVC?></service>
	<acl6>
<?
$target = tolower(cut($GETCFG_SVC, 0, "6"));
echo "\t\t<".$target.">\n";
echo dump(3, "/acl6/".$target);
echo "\t\t</".$target.">";
?>
	</acl6>
</module>
