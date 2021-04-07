<module>
	<service><?=$GETCFG_SVC?></service>
	<ddns4>
<?		echo dump(2, "/ddns4");
?>	</ddns4>
<?
	foreach("/inf")
	{
		$prefix=cut(query("uid"), 0, '-');
		if ($prefix=="WAN")
		{
			echo '\t<inf>\n';
			echo dump(2, "");
			echo '\t</inf>\n';
		}
	}
?></module>
