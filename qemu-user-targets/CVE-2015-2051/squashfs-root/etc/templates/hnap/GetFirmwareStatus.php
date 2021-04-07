<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$buildver = fread("s", "/etc/config/buildver");
$CurrentMajor = cut($buildver,0,".");
$CurrentMinor = substr(cut($buildver,1,"."), 0, 2);
$CurrentFWVersion = $CurrentMajor.".".$CurrentMinor;
$firmwarebuilddaytime = get("", "/runtime/device/firmwarebuilddaytime");
$LatestFWVersionDate = scut($firmwarebuilddaytime, 0, "")."/".scut($firmwarebuilddaytime, 1, "")."/".scut($firmwarebuilddaytime, 2, "");
$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $LAN1, 0);
$FWUploadUrl = get("",$path_run_inf_lan1."/inet/ipv4/ipaddr")."/fwupload.cgi";

//we run checkfw.sh in hnap.cgi, so we can get fw info. now
if(isfile("/tmp/fwinfo.xml")==1)
{
	TRACE_debug("checkfw.sh success!");
	
	$LatesMajor = substr(get("","/runtime/firmware/fwversion/Major"), 1, 2);
	$LatesMinor = get("","/runtime/firmware/fwversion/Minor");
	$LatestFWVersion = $LatesMajor.".".$LatesMinor;
	$LatestFWVersionDate = get("","/runtime/firmware/LatestFWVersionDate");
	$FWDownloadUrl = get("","/runtime/firmware/FWDownloadUrl");
}
else
{
	TRACE_debug("checkfw.sh fail!");
	
	$result = "ERROR";
	$LatestFWVersion = "null";
	$FWDownloadUrl = "null";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetFirmwareStatusResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetFirmwareStatusResult><?=$result?></GetFirmwareStatusResult>
		<CurrentFWVersion><?=$CurrentFWVersion?></CurrentFWVersion>
		<LatestFWVersion><?=$LatestFWVersion?></LatestFWVersion>
		<LatestFWVersionDate><?=$LatestFWVersionDate?></LatestFWVersionDate>
		<FWDownloadUrl><? echo escape("x",$FWDownloadUrl); ?></FWDownloadUrl>
		<FWUploadUrl><? echo escape("x",$FWUploadUrl); ?></FWUploadUrl>
	</GetFirmwareStatusResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>