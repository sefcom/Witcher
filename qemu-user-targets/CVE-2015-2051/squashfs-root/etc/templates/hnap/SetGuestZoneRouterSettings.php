HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result="OK";
/* those data have complete checked by client side, we just simply check at here */
$InetAcs = get("","/runtime/hnap/SetGuestZoneRouterSettings/InternetAccessOnly"); //not set, sammy
$ipaddr = get("","/runtime/hnap/SetGuestZoneRouterSettings/IPAddress");
$mask = get("","/runtime/hnap/SetGuestZoneRouterSettings/SubnetMask");
$en_dhcp = get("","/runtime/hnap/SetGuestZoneRouterSettings/DHCPServer");
$start = get("","/runtime/hnap/SetGuestZoneRouterSettings/DHCPRangeStart");
$end = get("","/runtime/hnap/SetGuestZoneRouterSettings/DHCPRangeEnd");
$leasetime = get("","/runtime/hnap/SetGuestZoneRouterSettings/DHCPLeaseTime");

if($en_dhcp != "true" && $en_dhcp != "false") { $result = "ERROR"; }

if($result == "OK")
{
	$path_inf_lan2 = XNODE_getpathbytarget("", "inf", "uid", $LAN2, 0);
	$lan2_inet = get("", $path_inf_lan2."/inet");
	$path_inet_lan2 = XNODE_getpathbytarget("inet", "entry", "uid", $lan2_inet, 0);
	TRACE_debug("path_inf_lan2=".$path_inf_lan2);
	TRACE_debug("path_inet_lan2=".$path_inet_lan2);
	
	/*	Ref. main trunk  dlob.hans\htdcos\webinc\js\adv_gzone.php
		Enable internet access only is equal to disable routing between zones.
	      /acl/obfilter (used to apply at FORWARD chain)
		    Rule FWL-1 -> drop guestzone traffic to hostzone
		    Rule FWL-2 -> drop hostzone traffic to guestzone
	      /acl/obfilter2 (apply at INPUT chain) for traffic that from guestzone and the destination is hostzone's ip or guestzone's ip.
	*/
	if ($InetAcs == "true")
	{
		set("/acl/obfilter/policy", "ACCEPT");
		set("/acl/obfilter2/policy", "ACCEPT");
	}
	else if ($InetAcs == "false")
	{
		set("/acl/obfilter/policy", "DISABLE");
		set("/acl/obfilter2/policy", "DISABLE");
	}
	
	if($ipaddr=="") 
	{ 
	    if(query($path_inet_lan2."/ipv4/ipaddr")=="")
	        { $ipaddr="192.168.7.1"; }
	    else
	        { $ipaddr=query($path_inet_lan2."/ipv4/ipaddr"); }
	}
	if($mask=="") 
	{ 
	    if(query($path_inet_lan2."/ipv4/ipaddr")=="")
	        { $mask=ipv4mask2int("255.255.255.0"); }
	    else
	        { $mask=query($path_inet_lan2."/ipv4/mask"); }	    
	}
	   
	set($path_inet_lan2."/ipv4/ipaddr", $ipaddr);
	set($path_inet_lan2."/ipv4/mask", $mask);
	
	if		 ($dnsr == "true")			set($path_inf_lan2."/dns4", "DNS4-1");
	else if($dnsr == "false")			set($path_inf_lan2."/dns4", "");
	$path_dhcps4_lan2 = XNODE_getpathbytarget("dhcps4", "entry", "uid", "DHCPS4-2", 0);
	
	if($start=="") { $start="100"; }
	if($end=="") { $end="199"; }
	if($leasetime=="") { $leasetime="10080"; }
	
	set($path_dhcps4_lan2."/start", $start);
	set($path_dhcps4_lan2."/end", $end);
	set($path_dhcps4_lan2."/leasetime", $leasetime*60);
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]\" > /dev/console\n");
if($result == "OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -k \"HNAP_".$SRVC_WLAN."\"\n");
	fwrite("a",$ShellPath, "xmldbc -t \"HNAP_".$SRVC_WLAN.":3:service ".$SRVC_WLAN." restart\"\n");
	fwrite("a",$ShellPath, "xmldbc -k \"HNAP_INET.".$LAN2."\"\n");
	fwrite("a",$ShellPath, "xmldbc -t \"HNAP_INET.".$LAN2.":3:service INET.".$LAN2." restart\"\n");
	fwrite("a",$ShellPath, "xmldbc -k \"HNAP_DHCPS4.".$LAN2."\"\n");
	fwrite("a",$ShellPath, "xmldbc -t \"HNAP_DHCPS4.".$LAN2.":3:service DHCPS4.".$LAN2." restart\"\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");	
}
?>

<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetGuestZoneRouterSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetGuestZoneRouterSettingsResult><?=$result?></SetGuestZoneRouterSettingsResult>
	</SetGuestZoneRouterSettingsResponse>
</soap:Body>
</soap:Envelope>
