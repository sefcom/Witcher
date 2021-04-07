<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
<?		foreach ("/runtime/phyinf") echo "\t\t<phyinf>\n".dump(3, "/runtime/phyinf:".$InDeX)."\t\t</phyinf>\n";
?>		<neighbor>
<?			echo "\t\t\t<wifi>\n".dump(4, "/runtime/neighbor/wifi")."\t\t\t</wifi>\n";
?>		</neighbor>
	</runtime>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
