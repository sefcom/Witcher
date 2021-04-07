<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
/* Removed by Enos, 2010/09/29,
include "/htdocs/phplib/setcfg/libs/bwc.php";
bwc_setcfg($SETCFG_prefix); */
movc($SETCFG_prefix."/bwc", "/bwc");
?>
