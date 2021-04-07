<module>
	<service><?=$GETCFG_SVC?></service>
	<callmgr>
            <voice_service>
                <phone>
                    <analog>
                        <?	echo dump(3, "/callmgr/voice_service:1/phone/analog:1");?>
                    </analog>
                </phone>
		<ctry_cid>
			<cid_std><?echo query("/callmgr/voice_service:1/ctry_cid/cid_std");?></cid_std>
		</ctry_cid>
            </voice_service>
	<fxs>
            <alm>
                <channel>
                    <volume>
                        <?	echo dump(3, "/callmgr/fxs/alm/channel:1/volume");?>
                    </volume>
                </channel>
            </alm>
	</fxs>
	</callmgr>
	<runtime>
		<callmgr>
			<voice_service>
				<mobile>
					<callerid_delivery><?echo query("/runtime/callmgr/voice_service/mobile/callerid_delivery");?></callerid_delivery>
					<callwaiting><?echo query("/runtime/callmgr/voice_service/mobile/callwaiting");?></callwaiting>
					<service_state>
						<?echo query("/runtime/callmgr/voice_service/mobile/service_state");?>
					</service_state>
					<call_forward>
					<?      echo dump(4, "/runtime/callmgr/voice_service/mobile/call_forward");?>
					</call_forward>
					</mobile>
			</voice_service>
		</callmgr>
	</runtime>	
</module>
