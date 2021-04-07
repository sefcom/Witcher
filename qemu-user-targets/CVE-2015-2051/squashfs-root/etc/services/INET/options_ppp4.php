<?
/*	$inf	- Interface UID. (ex. WAN-1)
 *  $devnam	- Linux device name (ex. eth0).
 *	$prof	- The XML node path of the INET profile. (ex: /inet/entry:1)
 *  $defrt	- defaultroute or not. (boolean)
 *	$mode	- dialup mode (auto/manual/ondemand).
 */
function create_pppoptions($inf, $devnam, $prof, $defrt, $mode)
{
	$OPTF = "/etc/ppp/options.".$inf;

	/* Read the configuration */
	anchor($prof);
	$mtu	= query("mtu");
	$mru	= query("mru"); if ($mtu=="") $mtu=1492;
	$user	= get("s","username");
	$pass	= get("s","password");
	$idle	= query("dialup/idletimeout");
	$over	= query("over");
	$mppe	= query("mppe/enable");
	$static	= query("static");
	/* special cases */
	if ($static==1) $ipaddr = query("ipaddr");
	if ($mode=="")  $mode   = query("dialup/mode");
	/* convert mtu to number. */
	$mtu = $mtu+0;
	/* convert idletimeout */
	if ($idle!="") $isec=$idle*60; else $isec=0;

	/* generate a new option file. */
	if ($mppe!=1) $NOCCP=" noccp";
	fwrite(w,$OPTF,	"noauth nodeflate nobsdcomp nodetach".$NOCCP."\n");

	/* for debug !!! */
	//fwrite(a,$OPTF,	"debug dump logfd 1\n");

	/* static options */
	fwrite(a,$OPTF,	"lcp-echo-failure 3\n");
	fwrite(a,$OPTF,	"lcp-echo-interval 30\n");
	fwrite(a,$OPTF,	"lcp-echo-failure-2 14\n");
	fwrite(a,$OPTF,	"lcp-echo-interval-2 6\n");
	fwrite(a,$OPTF,	"lcp-timeout-1 10\n");
	fwrite(a,$OPTF,	"lcp-timeout-2 10\n");
	fwrite(a,$OPTF,	"ipcp-accept-remote ipcp-accept-local\n");
	fwrite(a,$OPTF,	"linkname ".$inf."\n");
	fwrite(a,$OPTF,	"ipparam ".$inf."\n");
	fwrite(a,$OPTF,	"usepeerdns\n"); /* always use peer's dns. */
	fwrite(a,$OPTF,	"mtu ".$mtu."\n");

	if ($mru!="")	fwrite(a,$OPTF, 'mru '.$mru."\n");
	if ($user!="")	fwrite(a,$OPTF, 'user "'.$user.'"\n');
	if ($pass!="")	fwrite(a,$OPTF, 'password "'.$pass.'"\n');
	if ($defrt==1)	fwrite(a,$OPTF, 'defaultroute\n');
	/* Dial ondemand */
	if ($mode=="ondemand")
	{
		if ($isec>0) fwrite(a,$OPTF, 'idle '.$isec.'\n');
		fwrite(a,$OPTF, "demand\nconnect true\nktune\n");
		/* pick a fake IP for WAN port. */
		if ($ipaddr=="") $ipaddr = "10.112.112.".cut($inf, 1, "-");
	}
	if ($mode!="manual")
	{
		fwrite("a",$OPTF, "persist\nmaxfail 1\n");
	}
	/* Set local and remote IP */
	if ($ipaddr=="") fwrite(a,$OPTF, "noipdefault\n");
	else
	{
		fwrite(a,$OPTF, $ipaddr.":10.112.113.".cut($inf, 1, "-")."\n");
		if ($static==1) fwrite(a,$OPTF, "ipcp-ignore-local\n");
	}

	if ($over=="eth")
	{
		$acn = get(s, "pppoe/acname");
		$svc = get(s, "pppoe/servicename");
		if ($acn!="") fwrite(a,$OPTF, 'pppoe_ac_name "'. $acn.'"\n');
		if ($svc!="") fwrite(a,$OPTF, 'pppoe_srv_name "'.$svc.'"\n');
		if ($mppe==1) fwrite(a,$OPTF, 'refuse-eap refuse-chap refuse-mschap require-mppe\n');
		fwrite(a,$OPTF, "kpppoe pppoe_device ".$devnam."\n");
		fwrite(a,$OPTF, "pppoe_hostuniq\n");
	}
	else if ($over=="pptp")
	{
		if ($mppe==1) fwrite(a,$OPTF, 'refuse-eap refuse-chap refuse-mschap require-mppe\n');
		else	fwrite(a,$OPTF, 'refuse-eap\n');
		fwrite(a,$OPTF, 'pty_pptp pptp_server_ip '.query("pptp/server").'\n');
		fwrite(a,$OPTF, 'name "'.$user.'"\n');
		fwrite(a,$OPTF, 'sync pptp_sync\n');
	}
	else if ($over=="l2tp")
	{
		fwrite(a,$OPTF, 'refuse-eap\n');
		fwrite(a,$OPTF, 'pty_l2tp l2tp_peer '.query("l2tp/server").'\n');
		fwrite(a,$OPTF, 'sync l2tp_sync\n');
	}
	return $OPTF;
}
?>
