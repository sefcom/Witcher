<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

echo "#!/bin/sh\n";
$vid		=query("/runtime/tty/entry:1/vid");
$pid		=query("/runtime/tty/entry:1/pid");
$devname	=query("/runtime/tty/entry:1/devname");
$cmdport	=query("/runtime/tty/entry:1/cmdport/devname");
if($vid ==1e0e && $pid ==deff)
{
	echo "chat -D ".$devname." OK-ATE1-OK\n";
}
else
{
	if($cmdport != "")
	{
		echo "chat -D ".$cmdport." OK-AT-OK\n";
		echo "chat -e -v -c -D ".$cmdport." OK-AT+CIMI-OK\n";
	}
	else
	{
		echo "chat -D ".$devname." OK-AT-OK\n";
		echo "chat -e -v -c -D ".$devname." OK-AT+CIMI-OK\n";
	}
}
echo "exit 0\n";
?>
