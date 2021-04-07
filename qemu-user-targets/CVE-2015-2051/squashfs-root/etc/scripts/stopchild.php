#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function cmd($cmd) {echo $cmd."\n";}
function msg($msg) {cmd("echo [/etc/scripts/stopchild.php]".$msg." > /dev/console");}

function main_entry($uid)
{
	if ($uid=="") return "No Child UID !!";
	/* Check parent is up or not*/
	if(isfile("/var/run/CHILD.".$uid.".UP")==1)	cmd("service INET.".$uid." stop");
	else msg("Child interface already stopped. Do nothing.");
}

/*****************************************/
/* Required variables:
 *
 *	UID:		LAN-4...
 */
$uid = $_GLOBALS["CHILDUID"];
$ret = main_entry($uid);
if ($ret!="")	cmd("# ".$ret."\nexit 9\n");
else			cmd("exit 0\n");
?>
