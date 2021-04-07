<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<callmgr>
			<voice_service>
				<mobile>
					<sms>
						<send_state>
							<?echo query("/runtime/callmgr/voice_service/mobile/sms/send_state");?>
						</send_state>
						<send_coding_method>
							<?echo query("/runtime/callmgr/voice_service/mobile/sms/send_coding_method");?>
						</send_coding_method>
						<send_address>
							<?echo query("/runtime/callmgr/voice_service/mobile/sms/send_address");?>
						</send_address>
						<send_content>
							<?echo query("/runtime/callmgr/voice_service/mobile/sms/send_content");?>
						</send_content>
					</sms>
				</mobile>
			</voice_service>
		</callmgr>
	</runtime>	
</module>
