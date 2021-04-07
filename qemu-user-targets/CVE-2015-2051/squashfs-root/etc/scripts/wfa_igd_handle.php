<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/phyinf.php";


function exe_ouside_cmd($cmd)
{
    $ext_node="/runtime/webaccess/ext_node";
    setattr($ext_node, "get", $cmd);
	get("x", $ext_node);
	del($ext_node);
}


function get_public_ip()
{
	$ip="";
	$cmd = "urlget get http://checkip.dyndns.org";
	$result = "/var/tmp/checkip.tmp";
	$retry = 0;
    while($retry < 1)
	{
	    $cmd="urlget get http://checkip.dyndns.org > ".$result." &";
	    exe_ouside_cmd($cmd);

        $cmd="sleep 5";
		exe_ouside_cmd($cmd);

		$buf=fread("",$result);
		if($buf != "")
		{
			$idx_s=strstr($buf, "<body>")+strlen("<body>");
			$idx_e=strstr($buf, "</body>");
			$buf=substr($buf, $idx_s, $idx_e-$idx_s);
			$count=cut_count($buf, " ");
			$ip=cut($buf,$count-1, " ");
			break;
		}
		$retry=$retry+1;
	}
	unlink($result);
	return $ip;
}

function igd_prepare($wan_ip)
{
    $enable=query("/runtime/webaccess/igd/enable");
    $ipt=query("/runtime/webaccess/igd/ipt");

    if($ipt != "")
    {
        $full_ipt = "iptables -t nat -D ".$ipt;
        exe_ouside_cmd($full_ipt);
        set("/runtime/webaccess/igd/ipt", "");
    }

    if($enable=="1" && $wan_ip != "")
    {
        //PREROUTING -i eth1 -d 192.168.1.100 -p UDP --dport 41900 -j ACCEPT
        $stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
        if($stsp!="")
        {
            $dev=query($stsp."/devnam");
            $ipt="PREROUTING -i ".$dev." -d ".$wan_ip." -p UDP --dport 41900 -j ACCEPT";
            $full_ipt="iptables -t nat -I ".$ipt;

            exe_ouside_cmd($full_ipt);
            set("/runtime/webaccess/igd/ipt", $ipt);
        }
    }
    else
        {set("/runtime/webaccess/igd/enable", "0");}
}

function calcute_ds_port()
{
    $ds_port=48820;
    $inffp = XNODE_getpathbytarget("/runtime","inf","uid","LAN-1");
    $phy = query($inffp."/phyinf");
    $phyfp = XNODE_getpathbytarget("/runtime","phyinf","uid",$phy);
    $mac= query($phyfp."/macaddr");
    
    //use last two part of mac calcute a 4-digit number
    if($mac != "")
    {
        $count = cut_count($mac,":");
        $mac_cut=cut($mac, $count-2, ":").cut($mac, $count-1, ":");
        $tmp=strtoul($mac_cut, 16)%10000;
        $ds_port = 40000+$tmp;
    }
    return $ds_port;
}

if($MODE=="WAN_IP")
{
//add wan ip detect for get_direct_server API
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
   // $pub_ip=get_public_ip();
    $ds_default_port=calcute_ds_port();  //DirectServer Port
    
    set("/runtime/webaccess/wan_ext_ip", $pub_ip);
	set("/runtime/webaccess/wanip", "");
	set("/runtime/webaccess/mask", "");
    set("/runtime/webaccess/wanst", "");
    set("/runtime/webaccess/ds_default_port", $ds_default_port);
    set("/runtime/webaccess/igd/enable", "0");
    
    if($infp != "")
	{
	    $addr_type=query($infp."/inet/addrtype");
		if($addr_type == "ipv4")
		{ 
		    if(query($infp."ipv4/static")=="1")
		        {$addr_type="staitc";}
		    else
		        {$addr_type="dhcp";}
		        
		    $wan_ip=query($infp."/inet/ipv4/ipaddr");
		    $mask=query($infp."/inet/ipv4/mask");
		    set("/runtime/webaccess/wanst", $addr_type);
		}
	    else if($addr_type== "ppp4" || $addr_type == "ppp10")
		{ 
		    $wan_ip=query($infp."/inet/ppp4/local"); 
		    set("/runtime/webaccess/wanst", "ppp");
		}
		
		set("/runtime/webaccess/wanip", $wan_ip);
		set("/runtime/webaccess/mask", $mask);
		
		/*if($wan_ip != "")
		{
        	if($wan_ip==$pub_ip)
				{ set("/runtime/webaccess/wanst", "public"); }
			else
			{ 	    
			    set("/runtime/webaccess/wanst", "private"); 
			    set("/runtime/webaccess/igd/enable", "1");
			}
		}else
		{set("/runtime/webaccess/wanst", "none");}
		*/
	}
	else
	{set("/runtime/webaccess/wanst", "none");}
	
	if(query("/runtime/webaccess/wanst")!="ppp")
    {
        set("/runtime/webaccess/igd/enable", "1");
        igd_prepare($wan_ip);
    }
}

else if($MODE=="IGD")
{
    set("/runtime/webaccess/igd/status", $ST);
    
    if($ST != "OK")
    {
        set("/runtime/webaccess/igd/ext_ip", "");
        if($INT_PORT == "")  //no igd service
        {
            set("/runtime/webaccess/igd/ext_port", "");
           // set("/runtime/webaccess/igd/ext_port_s", "");
        }
        else  //port mapping fail
        {   
            //if($INT_PORT == query("/runtime/webaccess/DS_http_port"))
                set("/runtime/webaccess/igd/ext_port", "");
        }
        set("/runtime/webaccess/wanst", "noigd");        
    }
    else
    {
        set("/runtime/webaccess/igd/ext_ip", $EXT_IP);
        
        //if($INT_PORT == query("/runtime/webaccess/DS_http_port"))
        set("/runtime/webaccess/igd/ext_port", $EXT_PORT);
  
        if($EXT_IP != query("/runtime/webaccess/wan_ext_ip"))  //dual or more nat
            {set("/runtime/webaccess/wanst", "nat");}
        else
            {set("/runtime/webaccess/wanst", "upnpigd");}
    }
}
else if($MODE=="SEND_IGD")
{
    $wan_ip = query("/runtime/webaccess/wanip");
    $port= query("/runtime/webaccess/ds_default_port");
    if($wan_ip != "")
    {
        set("/runtime/webaccess/igd/enable", "1");
        igd_prepare($wan_ip);
        set("/runtime/webaccess/wanst", "noigd");
        $igd_cmd="upnpc -m ".$wan_ip." -W ";
        
        if($DS_PORT !='0')
        {
            $igd_cmd="upnpc -m ".$wan_ip." -r ";
            $port=$DS_PORT;
        }
        $igd_cmd=$igd_cmd.$port." tcp &";
        exe_ouside_cmd($igd_cmd);
    }
}

else if($MODE=="DS_IPT")  //add directserver iptable rules
{
    $ipt_cmd="";
    
    if($C_IP=="0.0.0.0")
        {$ipt_cmd="PRE.WFA -p tcp";}
    else
        {$ipt_cmd="PRE.WFA -p tcp -s ".$C_IP;}
        
    if($SSL == '0')
        {$ipt_cmd=$ipt_cmd." --dport ".$E_PORT." -j REDIRECT --to-ports ".query("/webaccess/httpport");}
    else
        {$ipt_cmd=$ipt_cmd." --dport ".$E_PORT." -j REDIRECT --to-ports ".query("/webaccess/httpsport");}
    
    if($ipt_cmd!="")
    {
        $del_ipt="iptables -t nat -D ".$ipt_cmd;
        exe_ouside_cmd($del_ipt);
        $add_ipt="iptables -t nat -A ".$ipt_cmd;
        exe_ouside_cmd($add_ipt);
    }
 
    /*$ext_ip=query("/runtime/webaccess/wan_ext_ip");
    if($ext_ip != "")
    {*/
        $ipt_cmd="";
        $wan_ip=query("/runtime/webaccess/wanip");
        $wan_st=query("/runtime/webaccess/wanst");
        $mask=query("/runtime/webaccess/mask");
      /*  if($C_IP==$ext_ip)
        {
       */
            $laninf = PHYINF_getruntimeifname("LAN-1");
            if($SSL == '0')
                {$ipt_cmd="PRE.WFA -i ".$laninf." -p tcp --dport ".$E_PORT." -j DNAT --to-destination ".$wan_ip.":".query("/webaccess/httpport")." &";}
            else
                {$ipt_cmd="PRE.WFA -i ".$laninf." -p tcp --dport ".$E_PORT." -j DNAT --to-destination ".$wan_ip.":".query("/webaccess/httpsport")." &";}
            if($ipt_cmd!="")
            {
                $del_ipt="iptables -t nat -D ".$ipt_cmd;
                exe_ouside_cmd($del_ipt);
                $add_ipt="iptables -t nat -A ".$ipt_cmd;
                exe_ouside_cmd($add_ipt);
            }
            
            //if($wan_st!="public" && $mask != "")
            if($wan_st!="ppp" && $mask != "")
            {
                $host_ip=ipv4networkid($wan_ip,$mask);
                if($SSL == '0')
                    {$ipt_cmd="PRE.WFA -p tcp -s ".$host_ip."/".$mask." --dport ".$E_PORT." -j REDIRECT --to-ports ".query("/webaccess/httpport")." &";}
                else
                    {$ipt_cmd="PRE.WFA -p tcp -s ".$host_ip."/".$mask." --dport ".$E_PORT." -j REDIRECT --to-ports ".query("/webaccess/httpsport")." &";}
                if($ipt_cmd!="")
                {
                    $del_ipt="iptables -t nat -D ".$ipt_cmd;
                    exe_ouside_cmd($del_ipt);
                    $add_ipt="iptables -t nat -A ".$ipt_cmd;
                    exe_ouside_cmd($add_ipt);
                }
            }

       // }
    //}
}

?>
