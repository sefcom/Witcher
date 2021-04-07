HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$percentage = 0;
$fwDownloadingFlag_path = "/runtime/firmware_downloading/FWDownloadingFlag";
$firmware_size_path = "/runtime/firmware_downloading/firmwareSize";
$firmware_size_default = 10*1024*1024;

TRACE_error ("Enter PollingFrimwareDownload.");

function getFileSize($filename)
{
	$exec_path = "/runtime/runcmd";
	setattr($exec_path,  "get", "ls -l ".$filename);
	$file_result = get ("", $exec_path);
	del ($exec_path);
	$current_size = scut($file_result, 4, "");
	return $current_size;
}

$fwDownloadingFlag = get("",$fwDownloadingFlag_path);
if ($fwDownloadingFlag != "1")
{	TRACE_error("Download is not in processing.");	}

$current_size = "0";
$fw_path = "/var/firmware.seama";
$fw_exist = isfile($fw_path);
if($fw_exist == "1")
{
	$current_size = getFileSize($fw_path);
	TRACE_error("Downloaded firmware is existed. Current size=".$current_size);
}

$firmware_size = get ("",$firmware_size_path);
if ($firmware_size == "")
{
	TRACE_error("Cannot get the firmware size, we assume the firmware size is 10M.");
	$firmware_size = $firmware_size_default;
}
TRACE_error("The full size of firmware =".$firmware_size);


//Start the main process.
if ($fwDownloadingFlag != "1")	// not downloading case
{
	if ($fw_exist != "1")
	{
		TRACE_error ("The firmware is not downloading nor existed, need to return error.");
		$result = "ERROR";
	}
	else
	{
		if ($firmware_size == $firmware_size_default )	// we do not know the firmware full size, let firmware checksum to make sure the firmware is completed or not.
		{ $percentage = 100; }
		else
		{
			if ($current_size != $firmware_size)	
			// the firmware current size is not euqal to firmware size. it means something wrong in the firmware. Deleting the file is necessary.
			{ 
				TRACE_error ("The firmware size is not match. Is something wrong with the firmware?");
				$result = "ERROR";
				unlink ($fw_path);
			}
			else
			{
				$percentage = "100";
			}
		}
	}
}
else // Downloading
{
	if ($fw_exist != "1")
	{
		TRACE_error ("The firmware is downloading but the file is not existed, maybe something delay?");
		$result = "OK";
		$percentage = "0";
	}
	else
	{
		$percentage = $current_size * 100 / $firmware_size ;
	}
}

if ($result != "OK")
{
	$percentage = "0";
}

TRACE_info ("Percentage = ".$percentage);

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
	<PollingFirmwareDownloadResponse xmlns="http://purenetworks.com/HNAP1/">
		<PollingFirmwareDownloadResult><?=$result?></PollingFirmwareDownloadResult>
		<DownloadPercentage><?=$percentage?></DownloadPercentage>
	</PollingFirmwareDownloadResponse>
</soap:Body>
</soap:Envelope>
