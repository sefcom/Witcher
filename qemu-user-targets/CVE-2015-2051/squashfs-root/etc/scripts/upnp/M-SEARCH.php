#!/bin/sh
<? /* vi : set sw=4 ts=4: */
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
require "/etc/scripts/upnp/ssdp.php";

/* check if the $dtype support.*/
function dtype_check($dtype, $infp)
{
	$count = query($infp."/upnp/count");
	//TRACE_debug("M-SERCH.php dtype_check: infp=[".$infp."], [".$dtype."], [".$count."]");
	$i = 0;
	while($i < $count)
	{
		$i++;
		if (query($infp."/upnp/entry:".$i)==$dtype) return 1;
	}
	return 0;
}

$ipaddr = INF_getcurripaddr($INF_UID);
if ($ipaddr == "")
{
	echo "echo -e \"Can't get the IP address of [".$INF_UID."]!!!\"";
	exit;
}
$inf_path = XNODE_getpathbytarget("", "inf", "uid", $INF_UID, 0);
if ($inf_path == "")
{
	echo "echo -e \"Can't get the inf path of [".$INF_UID."]!!!\"";
	exit;
}

/* check ipv6, sam_pan */
$inet = query($inf_path."/inet");
$inetp    = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$addrtype = query($inetp."/addrtype");
if($addrtype == "ipv6") { $ipaddr = "[".$ipaddr."]"; }

$phyinf = PHYINF_getifname(query($inf_path."/phyinf"));
if ($phyinf == "")
{
	echo "echo -e \"Can't get the phyinf name of [".$INF_UID."]!!!\"";
	exit;
}

$date = query("/runtime/device/rfc1123time");
$path = "/runtime/upnp/dev";
if ($SEARCH_TARGET == "ssdpall")
{
	foreach ($path)
	{
		$max_age	= query("maxage");
		$location	= "http://".$ipaddr.":".query("port")."/".query("location");
		$server		= query("server");
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		/* only response the supported device type. */
		if (dtype_check($dev_type, $inf_path)=="1")
		{
			/* root device */
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, "upnp:rootdevice", $uuid."::upnp:rootdevice");
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $uuid,	 $uuid);
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $dev_type, $uuid."::".$dev_type);

			/* service */
			foreach ("devdesc/device/serviceList/service")
			{
				$srv_type = query("serviceType");
				if ($srv_type != "")
					SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $srv_type, $uuid."::".$srv_type);
			}

			/* walk all embeded devices */
			$child_dpath = $path.":".$InDeX."/devdesc/device/deviceList/device";
  			SSDP_ms_walk_all_devices($TARGET_HOST, $phyinf, $child_dpath, $max_age, $date, $location, $server);
		}
	}
	exit;
}

if ($SEARCH_TARGET == "rootdevice")
{
	echo "# got a rootdevice\n";
	foreach ($path)
	{
		$max_age	= query("maxage");
		$location	= "http://".$ipaddr.":".query("port")."/".query("location");
		$server		= query("server");
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		echo "# SSDP_ms_send_resp(".$TARGET_HOST.", ".$phyinf.", ".$max_age.", ".$date.", ".$location.", ".$server.", \"upnp:rootdevice\", ".$uuid."\"::upnp:rootdevice\")\n";

		/* only response the supported device type. */
		if (dtype_check($dev_type, $inf_path)=="1")
		{
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, "upnp:rootdevice",	$uuid."::upnp:rootdevice");
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $uuid,	 $uuid);
			SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $dev_type, $uuid."::".$dev_type);
		}
	}
	exit;	
}

if ($SEARCH_TARGET == "uuid")
{
	foreach ($path)
	{
		$max_age	= query("maxage");
		$location	= "http://".$ipaddr.":".query("port")."/".query("location");
		$server		= query("server");
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		if (dtype_check($dev_type, $inf_path)=="1")
		{
			if ($uuid == $PARAM) SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $uuid, $uuid);
			$child_dpath = $path.":".$InDeX."/devdesc/device/deviceList/device";
			SSDP_ms_walk_device_by_uuid($PARAM, $TARGET_HOST, $phyinf, $child_dpath, $max_age, $date, $location, $server); 
		}
	}
	exit;
}

if ($SEARCH_TARGET == "devices")
{
	foreach ($path)
	{
		$max_age	= query("maxage");
		$location	= "http://".$ipaddr.":".query("port")."/".query("location");
		$server		= query("server");
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		if (dtype_check($dev_type, $inf_path)=="1")
		{
			if ($dev_type == $PARAM)
			{
				SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $uuid, $uuid);
				SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $dev_type, $uuid."::".$dev_type);
			}
			$child_dpath = $path.":".$InDeX."/devdesc/device/deviceList/device";
			SSDP_ms_walk_device_by_devtype($PARAM, $TARGET_HOST, $phyinf, $child_dpath, $max_age, $date, $location, $server);
		}
	}
	exit;
}

if ($SEARCH_TARGET == "services")
{
	foreach ($path)
	{
		$max_age	= query("maxage");
		$location	= "http://".$ipaddr.":".query("port")."/".query("location");
		$server		= query("server");
		$dev_type	= query("deviceType");
		$uuid		= query("UDN");

		if (dtype_check($dev_type, $inf_path)=="1")
		{
			/* service */
			foreach("serviceList/service")
			{
				$srv_type = query("serviceType");
				if ($srv_type == $PARAM) SSDP_ms_send_resp($TARGET_HOST, $phyinf, $max_age, $date, $location, $server, $srv_type, $uuid."::".$srv_type);
			}
			$child_dpath = $path.":".$InDeX."/devdesc/device/deviceList/device";
			SSDP_ms_walk_device_by_srvtype($PARAM, $TARGET_HOST, $phyinf, $child_dpath, $max_age, $date, $location, $server);
		}
	}
	exit;
}

echo "Invalid command:[".$SEARCH_TARGET."]!!!\n ";
?>
