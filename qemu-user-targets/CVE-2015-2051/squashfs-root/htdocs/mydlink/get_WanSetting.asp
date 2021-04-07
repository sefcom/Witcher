<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$path_inf_wan2 = XNODE_getpathbytarget("", "inf", "uid", $WAN2, 0);
$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
$wan1_inet = query($path_inf_wan1."/inet"); 
$wan1_phyinf = query($path_inf_wan1."/phyinf");
$wan2_inet = query($path_inf_wan2."/inet");
$path_wan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet, 0);
$path_wan1_phyinf = XNODE_getpathbytarget("", "phyinf", "uid", $wan1_phyinf, 0);
$path_wan2_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan2_inet, 0); 

$wan1_schedule = query($path_inf_wan1."/schedule");
$path_wan1_schedule = XNODE_getpathbytarget("/schedule", "entry", "uid", $wan1_schedule, 0);
$schedule = query($path_wan1_schedule."/description");
if ($schedule == "") $schedule = "Always";

$Gatewayname=query("/device/gw_name");
$Type="";
$Username="";
$Password="";
$MaxIdletime=0;
$ServiceName="";
$AutoReconnect="false";
if(query($path_run_inf_wan1."/inet/ipv4/valid") == 1)
{
	$ipaddr=query($path_run_inf_wan1."/inet/ipv4/ipaddr");
	$gateway=query($path_run_inf_wan1."/inet/ipv4/gateway");
	$mask=ipv4int2mask(query($path_run_inf_wan1."/inet/ipv4/mask"));	
	$dns1=query($path_run_inf_wan1."/inet/ipv4/dns");
	$dns2=query($path_run_inf_wan1."/inet/ipv4/dns:2");
}
$MTU=1500;
$mac=query("/runtime/devdata/wanmac");

$mode=query($path_wan1_inet."/addrtype");
if($mode == "ipv4")
{
	anchor($path_wan1_inet."/ipv4");
	if(query("static") == 1) //-----Static     
	{
		$Type="0";
		$ipaddr=query("ipaddr");
		$gateway=query("gateway");
		$mask=ipv4int2mask(query("mask"));
		$StaticMTU=query("mtu");
		$dns1=query("dns/entry");
		$dns2=query("dns/entry:2");
		if(query($path_wan1_phyinf."/macaddr")!="")
		{
			$mac=query($path_wan1_phyinf."/macaddr");
		}
	}
	if(query("static") == 0) //-----DHCP
	{
		$Type="1";
		$DhcpMTU=query("mtu");
		if(query($path_wan1_phyinf."/macaddr")!="")
		{
			$mac=query($path_wan1_phyinf."/macaddr");
		}
	}
}
else if($mode == "ppp4" && query($path_wan1_inet."/ppp4/over") == "eth") //-----PPPoE
{
	anchor($path_wan1_inet."/ppp4");
	if(query("static") == 1)
	{
		$PPPoEdynamic="false";
		$PPPoEipaddr=query($path_wan1_inet."/ppp4/ipaddr");
	}
	else
	{
		$PPPoEdynamic="true";
		$PPPoEipaddr=query($path_run_inf_wan1."/inet/ppp4/local"); 
	}
	$Type="2";
	$mask="255.255.255.255";
	$gateway=query($path_run_inf_wan1."/inet/ppp4/peer");
	$dns1=query($path_run_inf_wan1."/inet/ppp4/dns"); 
	$dns2=query($path_run_inf_wan1."/inet/ppp4/dns:2"); 
	$PPPoEUsername=get("x","username"); 
	$PPPoEPassword=get("x","password");
	$PPPoEMaxIdletime=query("dialup/idletimeout");
	$PPPoEServiceName=get("x","pppoe/servicename");  
	if(query($path_wan1_phyinf."/macaddr")!="")
	{
		$mac=query($path_wan1_phyinf."/macaddr");
	}
	if(query("dialup/mode") == "auto")
	{
		$PPPoEReconnect="0";
	}
	else if(query("dialup/mode") == "ondemand")
	{
		$PPPoEReconnect="1";
	}
	else if(query("dialup/mode") == "manual")
	{
		$PPPoEReconnect="2";
	}
	$PPPoEMTU=query("mtu");
	if(query("dns/count")==0)
	{
		$PPPoEDDNS="true";
	}
	else
	{
		$PPPoEDDNS="false";
	}
}
else if($mode == "ppp4" && query($path_wan1_inet."/ppp4/over") == "pptp")	//-----PPTP
{
	anchor($path_wan2_inet."/ipv4");

	if(query("static") == 1)
	{
		$PPTPipaddr=query("ipaddr");
		$gateway=query("gateway");
		$mask=ipv4int2mask(query("mask")); 
		$dns1=query("dns/entry");
		$dns2=query("dns/entry:2");
		$PPTPdynamic="false";	
	}
	else
	{
		$PPTPdynamic="true";
	}	

	$Type="3";
	$PPTPUsername=get("x",$path_wan1_inet."/ppp4/username");
	$PPTPPassword=get("x",$path_wan1_inet."/ppp4/password");
	$PPTPMaxIdletime=query($path_wan1_inet."/ppp4/dialup/idletimeout");
	$PPTPServiceName=get("x",$path_wan1_inet."/ppp4/pptp/server");    
	if(query($path_wan1_phyinf."/macaddr")!="")
	{
		$mac=query($path_wan1_phyinf."/macaddr");
	}
	if(query($path_wan1_inet."/ppp4/dialup/mode") == "auto")
	{
		$PPTPReconnect="0";
	}
	else if(query($path_wan1_inet."/ppp4/dialup/mode") == "ondemand")
	{
		$PPTPReconnect="1";
	}
	else if(query($path_wan1_inet."/ppp4/dialup/mode") == "manual")
	{
		$PPTPReconnect="3";
	}
	$PPTPMTU=query($path_wan1_inet."/ppp4/mtu");
}
else if($mode == "ppp4" && query($path_wan1_inet."/ppp4/over") == "l2tp")	//-----L2TP
{
	anchor($path_wan2_inet."/ipv4");
	if(query("static") == 1)
	{
		$ipaddr=query("ipaddr");		
		$gateway=query("gateway");
		$mask=ipv4int2mask(query("mask"));
		$dns1=query("dns/entry");
		$dns2=query("dns/entry:2");
		$L2TPdynamic="false";
	}
	else
	{
		$L2TPdynamic="true";
	}

	$Type="4";
	$L2TPUsername=get("x",$path_wan1_inet."/ppp4/username");
	$L2TPPassword=get("x",$path_wan1_inet."/ppp4/password");
	$L2TPMaxIdletime=query($path_wan1_inet."/ppp4/dialup/idletimeout");
	$L2TPServiceName=get("x",$path_wan1_inet."/ppp4/l2tp/server");
	if(query($path_wan1_phyinf."/macaddr")!="")
	{
		$mac=query($path_wan1_phyinf."/macaddr");
	}
	if(query($path_wan1_inet."/ppp4/dialup/mode") == "auto")
	{
		$L2TPReconnect="0";
	}
	else if(query($path_wan1_inet."/ppp4/dialup/mode") == "ondemand")
	{
		$L2TPReconnect="1";
	}
	if(query($path_wan1_inet."/ppp4/dialup/mode") == "manual")
	{
		$L2TPReconnect="2";
	}
	$L2TPMTU=query($path_wan1_inet."/ppp4/mtu");
}
if(query("/advdns/enable") == 1)
{
	$adv_dns_enable="true";
}
else
{
	$adv_dns_enable="false";
}

if($ipaddr=="0.0.0.0")
{
	$ipaddr="";
}
if($mask=="0.0.0.0")
{
	$mask="";
}
if($gateway=="0.0.0.0")
{
	$gateway="";
}
if($dns1=="0.0.0.0")
{
	$dns1="";
}
if($dns2=="0.0.0.0")
{
	$dns2="";
}

?>
<wansetting>
<config.wan_ip_mode><?=$Type?></config.wan_ip_mode>
<config.wan_dhcp_gw_name><?=$Gatewayname?></config.wan_dhcp_gw_name>
<mac_clone><?=$mac?></mac_clone>
<config.wan_primary_dns><?=$dns1?></config.wan_primary_dns>
<config.wan_secondary_dns><?=$dns2?></config.wan_secondary_dns>
<config.wan_static_mtu><?=$StaticMTU?></config.wan_static_mtu>
<config.wan_dhcp_mtu><?=$DhcpMTU?></config.wan_dhcp_mtu>
<config.wan_ppp_mtu><?=$PPPoEMTU?></config.wan_ppp_mtu>
<config.wan_l2tp_mtu><?=$L2TPMTU?></config.wan_l2tp_mtu>
<config.wan_ip_address><?=$ipaddr?></config.wan_ip_address>
<config.wan_subnet_mask><?=$mask?></config.wan_subnet_mask>
<config.wan_gateway><?=$gateway?></config.wan_gateway>
<config.pppoe_use_dynamic_address><?=$PPPoEdynamic?></config.pppoe_use_dynamic_address>
<config.pppoe_netsniper>false</config.pppoe_netsniper>
<config.pppoe_xkjs>false</config.pppoe_xkjs>
<config.xkjs_mode>0</config.xkjs_mode>
<config.pppoe_username><?=$PPPoEUsername?></config.pppoe_username>
<config.pppoe_password><?=$PPPoEPassword?></config.pppoe_password>
<config.pppoe_service_name><?=$PPPoEServiceName?></config.pppoe_service_name>
<config.pppoe_ip_address><?=$PPPoEipaddr?></config.pppoe_ip_address>
<pppoe_use_dynamic_dns_radio><?=$PPPoEDDNS?></pppoe_use_dynamic_dns_radio>
<config.pppoe_max_idle_time><?=$PPPoEMaxIdletime?></config.pppoe_max_idle_time>
<ppp_schedule_control_0><?=$schedule?></ppp_schedule_control_0>
<pptp_schedule_control_0><?=$schedule?></pptp_schedule_control_0>
<l2tp_schedule_control_0><?=$schedule?></l2tp_schedule_control_0>
<pppoe_reconnect_mode_radio><?=$PPPoEReconnect?></pppoe_reconnect_mode_radio>
<wan_pptp_use_dynamic_carrier_radio><?=$PPTPdynamic?></wan_pptp_use_dynamic_carrier_radio>
<config.wan_pptp_server><?=$PPTPServiceName?></config.wan_pptp_server>
<config.wan_pptp_username><?=$PPTPUsername?></config.wan_pptp_username>
<config.wan_pptp_password><?=$PPTPPassword?></config.wan_pptp_password>
<config.wan_pptp_max_idle_time><?=$PPTPMaxIdletime?></config.wan_pptp_max_idle_time>
<pptp_reconnect_mode_radio><?=$PPTPReconnect?></pptp_reconnect_mode_radio>
<wan_l2tp_use_dynamic_carrier_radio><?=$L2TPdynamic?></wan_l2tp_use_dynamic_carrier_radio>
<config.wan_l2tp_server><?=$L2TPServiceName?></config.wan_l2tp_server>
<config.wan_l2tp_username><?=$L2TPUsername?></config.wan_l2tp_username>
<config.wan_l2tp_password><?=$L2TPPassword?></config.wan_l2tp_password>
<config.wan_l2tp_max_idle_time><?=$L2TPMaxIdletime?></config.wan_l2tp_max_idle_time>
<l2tp_reconnect_mode_radio><?=$L2TPReconnect?></l2tp_reconnect_mode_radio>
</wansetting>
