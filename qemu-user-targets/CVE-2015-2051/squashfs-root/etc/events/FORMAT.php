<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

$XMLBASE = "/runtime/device/storage";
$PHPFILE = "/etc/events/FORMAT.php";
$UID = toupper($dev);
$prefix = substr($dev,0,3);
$pid = substr($dev,3,"");
if ($pid=="")
{
	$pid = 0;
	$UID = $UID.$pid;
}
$diskp = XNODE_getpathbytarget($XMLBASE, "disk", "uid", toupper($prefix), 0);
$path = XNODE_getpathbytarget($diskp, "entry", "uid", $UID, 0);
if ($path != "")
{
	if ($action=="try_unmount")
	{
		$mntstatus = fread("", "/proc/mounts");
		if (scut_count($mntstatus, $dev)!="0")
		{
			if ($counter=="30")
				echo "event UNMOUNT.".toupper($dev)."\n";
			$counter-=2;
			if ($counter>0)
				echo "xmldbc -t \"TRYUNMOUNT:2:phpsh /etc/events/FORMAT.php dev=".$dev." action=try_unmount counter=".$counter."\"\n";
			else
				echo "phpsh ".$PHPFILE." dev=".$dev." action=update state=FAILED\n";
		}
		else
		{
			echo "phpsh ".$PHPFILE." dev=".$dev." action=format\n";
		}
		setattr($path."/space/used",		"get", "");
		setattr($path."/space/available",	"get", "");
		set($path."/state",					"FORMATTING");
		set($path."/fmt_result",			"");
		set($path."/space/size",			"");
		set($path."/space/used",			"");
		set($path."/space/available",		"");
	}
	else if ($action=="format")
	{
		echo "#!/bin/sh\n";
		echo "mkfs.ext3 /dev/".$dev." -F\n";
		echo "if [ $? -eq 0 ]; then\n";
		echo "\tphpsh ".$PHPFILE." dev=".$dev." action=update state=SUCCESS\n";
		echo "else\n";
		echo "\tphpsh ".$PHPFILE." dev=".$dev." action=update state=FAILED\n";
		echo "fi\n";
	}
	else if ($action=="update")
	{
		if ($state=="SUCCESS")
		{
			set($path."/state",		"FORMATED");
			set($path."/fmt_result","SUCCESS");
		}
		else
		{
			set($path."/state", "FORMAT FAILED");
			set($path."/fmt_result","FAILED");
		}
		event("MOUNT.".toupper($dev));
	}
}
?>
