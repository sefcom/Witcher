<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_group()
{
}

$cnt = query($SETCFG_prefix."/device/group/count");
TRACE_debug("SETCFG: DEVICE.ACCOUNT got ".$cnt." groups");
//movc($SETCFG_prefix."/device/group", "/device/group");
$g = $SETCFG_prefix."/device/group";
$db = "/device/group";
set($db."/seqno", query($g."/seqno"));
set($db."/max", query($g."/max"));
set($db."/count", query($g."/count"));
foreach($g."/entry")
{
	$e = $db."/entry:".$InDeX;
	set($e."/uid", query("uid"));
	set($e."/name", query("name"));
	set($e."/gid", query("gid"));
	set($e."/member/seqno", query("member/seqno"));
	set($e."/member/max", query("member/max"));
	set($e."/member/count", query("member/count"));
	foreach($g."/entry:".$InDeX."/member/entry")
	{
		$m = $e."/member/entry:".$InDeX;
		set($m."/uid", query("uid"));
		set($m."/name", query("name"));
	}
}

$cnt = query($SETCFG_prefix."/device/account/count");
TRACE_debug("SETCFG: DEVICE.ACCOUNT got ".$cnt." accounts");
//movc($SETCFG_prefix."/device/account", "/device/account");
$a = $SETCFG_prefix."/device/account";
$db = "/device/account";
set($db."/seqno", query($a."/seqno"));
set($db."/max", query($a."/max"));
set($db."/count", query($a."/count"));
foreach($a."/entry")
{
	$e = $db."/entry:".$InDeX;
	set($e."/uid", query("uid"));
	set($e."/name", query("name"));
	set($e."/usrid", query("usrid"));
	set($e."/password", query("password"));
	set($e."/group", query("group"));
	set($e."/description", query("description"));
}

$captcha = query($SETCFG_prefix."/device/session/captcha");
if ($captcha!="0")
	set("/device/session/captcha", 1);
else
	set("/device/session/captcha", 0);
?>
