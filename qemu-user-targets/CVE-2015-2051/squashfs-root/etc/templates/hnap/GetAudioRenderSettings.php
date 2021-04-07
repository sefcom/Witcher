<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";

if(get("","/device/audiorender/airplay") == "1")	{$AirPlay="true";}
else												{$AirPlay="false";}

if(get("","/device/audiorender/dlna") == "1")	{$DLNA="true";}
else											{$DLNA="false";}

if(get("","/runtime/device/audiocableplug") == "1")	{$AudioCablePlug="true";}
else												{$AudioCablePlug="false";}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetAudioRenderSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetAudioRenderSettingsResult><?=$result?></GetAudioRenderSettingsResult>
			<AirPlay><?=$AirPlay?></AirPlay>
			<DLNA><?=$DLNA?></DLNA>
			<AudioCablePlug><?=$AudioCablePlug?></AudioCablePlug>
			<MediaName><? echo get("x","/device/audiorender/medianame");?></MediaName>
		</GetAudioRenderSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>