<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
set("/acl/dos/enable",	query($SETCFG_prefix."/acl/dos/enable"));
set("/acl/spi/enable",	query($SETCFG_prefix."/acl/spi/enable"));
set("/acl/applications/qq/action",       query($SETCFG_prefix."/acl/applications/qq/action"));
set("/acl/applications/msn/action",       query($SETCFG_prefix."/acl/applications/msn/action"));
set("/acl/applications/kaixin/action",       query($SETCFG_prefix."/acl/applications/kaixin/action"));
?>
