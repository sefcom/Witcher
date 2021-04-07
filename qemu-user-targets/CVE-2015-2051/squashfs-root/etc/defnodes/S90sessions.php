<?
/* prepare the passwd file: /var/passwd */
$file = "/var/passwd";
fwrite(w, $file, "");
foreach ("/device/account/entry")
{
	$name = get("s", "name");
	$pwd  = get("s", "password");
	$grp  = get("s", "group");
	fwrite(a, $file, '"'.$name.'" "'.$pwd.'" "'.$grp.'"\n');
}

/* prepare the session config file: /var/session/sesscfg */

if (query("/device/session/timeout")=="")
{
	set("/device/session/dumy", "3600"); /* make sure this path is exist, so the anchor() will always success. */
}
anchor("/device/session");
$captcha = query("captcha");
$timeout = query("timeout");
$maxsess = query("maxsession");
$maxauth = query("maxauthorized");

if ($captcha=="") { $captcha = "0";    set("captcha", $captcha); }
if ($timeout=="") { $timeout = "3600"; set("timeout", $timeout); }
if ($maxsess=="") { $maxsess = "128";  set("maxsession", $maxsess); }
if ($maxauth=="") { $maxauth = "16";   set("maxauthorized", $maxauth); }

fwrite(w, "/var/session/sesscfg", '"'.$timeout.'" "'.$maxsess.'" "'.$maxauth.'" "'.$captcha.'"');
?>
