<?
$XMLBASE = "/runtime/device/storage";

if ($action=="UNMOUNT")		$cmd = "usbmount unmount ";
else if ($action=="MOUNT")	$cmd = "usbmount mount ";
$cnt = query($XMLBASE."/disk#");
$i = 1;
echo "#!/bin/sh\n";
while ($i <= $cnt)
{
	foreach ($XMLBASE."/disk:".$i."/entry")
	{
		if (query("pid")=="0")	$dev = query("prefix");
		else					$dev = query("prefix").query("pid");
		echo $cmd.$dev."\n";
	}
	$i++;
}
echo "exit 0\n";
?>
