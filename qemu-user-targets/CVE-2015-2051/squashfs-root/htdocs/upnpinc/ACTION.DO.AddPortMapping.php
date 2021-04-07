<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inet.php";

if (query("/runtime/device/layout")!="router")
{
	$_GLOBALS["errorCode"]=501;
}
else
{
	anchor($_GLOBALS["ACTION_NODEBASE"]."/AddPortMapping");
	$NewRemoteHost				= query("NewRemoteHost");
	$NewExternalPort			= query("NewExternalPort");
	$NewProtocol				= query("NewProtocol");
	$NewInternalPort			= query("NewInternalPort");
	$NewInternalClient			= query("NewInternalClient");
	$NewEnabled					= query("NewEnabled");
	$NewPortMappingDescription	= query("NewPortMappingDescription");
	$NewLeaseDuration			= query("NewLeaseDuration");

	if($NewExternalPort=="" || isdigit($NewExternalPort)==0)	
	{
		$_GLOBALS["errorCode"]=716;
	}
	else if ($NewInternalPort !="" && isdigit($NewInternalPort)==0 ) 
	{
		$_GLOBALS["errorCode"]=402;
	}
	else if ($NewProtocol=="" || $NewInternalClient=="" || INET_validv4addr($NewInternalClient)==0)
	{
		$_GLOBALS["errorCode"]=402;
	}
	else
	{
		$done = 0;
		$_GLOBALS["errorCode"]=200;

		if ($NewInternalPort=="")	$NewInternalPort = $NewExternalPort;
		if ($NewProtocol=="TCP")	$proto = "TCP";
		else						$proto = "UDP";
		foreach ("/runtime/upnpigd/portmapping/entry")
		{
			/* if exist, update the description. */
			if ($NewRemoteHost == query("remotehost") && $NewExternalPort == query("externalport") && 
				$proto == query("protocol"))
			{
				$_GLOBALS["errorCode"]=718;
				$done = 1;
			}
			/* XBOX test wish us to report OK, if the reule is existing. */
			if ($proto				== query("protocol") && 
				$NewRemoteHost		== query("remotehost") && 
				$NewInternalClient	== query("internalclient") &&
				$NewInternalPort	== query("internalport") &&
				$NewExternalPort	== query("externalport"))
			{
				if ($NewPortMappingDescription != query("description"))
				{
					set("description", $NewPortMappingDescription);
				}
				$_GLOBALS["errorCode"]=200;
				$done = 1;
			}
			if ($done == 1) break;
		}
		if ($NewLeaseDuration != "" && $NewLeaseDuration > 0)
		{
			$_GLOBALS["errorCode"] = 725;
			$done = 1;
		}
		if ($done == 0)
		{
			$newentry = XNODE_add_entry("/runtime/upnpigd/portmapping", "PORTMAP");
			anchor($newentry);
			set("enable",			$NewEnabled);
			set("protocol",			$proto);
			set("remotehost",		$NewRemoteHost);
			set("externalport",		$NewExternalPort);
			set("internalport",		$NewInternalPort);
			set("internalclient",	$NewInternalClient);
			set("description",		$NewPortMappingDescription);
			set("leaseduration",	$NewLeaseDuration);

			if ($NewRemoteHost != "")	$sourceip = ' -s "'.$NewRemoteHost.'"';
			else						$sourceip = '';
			if ($proto == "TCP")		$proto = " -p tcp";
			else						$proto = " -p udp";
			if ($NewEnabled == 1)
			{
				$cmd =	'iptables -t nat -A DNAT.UPNP'.$proto.' --dport '.$NewExternalPort.
						' -j DNAT --to-destination "'.$NewInternalClient.'":'.$NewInternalPort;

				SHELL_info("a", $_GLOBALS["SHELL_FILE"], "UPNP:".$cmd);
				fwrite("a", $_GLOBALS["SHELL_FILE"], $cmd."\n");
			}
		}
	}
}
?>
