<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/wifi.php";
include "/htdocs/phplib/getchlist.php";

/****************************************************************************/
function result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}
/****************************************************************************/

//++++ hendry add for dfs 
function execute_cmd($cmd)
{
	fwrite("w","/var/run/exec.sh",$cmd);
	event("EXECUTE");
}

function setToRuntimeNode($blocked_channel, $timeleft)
{
	/* find blocked channel if already in runtime node */
	$blocked_chn_total = query("/runtime/dfs/blocked/entry#");
	/* if blocked channel exist before, use the old index. */
	$index = 1;
	while($index <= $blocked_chn_total)
	{
		if($blocked_chn_total == 0) {break;}
		$ch = query("/runtime/dfs/blocked/entry:".$index."/channel");
		if($ch == $blocked_channel)
		{
			break;	
		}
		$index++;
	}
	set("/runtime/dfs/blocked/entry:".$index."/channel",$blocked_channel);
	execute_cmd("xmldbc -t \"dfs-".$blocked_channel.":".$timeleft.":xmldbc -X /runtime/dfs/blocked/entry:".$index."\"");
	//execute_cmd("xmldbc -t \"dfs-".$blocked_channel.":5:xmldbc -X /runtime/dfs/blocked/entry:".$index."\"");
}
//---- hendry add

function valid_mac($validMac)
{
    if ($validMac=="") return 0;

    $num = cut_count($validMac, ":");
    if ($num != 6) return 0;
    $num--;
    while ($num >= 0)
    {
        $tmpMac = cut($validMac, $num, ":");
        if (isxdigit($tmpMac) == 0) return 0;
		if (strlen($tmpMac) > 2) return 0;
        $num--;
    }
	$validMac = tolower($validMac);
	if ($validMac=="00:00:00:00:00:00" || $validMac=="ff:ff:ff:ff:ff:ff") return 0;
    return 1;
}

function revise_mac($mac)
{
	if ($mac=="") return "";
	$num = cut_count($mac, ":");
	$num--;
	while ($num >= 0)
	{
		$tmp = cut($mac, $num, ":");
		if (strlen($tmp) == 1) $tmp = "0".$tmp;
		$ret_mac = $tmp.$delimiter.$ret_mac;
		$delimiter=":";
		$num--;
	}
	return $ret_mac;
}
/****************************************************************************/

function check_authtype_encrtype($path, $authtype, $encrtype)
{
	$err = 0;
	if ($authtype=="OPEN" || $authtype=="SHARED" || $authtype=="WEPAUTO")
	{
		if ($encrtype=="NONE" || $encrtype=="WEP") return "OK";
		return result("FAILED", $path."/encrtype", i18n("Invalid encryption type."));
	}
	else if ($authtype=="WPA"		|| $authtype=="WPA2"	|| $authtype=="WPA+2" ||
			 $authtype=="WPAPSK"	|| $authtype=="WPA2PSK"	|| $authtype=="WPA+2PSK")
	{
		if ($encrtype=="TKIP" || $encrtype=="AES" || $encrtype=="TKIP+AES") return "OK";
		return result("FAILED", $path."/encrtype", i18n("Invalid encryption type."));
	}
	else if ($authtype=="WAPI"	|| $authtype=="WAPIPSK")
	{
		if ($encrtype=="SMS4") return "OK";
		return result("FAILED", $path."/encrtype", i18n("Invalid encryption type."));
	}
	
	return result("FAILED", $path."/authtype", i18n("Invalid authentication type."));
}

function check_wep($p)
{
	$p = $p."/nwkey";
	$size	= query($p."/wep/size");
	$ascii	= query($p."/wep/ascii");
	$idx	= query($p."/wep/defkey");

	$err_s	= 0;	$err_i	= 0;
	$err_kl	= 0;	$err_km	= 0;

	$i = 0;
	while ($i < 1)
	{
		$i++;
		$len = strlen(query($p."/wep/key:".$idx));
		if ($idx < 1 || $idx > 4) {$err_i++; break;}
		if ($size=="")
		{
			if		($len == "5"  || $len == "10")	$size = 64;
			else if ($len == "13" || $len == "26")	$size = 128;
			else	{$err_s++; break;}
		}
		if ($ascii=="")
		{
			if		($len == 5  || $len == 13) $ascii = 1;
			else if ($len == 10 || $len == 26) $ascii = 0;
			else	{$err_s++; break;}
		}

		if ($ascii == 0)
		{
			if		($size == 64)	$len = 10;
			else if ($size == 128)	$len = 26;
			else					{$err_s++; break;}
		}
		else
		{
			$ascii = 1;
			if		($size == 64)	$len = 5;
			else if ($size == 128)	$len = 13;
			else					{$err_s++; break;}
		}

		foreach ($p."/wep/key")
		{
			if ($VaLuE == "")
			{
				if ($InDeX != $idx)		 continue;
				else					{$err_kl = $InDeX; break;}
			}
			if (strlen($VaLuE) != $len)	{$err_kl = $InDeX; break;}
			if ($ascii==1)
			{
				if (isprint($VaLuE)!=1)	{$err_km = $InDeX; break;}
			}
			else
			{
				if (isxdigit($VaLuE)!=1){$err_km = $InDeX; break;}
			}
		}
	}

	/* set the validated value. */
	set($p."/wep/ascii", $ascii);
	set($p."/wep/size",  $size);

	if ($err_s > 0)
		return result("FAILED", $p."/wep/size",
				i18n("The size of WEP key must be 64 or 128 bits.")." ".
				i18n("The WEP key should be 5, 10, 13 or 26 characters long."));
	if ($err_i > 0)
		return result("FAILED", $p."/wep/defkey",
				i18n("The default WEP key index should be between 1 to 4."));
	if ($err_kl > 0)
		return result("FAILED", $p."/wep/key:".$err_kl,
				i18n("The length of WEP key $1 should be $2.",$err_kl, $len));
	if ($err_km > 0)
		return result("FAILED", $p."/wep/ascii",
				i18n("The WEP key should be printable characters or hexadecimal numbers."));

	return "OK";
}

function check_radius($p)
{
	$p = $p."/nwkey";
	$val = query($p."/wpa/groupintv");
	if($val!="")
	{
		if (isdigit($val)!=1 || $val > 65535 || $val < 30)
			return result("FAILED", $p."/wpa/groupintv",
					i18n("The value of the group key update interval should be between 30 to 65535."));				
	}
	if (query($p."/eap#")==0)
		return result("FAILED", $p."/eap",
				"CAN NOT find the EAP nodes!");
				
	foreach($p."/eap")
	{
		if($InDeX > 1 && query($p."/eap:".$InDeX."/radius")=="")
			break;	//we consider that we don't have second radius server
		
		if (INET_validv4addr(query($p."/eap:".$InDeX."/radius"))!=1)
			return result("FAILED", $p."/eap:".$InDeX."/radius",
				i18n("The IP address of RADIUS is invalid."));
		$val = query($p."/eap:".$InDeX."/port");
		if (isdigit($val)!=1 || $val > 65535 || $val < 1)
			return result("FAILED", $p."/eap:".$InDeX."/port",
				i18n("The value of the port should be 1~65535. (recommend port : 1812)."));
		$val = query($p."/eap:".$InDeX."/secret");
	$len = strlen($val);
	if ($len > 64 || $len < 1)
			return result("FAILED", $p."/eap:".$InDeX."/secret",
				i18n("The length of the secret should be between 1 to 64."));
	if (isprint($val)!=1)
			return result("FAILED", $p."/eap:".$InDeX."/secret",
				i18n("The secret should be printable characters."));
	}
	return "OK";
}

function check_wapias($p)
{
	$p = $p."/nwkey";
	if (query($p."/wapi#")==0)
		return result("FAILED", $p."/wapi", "CAN NOT find the WAPI nodes!");
	if (INET_validv4addr(query($p."/wapi/as"))!=1)
		return result("FAILED", $p."/wapi/as",
				i18n("The IP address of the authentication server is invalid."));
	$val = query($p."/wapi/port");
	if ($val > 65535 || $val < 1)
		return result("FAILED", $p."/wapi/port",
				i18n("The value of the port should be 1~65535."));

	return "OK";
}

function check_psk($p)
{
	$p = $p."/nwkey";
	$type = query($p."/psk/passphrase");
	$key = query($p."/psk/key");
	$gtk = query($p."/rekey/gtk");
	$len = strlen($key);
	if ($type == "")
	{
		if ($len>=8 && $len<=63)	$type = 1;
		else if ($len==64)			$type = 0;
		else return result("FAILED", $p."/psk/key",
						i18n("The length of the passphrase (PSK) must be at least 8 characters."));
	}
	if ($type==0)
	{
		if ($len!=64 || isxdigit($key)!=1)
			return result("FAILED", $p."/psk/key",
				i18n("The PSK should be 64 characters of the hexadecimal number."));
	}
	else if ($len<8 || $len>63 || isprint($key)!=1)
	{
		return result("FAILED", $p."/psk/key",
				i18n("The PSK should be 8~63 printable characters."));
	}
	if ($gtk!="")
	{
		if (isdigit($gtk)!=1)
			return result("FAILED", $p."/rekey/gtk",
					i18n("The value of rekey interval should be a number."));
	}
	set($p."/psk/passphrase", $type);
	
	$val = query($p."/wpa/groupintv");
	if($val!="")
	{
		if (isdigit($val)!=1 || $val > 65535 || $val < 30)
			return result("FAILED", $p."/wpa/groupintv",
					i18n("The value of the group key update interval should be between 30 to 65535."));				
	}
	
	return "OK";
}
function check_pin($pin)
{
	if (strlen($pin)!=8) return 0;
	$i = 0;	$pow = 3; $sum = 0;
	while($i < 8)
	{
		$sum = $pow * substr($pin, $i, 1) + $sum;
		if ($pow == 3)	$pow = 1;
		else			$pow = 3;
		$i++;
	}
	$sum = $sum % 10;
	if ($sum == 0)	return 1;
	else			return 0;
}
function check_wps($p)
{
	$val = query($p."/wps/enable");
	$authtype = query($p."/authtype");
	if($val==1 && $authtype=="SHARED")
		return result("FAILED", $p."/wps/pin", i18n("Can't choose shared key when wps is enable !!"));
	if ($val!=1) set($p."/wps/enable", 0);

	$pin = query($p."/wps/pin");
	if ($pin!="" && check_pin($pin)!=1)
		return result("FAILED", $p."/wps/pin", i18n("The WPS pin code is invalid."));

	$val = query($p."/wps/configured");
	if ($val != 1) set($p."/wps/configured", 0);
	
	return "OK";
}
function check_acl($p)
{
	$val = query($p."/acl/policy");
	if ($val!="ACCEPT" && $val!="DROP") set($p."/acl/policy", "DISABLED");

	$max = query($p."/acl/max");
	$cnt = query($p."/acl/count");
	$num = query($p."/acl/entry#");
	if ($cnt > $max)
		return result("FAILED", $p."/acl/count", i18n("The ACL rules are full."));

	/* delete the extra rule. */
	while ($num > $cnt) { del($p."/acl/entry:".$num); $num--; }

	$seqno = query($p."/acl/seqno");
	foreach ($p."/acl/entry")
	{
		$mac = query("mac");
		if (valid_mac($mac)==0)
			return result("FAILED", $p."/acl/entry:".$InDeX, i18n("Invalid MAC address value."));

		/* Convert to lower case */
		$mac = tolower($mac);
		$mac = revise_mac($mac);
		set("mac", $mac);

		$uid = query("uid");
		/* Check empty UID */
		if ($uid == "")
		{
			$uid = "ACL-".$seqno;
			set("uid", $uid);
			$seqno++;
		}
		/* Check duplicated UID */
		if ($$uid == "1")
			return result("FAILED", $p.":".$InDeX."/uid", "Duplicated UID - ".$uid);

    	$$uid = "1";
	}
	/* Check duplicate mac after all MACs are valid & in lower case.*/
	foreach ($p."/acl/entry")
	{
		$m1 = query("mac");
		$i2 = $InDeX;
		//TRACE_debug("acl[".$i2."]");
		while ($i2 < $cnt)
		{
			$i2++;
			$m2 = query($p."/acl/entry:".$i2."/mac");
			$m2 = tolower($m2);
			//TRACE_debug("acl:".$i2."-".$m2);
			if ($m1 == $m2) {$err++; break;}
		}
		if ($err > 0)
			return result("FAILED", $p."/acl/entry:".$InDeX, i18n("Duplicate MAC address."));
	}
	set($p."/acl/seqno", $seqno);
	return "OK";
}

function check_dfs($current_channel)
{
	/*1. Update new blocked channel to runtime nodes */
	$blockch_list = fread("", "/proc/dfs_blockch");
	//format is : "100,960;122,156;" --> channel 100, remaining time is 960 seconds
	//								 --> channel 122, remaining time is 156 seconds
	$ttl_block_chn = cut_count($blockch_list, ";")-1;
	$i = 0;
	while($i < $ttl_block_chn)
	{
		//assume that blocked channel can be more than one channel.
		$ch_field = cut($blockch_list, $i, ';');	//i mean each "100,960;" represent 1 field 
		$ch = cut ($ch_field, 0, ',');
		$remaining_time = cut ($ch_field, 1, ',');
		setToRuntimeNode($ch, $remaining_time);
		$i++;
	}
	

	/*2. Check if blocked dfs channels matched our current channel */
	$ct=1;
	$ttl_blocked_chn = query("/runtime/dfs/blocked/entry#");	
	while($ct <= $ttl_blocked_chn)
	{
		if($ttl_blocked_chn == 0) {break;}
		$blck_chnl = query("/runtime/dfs/blocked/entry:".$ct."/channel");
		if($current_channel == $blck_chnl) return 0;
		$ct++;
	}
	return 1;
}


function check_media($band, $p)
{
	/* re-assign path at $p./media */
	$p = $p."/media";
	/* wlmode */
	$wlmode	= query($p."/wlmode");
	TRACE_debug("FATLADY: wlmode=".$wlmode);
	if ($wlmode == "b"  || $wlmode == "g"  || $wlmode == "a"  || $wlmode == "n" ||
		$wlmode == "bg" || $wlmode == "bn" || $wlmode == "gn" || $wlmode == "an" ||
		$wlmode == "bgn"|| $wlmode == "ac" || $wlmode == "acn" || $wlmode == "acna")
	{
		/* This make the script run faster. */
	}
	else
		return result("FAILED", $p."/wlmode",
				i18n("The wireless mode is invalid.")." (".$wlmode.")");
	/* beacon */
	$v = query($p."/beacon");
	TRACE_debug("FATLADY: beacon=".$v);
	if ($v<20 || $v>1000)
		return result("FAILED", $p."/beacon",
				i18n("Invalid value of the beacon interval.")." ".
				i18n("The value should be between 20 to 1000."));
	/* fragthresh */
	$v = query($p."/fragthresh");
	TRACE_debug("FATLADY: fragthresh=".$v);
	$mod = $v%2;
	if ($v<1500 || $v>2346 || $mod!=0)
		return result("FAILED", $p."/fragthresh",
					i18n("Invalid value of the fragmentation threshold.")." ".
					i18n("The value should be between 1500 to 2346, and even number only."));
	/* rtsthresh */
	$v = query($p."/rtsthresh");
	TRACE_debug("FATLADY: rtsthresh=".$v);
	if ($v<256 || $v>2346)
		return result("FAILED", $p."/rtsthresh",
					i18n("Invalid value of the RTS threshold.")." ".
					i18n("The value should be between 256 to 2346."));
	/* ctsmode */
	/* dtim */
	$v = query($p."/dtim");
	TRACE_debug("FATLADY: dtim=".$v);
	if (isdigit($v)!=1 || $v < 1 || $v > 255)
		return result("FAILED", $p."/dtim",
					i18n("Invalid value of DTIM.")." ".
					i18n("The value should be between 1 to 255."));
	/* channel */
	$v = query($p."/channel");
	TRACE_debug("FATLADY: channel=".$v);
	if ($v != 0)
	{
		if ($band=="BAND5G") 
		{
			$band = "a";
			//hendry, we check for dfs blocked channel if A band
			if (check_dfs($v)!=1) 
			{
				return result("FAILED", $p."/channel", i18n("Selected channel is blocked. Please select other channel !"));	
			}
		}
		else 
			$band="g";
		$clist = WIFI_getchannellist($band);
		$count = cut_count($clist,",");
		$i = 0;
		$found = 0;
		while($i < $count)
		{
			if ($v==cut($clist, $i, ",")) {$found++; break;}
			$i++;
		}
		if ($found==0)
			return result("FAILED", $p."/channel",
					i18n("Invalid channel number.")." (".$v.")".
					i18n("The valid channel number should be in the following list.").
					" (".$clist.")");
	}
	/* tx rate */
	$valid = 1;
	if ($wlmode=="n" || $wlmode=="bn" || $wlmode=="gn" || $wlmode=="bgn" || $wlmode=="an")
	{
		/* dot11n/mcs */
		$auto = query($p."/mcs/auto");
		TRACE_debug("FATLADY: mcs=".$auto);
		if ($auto!=1)
		{
			set($p."/mcs/auto", 0);	/* it must be 0 if not 1. */
			$idx = query($p."/mcs/index");
			if ($idx<0 || $idx>15) $valid=0;
		}
	}
	else
	{
		$v = query($p."/txrate");
		TRACE_debug("FATLADY: txrate=".$v);
		if ($v!="auto")
		{
			if ($wlmode=="bg" || $wlmode=="g" || $wlmode=="a")
			{
				if ($v=="1" || $v=="2" || $v=="5.5" || $v=="11" ||
					$v=="6" || $v=="9" || $v=="12" || $v=="18" ||
					$v=="24" || $v=="36" || $v=="48" || $v=="54") $valid=1;
				else $valid=0;
			}
			else /* it must be 11b. */
			{
			 	if ($v=="1" || $v=="2" || $v=="5.5" || $v=="11") $valid=1;
				else $valid=0;
			}
		}
	}
	if ($valid == 0) return result("FAILED", $p."/txrate", i18n("Invalid Tx Rate."));
	/* tx power */
	$v = query($p."/txpower");
	TRACE_debug("FATLADY: txpower=".$v);
	if ($v!="100" && $v!="50" && $v!="25" && $v!="12.5")
		return result("FAILED", $p."/txpower", "Invalid Tx power - ".$v.".");
	/* preamble */
	$v = query($p."/preamble");
	TRACE_debug("FATLADY: preamble=".$v);
	if ($v!="short" && $v!="long")
		return result("FAILED", $p."/preamble", "Invalid Preamble - ".$v);
	/* dot11n/bandwidth */
	$v = query($p."/dot11n/bandwidth");
	TRACE_debug("FATLADY: bandwidth=".$v);
	if ($v!="20" && $v!="20+40" && $v!="20+40+80") set($p."/dot11n/bandwidth", "20");
	/* dot11n/coexist */
	$v = query($p."/dot11n/bw2040coexist");
	TRACE_debug("FATLADY: bw2040coexist=".$v);
	if ($v!="0") set($p."/dot11n/bw2040coexist", "1");
	/* dot11n/guardinterval */
	$v = query($p."/dot11n/guardinterval");
	TRACE_debug("FATLADY: GI=".$v);
	if ($v!="400" && $v!="800") set($p."/dot11n/guardinterval", "400");
	/* wmm */
	$v = query($p."/wmm/enable");
	TRACE_debug("FATLADY: WMM=".$v);
	if ($v!=0) set($p."/wmm/enable", 1);

	return "OK";
}

function fatlady_wifi($prefix, $phyinf)
{
	/********************* phyinf ***********************/
	$p = XNODE_getpathbytarget($prefix, "phyinf", "uid", $phyinf, 0);
	if ($p=="")
		return result("FAILED", "",
				i18n("The interface is not activated.")." (".$phyinf.")");

	/* The UID. */
	$UID = cut($phyinf, 0, '-');
	$IDX = cut($phyinf, 1, '-');
	$major = cut($IDX, 0, '.');
	$minor = cut($IDX, 1, '.');

	/* media */
	if ($minor==1)
	{
		$ret = check_media($UID, $p);
		if ($ret != "OK") return $ret;
	}

	/********************* wifi ***********************/
	$wifi_uid = query($p."/wifi");
	$p = XNODE_getpathbytarget($prefix."/wifi", "entry", "uid", $wifi_uid, 0);
	if ($p == "") return result("FAILED", $prefix."/wifi/uid", "wifi [".$wifi_uid."] not exist!");

	/* opmode */
	$val = query($p."/opmode");
	TRACE_debug("FATLADY: opmode=".$val);
	if ($val!="STA" && $val!="AP" && $val!="WDS" && $val!="REPEATER" && $val!="APNF")
		return result("FAILED", $p."/opmode", "The operation mode of this WIFI is invalid.");
	/* ssid */
	$val = query($p."/ssid");
	$len = strlen($val);
	TRACE_debug("FATLADY: SSID=".$val.", len=".$len);
	if ($len==0) return result("FAILED", $p."/ssid", i18n("The SSID should not be empty."));
	if ($len>32) return result("FAILED", $p."/ssid", i18n("The maximum length of SSID should 32."));
	if (isprint($val)!=1)
		return result("FAILED", $p."/ssid", i18n("The SSID should be printable characters."));
	/* ssidhidden */
	$val = query($p."/ssidhidden");
	TRACE_debug("FATLADY: ssidhidden=".$val);
	if ($val!=1) set($p."/ssidhidden", 0);
	/* authtype & encryption */
	$authtype = query($p."/authtype");
	$encrtype = query($p."/encrtype");
	TRACE_debug("FATLADY: authtype=".$authtype);
	TRACE_debug("FATLADY: encrtype=".$encrtype);
	$ret = check_authtype_encrtype($p, $authtype, $encrtype);
	TRACE_debug("FATLADY: check_authtype_encrtype() return ".$ret);
	if ($ret != "OK") return $ret;
	/* nwkey */
	if ($authtype=="OPEN" || $authtype=="SHARED" || $authtype=="WEPAUTO")
	{
		if ($encrtype=="WEP")
		{
			$ret = check_wep($p);
			TRACE_debug("FATLADY: check_wep() return ".$ret);
			if ($ret!="OK") return $ret;
		}
	}
	else if ($authtype=="WPA" || $authtype=="WPA2" || $authtype=="WPA+2")
	{
		$ret = check_radius($p);
		TRACE_debug("FATLADY: check_radius() return ".$ret);
		if ($ret!="OK") return $ret;
	}
	else if ($authtype=="WAPI")
	{
		$ret = check_wapias($p);
		TRACE_debug("FATLADY: check_wapias() return ".$ret);
		if ($ret!="OK") return $ret;
	}
	else if ($authtype=="WPAPSK" || $authtype=="WPA2PSK" || $authtype=="WPA+2PSK" || $authtype=="WAPIPSK")
	{
		$ret = check_psk($p);
		TRACE_debug("FATLADY: check_psk() return ".$ret);
		if ($ret!="OK") return $ret;
	}
	/* wps */
	$ret = check_wps($p);
	TRACE_debug("FATLADY: check_wps() return ".$ret);
	if ($ret!="OK") return $ret;
	/* ACL */
	$ret = check_acl($p);
	TRACE_debug("FATLADY: check_acl() return ".$ret);
	if ($ret!="OK") return $ret;

	//TRACE_debug("====== dump ".$p." ======\n".dump(0, $p)."====== end of dump ".$p." ======\n");
	set($prefix."/valid", 1);
	TRACE_debug("FATLADY: fatlady_wifi() mark valid !\n");
	return result("OK","","");
}

function fatlady_runtime_wps($prefix, $phyinf)
{
	$p = XNODE_getpathbytarget($prefix."/runtime", "phyinf", "uid", $phyinf, 0);
	if ($p=="") return result("FAILED", "", "phyinf=[".$phyinf_uid."] not exist!");

	$method	= query($p."/media/wps/enrollee/method");
	if ($method=="pbc")
		set($p."/media/wps/enrollee/pin", "00000000");
	else if	($method=="pin")
	{
		$pin = query($p."/media/wps/enrollee/pin");
		if (check_pin($pin)!=1)
			return result("FAILED",
						$p."/media/wps/enrollee/pin",
						i18n("The WPS pin code is invalid."));
	}
	else
		return result("FAILED",
					$p."/media/wps/enrollee/method",
					"Invalid WPS method - ".$method."!");

	set($p."/media/wps/enrollee/state", "");
	set($prefix."/valid", 1);
	return result("OK","","");
}
?>
