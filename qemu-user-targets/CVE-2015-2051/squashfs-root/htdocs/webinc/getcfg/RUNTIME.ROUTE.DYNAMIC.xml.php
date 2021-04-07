<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<dynamic>
			<route>
<?			foreach ("/runtime/dynamic/route/entry")
			{
					echo "\t\t\t\t<entry>\n".dump(5, "/runtime/dynamic/route/entry:".$InDeX)."\t\t\t\t</entry>\n";
			}
?>			</route>
			<route6>
<?			foreach ("/runtime/dynamic/route6/entry")
			{
					echo "\t\t\t\t<entry>\n".dump(5, "/runtime/dynamic/route6/entry:".$InDeX)."\t\t\t\t</entry>\n";
			}
?>			</route6>
		</dynamic>		
	</runtime>	
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
