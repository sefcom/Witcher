HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/config.php";

function phytype($protype)
{
	if($protype=="STATIC" || $protype=="DHCP" || $protype=="PPPoE") { return "eth"; }
	else if($protype=="USB3G") { return "3g"; }
	else if($protype=="WISP") { return "wisp"; }
	
	TRACE_error("unkonw type:[".$protype."]\n");
}

$nodebase = "/runtime/hnap/SetInternetProfileAlpha";
$node_info = $nodebase."/InternetProfileLists/InternetProfile";
$profile_p = "/internetprofile";
$profile_e = $profile_p."/entry";
$sitesurvey_node = "/runtime/wifi_tmpnode";

set($profile_p."/max", "32"); 

$result = "OK";
fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
foreach($node_info)
{
	$DeletePro = get("x", "DeleteProfile");
	$ActivePro = get("x", "ActiveProfile");
	$ProName = get("x", "ProfileName");
	$ProType = get("x", "ProfileType");
	$HostName = get("x", "HostName");
	$MAC = get("x", "MAC");
	$IPAddr = get("x", "IPAddress");
	$SubMask = get("x", "SubnetMask");
	$Gateway = get("x", "Gateway");
	$PrimaryDNS = get("x", "DNS/Primary");
	$SecondaryDNS = get("x", "DNS/Secondary");
	$MTU = get("x", "MTU");
	$UserName = get("x", "UserName");
	$Passwd = get("x", "Password");
	$AddrMode = get("x", "AddressMode");
	$ServiceName = get("x", "ServiceName");
	$DialMode = get("x", "ReconnectMode");
	$MaxIdleTime = get("x", "MaxIdleTime");
	$SecurityType = get("x", "SecurityType");
	$PasswordLength = get("x", "PasswordLength");
	$DialNO = get("x", "DialupNumber");
	$APN = get("x", "APN");
	$Country = get("x", "Country");
	$ISP = get("x", "ISP");
	$AuthProto = get("x", "AuthProtocol");
	$SIMsts = get("x", "SIMCardStatus");

	if($DeletePro!="") /* if DeleteProfile node is not empty, it means to delete the profile. */
	{
		$seqno = get("x", "/internetprofile/seqno");
		$count = get("x", "/internetprofile/count");
		
		$del_path = XNODE_getpathbytarget("/internetprofile", "entry", "profilename", $DeletePro, 0);
		$del_uid = get("x", $del_path."/uid");
		del($del_path);
		
		$del_no = cut($del_uid, 1, "-");
		
		/* if we delete one node, it should adjust the uid, seqno and count. */
		$i=0;
		foreach($profile_e)
		{
			if($InDeX >= $del_no)
			{
				$new_uid = $del_no + $i;
				set("uid", "PRO-".$new_uid);
				$i = $i + 1;
			}
		}

		$seqno = $seqno - 1;
		$count = $count - 1;
		set($profile_p."/seqno", $seqno);
		set($profile_p."/count", $count);
	}
	else if($ActivePro!="")
	{
		$pro_p = XNODE_getpathbytarget("/internetprofile", "entry", "profilename", $ActivePro, 0);
		$uid = get("x", $pro_p."/uid");
		$type = get("x", $pro_p."/profiletype");
		
		/* need to check phystatus first */
		if(phytype($type)=="eth")
		{
			$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
			$wan1_phyinf = query($path_inf_wan1."/phyinf");
			$path_run_phyinf_wan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyinf, 0);
			$status = get("",$path_run_phyinf_wan1."/linkstatus");
			
			if( $status != "0" && $status != "") { $result = "OK"; }
			else { $result = "INFNOTFOUND"; }
		}
		else if(phytype($type)=="3g")
		{
			$device_sim = query("/runtime/device/SIM/PINsts");
			if($device_sim!="") { $result = "OK"; }
			else { $result = "INFNOTFOUND"; }
		}
		else if(phytype($type)=="wisp")
		{
			$state = query($sitesurvey_node."/state");
			if($state=="" || $state=="ALLDONE") { event("SITESURVEY"); }
		}
		
		if($result=="OK")
		{
			fwrite("a", $ShellPath, "usockc /var/wanmonitor_ctrl UNSELECTED\n");
			fwrite("a", $ShellPath, "usockc /var/wanmonitor_ctrl SELECTED:".$type.":".$uid."\n");
			
			if(phytype($type)=="wisp")
			{
				fwrite("a", $ShellPath, "sleep 2\n");
				fwrite("a", $ShellPath, "phpsh debug /etc/scripts/autoprofile/try_wisp_linkup.php ACTIVEPRO=".$uid."\n");
			}
		}
	}
	else /* if DeleteProfile node is empty, it means to add or modify the profile. */
	{
		/* using the profile name to find out the profile exists or not. */
		$pro_path = XNODE_getpathbytarget("/internetprofile", "entry", "profilename", $ProName, 0);
		
		/* init saving path of profile. */
		if($pro_path!="") /* if the profile exits. */
		{
			$config = $pro_path."/config";
			/* it need to delete the current config of profile at first. */
			set("/runtime/hnap/dummy", "");
			movc($config, "/runtime/hnap/dummy"); 
			del("/runtime/hnap/dummy");
		}
		else /* if the profile does not exit. */
		{
			if($ProType!="PPPoE")
			{
				$current_c = get("x", $profile_e."#");
				$current_c = $current_c + 1;
				$pro_path = $profile_e.":".$current_c;
				
				set($pro_path."/uid", "PRO-".$current_c);
			}
			else /* pppoe is a special case that it has only one profile, but it could modify profile name. */
			{
				$pro_path = XNODE_getpathbytarget("/internetprofile", "entry", "profiletype", $ProType, 0);
			}
			set($pro_path."/profilename", $ProName);
			set($pro_path."/profiletype", $ProType);
		}
		
		anchor($pro_path."/config");
		if($ProType=="DHCP")
		{
			set("hostname", $HostName);
			set("mac", $MAC);
		}
		else if($ProType=="STATIC")
		{
			set("ipaddr", $IPAddr);
			$mask = ipv4mask2int($SubMask);
			set("mask", $mask);
			set("gateway", $Gateway);
			if ($PrimaryDNS!="") 
			{
				set("dns/count", 1);
				set("dns/entry:1", $PrimaryDNS);
				if($SecondaryDNS!="")
				{
					set("dns/entry:2", $SecondaryDNS);
					set("dns/count", 2);
				}
			}
			else { set("dns/count", 0); }
			set("mtu", $MTU);
		}
		else if($ProType=="PPPoE")
		{
			set("username", $UserName);
			set("password", $Passwd);
			
			if ($AddrMode=="DYNAMICIP") { $static = 0; }
			else if ($AddrMode=="STATICIP") { $static = 1; }
			set("static", $static);
			
			if($static==1) { set("ipaddr", $IPAddr); }
			set("servicename", $ServiceName);
			set("dialup/mode", $DialMode);
			set("dialup/idletimeout", $MaxIdleTime);
			if ($PrimaryDNS!="") 
			{
				set("dns/count", 1);
				set("dns/entry:1", $PrimaryDNS);
				if($SecondaryDNS!="")
				{
					set("dns/count", 2);
					set("dns/entry:2", $SecondaryDNS);
				}
			}
			else { set("dns/count", 0); }
			set("mtu", $MTU);
		}
		else if($ProType=="WISP")
		{
			if($SecurityType=="NONE")
			{
				set("authtype", "OPEN");
				set("encrtype", "NONE");
			}
			else if($SecurityType=="WEP")
			{
				set("authtype", "WEPAUTO");
				set("encrtype", "WEP");
				
				if($PasswordLength=="64Hex") { $size = 64; $ascii = 0; }
				else if($PasswordLength=="64ASCII") { $size = 64; $ascii = 1; }
				else if($PasswordLength=="128Hex") { $size = 128; $ascii = 0; }
				else if($PasswordLength=="128ASCII") { $size = 128; $ascii = 1; }
				
				set("wep/size", $size);
				set("wep/ascii", $ascii);
				set("wep/defkey", 1);
				set("wep/key", $Passwd);
			}
			else if($SecurityType=="WPAPSK" || $SecurityType=="WPA2PSK" || $SecurityType=="WPA+2PSK")
			{
				set("authtype", $SecurityType);
				
				if($SecurityType=="WPAPSK") { set("encrtype", "TKIP"); }
				else if($SecurityType=="WPA2PSK") { set("encrtype", "AES"); }
				else if($SecurityType=="WPA+2PSK") { set("encrtype", "TKIP+AES"); }
				
				set("psk/passphrase", 1);
				set("psk/key", $Passwd);
				set("wpa/groupintv", 3600);
			}
		}
		else if($ProType=="USB3G")
		{
			set("dialno", $DialNO);
			set("apn", $APN);
			set("country", $Country);
			set("isp", $ISP);
			set("username", $UserName);
			set("password", $Passwd);
			set("authprotocol", $AuthProto);
			set("simcardstatus", $SIMsts);
			set("dialup/mode", $DialMode);
			set("dialup/idletimeout", $MaxIdleTime);
			set("mtu", $MTU);
		}
		else { $result = "ERROR"; }
		
		$cnt = get("x", $profile_e."#");
		set($profile_p."/seqno", $cnt+1);
		set($profile_p."/count", $cnt);
		
		/* +++ HuanYao Kang: reactive the profile if the selected profile is editing.*/
		if ($result == "OK")
		{
			$pro_uid = get("",$pro_path."/uid");
			$pro_r_p = XNODE_getpathbytarget("/runtime/internetprofile", "entry", "profileuid", $pro_uid, 0);

			if ($pro_r_p != "")
			{
				if (get("",$pro_r_p."/active") == "1")
				{
					TRACE_error("The editing profile is actived. Reactive profile:".$pro_uid);
					fwrite("a",$ShellPath, "phpsh /etc/scripts/inactiveinf.php > /dev/console \n");
					fwrite("a",$ShellPath, "phpsh /etc/scripts/activeprofile.php PROUID=".$pro_uid." > /dev/console \n");
				}
			}
		}
	}
}

if($result == "OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetInternetProfileAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetInternetProfileAlphaResult><?=$result?></SetInternetProfileAlphaResult>
    </SetInternetProfileAlphaResponse>
  </soap:Body>
</soap:Envelope>