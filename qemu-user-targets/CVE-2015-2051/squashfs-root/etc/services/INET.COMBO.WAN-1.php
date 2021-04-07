<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd) { fwrite(a, $_GLOBALS["START"], $cmd."\n"); }
function stop_cmd($cmd) { fwrite(a, $_GLOBALS["STOP"],  $cmd."\n"); }
function setup_error($errno) { startcmd("exit ".$errno); stop_cmd("exit ".$errno); }

function combo_inf($uid, $infp, $lower)
{
	$inet = query($infp."/inet");
	if ($inet == "") return "9";

	/* Get the profile. */
	$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
	if ($inetp == "") return "9";

	/* Determine "redial" */
	$redail = 0;
	$addrtype = query($inetp."/addrtype");
	if ($addrtype != "ppp4") return "9";

	/* Check dialup mode */
	$dial = query($inetp."/ppp4/dialup/mode");
	if ($dial=="auto")
	{
		startcmd('event '.$uid.'.COMBO.DIALUP add "service INET.'.$lower.' restart"');
		startcmd('event '.$uid.'.COMBO.HANGUP add "service INET.'.$uid.' stop"');
		$redail++;
	}
	else if ($dial=="ondemand")
	{
		startcmd('event '.$uid.'.COMBO.DIALUP add "event '.$uid.'.PPP.DIALUP"');
		startcmd('event '.$uid.'.COMBO.HANGUP add "event '.$uid.'.PPP.HANGUP"');
		$redail++;
	}
	else
	{
		$dial = "manual";
		startcmd('event '.$uid.'.COMBO.DIALUP add "service INET.'.$lower.' restart"');
		startcmd('event '.$uid.'.COMBO.HANGUP add "service INET.'.$uid.' stop"');
	}

	/* If we need to auto-redial, trigger the service start at lowerlayer-down event. */
	if ($redail>0)	startcmd('event INFSVCS.'.$lower.'.DOWN add "service INET.'.$lower.' restart"');
	else			startcmd('event INFSVCS.'.$lower.'.DOWN add true');

	/* Starting from lowerlayer. */
	if ($dial!="manual") startcmd('service INET.'.$lower.' start');

	/* Stopping from upperlayer. */
	stop_cmd('event INFSVCS.'.$lower.'.DOWN add true');
	stop_cmd('event '.$uid.'.COMBO.DIALUP add true');
	stop_cmd('event '.$uid.'.COMBO.HANGUP add true');
	stop_cmd('service INET.'.$uid.' stop');
	return "0";
}


/* Main script *************************************************************/
if ($me == "") $me = "WAN-1";

fwrite(w, $START, "#!/bin/sh\n");
fwrite(w, $STOP,  "#!/bin/sh\n");

$infp = XNODE_getpathbytarget("", "inf", "uid", $me, 0);
if ($infp != "")
{
	$lower = query($infp."/lowerlayer");
	if ($lower!="")
	{
		$ret = combo_inf($me, $infp, $lower);
		setup_error($ret);
	}
	else
	{
		SHELL_info($START, "INET.COMBO.".$me.": no lowerlayer !");
		SHELL_info($STOP,  "INET.COMBO.".$me.": no lowerlayer !");
		setup_error("9");
	}
}
else
{
	SHELL_info($START, "INET.COMBO.".$me.": no INF !");
	SHELL_info($STOP,  "INET.COMBO.".$me.": no INF !");
	setup_error("9");
}
?>
