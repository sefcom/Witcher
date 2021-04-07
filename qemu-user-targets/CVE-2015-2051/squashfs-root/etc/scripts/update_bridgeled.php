<?
//this script needs argument EVENT, we need this to control WIFI LEDs

include "/etc/services/PHYINF/phywifi.php";

echo "#!/bin/sh\n";

if($EVENT == "LED_OFF")
{
	echo "usockc /var/gpio_ctrl WIFI5_LED_OFF\n";
	echo "usockc /var/gpio_ctrl WIFI2_LED_OFF\n";
}

if($EVENT == "LED_ON")
{
	if(is_active("WIFISTA-1.1") != 1)
	{
		return;
	}

	$freq = get_phyinf_freq("WIFISTA-1.1");
	if($freq == "")
	{
		return;
	}
	
	if($freq == 5)
	{
		echo "usockc /var/gpio_ctrl WIFI5_LED_BLINK_SLOW\n";
	}
	else
	{
		echo "usockc /var/gpio_ctrl WIFI2_LED_BLINK_SLOW\n";
	}
}

if($EVENT == "BAND24G_ASSOCIATED")
{
	if(is_active("WIFISTA-1.1") != 1)
	{
		return;
	}

	echo "usockc /var/gpio_ctrl WIFI2_LED_ON\n";
}

if($EVENT == "BAND5G_ASSOCIATED")
{
	if(is_active("WIFISTA-1.1") != 1)
	{
		return;
	}

	echo "usockc /var/gpio_ctrl WIFI5_LED_ON\n";
}

?>
