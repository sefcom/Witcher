<?
echo "#!/bin/sh\n";
foreach ("/inf")
{
	$uid    = query("uid");
	$disable= query("disable");
	$active = query("active");
	$dhcps4 = query("dhcps4");
	if ($disable != "1" && $active=="1" && $dhcps4!=""){
		echo "service DHCPS4.".$uid." restart\n";
	}
}
echo "exit 0\n";
?>
