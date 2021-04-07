<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function port_trigger_command($iptcmd)
{
	$i = 0;
	$cnt = query("/nat/count");
	if ($cnt=="") $cnt = 0;
	while ($i < $cnt)
	{
		$i++;
		$UID = query("/nat/entry:".$i."/uid");
		$portt = XNODE_get_var("PORTT.".$UID.".USED");
		if ($portt > 0) fwrite("a", $_GLOBALS["START"], $iptcmd." -j PORTT.".$UID."\n");
	}
}

function IPTLAN_build_command($name)
{
	fwrite("w",$_GLOBALS["START"], "#!/bin/sh\n");
	fwrite("w",$_GLOBALS["STOP"],  "#!/bin/sh\n");
	fwrite("a",$_GLOBALS["START"], "iptables -t nat -F PRE.".$name."\n");
	/* if snmp open wan, drop udp port 161 from lan port */
	$snmp_inf = query("/snmp/inf"); 	 
    $enable_snmp = query("/snmp/active");
    $iptcmdNAT = "iptables -t nat -A PRE.".$name;
    $dev = PHYINF_getruntimeifname($name);
   
    if($enable_snmp=="1")
    {	 
      if($snmp_inf!= $name) //get wan interface ip
      {
  	  	 	$path = XNODE_getpathbytarget("", "inf", "uid", $snmp_inf, 0);
  		   	$inet	= query($path."/inet"); 
  		   	$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
  		   	$ipaddr=query($inetp."/ipv4/ipaddr");     
      		if ($ipaddr != "")
		    {	  	
      	     	fwrite("a",$_GLOBALS["START"], $iptcmdNAT." -i ".$dev. "  -p udp --dport 161 -d ".$ipaddr." -j DROP\n");
      		}
    	}
    }		
	fwrite("a",$_GLOBALS["START"], "iptables -t nat -A PRE.".$name." -j ACCEPT\n");
	
	/* firewall */
	fwrite("a",$_GLOBALS["START"], "echo -1 > /proc/fastnat/forskipsupport\n");
	fwrite("a",$_GLOBALS["START"], "iptables -t filter -F FWD.".$name."\n");
	fwrite("a",$_GLOBALS["START"], "iptables -t filter -F INP.".$name."\n");

	$iptcmdFWD = "iptables -t filter -A FWD.".$name;
	$iptcmdIN  = "iptables -t filter -A INP.".$name;
	$path = XNODE_getpathbytarget("", "inf", "uid", $name, 0);

	if ($path!="")
	{
		$macf = XNODE_get_var("MACF.".$name.".USED");
		$urlf = XNODE_get_var("URLF.".$name.".USED");
		$fw   = XNODE_get_var("FIREWALL.USED");
		$fw2  = XNODE_get_var("FIREWALL-2.USED");
		$fw3  = XNODE_get_var("FIREWALL-3.USED");

		$pptppt   = query("/device/passthrough/pptp");
		$ipsecpt  = query("/device/passthrough/ipsec");

		/* Outbound filter will be run faster to drop some packets. */
		fwrite("a",$_GLOBALS["START"],  $iptcmdFWD." -j FWD.OBFILTER\n");
		fwrite("a",$_GLOBALS["START"],  $iptcmdIN." -j INP.OBFILTER\n");


		if ($macf > 0)	fwrite("a", $_GLOBALS["START"],
							$iptcmdFWD." -j MACF.".$name."\n".
							$iptcmdIN. " -j MACF.".$name."\n");
		if ($fw > 0)	fwrite("a", $_GLOBALS["START"],
							$iptcmdFWD." -j FIREWALL\n");
		if ($fw2 > 0)	fwrite("a", $_GLOBALS["START"],
							$iptcmdFWD." -j FIREWALL-2\n");
		if ($fw3 > 0)	fwrite("a", $_GLOBALS["START"],
							$iptcmdFWD." -j FIREWALL-3\n");
		if ($urlf > 0)
		{
			fwrite("a", $_GLOBALS["START"],
						$iptcmdFWD." -p tcp --dport 80 -j URLF.".$name."\n".
						"echo 80 > /proc/fastnat/forskipsupport\n".
						"event SW.FASTNAT.DOWN\n");
			fwrite("a",$_GLOBALS["STOP"],  "event SW.FASTNAT.UP\n");
		}

		fwrite("a",$_GLOBALS["START"],  $iptcmdFWD." -j FOR_POLICY\n");
		
		port_trigger_command($iptcmdFWD);
		if ($pptppt==0)	fwrite("a", $_GLOBALS["START"],
							$iptcmdFWD." -p tcp --dport 1723 -j DROP\n".
							"echo 1723 > /proc/fastnat/forskipsupport\n");
		if ($ipsecpt==0) fwrite("a",$_GLOBALS["START"],
							$iptcmdFWD." -p udp --dport 500 -j DROP\n".
							$iptcmdFWD." -p udp --dport 4500 -j DROP\n".
							$iptcmdFWD." -p ah -j DROP\n".
							$iptcmdFWD." -p esp -j DROP\n");
	}
	fwrite("a",$_GLOBALS["START"], "exit 0\n");

	fwrite("a",$_GLOBALS["STOP"],  "iptables -t nat -F PRE.".$name."\n");
	/* firewall */
	fwrite("a",$_GLOBALS["STOP"],  "echo -1 > /proc/fastnat/forskipsupport\n");
	fwrite("a",$_GLOBALS["STOP"],  "iptables -t filter -F FWD.".$name."\n");
	fwrite("a",$_GLOBALS["STOP"],  "iptables -t filter -F INP.".$name."\n");
	fwrite("a",$_GLOBALS["STOP"],  "exit 0\n");
	
}

?>
