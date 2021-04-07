<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
/*
 * The result of WPS registration protocol will be saved under /runtime/wps/setting/Xxx.
 * This file is used to move the setting to the proper place for the device.
 */
$dbg="/dev/console";

anchor("/runtime/wps/setting");
$scfg	= query("selfconfig");	TRACE_debug("selfconf = ".$scfg);
$ssid	= query("ssid");		TRACE_debug("ssid     = ".$ssid);
$atype	= query("authtype");	TRACE_debug("authtype = ".$atype);
$etype	= query("encrtype");	TRACE_debug("encrtype = ".$etype);
$defkey	= query("defkey");		TRACE_debug("defkey   = ".$defkey);
$maddr	= query("macaddr");		TRACE_debug("macaddr  = ".$maddr);
$newpwd	= query("newpassword");	TRACE_debug("newpwd   = ".$newpwd);
$devpid	= query("devpwdid");	TRACE_debug("devpwdid = ".$devpid);

/* If we started from Unconfigured AP (self configured),
 * change the setting to auto. */
if ($scfg == 1)		{ $atype = 7; $etype = 4; /* WPA/WPA2 PSK & TKIP+AES */ }
if		($atype == 0)	$atype = "OPEN"; 
else if ($atype == 1)	$atype = "SHARED"; 
else if ($atype == 2)	$atype = "WPA"; 
else if ($atype == 3)	$atype = "WPAPSK"; 
else if ($atype == 4)	$atype = "WPA2"; 
else if ($atype == 5)	$atype = "WPA2PSK"; 
else if ($atype == 6)	$atype = "WPA+2"; 
else if ($atype == 7)	$atype = "WPA+2PSK"; 
if		($etype == 0)	$etype = "NONE";
else if	($etype == 1)	$etype = "WEP";
else if	($etype == 2)	$etype = "TKIP";
else if	($etype == 3)	$etype = "AES";
else if	($etype == 4)	$etype = "TKIP+AES";

if ($PHY_UID=="")
{
	TRACE_error("/etc/scripts/wifi/wpsset.php: no PHY_UID! DO nothing & leaving...");
	return;
}
$phy	= XNODE_getpathbytarget("", "phyinf", "uid", $PHY_UID);
$wifi	= XNODE_getpathbytarget("/wifi", "entry", "uid", query($phy."/wifi"));

set($wifi."/ssid",		$ssid);
set($wifi."/authtype",	$atype);
set($wifi."/encrtype",	$etype);

if ($etype == "WEP")
{
	/* WEP keys */
	foreach ("key")
	{
		$idx = query("index");	TRACE_debug("key index = ".$idx);
		$key = query("key");	TRACE_debug("key       = ".$key);
		$fmt = query("format");	TRACE_debug("format    = ".$fmt);
		$len = query("len");	TRACE_debug("len       = ".$len);

		if ($idx < 5 && $idx > 0) { set($wifi."/nwkey/wep/key:".$idx, $key); }
	}
	if ($fmt == 1)	$fmt = 1;
	else			$fmt = 0;
	set($wifi."/nwkey/wep/defkey",	$idx);
	set($wifi."/nwkey/wep/ascii",	$fmt);
	//set($wifi."/nwkey/wep/size",	$len);
	/*
							length 					length
		Ascii 64 bit ->     5			128 bit -> 	13 			
		Hex   64 bit -> 	10			128 bit ->  26
		
		size should be filled with "64" and "128", so we derive it from above.  
	*/
	if($len == 5 || $len == 10)
		set($wifi."/nwkey/wep/size",	"64");
	else if($len == 13 || $len == 26)
		set($wifi."/nwkey/wep/size",	"128");
	else 
	{
		TRACE_debug("Error wep key length = ".$len);
		set($wifi."/nwkey/wep/size",	"64");			//just for default 
	}
}
else
{
	/* We only need the first key */
	$idx = query("key:1/index");	TRACE_debug("key index = ".$idx);
	$key = query("key:1/key");		TRACE_debug("key       = ".$key);
	$fmt = query("key:1/format");	TRACE_debug("format    = ".$fmt);
	$len = query("key:1/len");		TRACE_debug("len       = ".$len);
	if ($fmt == 1)	$fmt = 1;
	else			$fmt = 0;
	set($wifi."/nwkey/psk/passphrase", $fmt);
	set($wifi."/nwkey/psk/key", $key);
}

set($wifi."/wps/configured", "1");
?>
