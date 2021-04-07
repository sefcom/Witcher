<module>
	<service><?=$GETCFG_SVC?></service>
	<dhcps6>
<?		echo dump(2, "/dhcps6");
?>	</dhcps6>
<?
	foreach("/inf")
	{
		$prefix=cut(query("uid"), 0, '-');
		if ($prefix!="WAN")
		{
			echo '\t<inf>\n';
			echo dump(2, "");
			echo '\t</inf>\n';
		}
	}
?></module>
