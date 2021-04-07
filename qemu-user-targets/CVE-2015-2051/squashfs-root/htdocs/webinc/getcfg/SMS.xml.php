<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<callmgr>
			<voice_service>
				<mobile>
					<sms>
						<?      echo dump(4, "/runtime/callmgr/voice_service/mobile/sms");?>
					</sms>
				</mobile>
			</voice_service>
		</callmgr>
	</runtime>	
</module>
