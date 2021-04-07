<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

/*
 * SMS.
 */
movc($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/sms","/runtime/callmgr/voice_service/mobile/sms");

?>
