<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

//$dev_entry = "/runtime/webaccess/device/entry";
$dev_entry = "/runtime/device/storage/disk";
$result = "OK";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetUSBStorageDeviceResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetUSBStorageDeviceResult><?=$result?></GetUSBStorageDeviceResult >
			<StorageDeviceInfoLists>
<? 
                $idx=0;
				foreach($dev_entry)
				{
				    $idx=$idx+1;
				    $device = get("x", "vendor");
					echo "				<StorageDevice> \n";
					echo "					<Device>".$device."</Device>\n";
					$total_space=0;
					$free_space=0;
					
					foreach ($dev_entry.":".$idx."/entry")
					{
						$space_size = get("x", "space/size");
						$space_available = get("x", "space/available");
						$t_size_mb= $space_size / 1024;
						$f_size_mb= $space_available / 1024;
						$total_space = $total_space + $t_size_mb;
						$free_space = $free_space + $f_size_mb;
						// HuanYao Kang: modify the size to be megabyte.
//						$total_space = volume_unit($space_size);
//						$free_space = volume_unit($space_available);
					}
					
					echo "					<TotalSpace>".$total_space."</TotalSpace>\n";
					echo "					<FreeSpace>".$free_space."</FreeSpace>\n";
					echo "				</StorageDevice>\n";
				}
?>
			</StorageDeviceInfoLists>
		</GetUSBStorageDeviceResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>