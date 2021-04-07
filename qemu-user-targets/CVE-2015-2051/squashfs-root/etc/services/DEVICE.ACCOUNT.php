<?
include "/htdocs/phplib/trace.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"],  $cmd."\n");}

/* Main */
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

/* Clean all account info. */
stopcmd("xmldbc -X /runtime/device/group");
stopcmd("xmldbc -X /runtime/device/account");
stopcmd("echo -n > /etc/passwd");
stopcmd("echo -n > /etc/shadow");
stopcmd("echo -n > /etc/group");

/* Create root account */
startcmd("addgroup -g 0 root");
startcmd("adduser -D -H -S -G root root");
/* Create nobody */
startcmd("addgroup -g 500 nobody");
startcmd("adduser -H -D -G nobody nobody");
/* group name shortcut */
$GROUP_0	= "root";
$GROUP_500	= "nobody";

/* add groups */
$i = 0;
$cnt = query("/device/group/count"); if ($cnt=="") $cnt=0;
foreach ("/device/group/entry")
{
	if ($InDeX > $cnt) break;
	$name = get(s,"name");
	$gid = get(s,"gid");
	if ($gid=="" || $name=="") continue;

	/* The runtime nodes */
	$i++;
	$entry = "/runtime/device/group/entry:".$i;
	set($entry."/name", $name);
	setattr($entry."/gid","get","grep \"^".$name."\" /etc/group | cut -d: -f3");
	/* Save the group name in the globals for later lookup. */
	$gid+=0; // Make sure it's integer.
	$GNAME = "GROUP_".$gid;
	$$GNAME = $name;
	startcmd("addgroup -g ".$gid." ".$name);
}

/* add account */
/* prepare the passwd file: /var/passwd
 * This file is used by HTTP access, CGI program. */
$i = 0;
$file = "/var/passwd";
$pwdf = "/var/passwds";
$hnapf = "/var/etc/hnapasswd";
fwrite("w", $file, "");
fwrite("w", $pwdf, "");
fwrite("w", $hnapf, "");
$cnt = query("/device/account/count"); if ($cnt=="") $cnt=0;
foreach ("/device/account/entry")
{
	if ($InDeX > $cnt) break;
	$name	= get("s", "name");
	$passwd	= get("s", "password");
	$passwd_noescp	= get("", "password");
	$gid	= get("s", "group");
	$usrid	= get("s", "usrid");

	if ($gid=="")	$gid=500;
	if ($usrid=="")	$uidcmd = "";	

	$gname = "GROUP_".$gid;
	$group = $$gname;
	if ($group=="") $group = "nobody";

	startcmd('# SERVICE: DEVICE.ACCOUNT: entry['.$InDeX.']: '.$name.':'.$passwd.':'.$usrid.':'.$gid);

	/* the runtime nodes */
	$i++;
	$entry = "/runtime/device/account/entry:".$i;
	set($entry."/name", $name);
	setattr($entry."/usrid", "get", "grep \"^".$name."\" /etc/passwd | cut -d: -f3");
	setattr($entry."/group", "get", "grep \"^".$name."\" /etc/passwd | cut -d: -f4");

	startcmd("adduser -H -D -G ".$group." ".$name);
	fwrite("a", $file, '"'.$name.'" "'.$passwd.'" "'.$gid.'"\n');
	fwrite("a", $pwdf, $name.":".$passwd."\n");
	fwrite("a", $hnapf, $name.":".$passwd_noescp."\n");
}
startcmd("chpasswd < ".$pwdf);
startcmd("rm -f ".$pwdf);

/* prepare the session config file: /var/session/sesscfg */
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

startcmd("service WEBACCESS restart");
startcmd("service SAMBA restart");

startcmd("exit 0\n");
stopcmd( "exit 0\n");

?>
