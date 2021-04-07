<?
if ($ACTION=="STARTTODOWNLOADFILE")
{
	/* hendry,fix :I want a node that is very UNIQUE !! */
	set("/runtime/hendry_user_setting_tmp","");
	set("/runtime/mydlink_user_setting_tmp","");
	movc("/device/account","/runtime/hendry_user_setting_tmp");
	mov("/mydlink","/runtime/mydlink_user_setting_tmp");
	
}
else if ($ACTION=="ENDTODOWNLOADFILE")
{
	movc("/runtime/hendry_user_setting_tmp/","/device/account");
	del("/runtime/hendry_user_setting_tmp");
	mov("/runtime/mydlink_user_setting_tmp/mydlink","/");
	del("/runtime/mydlink_user_setting_tmp");
}
?>
