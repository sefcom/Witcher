<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$radioID = query($nodebase."RadioID");

if( $radioID == "2.4GHZ" || $radioID == "RADIO_24GHz" || $radioID == "RADIO_2.4GHz")
{	$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);	}
if( $radioID == "5GHZ" || $radioID == "RADIO_5GHz")
{	$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0);	}
if( $radioID == "RADIO_2.4G_Guest")
{	$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1_GZ, 0);	} 
if( $radioID == "RADIO_5G_Guest")
{	$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2_GZ, 0);	} 

$path_wlan_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf_wlan."/wifi"), 0);
anchor($path_wlan_wifi);

if( $radioID != "2.4GHZ" && $radioID != "5GHZ" && $radioID != "RADIO_24GHz" && 
		$radioID != "RADIO_5GHz" && $radioID != "RADIO_2.4GHz" && $radioID != "RADIO_2.4G_Guest" && $radioID != "RADIO_5G_Guest")   
{ $result = "ERROR_BAD_RADIO"; } 
else
{
	if(query($nodebase."Enabled") == "false" )
	{
		set("encrtype","NONE");
		set("authtype","OPEN");
	}
	else
	{
		$type = query($nodebase."Type");
		$encrypt = query($nodebase."Encryption");
		$key = query($nodebase."Key");
		$keyRenewal = query($nodebase."KeyRenewal");
		$radiusIP1 = query($nodebase."RadiusIP1");
		$radiusPort1 = query($nodebase."RadiusPort1");
		$radiusSecret1 = query($nodebase."RadiusSecret1");
		$radiusIP2 = query($nodebase."RadiusIP2");
		$radiusPort2 = query($nodebase."RadiusPort2");
		$radiusSecret2 = query($nodebase."RadiusSecret2");
		if( $type == "WEP-OPEN" || $type == "WEP-SHARED" )
		{
			if( $encrypt == "WEP-64" )
			{
				$wepLen = 64;
			}
			else if( $encrypt == "WEP-128" )
			{
				$wepLen = 128;
			}
			else
			{
				$result = "ERROR_ENCRYPTION_NOT_SUPPORTED";
			}
			if( $type == "WEP-OPEN" )
			{
				$auth = "OPEN";
			}
			else
			{
				$auth = "SHARED";
			}
			if( $key == "" )
			{ $result = "ERROR_ILLEGAL_KEY_VALUE"; }
			if( $result == "OK" )
			{
				set("wps/configured", "1");
				set("authtype", $auth);
				set("encrtype","WEP");
				set("nwkey/wep/size", $wepLen);
				set("nwkey/wep/ascii", "0");
				set("nwkey/wep/defkey", "1"); 
				$defKey = query("nwkey/wep/defkey");
				set("nwkey/wep/key:".$defKey, $key);
			}
		}
		else if( $type == "WPA-PSK" || $type == "WPA2-PSK" || $type == "WPAORWPA2-PSK" )
		{
			if( $keyRenewal == "" )
			{
				$result = "ERROR_KEY_RENEWAL_BAD_VALUE";
			}
			//more strict
			if( $keyRenewal < 60 || $keyRenewal > 7200 )
			{
				$result = "ERROR_KEY_RENEWAL_BAD_VALUE";
			}
			if( $key == "" )
			{
				$result = "ERROR_ILLEGAL_KEY_VALUE";
			}
			if( $encrypt != "TKIP" && $encrypt != "AES" && $encrypt != "TKIPORAES" )
			{
				$result = "ERROR_ENCRYPTION_NOT_SUPPORTED";
			}
			if( $type == "WPA-PSK" )
			{ $auth = "WPAPSK"; }
			else if( $type == "WPA2-PSK" )
			{ $auth = "WPA2PSK"; }
			else
			{ $auth = "WPA+2PSK"; }
			if( $encrypt == "TKIP" )
			{ $encrypttype = "TKIP"; }
			else if( $encrypt == "AES" )
			{ $encrypttype = "AES"; }
			else
			{ $encrypttype = "TKIP+AES"; }
			if( $result == "OK" )
			{
				set("wps/configured", "1");
				set("authtype",$auth);
				set("encrtype",$encrypttype);
				set("nwkey/wep/ascii","1");
				set("nwkey/psk/key",$key);
				set("nwkey/psk/passphrase", "1");
				set("nwkey/rekey/gtk",$keyRenewal);
			}
		}
		else if( $type == "WPA-RADIUS" || $type == "WPA2-RADIUS" || $type == "WPAORWPA2-RADIUS" )
		{
			if( $keyRenewal == "" )
			{
				$result = "ERROR_KEY_RENEWAL_BAD_VALUE";
			}
			//more strict
			if( $keyRenewal < 60 || $keyRenewal > 7200 )
			{
				$result = "ERROR_KEY_RENEWAL_BAD_VALUE";
			}
			if( $encrypt != "TKIP" && $encrypt != "AES" && $encrypt != "TKIPORAES" )
			{
				$result = "ERROR_ENCRYPTION_NOT_SUPPORTED";
			}
			if( $radiusIP1 == "" || $radiusPort1 == "" || $radiusSecret1 == "" )
			{
				$result = "ERROR_BAD_RADIUS_VALUES";
			}
			if( $type == "WPA-RADIUS" )
			{ $auth = "WPA"; }
			else if( $type == "WPA2-RADIUS" )
			{ $auth = "WPA2"; }
			else
			{ $auth = "WPA+2"; }
			if( $encrypt == "TKIP" )
			{ $encrypttype = "TKIP"; }
			else if( $encrypt == "AES" )
			{ $encrypttype = "AES"; }
			else
			{ $encrypttype = "TKIP+AES"; }
			if( $result == "OK" )
			{
				set("wps/configured", "1");
				set("authtype",$auth);
				set("encrtype",$encrypttype);
				set("nwkey/wep/ascii","1");
				set("nwkey/eap/radius",$radiusIP1);
				set("nwkey/eap/port",$radiusPort1);
				set("nwkey/eap/secret",$radiusSecret1);
				set("nwkey/eap/radius2",$radiusIP2);
				set("nwkey/eap/port2",$radiusPort2);
				set("nwkey/eap/secret2",$radiusSecret2);
				set("nwkey/rekey/gtk",$keyRenewal);
			}
		}
		else
		{
			$result = "ERROR_TYPE_NOT_SUPPORT";
		}
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->WLan Change\" > /dev/console\n");
if($result=="OK")
{
	fwrite("a",$ShellPath, "service WIFI.WLAN-1 restart > /dev/console\n");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}
?>
