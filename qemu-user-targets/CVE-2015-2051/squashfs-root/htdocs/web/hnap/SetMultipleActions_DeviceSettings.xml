<?
/*this file is for include in SetMultipleAction*/
$dev_name = query($nodebase."DeviceName");
$captcha  = query($nodebase."CAPTCHA");
set("/sys/devicename", $dev_name);
if($captcha=="true")
{
	set("/device/session/captcha", 1);
}
else if($captcha=="false")
{
	set("/device/session/captcha", 0);
}
/*if password is empty do not set it.*/
if(query($nodebase."AdminPassword")!="")
{
	foreach("/device/account/entry")
	{
		if(query("group")==0)
		{
			set("password", query($nodebase."AdminPassword"));
		}
	}
}

fwrite("a",$ShellPath, "echo \"[$0]-->Password Changed\" > /dev/console\n");
fwrite("a",$ShellPath, "/etc/scripts/dbsave.sh > /dev/console\n");
fwrite("a",$ShellPath, "service DEVICE.ACCOUNT restart > /dev/console\n");
?>

