<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

/*
 * Send SMS.
 */
set("/runtime/callmgr/voice_service/mobile/sms/send_state", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/sms/send_state"));
set("/runtime/callmgr/voice_service/mobile/sms/send_coding_method", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/sms/send_coding_method"));
set("/runtime/callmgr/voice_service/mobile/sms/send_address", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/sms/send_address"));
set("/runtime/callmgr/voice_service/mobile/sms/send_content", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/sms/send_content"));

?>
