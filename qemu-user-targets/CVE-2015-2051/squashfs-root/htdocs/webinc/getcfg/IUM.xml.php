<module>
	<service><?=$GETCFG_SVC?></service>
	<callmgr>
		<mobile>
			<flowmeter>
				<? echo dump(2, "/callmgr/mobile/flowmeter");?>
			</flowmeter>
		</mobile>
	</callmgr>
	<device>
		<time>
			<ntp>
				<enable>
					<? echo query("/device/time/ntp/enable");?>
				</enable>
			</ntp>
		</time>
	</device>
	<runtime>
		<tty>
			<entry>
				<dsflow>
					<? echo dump(3, "/runtime/tty/entry/dsflow");?>
				</dsflow>
			</entry>
		</tty>
	</runtime>
</module>
