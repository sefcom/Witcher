<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
TRACE_debug("ROUTE6.STATIC: prefix= ".);
movc($SETCFG_prefix."/route6/static", "/route6/static");
?>
