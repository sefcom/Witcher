<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/setcfg/libs/inet.php";
inet_setcfg($SETCFG_prefix, $SETCFG_prefix."/inf");
/* The inet setting of WAN is changed, flush the resolved
 * DNS cache for domain routing function. */
event("DNSCACHE.FLUSH");
?>
