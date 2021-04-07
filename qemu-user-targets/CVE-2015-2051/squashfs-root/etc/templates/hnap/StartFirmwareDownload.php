HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";

//Initialing the path.
// We put firmware size and downloading flag to other path to avoid the interference of checkfw from another WEB.
$firmware_size_path = "/runtime/firmware_downloading/firmwareSize";
$fwDownloadingFlag_path = "/runtime/firmware_downloading/FWDownloadingFlag";
$fw_path = "/var/firmware.seama";
$fw_download_url_path = "/runtime/firmware/FWDownloadUrl";


// We use the flag to indicate that the downloading is in processing or not.
$fwDownloadingFlag = get("",$fwDownloadingFlag_path);
if ($fwDownloadingFlag == "1")
{
	TRACE_error("Download is in processing.");
}

$fw_exist = isfile($fw_path);
if($fw_exist == "1")
{
	TRACE_error("Downloaded firmware is existed.");
}

// need to check fw first.
$fw_download_url = get ("", $fw_download_url_path);
if ($fw_download_url == "")
{
	TRACE_error("Cannot get firmware download url, need to checkfw first.");
	$result = "ERROR";
}

//check the network status.
if ($result == "OK")
{
	$layout = get("", "/device/layout");
	if ($layout == "router")
	{
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
	}
	else if ($layout == "bridge")
	{
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $BR1, 0);
	}
	$wan1_phyinf = get("",$path_inf_wan1."/phyinf");
	$path_run_phyinf_wan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyinf, 0);
	$status = get("",$path_run_phyinf_wan1."/linkstatus");
	if( $status != "0" && $status != "")
	{ $statusStr = "CONNECTED"; }
	else 
	{ $statusStr = "DISCONNECTED"; }

	if ($statusStr == "CONNECTED")
	{
		$result = "OK";
	}
	else
	{
		TRACE_error("Internet is not connected.");
		$result = "ERROR";
	}
}

//If the firmware is downloading or download completed, return OK directly.
//write the shell script file to execute the firmware check and download.
if ($result != "OK")
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");
}
else if ($fwDownloadingFlag == "1" || $fw_exist == "1")
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "echo \"Firmware is downloading or existed, so we do nothing....\" > /dev/console\n");
}
else 
{
	// Set the download flag, ready to start download firmware.
	del ($firmware_size_path);
	set ($fwDownloadingFlag_path, "1");

	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	$cmd = "echo \"Starting download firmware.\"";
	fwrite("a",$ShellPath, $cmd." > /dev/console\n");

	// Use wget spider mode , try to get the firmware size.
	fwrite("a", $ShellPath, "fwsize=`wget -s ".$fw_download_url." 2>/dev/null | grep 'Content length' | cut -d ':' -f 2`\n");
	fwrite("a",$ShellPath, "echo \"Get firmware size:$fwsize\" > /dev/console\n");
	fwrite("a", $ShellPath, "test \"$fwsize\" -eq \"\" ||  xmldbc -s ".$firmware_size_path." $fwsize\n");

	fwrite("a",$ShellPath, "sleep 2\n");
	// Use wget to download the firmware.
	fwrite("a", $ShellPath, "wget -T 20 -O ".$fw_path." ".$fw_download_url." > /dev/console \n");
	fwrite("a",$ShellPath, "echo \"Download completed. Result=$?\" > /dev/console\n");

	// after download completed, clear the downloading flag.
	$cmd = "xmldbc -X ".$fwDownloadingFlag_path;
	fwrite("a",$ShellPath, $cmd." > /dev/console\n");
	//If the firmware is download but not update for 30 seconds, the downloaded firmware would be removed to save the memory.
	fwrite("a",$ShellPath, "xmldbc -t 'fwdelete:30:rm ".$fw_path."' > /dev/console\n");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
	<StartFirmwareDownloadResponse xmlns="http://purenetworks.com/HNAP1/">
		<StartFirmwareDownloadResult><?=$result?></StartFirmwareDownloadResult>
	</StartFirmwareDownloadResponse>
</soap:Body>
</soap:Envelope>
