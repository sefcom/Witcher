<?
fwrite(w,$START,"#!/bin/sh\n");
fwrite(w,$STOP, "#!/bin/sh\n");
foreach ("/inf")
{
	$uid = query("uid");
	$active = query("active");
	$prefix = cut($uid,0,'-');
	if ($prefix == "LAN" || $prefix == "WAN")
	{
		if($active == "1")
		{
			fwrite("a",$START,"service BWC.".$uid." restart\n");
			fwrite("a",$STOP,"service BWC.".$uid." stop\n");
		}
	}
}
?>
