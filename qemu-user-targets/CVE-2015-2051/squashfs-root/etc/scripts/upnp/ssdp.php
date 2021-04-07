<?
include "/htdocs/phplib/trace.php";

function SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $st, $usn)
{
	echo "xmldbc -P /etc/scripts/upnp/__M-SEARCH.resp.php";
	echo " -V \"MAX_AGE="	.$max_age	."\"";
	echo " -V \"DATE="		.$date		."\"";
	echo " -V \"LOCATION="	.$location	."\"";
	echo " -V \"SERVER="	.$server	."\"";
	echo " -V \"ST="		.$st		."\"";
	echo " -V \"USN="		.$usn		."\"";

	echo " | httpc -i ".$phyinf." -d \"".$target_host."\" -p UDP\n";
}

function SSDP_ms_walk_all_devices($target_host, $phyinf, $path, $max_age, $date, $location, $server)
{
	foreach ($path)
	{
		/* get device info */
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		if ($dev_type != "")
		{
			SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $uuid,		$uuid);
			SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $dev_type,	$uuid."::".$dev_type);

			/* service */
			foreach("serviceList/service")
			{
				$srv_type = query("serviceType");
				if ($srv_type != "")
					SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $srv_type, $uuid."::".$srv_type);
			}
			/* walk for embeded devices */
			$child_dpath = $path.":".$InDeX."/deviceList/device";
			SSDP_ms_walk_all_devices($target_host, $phyinf, $child_dpath, $max_age, $date, $location, $server);
		}
	}
}

function SSDP_ms_walk_device_by_uuid($target_uuid, $target_host, $phyinf, $path, $max_age, $date, $location, $server)
{
	foreach($path)
	{
		$uuid = query("UDN");
		if ($uuid == $target_uuid)
			SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $uuid, $uuid);

		/* walk for embeded devices */
		$child_dpath = $path.":".$InDeX."/deviceList/device";
		SSDP_ms_walk_device_by_uuid($target_uuid, $target_host, $phyinf, $child_dpath, $max_age, $date, $location, $server);
	}
}

function SSDP_ms_walk_device_by_devtype($target_device, $target_host, $phyinf, $path, $max_age, $date, $location, $server)
{
	foreach($path)
	{
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");
	
		if ($dev_type == $target_device)
			SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $dev_type, $uuid."::".$dev_type);

		/* walk for embeded devices */
		$child_dpath = $path.":".$InDeX."/deviceList/device";
		SSDP_ms_walk_device_by_devtype($target_device, $target_host, $phyinf, $child_dpath, $max_age, $date, $location, $server);
	}
}

function SSDP_ms_walk_device_by_srvtype($target_service, $target_host, $phyinf, $path, $max_age, $date, $location, $server)
{
	foreach($path)
	{
		$uuid = query("UDN");
		/* service */
		foreach("serviceList/service")
		{
			$srv_type = query("serviceType");
			if ($srv_type == $target_service) SSDP_ms_send_resp($target_host, $phyinf, $max_age, $date, $location, $server, $srv_type, $uuid."::".$srv_type);
		}
		/* walk for embeded devices */
		$child_dpath = $path.":".$InDeX."/deviceList/device";
		SSDP_ms_walk_device_by_srvtype($target_service, $target_host, $phyinf, $child_dpath, $max_age, $date, $location, $server);
	}
}

/***************************************************************/
/* Notify for alive/byebye*/
function SSDP_nt_send_req($phyinf, $max_age, $location, $server, $nts, $nt, $usn)
{
	echo "xmldbc -P /etc/scripts/upnp/__NOTIFY.req.ab.php";
	echo " -V \"MAX_AGE="	.$max_age	."\"";
	echo " -V \"LOCATION="	.$location	."\"";
	echo " -V \"SERVER="	.$server	."\"";
	echo " -V \"NTS="		.$nts		."\"";
	echo " -V \"NT="		.$nt		."\"";
	echo " -V \"USN="		.$usn		."\"";
	
	$ipaddr = "239.255.255.250:1900";
	if(strstr($location,"[")!="") { $ipaddr = "ff02::c:1900"; }
	echo " | httpc -i ".$phyinf." -d \"".$ipaddr."\" -p UDP\n";
}

function SSDP_nt_send_reqs($times, $phyinf, $max_age, $location, $server, $nts, $nt, $usn)
{
	$i = 0;
	while($i < $times)
	{
		SSDP_nt_send_req($phyinf, $max_age, $location, $server, $nts, $nt, $usn);
		$i++;
	}
}

function SSDP_nt_walk_all_devices($phyinf, $path, $max_age, $location, $server, $nts, $uuid)
{
	foreach ($path)
	{
		/* get device info */
		$dtype = query("deviceType");
		echo "# curr:".$InDeX."[".$dtype."], [".$uuid."]\n";
		if ($dtype != "")
		{
			$nt	= $uuid;
			$usn= $uuid;
			SSDP_nt_send_reqs(2, $phyinf, $max_age, $location, $server, $nts, $nt, $usn);

			$nt	= $dtype;
			$usn= $uuid."::".$dtype;
			SSDP_nt_send_reqs(2, $phyinf, $max_age, $location, $server, $nts, $nt, $usn);
			
			/* services */
			foreach ("serviceList/service")
			{
				$stype = query("serviceType");
				echo "# service:".$InDeX." = [".$stype."]\n";
				if ($stype != "")
				{
					$nt = $stype;
					$usn = $uuid."::".$stype;
					SSDP_nt_send_reqs(2, $phyinf, $max_age, $location, $server, $nts, $nt, $usn);
				}
			}
			/* walk for embeded devices */
			$child_dpath = $path.":".$InDeX."/deviceList/device";
			SSDP_nt_walk_all_devices($target_host, $phyinf, $child_dpath, $max_age, $location, $server, $nts, $uuid);
		}
	}
}
?>
