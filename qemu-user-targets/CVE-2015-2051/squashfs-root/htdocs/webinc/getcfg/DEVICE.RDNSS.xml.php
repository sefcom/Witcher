<module>
	<service><?=$GETCFG_SVC?></service>
	<device>
		<rdnss><?echo get("x","/device/rdnss");?></rdnss>
		<dhcp6hint><?echo get("x","/device/dhcp6hint");?></dhcp6hint>
		<v6modechange><?echo get("x","/device/v6modechange");?></v6modechange>
	</device>
</module>
