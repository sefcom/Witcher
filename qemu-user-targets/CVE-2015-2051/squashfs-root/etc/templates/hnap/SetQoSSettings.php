HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inet.php";

$nodebase= "/runtime/hnap/SetQoSSettings";
$node_info = $nodebase."/QoSInfoData/QoSInfo";
$path_dev_log = "/device/log";
$result = "REBOOT";
$rule_count = 0; //QoS rules count on UI

//Error Check
foreach($node_info)
{
	if(INET_validv4addr(get("", "IPAddress"))==0)	{$result = "ERROR";}
}

$entry_bwc1 = "/bwc/entry:1/rules/entry";
$entry_bwc2 = "/bwc/entry:2/rules/entry";
$entry_bwcf = "/bwc/bwcf/entry";


$bwc1 = get("x", "/bwc/entry:1/uid");
$bwc1infp =  XNODE_getpathbytarget("", "inf", "bwc", $bwc1, 0);
$inetinf = get("x", $bwc1infp."/uid");


if(substr($inetinf, 0, 3) == "WAN") 
{
	$bwc1link = "up";
}
else if(substr($inetinf, 0, 3) == "LAN") 
{
	$bwc1link = "down";
}

if($result=="OK" || $result=="REBOOT")
{
	if ($bwc1link == "up")
	{
		set("/bwc/entry:1/bandwidth", 2048); //upstream
		set("/bwc/entry:2/bandwidth", 8192); //downstream
	}
	else if ($bwc1link == "down")
	{
		set("/bwc/entry:1/bandwidth", 8192); //downstream
		set("/bwc/entry:2/bandwidth", 2048); //upstream
	}

	//Clean the /bwc/bwcf entries before we set new entries.
	foreach($entry_bwcf)
	{
		set($entry_bwcf.":".$InDeX."/ipv4/start", "");
		set($entry_bwcf.":".$InDeX."/ipv4/end", "");
		set($entry_bwcf.":".$InDeX."/dst/ipv4/start", "");
		set($entry_bwcf.":".$InDeX."/dst/ipv4/end", "");
		del($entry_bwcf.":".$InDeX."/mac");
	}

	//Clean the bwc rules before we set new rules.
	foreach($entry_bwc1)
	{
		set($entry_bwc1.":".$InDeX."/enable", "");
		set($entry_bwc1.":".$InDeX."/description", "");
		set($entry_bwc1.":".$InDeX."/bwcqd", "");
		set($entry_bwc1.":".$InDeX."/bwcf", "");
	}
	foreach($entry_bwc2)
	{
		set($entry_bwc2.":".$InDeX."/enable", "");
		set($entry_bwc2.":".$InDeX."/description", "");
		set($entry_bwc2.":".$InDeX."/bwcqd", "");
		set($entry_bwc2.":".$InDeX."/bwcf", "");
	}

	foreach($node_info)
	{
		set($entry_bwcf."/seqno", $InDeX+1);
		set($entry_bwcf."/count", $InDeX);

		$HOSTNAME = query("Hostname");
		$IPADDR = query("IPAddress");
		$MACADDR = query("MACAddress");
		$PRIORITY = query("Priority");
		$TYPE = query("Type");

		TRACE_debug("$HOSTNAME=".$HOSTNAME);
		TRACE_debug("$IPADDR=".$IPADDR);
		TRACE_debug("$MACADDR=".$MACADDR);
		TRACE_debug("$PRIORITY=".$PRIORITY);
		TRACE_debug("$TYPE=".$TYPE);

		$uid = "BWCF-".$InDeX;
		set($entry_bwcf.":".$InDeX."/uid", $uid);
		set($entry_bwcf.":".$InDeX."/protocol", "ALL");
		set($entry_bwcf.":".$InDeX."/ipv4/start", $IPADDR);
		set($entry_bwcf.":".$InDeX."/ipv4/end", $IPADDR);
		set($entry_bwcf.":".$InDeX."/dst/ipv4/start", "1.0.0.1");
		set($entry_bwcf.":".$InDeX."/dst/ipv4/end", "254.254.254.254");
		set($entry_bwcf.":".$InDeX."/dst/port/type", "1");
		set($entry_bwcf.":".$InDeX."/dst/port/name", "ALL");
		set($entry_bwcf.":".$InDeX."/mac", $MACADDR);

		set($entry_bwc1.":".$InDeX."/enable", "1");
		set($entry_bwc1.":".$InDeX."/description", $HOSTNAME);
		if ($PRIORITY == "0") { set($entry_bwc1.":".$InDeX."/bwcqd", "BWCQD-4"); } //Best effort
		else if ($PRIORITY == "1") { set($entry_bwc1.":".$InDeX."/bwcqd", "BWCQD-3"); } //Normal
		else if ($PRIORITY == "2") { set($entry_bwc1.":".$InDeX."/bwcqd", "BWCQD-2"); } //Higher
		else if ($PRIORITY == "3") { set($entry_bwc1.":".$InDeX."/bwcqd", "BWCQD-1"); } //Higest
		set($entry_bwc1.":".$InDeX."/bwcf", $uid);

		set($entry_bwc2.":".$InDeX."/enable", "1");
		set($entry_bwc2.":".$InDeX."/description", $HOSTNAME);
		if ($PRIORITY == "0") { set($entry_bwc2.":".$InDeX."/bwcqd", "BWCQD-4"); }
		else if ($PRIORITY == "1") { set($entry_bwc2.":".$InDeX."/bwcqd", "BWCQD-3"); }
		else if ($PRIORITY == "2") { set($entry_bwc2.":".$InDeX."/bwcqd", "BWCQD-2"); }
		else if ($PRIORITY == "3") { set($entry_bwc2.":".$InDeX."/bwcqd", "BWCQD-1"); }
		set($entry_bwc2.":".$InDeX."/bwcf", $uid);

		//only available TC_SPQ_2013GUI for new UI QoS now
	  	$TYPE = "TC_SPQ_2013GUI";
		set("/bwc/entry:1/flag", $TYPE);
		set("/bwc/entry:2/flag", $TYPE);

		set("/bwc/entry:1/rules/count", $InDeX);
		set("/bwc/entry:2/rules/count", $InDeX);
		set("/bwc/bwcf/count", $InDeX);

		if ($PRIORITY != "0") $rule_count++;
	}

	//enable Qos if we have any QoS rules
	TRACE_debug("$rule_count=".$rule_count);
	if($rule_count > 0)
	{
		set("/bwc/entry:1/enable", "1");
		set("/bwc/entry:2/enable", "1");
	}
	else
	{
		set("/bwc/entry:1/enable", "0");
		set("/bwc/entry:2/enable", "0");
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->QoS Settings\" > /dev/console\n");

if($result=="REBOOT")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service BWC restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetQoSSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetQoSSettingsResult><?=$result?></SetQoSSettingsResult>
		</SetQoSSettingsResponse>
	</soap:Body>
</soap:Envelope>
