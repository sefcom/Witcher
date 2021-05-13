<?php
	@include('sessionCheck.inc');
	$postLoginMenuNew	=	array(	"Configuration"	=>	array(	"System"	=>	array(	"Basic"	=>	array(	"General"	=>	"basicSettings",
																										"Time"	=>	"timeSettings"),
																					"Advanced"	=>	array(	"Hotspot"	=>	"httpRedirectSettings",
																										"Syslog"	=>	"logSettings")),
															"IP"	=>	array(	"Basic"	=>	array(	"IP Settings"	=>	"basicSettings"),
																					"Advanced"	=>	array(	"IP Settings"	=>	"dhcpsSettings")),
															"Wireless"	=>	array(	"Basic"	=>	array(	"Wireless Settings"	=>	"wlanSettings:vapSettings",
																										"QoS Settings"	=>	"wlanSettings:wmmSettings"),
																					"Advanced"	=>	array(	"Wireless Settings"	=>	"wlanSettings",
																										"QoS Settings"	=>	"wlanSettings:wmmSettings")),
															"Security"	=>	array(	"Basic"	=>	array(	"Profile Settings"	=>	"wlanSettings:vapSettings"),
																					"Advanced"	=>	array(	"Rogue AP"	=>	"wlanSettings:apList",
																											"MAC Authentication"	=>	"wlanSettings:accessControlSettings",
																											"Radius Server Settings"	=>	"info802dot1x")),
															"Wireless Bridge"	=>	array(	"Bridging and Repeating"	=>	"wlanSettings:wdsSettings"),
														),
							"Monitoring"	=>	array(	"System"	=>	array(	"System"	=>	"System"),
														"Wireless Stations"	=>	array(	"Wireless Stations"	=>	"wlanStations"),
														"Rogue AP"	=>	array(	"Rogue AP List"	=>	"rogueAp"),
														"Logs"	=>	array(	"Logs"	=>	"logs"),
														"Statistics"	=>	array(	"Statistics"	=>	"statistics")
														),
							"Maintenance"	=>	array(	"Password"	=>	array(	"Change Password"	=>	"localSettings"),
														"Reset"	=>	array(	"Reboot AP"	=>	"localSettings",
																			"Restore Defaults"	=>	"configbackup"),
														"Remote Management"	=>	array(	"SNMP"	=>	"remoteSettings",
																						"Remote Console"	=>	"remoteSettings",
																						"TR 069"	=>	"remoteSettings"),
														"Upgrade"	=>	array(	"Firmware Upgrade"	=>	"upgradefirmware",
																				"Config Management"	=>	"configbackup")),
							"Support"	=>	array(	"Documentation"	=>	array(	"Documentation"	=>	"documentation"),
													"Report Issues"	=>	array(	"Report Issues"	=>	"reportIssues")
													));


	$preLoginMenu = array(	"Login"	=>	array(	"Login"	=>	array(	"Login"	=>	"Login")),
							"Help"	=>	array(	"Help"	=>	array(	"Help"	=>	"Help")));

										
?>