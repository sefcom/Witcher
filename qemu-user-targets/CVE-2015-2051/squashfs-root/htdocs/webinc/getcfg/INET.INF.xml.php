<module>
	<service><?=$GETCFG_SVC?></service>
	<inet>
<?		echo dump(2, "/inet");
?>	</inet>
<?
	foreach("/inf")
	{
		echo '\t<inf>\n';
		echo dump(2, "");
		echo '\t</inf>\n';
	}
?>	<ACTIVATE>event</ACTIVATE>
	<ACTIVATE_EVENT>INF.RESTART</ACTIVATE_EVENT>
	<ACTIVATE_DELAY>2</ACTIVATE_DELAY>
</module>
