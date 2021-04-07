<module>
	<service><?=$GETCFG_SVC?></service>
	<dfs_blocked>	
<?		foreach ("/runtime/dfs/blocked/entry") 
		{
			echo "\t\t<entry>\n".dump(3, "/runtime/dfs/blocked/entry:".$InDeX)."\t\t</entry>\n";
		}
?>		
	</dfs_blocked>
	
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
