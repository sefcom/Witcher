<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/webinc/config.php";

function UrlEncode($value)	
{
	$ValueUrlEncode = urlencode("e",$value);
	return $ValueUrlEncode;
}
function show_result($cmd)	{echo $cmd;}
function error($code)		{return $code;}

//Use this function to prevent from duplicate service
function service_add($servicecmd,$newsvc_name,$svc_action)
{
	if($servicecmd != "")
	{
		$delimiter1 = "\n";
		$cnt = cut_count($servicecmd, $delimiter1);
		$i = 0;
		$found = 0;
		while ($i < $cnt)
		{
			$token = cut($servicecmd, $i, $delimiter1);
			if($token != "")
			{
				$svn_name = scut($token,1,"");
				if($svn_name == $newsvc_name)
				{
					$found = 1;
					break;
				}
			}
			$i++;
		}
	}
	if($servicecmd == "" || $found == 0)
	{
		$servicecmd = $servicecmd."service ".$newsvc_name." ".$svc_action."\n";
	}
	return $servicecmd;
}

function mdb_get($cmd_name,$WAN1,$WLAN1)
{
	$mydlink_path = "/mydlink";
	$run_mdb_path = "/runtime/mydlink/mdb";
	if($cmd_name == "fw_version")		{show_result(UrlEncode(query("/runtime/device/firmwareversion")));}
	else if($cmd_name == "dev_model")	{show_result(UrlEncode(query("/runtime/device/modelname")));}
	else if($cmd_name == "dev_name")	{show_result(UrlEncode(query("/device/gw_name")));}
	else if($cmd_name == "admin_passwd")
	{
		$found = 0;
		$cnt = query("/device/account/count");
		foreach("/device/account/entry")
		{
			if ($InDeX > $cnt) break;
			$name = query("name");
			if(tolower($name) == "admin")
			{
				show_result(UrlEncode(query("password")));
				$found = 1;
				break;
			}
		}
		if($found != 1) {return error("1");}
	}
	else if($cmd_name == "http_port")
	{
		/*this is mean mydlink agent which port to connect api on loopback.*/
		$http_port="80";
		show_result($http_port);
	}
	else if($cmd_name == "sp_http_port")
	{
		$sp_http_port=query("/webaccess/httpport");
		if($sp_http_port == "") {$sp_http_port="8181";}
		show_result($sp_http_port);
	}
	else if($cmd_name == "sp_https_port")
	{
		$sp_https_port=query("/webaccess/httpsport");
		if($sp_https_port == "") {$sp_https_port="4433";}
		show_result($sp_https_port);
	}
	else if($cmd_name == "https_port")
	{
		/*this is mean mydlink agent which port to connect api on loopback.*/
		$https_port="443";
		show_result($https_port);
	}
	else if($cmd_name == "register_st")
	{
		$reg_status = query($mydlink_path."/register_st");
		if($reg_status == "") {$reg_status=0;}
		show_result($reg_status);
	}
	else if($cmd_name == "mac_addr")
	{
		$mac_addr = query("/runtime/devdata/lanmac");
		$mac_fmt = cut($mac_addr,0,":").cut($mac_addr,1,":").cut($mac_addr,2,":").
				   cut($mac_addr,3,":").cut($mac_addr,4,":").cut($mac_addr,5,":");
		show_result(toupper($mac_fmt));
	}
	else if(strstr($cmd_name,"attr_") != "")
	{
		show_result(query($mydlink_path."/".$cmd_name));
	}
	else if($cmd_name == "wan_mode")
	{
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
		$wan1_inet = query($path_inf_wan1."/inet");
		$path_wan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
		$wan_mode = query($path_wan1_inet."/ipv4/static");
		show_result($wan_mode);
	}
	else if($cmd_name == "static_ip_info")
	{
		$static_ip_info = "";
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
		$wan1_inet = query($path_inf_wan1."/inet");
		$path_wan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
		$is_static = query($path_wan1_inet."/ipv4/static");
		if($is_static == "1")
		{
			$mask = "";
			anchor($path_wan1_inet."/ipv4");
			if(query("mask") != "") {$mask = ipv4int2mask(query("mask"));}
			$static_ip_info = "I=".query("ipaddr")."&N=".$mask."&G=".query("gateway").
							  "&D1=".query("dns/entry:1")."&D2=".query("dns/entry:2");
		}
		show_result($static_ip_info);
	}
	else if($cmd_name == "eth_cable_st")
	{
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
		$wan1_phyuid = query($path_inf_wan1."/phyinf");
		$path_rwan1_phy = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyuid, 0);
		$linkstatus = query($path_rwan1_phy."/linkstatus");
		$eth_cable_st = 0;
		if($linkstatus != "0" && $linkstatus != "") {$eth_cable_st = 1;}
		show_result($eth_cable_st);
	}
	else if($cmd_name == "wlan_conn_st")
	{
		// TODO function 
		// For now, we always return 0.
		$wlan_conn_st = 0;
		show_result($wlan_conn_st);
	}
	else if($cmd_name == "cur_ip_info")
	{
		$path_rwan1_inf = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
		anchor($path_rwan1_inf."/inet/ipv4");
		$cur_ip_info = "";
		$mask = "";
		if(query("mask") != "") {$mask = ipv4int2mask(query("mask"));}
		$cur_ip_info = "I=".query("ipaddr")."&N=".$mask."&G=".query("gateway").
					   "&D1=".query("dns:1")."&D2=".query("dns:2");
		show_result($cur_ip_info);
	}
	else if($cmd_name == "pppoe_info")
	{
		$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
		$wan1_inet = query($path_inf_wan1."/inet");
		$path_inet_wan1 = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
		anchor($path_inet_wan1);
		$addrtype = query("addrtype");
		$over     = query("ppp4/over");
		if($addrtype == "ppp4" && $over == "eth") {$pppoe_enable = "1";}
		else									  {$pppoe_enable = "0";}
		$username = UrlEncode(query("ppp4/username"));
		$password = UrlEncode(query("ppp4/password"));
		$pppoe_info = "E=".$pppoe_enable."&U=".$username."&P=".$password;
		show_result($pppoe_info);                                                                                                                                                                                                                                                                                                                                                                                   
	}
	else if($cmd_name == "wlan_st_info")
	{
		$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
		$wlan_st_info = query($path_phyinf_wlan1."/active");
		if($wlan_st_info == "") {$wlan_st_info = 0;}
		show_result($wlan_st_info);
	}
	else if($cmd_name == "wlan_info")
	{
		$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
		$path_wifi_wlan1 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf_wlan1."/wifi"), 0);
		anchor($path_wifi_wlan1);
		$op_mode = query($mydlink_path."/wifi/op_mode");
		if($op_mode == "") {$op_mode=0;}
		$ssid = UrlEncode(query("ssid"));
		$str_auth = query("authtype");
		$str_encrypt = query("encrtype");
		if($str_auth == "OPEN" && $str_encrypt == "NONE") {$security_type = 0;}
		else if($str_auth == "OPEN" && $str_encrypt == "WEP") {$security_type = 1;}
		else if($str_auth == "SHARED" && $str_encrypt == "WEP") {$security_type = 2;}
		else if($str_auth == "WPAPSK") {$security_type = 3;}
		else if($str_auth == "WPA2PSK") {$security_type = 4;}
		else if($str_auth == "WPA+2PSK") {$security_type = 5;}
		else {$security_type = 0;}
		
		if($str_encrypt == "WEP")
		{
			if(query("nwkey/wep/size") == 64) {$encrypt_type = 0;}
			else 												{$encrypt_type = 1;}			
		}
		else if($str_encrypt == "AES")	{$encrypt_type = 2;}
		else if($str_encrypt == "TKIP")	{$encrypt_type = 3;}
		else if($str_encrypt == "TKIP+AES")	{$encrypt_type = 4;}
		else {$encrypt_type = 0;}
		
		if($str_encrypt == "WEP") 			   {$key = UrlEncode(query("nwkey/wep/key:1"));}
		else if(strstr($str_auth,"PSK") != "") {$key = UrlEncode(query("nwkey/psk/key"));}
		
		$wlan_info = "M=".$op_mode."&I=".$ssid."&S=".$security_type."&E=".$encrypt_type."&K=".$key;
		show_result($wlan_info);
	}
	else if($cmd_name == "wlan_ap_list")
	{
		$SiteSurveyPath = "/runtime/wifi_tmpnode/sitesurvey";
		$wlan_ap_list = "";
		foreach($SiteSurveyPath."/entry")
		{
			if($InDeX > 1) {$wlan_ap_list = $wlan_ap_list.";";}
			anchor($SiteSurveyPath."/entry:".$InDeX);
			$ssid = UrlEncode(query("ssid"));
			$op_mode = 0; // For now, always 0.
			$channel = query("channel");
			if($channel == "" || $channel < 0) {$channel=0;}
			$str_auth = query("authtype");
			$str_encrypt = query("encrtype");
			$signal = query("rssi");
			if($signal == "" || $signal < 0) {$signal=0;}
			
			if($str_auth == "OPEN" && $str_encrypt == "NONE") {$security_type = 0;}
			else if($str_encrypt == "WEP")
			{
				if($str_auth == "WEPAUTO" || $str_auth == "OPEN") {$security_type = 1;} //Mydlink doesn't define this type. We temporarily see it as WEP OPEN.
				else if($str_auth == "SHARED") 					  {$security_type = 2;}
				else 						   					  {$security_type = 0;}
			}
			else if($str_auth == "WPAPSK")   {$security_type = 3;}
			else if($str_auth == "WPA2PSK")  {$security_type = 4;}
			else if($str_auth == "WPA+2PSK") {$security_type = 5;}
			else {$security_type = 0;}
			
			if($str_encrypt == "WEP")
			{
				//We couldn't get the WEP key size, so we can't tell whether it's WEP-64bit or WEP-128bit.
				//For now, we use WEP-64bit as default.
				$encrypt_type = 0;
			}
			else if($str_encrypt == "AES")	{$encrypt_type = 2;}
			else if($str_encrypt == "TKIP")	{$encrypt_type = 3;}
			else if($str_encrypt == "TKIP+AES")	{$encrypt_type = 4;}
			else {$encrypt_type = 0;}
			$wlan_ap_list  = $wlan_ap_list."I=".$ssid."&M=".$op_mode."&C=".$channel."&S=".
							 $security_type."&E=".$encrypt_type."&P=".$signal;
		}
		if($wlan_ap_list == "") {$wlan_ap_list = "I=&M=0&C=0&S=0&E=0&P=0";}
		show_result($wlan_ap_list);
	}
	else if($cmd_name == "mdb_st")
	{
		$mdb_set = query($run_mdb_path."/".$cmd_name);
		if($mdb_set == "") {$mdb_set = 1;}
		show_result($mdb_set);
	}
	else	{return error("1");}
	
	return 0;
}

function mdb_set($cmd_name,$arg)
{
	$run_mdb_path = "/runtime/mydlink/mdb";
	$mdb_tmp_path = $run_mdb_path."/tmp";
	set($run_mdb_path."/mdb_st",0);
	if($cmd_name != "")
	{
		$found = 0;
		$mdb_tmp_cnt = query($mdb_tmp_path."/entry#");
		if($mdb_tmp_cnt == "") $mdb_tmp_cnt = 0;
		foreach($mdb_tmp_path."/entry")
		{
			if ($InDeX > $mdb_tmp_cnt) break;
			$name = query("name");
			if($name == $cmd_name)
			{
				$found = 1;
				set("value",$arg);
				break;
			}
		}
		
		if($found == 0)
		{
			$next_ID = $mdb_tmp_cnt+1;
			set($mdb_tmp_path."/entry:".$next_ID."/name",$cmd_name);
			set($mdb_tmp_path."/entry:".$next_ID."/value",$arg);
		}
	}
	show_result($arg);
	set($run_mdb_path."/mdb_st",1);
	return 0;
}
	
function mdb_apply($WAN1,$WLAN1)
{
	$mydlink_path = "/mydlink";
	$run_mdb_path = "/runtime/mydlink/mdb";
	$mdb_tmp_path = $run_mdb_path."/tmp";
	
	$servicecmd="";
	$dbsave=0;
	$mdb_tmp_cnt = query($mdb_tmp_path."/entry#");
	if($mdb_tmp_cnt == "") {$mdb_tmp_cnt = 0;}
	foreach($mdb_tmp_path."/entry")
	{
		if ($InDeX > $mdb_tmp_cnt) break;
		$cmd_name = query("name");
		$cmd_value = query("value");
		
		if($cmd_name == "admin_passwd")
		{
			$cnt = query("/device/account/count");
			foreach("/device/account/entry")
			{
				if ($InDeX > $cnt) break;
				$name = query("name");
				if(tolower($name) == "admin")
				{
					set("password",$cmd_value);
					$servicecmd = service_add($servicecmd,"DEVICE.ACCOUNT","restart");
					$dbsave = 1;
					break;
				}
			}
		}
		else if($cmd_name == "register_st")
		{
			set($mydlink_path."/register_st",$cmd_value);
			$dbsave = 1;
		}
		else if(strstr($cmd_name,"attr_") != "")
		{
			set($mydlink_path."/".$cmd_name,$cmd_value);
			$dbsave = 1;
		}
		else if($cmd_name == "wan_mode")
		{
			$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
			$wan1_inet = query($path_inf_wan1."/inet");
			$path_inet_wan1 = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
			set($path_inet_wan1."/ipv4/static",$cmd_value);
			$servicecmd = service_add($servicecmd,"WAN","restart");
			$dbsave = 1;
		}
		else if($cmd_name == "static_ip_info")
		{
			$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
			$wan1_inet = query($path_inf_wan1."/inet");
			$path_inet_wan1 = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
			anchor($path_inet_wan1."/ipv4");
			$delimiter1 = "&";
			$delimiter2 = "=";
			$cnt = cut_count($cmd_value, $delimiter1);
			$i = 0;
			while ($i < $cnt)
			{
				$token = cut($cmd_value, $i, $delimiter1);
				if (charcodeat($token,0) == "I") //IP address
				{
					$Value = cut($token, 1, $delimiter2);
					set("ipaddr",$Value);
				}
				else if (charcodeat($token,0) == "N") //Netmask
				{
					$Value = cut($token, 1, $delimiter2);
					set("mask",ipv4mask2int($Value));
				}
				else if (charcodeat($token,0) == "G") //Gateway
				{
					$Value = cut($token, 1, $delimiter2);
					set("gateway",$Value);
				}
				else if (charcodeat($token,0) == "D" && charcodeat($token,1) == "1") //1st DNS
				{
					$Value = cut($token, 1, $delimiter2);
					set("dns/entry:1",$Value);
					if($Value != "") {set("dns/count",query("dns/count")+1);}
				}
				else if (charcodeat($token,0) == "D" && charcodeat($token,1) == "2") //2nd DNS
				{
					$Value = cut($token, 1, $delimiter2);
					set("dns/entry:2",$Value);
					if($Value != "") {set("dns/count",query("dns/count")+1);}
				}
				$i++;
			}
			$servicecmd = service_add($servicecmd,"WAN","restart");
			$dbsave = 1;
		}
		else if($cmd_name == "pppoe_info")
		{
			$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
			$wan1_inet = query($path_inf_wan1."/inet");
			$path_inet_wan1 = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
			anchor($path_inet_wan1);
			$delimiter1 = "&";
			$delimiter2 = "=";
			$cnt = cut_count($cmd_value, $delimiter1);
			$i = 0;
			while ($i < $cnt)
			{
				$token = cut($cmd_value, $i, $delimiter1);
				if (charcodeat($token,0) == "E") // Enable or disable the PPPoE 
				{
					$Value = cut($token, 1, $delimiter2);
					if($Value == 1)
					{
						set("addrtype","ppp4");
						set("ppp4/over","eth");
						set("ppp4/static","0"); //Dynamic as default
						del("ppp4/ipaddr");
					}
					else // If we disable PPPoE, we currently use DHCP mode as default
					{
						set("addrtype","ipv4");
						set("ipv4/static","0");
					}
				}
				else if(charcodeat($token,0) == "U")// Username of the PPPoE account
				{
					$Value = cut($token, 1, $delimiter2);
					set("ppp4/username",$Value);
				}
				else if(charcodeat($token,0) == "P")// Password of the PPPoE account
				{
					$Value = cut($token, 1, $delimiter2);
					set("ppp4/password",$Value);
				}
				$i++;
			}
			$servicecmd = service_add($servicecmd,"WAN","restart");
			$dbsave = 1;
		}
		else if($cmd_name == "wlan_st_info")
		{
			$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
			set($path_phyinf_wlan1."/active",$cmd_value);
			$servicecmd = service_add($servicecmd,"PHYINF.WIFI","restart");
			$dbsave = 1;
		}
		else if($cmd_name == "wlan_info")
		{
			$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
			$path_wifi_wlan1 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf_wlan1."/wifi"), 0);
			anchor($path_wifi_wlan1);
			$delimiter1 = "&";
			$delimiter2 = "=";
			$cnt = cut_count($cmd_value, $delimiter1);
			$is_wep = 0;
			$i = 0;
			while ($i < $cnt)
			{
				$token = cut($cmd_value, $i, $delimiter1);
				if (charcodeat($token,0) == "M") // Wireless operation mode
				{
					$Value = cut($token, 1, $delimiter2);
					// We save to mydlink root
					set($mydlink_path."/wifi/op_mode",$Value);
				}
				else if (charcodeat($token,0) == "I") // SSID of the connecting/connected AP
				{
					$Value = cut($token, 1, $delimiter2);
					set("ssid", $Value);
				}
				else if (charcodeat($token,0) == "S") // Security type
				{
					$Value = cut($token, 1, $delimiter2);
					if($Value == 0) //None
					{
						set("authtype", "OPEN");
						set("encrtype", "NONE");
					}
					else if($Value == 1) //WEP, OPEN
					{
						set("authtype", "OPEN");
					}
					else if($Value == 2) //WEP, SHARED
					{
						set("authtype", "SHARED");
					}
					else if($Value == 3) //WPA-PSK
					{
						set("authtype", "WPAPSK");
					}
					else if($Value == 4) //WPA2-PSK
					{
						set("authtype", "WPA2PSK");
					}
					else if($Value == 5) //WPA+2PSK
					{
						set("authtype", "WPA+2PSK");
					}
					else //Default
					{
						set("authtype", "OPEN");
					}
				}
				else if (charcodeat($token,0) == "E") //  Encryption type
				{
					$Value = cut($token, 1, $delimiter2);
					if($Value == 0) // WEP, 64bit 
					{
						$is_wep = 1;
						set("encrtype", "WEP");
						set("nwkey/wep/size",64);
						set("nwkey/wep/ascii",1);
					}
					else if($Value == 1) // WEP, 128bit 
					{
						$is_wep = 1;
						set("encrtype", "WEP");
						set("nwkey/wep/size",128);
						set("nwkey/wep/ascii",1);
					}
					else if($Value == 2) // AES
					{
						set("encrtype", "AES");
					}
					else if($Value == 3) // TKIP
					{
						set("encrtype", "TKIP");
					}
					else if($Value == 4) // TKIP or AES
					{
						set("encrtype", "TKIP+AES");
					}
					else //Default
					{
						set("encrtype", "NONE");
					}
				}
				else if (charcodeat($token,0) == "K") //  Encryption key
				{
					$Value = cut($token, 1, $delimiter2);
					if($is_wep == 1)
					{
						set("nwkey/wep/defkey",1);
						set("nwkey/wep/key:1",$Value);
					}
					else
					{
						set("nwkey/psk/key", $Value);
					}
				}
				$i++;
			}
			$servicecmd = service_add($servicecmd,"PHYINF.WIFI","restart");
			$dbsave = 1;
		}
	}
	
	del($run_mdb_path);
	if($servicecmd != "" || $dbsave == 1)
	{
		fwrite("w+", $_GLOBALS["SCRIPTFILE"], "#!/bin/sh\n");
		if($dbsave != 0)	  {fwrite("a", $_GLOBALS["SCRIPTFILE"], "event DBSAVE\n");}
		if($servicecmd != "") {fwrite("a", $_GLOBALS["SCRIPTFILE"], $servicecmd);}
	}
	
	return 0;
}

function mdb_main($WAN1,$WLAN1)
{
	$URLDECODED_ARGV = urlencode("d",$_GLOBALS["ARGV"]);
	if		($_GLOBALS["ACTION"]=="GET")   {return mdb_get($_GLOBALS["CMD"],$WAN1,$WLAN1);}
	else if	($_GLOBALS["ACTION"]=="SET")   {return mdb_set($_GLOBALS["CMD"],$URLDECODED_ARGV);}
	else if	($_GLOBALS["ACTION"]=="APPLY") {return mdb_apply($WAN1,$WLAN1);}
	else {return error("1");}
}
$ret = mdb_main($WAN1,$WLAN1);
?>