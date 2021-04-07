<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

function print_usb()
{
	include "/htdocs/phplib/xnode.php";
	include "/htdocs/webinc/config.php";
	foreach ("/runtime/device/storage/disk")
	{
		$port = $InDeX;	// Currently, we do not know which port is inserted by the USB device.
		$model = get("x","model");
		$vid = get("x","idVendor");
		$pid = get("x","idProduct");
		$totalspace = get ("x","size")/1024;	// always shows megabytes.
		$freespace = 0;
		foreach ("/runtime/device/storage/disk:".$InDeX."/entry")
		{
			/* HuanYao Kang: modify the size to be megabyte. */
			$space_size = get("x", "space/size");
			$space_available = get("x", "space/available");
			$totalspace = $space_size /1024;
			$freespace = $space_available /1024;

			/*
			$free = get("x","space/available");
			if (isdigit($free) == "1")
			{
				$freespace += $free;
			}
			*/
		}
		echo "<USBDevice>\n";
		echo "\t<Port>".$port."</Port>\n";
		echo "\t<Model>".$model."</Model>\n";
		echo "\t<VID>".$vid."</VID>\n";
		echo "\t<PID>".$pid."</PID>\n";
		echo "\t<TotalSpace>".$totalspace."</TotalSpace>\n";
		echo "\t<FreeSpace>".$freespace."</FreeSpace>\n";
		echo "</USBDevice>\n";
	}
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetUSBDeviceResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetUSBDeviceResult><?=$result?></GetUSBDeviceResult>
			<USBDeviceInfoLists>
<? print_usb(); ?>
			</USBDeviceInfoLists>
		</GetUSBDeviceResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
