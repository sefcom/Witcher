<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$LOGP		="/device/log/mydlink/eventmgnt/pushevent";
$PUSH		=query($LOGP."/enable");
$USERLOGIN	=query($LOGP."/types/userlogin");
$FWUPGRADE	=query($LOGP."/types/firmwareupgrade");
$WLINTRU	=query($LOGP."/types/wirelessintrusion");
?>
<mydlink_logopt>
<config.log_enable><?=$PUSH?></config.log_enable>
<config.log_userloginfo><?=$USERLOGIN?></config.log_userloginfo>
<config.log_fwupgrade><?=$FWUPGRADE?></config.log_fwupgrade>
<config.wirelesswarn><?=$WLINTRU?></config.wirelesswarn>
</mydlink_logopt>
