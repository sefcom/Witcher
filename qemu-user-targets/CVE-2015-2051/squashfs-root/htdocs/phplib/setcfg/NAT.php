<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
movc($SETCFG_prefix."/nat", "/nat");
?>
