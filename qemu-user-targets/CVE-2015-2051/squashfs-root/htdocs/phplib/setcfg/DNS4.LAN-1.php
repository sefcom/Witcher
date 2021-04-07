<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/setcfg/libs/dns.php";
dns4_setcfg($SETCFG_prefix, "LAN-1");
/* Domain routing is implemented in DNS proxy/relay,
 * so flush the resolved DNS cache here. */
event("DNSCACHE.FLUSH");
?>
