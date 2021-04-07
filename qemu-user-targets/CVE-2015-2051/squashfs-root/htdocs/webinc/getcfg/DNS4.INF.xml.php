<module>
	<service><?=$GETCFG_SVC?></service>
	<dns4><? echo "\n".dump(2, "/dns4")."\t";?></dns4>
<?	foreach("/inf") if (cut(query("uid"),0,'-')!="WAN") echo "\t<inf>\n".dump(2,"")."\t</inf>\n";
?></module>
