<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/setcfg/libs/portt.php";
portt_setcfg($SETCFG_prefix, "NAT-1");
?>
