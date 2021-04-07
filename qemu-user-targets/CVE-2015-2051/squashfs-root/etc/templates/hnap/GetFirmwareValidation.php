<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

/***************************************************************************/
/*
 *  call this action after QRS upload firmware to router
 *	firmware location is /var/firmware.seama
 *	ckfw() is used to check firmware validation (return 0 success)
 *	count_fwuptime() is used to count firmware upgrade time
 *	
 *	sammy 2012/8/28
 */
function ckfw()
{
	setattr("/runtime/chfw",  "get", "fwupdater -v /var/firmware.seama; echo $?");
	return get("","/runtime/chfw");
}

function count_fwuptime()
{
	$size	= fread("x","/var/session/imagesize"); if ($size == "") $size = "8000000";
	$fptime	= query("/runtime/device/fptime"); if ($fptime == "") $fptime = "1000";
	$bt		= query("/runtime/device/bootuptime"); if ($bt == "") $bt = "60";
	$delay	= 10;	
	
	return $size/64000*$fptime/1000+$bt+$delay+20;
}

$result = "REBOOT";
$IsValid = ckfw();
$IsUpdating = get("", "/runtime/FWUpdatingFlag");

//If check firmware is valid and the firmware is not updating now, it will update the firmware.
if($IsValid == "0" && $IsUpdating != "1")
{
	$IsValid = "true";
	$CountDown = count_fwuptime();
	
	//Prevent the firmware download repeating.
	set("/runtime/FWUpdatingFlag", "1");
	//If the firmware is download but not update for 30 seconds, the downloaded firmware would be removed to save the memory.
	//Kill the timer if the firmware is downloading now.
	fwrite("a",$ShellPath, "xmldbc -k fwdelete > /dev/console\n");

	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "sleep 3 > /dev/console\n");
	fwrite("a",$ShellPath, "fw_upgrade /var/firmware.seama\n");
}
else
{
	$IsValid = "false";
	$CountDown = "0";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetFirmwareValidationResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetFirmwareValidationResult><?=$result?></GetFirmwareValidationResult>
	<IsValid><?=$IsValid?></IsValid>
	<CountDown><?=$CountDown?></CountDown>
</GetFirmwareValidationResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
