HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$rlt = "OK";
$nodebase = "/runtime/hnap/SetVlanSettings/";

$Mode                = query($nodebase."devmode");
$Type                = query($nodebase."wantype");
$EnableVLAN          = query($nodebase."active");
$interid_pppoe       = query($nodebase."interid_pppoe");
$voipid_pppoe        = query($nodebase."voipid_pppoe");
$iptvid_pppoe        = query($nodebase."iptvid_pppoe");
$interid_dhcp        = query($nodebase."interid_dhcp");
$voipid_dhcp         = query($nodebase."voipid_dhcp");
$iptvid_dhcp         = query($nodebase."iptvid_dhcp");
$lan1_pppoe          = query($nodebase."lanport/lan1_pppoe");
$lan2_pppoe          = query($nodebase."lanport/lan2_pppoe");
$lan3_pppoe          = query($nodebase."lanport/lan3_pppoe");
$lan4_pppoe          = query($nodebase."lanport/lan4_pppoe");
$lan1_dhcp           = query($nodebase."lanport/lan1_dhcp");
$lan2_dhcp           = query($nodebase."lanport/lan2_dhcp");
$lan3_dhcp           = query($nodebase."lanport/lan3_dhcp");
$lan4_dhcp           = query($nodebase."lanport/lan4_dhcp");
$lan1                = query($nodebase."lanport/lan1");
$lan2                = query($nodebase."lanport/lan2");
$lan3                = query($nodebase."lanport/lan3");
$lan4                = query($nodebase."lanport/lan4");
$wlan01_pppoe        = query($nodebase."wlanport/wlan01_pppoe");
$wlan02_pppoe        = query($nodebase."wlanport/wlan02_pppoe");
$wlan01_dhcp         = query($nodebase."wlanport/wlan01_dhcp");
$wlan02_dhcp         = query($nodebase."wlanport/wlan02_dhcp");
$wlan01              = query($nodebase."wlanport/wlan01");
$wlan02              = query($nodebase."wlanport/wlan02");
$current_vlan = query("/device/vlan/active");

if($Mode == "ROUTER")
{
	anchor("/device/vlan");
	if($EnableVLAN == "1")
	{
		set("active", "1");
		if($Type == "DHCP")
		{
			set("interid_dhcp", $interid_dhcp);
			set("voipid_dhcp", $voipid_dhcp);
			set("iptvid_dhcp", $iptvid_dhcp);
			set("interid", $interid_dhcp);
			set("voipid", $voipid_dhcp);
			set("iptvid", $iptvid_dhcp);

			set("lanport/lan1_dhcp", $lan1_dhcp);
			set("lanport/lan2_dhcp", $lan2_dhcp);
			set("lanport/lan3_dhcp", $lan3_dhcp);
			set("lanport/lan4_dhcp", $lan4_dhcp);
			set("wlanport/wlan01_dhcp", $wlan01_dhcp);
			set("wlanport/wlan02_dhcp", $wlan02_dhcp);
			set("wlanport/wlan11_dhcp", $wlan01_dhcp);
			set("wlanport/wlan12_dhcp", $wlan02_dhcp);
			set("wlanport/wlan21_dhcp", $wlan01_dhcp);
			set("wlanport/wlan22_dhcp", $wlan02_dhcp);
		}
		else if($Type == "UniPPPoE")
		{
			set("interid_pppoe", $interid_pppoe);
			set("voipid_pppoe", $voipid_pppoe);
			set("iptvid_pppoe", $iptvid_pppoe);
			set("interid", $interid_pppoe);
			set("voipid", $voipid_pppoe);
			set("iptvid", $iptvid_pppoe);

			set("lanport/lan1_pppoe", $lan1_pppoe);
			set("lanport/lan2_pppoe", $lan2_pppoe);
			set("lanport/lan3_pppoe", $lan3_pppoe);
			set("lanport/lan4_pppoe", $lan4_pppoe);
			set("wlanport/wlan01_pppoe", $wlan01_pppoe);
			set("wlanport/wlan02_pppoe", $wlan02_pppoe);
			set("wlanport/wlan11_pppoe", $wlan01_pppoe);
			set("wlanport/wlan12_pppoe", $wlan02_pppoe);
			set("wlanport/wlan21_pppoe", $wlan01_pppoe);
			set("wlanport/wlan22_pppoe", $wlan02_pppoe);
		}
		set("lanport/lan1", $lan1);
		set("lanport/lan2", $lan2);
		set("lanport/lan3", $lan3);
		set("lanport/lan4", $lan4);
		set("wlanport/wlan01", $wlan01);
		set("wlanport/wlan02", $wlan02);
		set("wlanport/wlan11", $wlan01);
		set("wlanport/wlan12", $wlan02);
		set("wlanport/wlan21", $wlan01);
		set("wlanport/wlan22", $wlan02);		
	}
	else
		set("active", "0");
}
else
	set("/device/vlan/active", "0");

if($current_vlan!=$EnableVLAN || $EnableVLAN=="1") $rlt = "REBOOT";

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Vlan Change\" > /dev/console\n");

if($rlt=="REBOOT")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
}
else if($rlt=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetVlanSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetVlanSettingsResult><?=$rlt?></SetVlanSettingsResult>
</SetVlanSettingsResponse>
</soap:Body>
</soap:Envelope>

