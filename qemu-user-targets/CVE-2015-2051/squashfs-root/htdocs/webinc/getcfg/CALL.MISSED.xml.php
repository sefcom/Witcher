<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<callmgr>
			<voice_service>
				<log>
					<?      echo dump(3, "/runtime/callmgr/voice_service/log");?>
				</log>
			</voice_service>
		</callmgr>
	</runtime>	
</module>
