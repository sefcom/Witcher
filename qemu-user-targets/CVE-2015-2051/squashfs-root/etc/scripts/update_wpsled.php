<?
//this script needs argument EVENT, we need this to control WIFI LEDs

include "/etc/services/PHYINF/phywifi.php";

echo "#!/bin/sh\n";

function get_wifi_bss($uid)
{
	$dev = devname($uid);
	if($dev == "")
		return error(9);
	
	$cmd = "wl -i ".$dev." bss";
	setattr("/runtime/".$dev."/bss", "get", $cmd);
	$bss = get("", "/runtime/".$dev."/bss");
	
	return $bss;
}

if(get("x", "/device/layout") != "router" && get("x", "/device/layout") != "bridge")
{
	return;
}

if($EVENT == "WPS_IN_PROGRESS" || $EVENT == "WPS_OVERLAP")
{
	if(is_active("BAND24G-1.1") == 1)
	{
		echo "usockc /var/gpio_ctrl WIFI2_LED_BLINK_FAST\n";
	}

	if(is_active("BAND5G-1.1") == 1 || is_active("BAND5G-2.1") == 1)
	{
		echo "usockc /var/gpio_ctrl WIFI5_LED_BLINK_FAST\n";
	}
}

if($EVENT == "WPS_SUCCESS" || $EVENT == "WPS_ERROR" || $EVENT == "WPS_NONE")
{
	/* use wl command to check wifi interface is down or up,
	   because schedule does not set wifi active node as 0. */
	
	if(get_wifi_bss("BAND24G-1.1")=="up")
	{
		echo "usockc /var/gpio_ctrl WIFI2_LED_ON\n";
	}

	if(get_wifi_bss("BAND5G-1.1")=="up" || get_wifi_bss("BAND5G-2.1")=="up")
	{
		echo "usockc /var/gpio_ctrl WIFI5_LED_ON\n";
	}	
}

?>
