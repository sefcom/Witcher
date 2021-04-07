HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/phyinf.php";
$nodebase="/runtime/hnap/SetTriggerADIC/";

function addevent($name,$handler)
{
	$cmd = $name." add \"".$handler."\"";
	event($cmd);
}

function wandetect($inf)
{
	$infp    = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$phyinf    = query($infp."/phyinf");
	$phyinfp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$ifname    = PHYINF_getifname($phyinf);
	$linkstatus = query($phyinfp."/linkstatus");

	if($linkstatus == "")
	{
		return "ERROR_WANLinkDown";
	}
	del("/runtime/wanispppoe");
	del("/runtime/wanisdhcp");
	addevent("detectpppoe","xmldbc -s /runtime/wanispppoe \\`pppd pty_pppoe pppoe_discovery pppoe_device ".$ifname."\\` &");
	addevent("detectdhcp","xmldbc -s /runtime/wanisdhcp \\`udhcpc -i ".$ifname." -d -D 1 -R 3\\`&");
	event("detectpppoe");
	event("detectdhcp");
	return "OK_DETECTING_2";
}
function wanlinkstatus($inf)
{
	$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$phyinf = query($infp."/phyinf");
	$phyinfp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$linkstatus = query($phyinfp."/linkstatus"); 	
	if($linkstatus == "")
	{
		return "down";
	}
	return $linkstatus;
}
function wandetectresult($inf)
{
	$infp    = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$phyinf    = query($infp."/phyinf");
	$phyinfp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$ifname    = PHYINF_getifname($phyinf);
	$linkstatus = query($phyinfp."/linkstatus");

	if($linkstatus == "")
	{
		return "UNKNOWN";
	}
	setattr("/runtime/detectpppoe",  "get", "pppd pty_pppoe pppoe_discovery pppoe_device ".$ifname);
	$ret = query("/runtime/detectpppoe");
	del("/runtime/detectpppoe");
	if($ret=="yes")
	{
		return "PPPOE";
	}
	setattr("/runtime/detectdhcp",  "get", "udhcpc -i ".$ifname." -d -D 1 -R 2");
	$ret = query("/runtime/detectdhcp");
	del("/runtime/detectdhcp");
	if($ret=="yes")
	{
		return "DHCP";
	}
	return "UNKNOWN";
}

$ACTION = get("", $nodebase."TriggerADIC");
if ($ACTION == "true")
{
	$result = wandetect("WAN-1");
}
else if ($ACTION == "false")
{
	$link = wanlinkstatus("WAN-1");
	if($link == "down")
	{
		$result = "ERROR_WANLinkDown";
	}
	else
	{
		$isPPPoE = query("/runtime/wanispppoe");
		$isDHCP = query("/runtime/wanisdhcp");
		
		if($isPPPoE == "yes")
		{
			$result = "OK_PPPoE";
		}
		/*if pppoe not finish we need wait it.*/
		else if($isPPPoE !="" && $isDHCP== "yes")
		{
			$result = "OK_DHCP";	
		}
		else if($isDHCP != "" && $isPPPoE != "")
		{
			$result = "ERROR_UnableToDetect";
		}
		else
		{
			$result = "OK_DETECTING_2";
		}
	}	
}
else if ($ACTION == "") //For older SetTriggerADIC spec. used in mobile APP. It would detect WAN type and get the result in one procedure.
{
	$testret = wandetectresult("WAN-1");
	if($testret == "PPPOE")
	{
		 $result="OK_PPPoE";
	}
	else if($testret == "DHCP")
	{
		$result="OK_DHCP";
	}
	else
	{
		$result="ERROR";
	}
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><SetTriggerADICResponse xmlns="http://purenetworks.com/HNAP1/"><SetTriggerADICResult><?=$result?></SetTriggerADICResult></SetTriggerADICResponse></soap:Body></soap:Envelope>
