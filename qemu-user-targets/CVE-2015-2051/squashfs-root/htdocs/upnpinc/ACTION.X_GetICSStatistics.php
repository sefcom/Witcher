<TotalBytesSent><?
	include "/htdocs/phplib/xnode.php";

	if (query("/runtime/device/layout")=="router")
		$rt_on = 1;
	else
		$rt_on = 0;

	$phyinf	= query(XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0)."/phyinf");
	$p		= XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);

	if ($rt_on!=1)	echo "0";
	else			echo map($p."/stats/tx/bytes", "", "0");

?></TotalBytesSent>
<TotalBytesReceived><?

	if ($rt_on!=1)	echo "0";
	else			echo map($p."/stats/rx/bytes", "", "0");

?></TotalBytesReceived>
<TotalPacketsSent><?

	if ($rt_on!=1)	echo "0";
	else			echo map($p."/stats/tx/packets", "", "0");

?></TotalPacketsSent>
<TotalPacketsReceived><?

	if ($rt_on!=1)	echo "0";
	else			echo map($p."/stats/rx/packets", "", "0");

?></TotalPacketsReceived>
<Layer1DownstreamMaxBitRate>100000000</Layer1DownstreamMaxBitRate>
<Uptime><?

	if ($rt_on!=1)	echo "0";
	else
	{
		/* not impletment yet! joanw*/
		echo "0";
	}

?></Uptime>
