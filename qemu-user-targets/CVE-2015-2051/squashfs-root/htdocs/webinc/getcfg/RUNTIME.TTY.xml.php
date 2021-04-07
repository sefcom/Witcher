<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
	<tty>
		<entry>
		<connection><?echo query("/runtime/tty/entry:1/connection");?></connection>
		<rssi><?echo query("/runtime/tty/entry:1/rssi");?></rssi>
		</entry>
	</tty>
	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
