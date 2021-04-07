<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$XMLBASE = "/runtime/device/storage";

$UID = toupper($prefix.$pid);
if ($pid=="0")	$dev = $prefix;
else			$dev = $prefix.$pid;
$diskp = XNODE_getpathbytarget($XMLBASE, "disk", "uid", toupper($prefix), 0);
$base = XNODE_getpathbytarget($diskp, "entry", "uid", $UID, 0);

if ($action=="add")
{
	/* add new disk */
	if ($diskp=="")
	{
		if (isfile("/sys/block/".$prefix."/../../../../../../manufacturer")=="1")
			$mfact = fread("", "/sys/block/".$prefix."/../../../../../../manufacturer");
		else
			$mfact = "";
		if (isfile("/sys/block/".$prefix."/../../../../../../product")=="1")
			$product = fread("", "/sys/block/".$prefix."/../../../../../../product");
		else
			$product = "";
		if (isfile("/sys/block/".$prefix."/../../../../../../serial")=="1")
			$serial = fread("", "/sys/block/".$prefix."/../../../../../../serial");
		else
			$serial = "";
		$vendor	= fread("", "/sys/block/".$prefix."/device/vendor");
		$model	= fread("", "/sys/block/".$prefix."/device/model");
		$size	= fread("", "/sys/block/".$prefix."/size") * 512;
		$cnt = query($XMLBASE."/disk#") + 1;
		$diskp = $XMLBASE."/disk:".$cnt;

		set($XMLBASE."/count",	$cnt);
		set($diskp."/uid",		toupper($prefix));
		set($diskp."/vendor",	strip($vendor));
		set($diskp."/model",	strip($model));
		set($diskp."/mfact",	strip($mfact));
		set($diskp."/product",	strip($product));
		set($diskp."/serial",	strip($serial));
		set($diskp."/size",		$size);
	}
	if ($pid!="0"||$fs!="UNKNOWN")
	{
		/* add new entry */
		if ($base=="")
		{
			$cnt = query($diskp."/entry#") + 1;
			$base= $diskp."/entry:".$cnt;
		}
		/* update entry, if got filesystem */
		else if ($fs!="UNKNOWN")
		{
			$cnt = query($diskp."/entry#");
		}
		else
		{
			return;
		}
		set($diskp."/count",$cnt);
		set($base."/uid",	$UID);
		set($base."/prefix",$prefix);
		set($base."/pid",	$pid);
		set($base."/fs",	$fs);
		if (isfile("/sbin/sfdisk")=="1"&&$pid!="0")
			setattr($base."/id", "get", "sh /etc/scripts/usbmount_fsid.sh ".$prefix.$pid);
		else
			set($base."/id","");
		set($base."/mntp",	$mntp);
		if ($fs=="UNKNOWN")
		{
			set($base."/state",				"NOT MOUNTED");
			set($base."/space/size",		"");
			set($base."/space/used",		"");
			set($base."/space/available",	"");
		}
		else
		{
			set($base."/state",				"MOUNTED");
			set($base."/space/size",		"CALCULATING");
			set($base."/space/used",		"CALCULATING");
			set($base."/space/available",	"CALCULATING");
		}
		//jef add + for df work around  880 only
		//echo "#!/bin/sh\n";
    	echo "sh mkdir -p /tmp/disk \n";
    	echo "sh echo 0 > /tmp/disk/".$prefix.$pid."\n";
    	//echo "exit 0\n";
    	//jef add -
	}
}
else if ($action=="remove")
{
	if ($base!="")
	{
		/* remove entry */
		del($base);
		/* counting child nodes. */
		$cnt = query($diskp."/entry#");
		if ($i>0)	set($diskp."/count", $cnt);
		else		set($diskp."/count", "0");
		set($diskp."/count", $cnt);
	}

	if ($pid=="0")
	{
		/* remove disk */
		if ($diskp!="")	del($diskp);
		set($XMLBASE."/count", query($XMLBASE."/disk#"));
	}
}
else if ($action=="mount")
{
	if ($base!="")
	{
		setattr($base."/space/used",		"get", "");
		setattr($base."/space/available",	"get", "");
		set($base."/fs",	$fs);
		if ($fs=="UNKNOWN")
		{
			set($base."/state",				"NOT MOUNTED");
			set($base."/space/size",		"");
			set($base."/space/used",		"");
			set($base."/space/available",	"");
		}
		else
		{
			set($base."/state",				"MOUNTED");
			set($base."/space/size",		"CALCULATING");
			set($base."/space/used",		"CALCULATING");
			set($base."/space/available",	"CALCULATING");
		}
	}
}
else if ($action=="unmount")
{
	if ($base!="")
	{
		setattr($base."/space/used",		"get", "");
		setattr($base."/space/available",	"get", "");
		set($base."/fs",					"UNKNOWN");
		set($base."/state",					"NOT MOUNTED");
		set($base."/space/size",			"");
		set($base."/space/used",			"");
		set($base."/space/available"	,	"");
	}
}
else if ($action=="update")
{
	if ($base!=""&&query($base."/space/size")=="CALCULATING")
	{
		set($base."/space/size", $size);
        set($base."/space/used", $size_used);
        set($base."/space/available", $size_ava);
		//setattr($base."/space/used",		"get", "df|scut -p".$dev." -f2 &");
		//setattr($base."/space/available",	"get", "df|scut -p".$dev." -f3 &");
		//setattr($base."/space/used",		"get", "cat /tmp/disk/".$dev." | scut -p".$dev." -f2 &");
		//setattr($base."/space/available",	"get", "cat /tmp/disk/".$dev." | scut -p".$dev." -f3 &");
	}
}
else if ($action=="detach")
{
	echo "#!/bin/sh\n";
	echo "mount|grep ".$dev." > /dev/null\n";
	echo "while ([ \"\$?\" = \"0\" ])\n";
	echo "do\n";
	echo "\tumount /dev/".$dev." 2> /dev/null\n";
	echo "\tmount|grep ".$dev." > /dev/null\n";
	echo "done\n";
	if ($mntp!="")
		echo "rm -rf ".$mntp."\n";
	echo "exit 0\n";
}
?>
