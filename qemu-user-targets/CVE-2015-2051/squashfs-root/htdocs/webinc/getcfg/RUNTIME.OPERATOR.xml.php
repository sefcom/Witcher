<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<services>
			<operator>
<?
	echo dump(4, "/runtime/services/operator");
?>			</operator>
		</services>
		<auto_config>
<?
	echo dump(3, "/runtime/auto_config");
?>
		</auto_config>
	</runtime>
	<ACTIVATE>ignore</ACTIVATE>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>
</module>
