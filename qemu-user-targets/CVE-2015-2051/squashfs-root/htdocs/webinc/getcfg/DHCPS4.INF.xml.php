<module>
	<service><?=$GETCFG_SVC?></service>
	<dhcps4><? echo "\n".dump(2, "/dhcps4")."\t";?></dhcps4>
<?	foreach("/inf") if (cut(query("uid"),0,'-')!="WAN") echo "\t<inf>\n".dump(2, "")."\t</inf>\n";
?></module>
