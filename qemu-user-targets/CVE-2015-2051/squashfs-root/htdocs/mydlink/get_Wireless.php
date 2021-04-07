<? 
$displaypass = $_GET["displaypass"];
$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN, 0);
$path_wlan_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf_wlan."/wifi"), 0);
$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
$wan1_phyinf = query($path_run_inf_wan1."/phyinf");
$path_run_wan1_phyinf = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wan1_phyinf, 0);
$channel=query($path_phyinf_wlan."/media/channel");
if ($channel == 0) $auto_ch=1; else $auto_ch=0;
if(query($path_phyinf_wlan."/active")=="1" && query($path_phyinf_wlan."/media/channel")=="0")
{
	$path_run_phyinf_wlan = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $WLAN, 0);
    $channel = query($path_run_phyinf_wlan."/media/channel");
}

$authtype=query($path_wlan_wifi."/authtype");
$encrypt=query($path_wlan_wifi."/encrtype");

//default
$key="";
$pskkey="";
$auth_type=0;
$wepauth_type=0;
$cipher_type=0;
$weplen=0;
$wepformat=0;
$defkey=0;
$wpa_type=0;
$eapip=0;
$eapport=0;
$eapkey=0;

if($authtype == "OPEN" && $encrypt == "NONE")
{
	$auth_type=0;
}
else
{
	if($authtype != "OPEN" && $authtype != "SHARED" && $authtype != "WEPAUTO")//WPA
	{
		if($authtype == "WPAPSK" || $authtype == "WPA") 	     {$auth_type=2;}
		else if($authtype == "WPA2PSK" || $authtype == "WPA2")   {$auth_type=4;}
		else if($authtype == "WPA+2PSK" || $authtype == "WPA+2") {$auth_type=6;}
	
		if (strstr($authtype,"PSK") != "")	$wpa_type=2;
		else								$wpa_type=1;
		
		if 		($encrypt=="TKIP")	$cipher_type=1;
		else if ($encrypt=="AES")	$cipher_type=2;
		else						$cipher_type=3;

		$pskkey=get("x",$path_wlan_wifi."/nwkey/psk/key");
		$eapip=query($path_wlan_wifi."/nwkey/eap/radius");
		$eapport=query($path_wlan_wifi."/nwkey/eap/port");
		$eapkey=query($path_wlan_wifi."/nwkey/eap/secret");
	}
	else//WEP
	{
		$auth_type=1;
		$weplen=query($path_wlan_wifi."/nwkey/wep/size");
		if($weplen==64)			$weplen=0;
		else if($weplen==128)	$weplen=1;
		
		$wepformat=query($path_wlan_wifi."/nwkey/wep/ascii");
		if ($wepformat!=1) $wepformat=2;
		
		$defkey=query($path_wlan_wifi."/nwkey/wep/defkey");
		if($defkey=="") $defkey=1;
		
		if($authtype == "OPEN")	 $wepauth_type=1;
		else					 $wepauth_type=2;
		$id=query($path_wlan_wifi."/nwkey/wep/defkey");
		$key=get("x",$path_wlan_wifi."/nwkey/wep/key:".$id);
	}
}

?>
<Wireless>
	<Wireless_sta><? echo query($path_phyinf_wlan."/active"); ?></Wireless_sta> 
	<dns><? echo query($path_run_inf_wan1."/inet/ipv4/dns"); ?></dns> 
	<f_auto_channel><?=$auto_ch?></f_auto_channel>
	<ssid><? echo get("x", $path_wlan_wifi."/ssid"); ?></ssid> 
	<channel><?=$channel?></channel> 
	<mac><? echo query("/runtime/devdata/lanmac"); ?></mac> 
	<f_authentication><?=$auth_type?></f_authentication>
	<f_wep_auth_type><?=$wepauth_type?></f_wep_auth_type>
	<cipher_type><?=$cipher_type?></cipher_type>
	<f_wep_len><?=$weplen?></f_wep_len>
	<f_wep_format><?=$wepformat?></f_wep_format>
	<f_wep_def_key><?=$defkey?></f_wep_def_key>
	<f_wep><? if ($displaypass==1){echo $key;}else{echo "";} ?></f_wep>
	<f_wpa_psk_type><?=$wpa_type?></f_wpa_psk_type>
	<f_wps_psk><? if ($displaypass==1){echo $pskkey;} ?></f_wps_psk>
	<f_radius_ip1><?=$eapip?></f_radius_ip1>
	<f_radius_port1><?=$eapport?></f_radius_port1>
	<f_radius_secret1><? if ($displaypass==1){echo $eapkey;} ?></f_radius_secret1>
</Wireless>

