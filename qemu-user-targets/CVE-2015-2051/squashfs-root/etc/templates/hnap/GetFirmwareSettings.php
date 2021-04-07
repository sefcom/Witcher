<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

	$builddaytime = get("", "/runtime/device/firmwarebuilddaytime");
	$FirmwareDate = scut($builddaytime,0,"")."-".scut($builddaytime,1,"")."-".scut($builddaytime,2,"")."T".scut($builddaytime,3,"").":".scut($builddaytime,4,"").":"."00";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetFirmwareSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetFirmwareSettingsResult>OK</GetFirmwareSettingsResult>
			<VendorName><? echo get("", "/runtime/device/vendor");?></VendorName>
			<ModelName><? echo get("", "/runtime/device/modelname");?></ModelName>			
			<ModelRevision><? echo get("", "/runtime/device/hardwareversion");?></ModelRevision>
			<FirmwareVersion><? echo get("", "/runtime/device/firmwareversion");?>, <? echo get("", "/runtime/device/firmwarebuilddate");?></FirmwareVersion>
			<FirmwareDate><? echo $FirmwareDate;?></FirmwareDate>
			<UpdateMethods>HNAP_UPLOAD</UpdateMethods>			
		</GetFirmwareSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
