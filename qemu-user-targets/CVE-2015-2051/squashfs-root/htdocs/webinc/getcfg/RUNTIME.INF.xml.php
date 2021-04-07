<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
<?		foreach ("/runtime/inf") echo "\t\t<inf>\n".dump(3, "/runtime/inf:".$InDeX)."\t\t</inf>\n";
?>	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
