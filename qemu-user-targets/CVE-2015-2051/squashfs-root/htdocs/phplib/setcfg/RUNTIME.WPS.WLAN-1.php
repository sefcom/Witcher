<?
/* Notice:
 *   This service, RUNTIME.WPS.WLAN-1, is created before the php built-in function,
 *   'event' and 'service', are impletemented.
 *
 *   So DON'T use this service in the new impletementation any more.
 *   Please use the php built-in function, 'event', instead.
 *
 *	 You can use this getcfg file to get config only.
 *	 Don't use its fatlay, setcfg, and service file to make WPS PIN and PBC
 *	 take effect.
 *
 *                       Joan Wang <joan_wang@alphanetworks.com>
 */

/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$to = XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($SETCFG_prefix."/runtime/phyinf/uid"));
$to = $to."/media/wps/enrollee";
$from = $SETCFG_prefix."/runtime/phyinf/media/wps/enrollee";

set($to."/method",	query($from."/method"));
set($to."/pin",		query($from."/pin"));
set($to."/state",	query($from."/state"));
?>
