<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function INET_getpathbyinf($name)
{
	$path = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	if ($path == "") return "";
	$inet = query($path."/inet");
	if ($inet == "") return "";
	$path = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
	if ($path == "") return "";
	return $path;
}

function INET_getinetinfo($inet, $info)
{
    $infp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
    if ($infp != "") return query($infp."/".$info);
    return "";
}

/* return 1 if the ipaddr is a valid v4 dot-number IP address. */
function INET_validv4addr($ipaddr)
{
	$host = ipv4hostid($ipaddr, 0);
	if ($host == ""||$host == 0) return 0;
	$network = ipv4networkid($ipaddr, 8);
	if (cut($network, 0, ".") < 1) return 0;
	if (cut($network, 0, ".") > 223) return 0;
	if (cut($network, 0, ".") == 127) return 0;
	return 1;
}

function INET_validv4host($ipaddr, $mask)
{
	$hostid = ipv4hostid($ipaddr, $mask);
	if ($hostid == "") return 0;
	$maxhid	= ipv4maxhost($mask);
	if ($hostid > 0 && $hostid < $maxhid) return 1;
	return 0;
}

/* return 1 if the ipaddr has the same network id with the lanip. */
function INET_validv4network($ipaddr, $lanip, $mask)
{
	$ipid = ipv4networkid($ipaddr, $mask);
	if ($ipid == "") return 0;
	$lanid = ipv4networkid($lanip, $mask);
	if ($lanid == "") return 0;
	if ($ipid == $lanid) return 1;
	else return 0;
}

function INET_ARP($ipaddr)
{
	$file = "/proc/net/arp";
	$arplist = fread("", "/proc/net/arp");

	if (ipv6checkip($ipaddr)=="1")
	{
		setattr("/runtime/tmp/neigh", "get", "ip -6 neigh show > /var/tmp/neigh.txt");
		get("s", "/runtime/tmp/neigh");
		$neigh_show = fread("s", "/var/tmp/neigh.txt");
		return scut($neigh_show, 3, $ipaddr);
		unlink("/var/tmp/neigh.txt");
		del("/runtime/tmp/neigh");
	}
	
    return scut($arplist, 2, $ipaddr);
}
/*
  Strip off the leading zeros of an valid IPv4 address, and return the stripped address string.
  The function doesn't check the validity of the incoming IPv4 address. so, call it after having performed other checks.
*/
function INET_addr_strip0($ip)
{
	$new_ip="";
	
	if( cut_count($ip, ".")!=4 ) return $ip;
	
	$i = 0;
	while ($i < 4)
	{
		$part = cut($ip, $i, ".");
		if ( isdigit($part)==0 ) return $ip;
		$dec = strtoul($part, 10);
		if($i==0) $new_ip = $dec;
		else      $new_ip = $new_ip.".".$dec;
		$i++;
	}
	return $new_ip;
}
?>
