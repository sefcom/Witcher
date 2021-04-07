<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

set("/runtime/callmgr/voice_service/mobile/callerid_delivery", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/callerid_delivery"));
set("/runtime/callmgr/voice_service/mobile/callwaiting", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/callwaiting"));

set("/runtime/callmgr/voice_service/mobile/call_forward/unconditional", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/unconditional"));
set("/runtime/callmgr/voice_service/mobile/call_forward/unconditional_number", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/unconditional_number"));
set("/runtime/callmgr/voice_service/mobile/call_forward/busy", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/busy"));
set("/runtime/callmgr/voice_service/mobile/call_forward/busy_number", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/busy_number"));
set("/runtime/callmgr/voice_service/mobile/call_forward/no_reply", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/no_reply"));
set("/runtime/callmgr/voice_service/mobile/call_forward/no_reply_number", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/no_reply_number"));
set("/runtime/callmgr/voice_service/mobile/call_forward/no_reply_time", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/no_reply_time"));
set("/runtime/callmgr/voice_service/mobile/call_forward/not_reachable", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/not_reachable"));
set("/runtime/callmgr/voice_service/mobile/call_forward/not_reachable_number", query($SETCFG_prefix."/runtime/callmgr/voice_service/mobile/call_forward/not_reachable_number"));


anchor($SETCFG_prefix."/callmgr");
set("/callmgr/voice_service:1/phone/analog:1/enable",	query("voice_service:1/phone/analog:1/enable"));
set("/callmgr/voice_service:1/phone/analog:1/interdigit_timer",	query("voice_service:1/phone/analog:1/interdigit_timer"));
set("/callmgr/voice_service:1/phone/analog:1/callerid_display",	query("voice_service:1/phone/analog:1/callerid_display"));
set("/callmgr/voice_service:1/ctry_cid/cid_std",   query("voice_service:1/ctry_cid/cid_std"));

set("/callmgr/fxs/alm/channel:1/volume/gaintx",	query("fxs/alm/channel:1/volume/gaintx"));
set("/callmgr/fxs/alm/channel:1/volume/gainrx",	query("fxs/alm/channel:1/volume/gainrx"));

?>
