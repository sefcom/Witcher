#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function cmd($cmd) {echo $cmd."\n";}
function msg($msg) {cmd("echo [IPV6.CHILD.php]: ".$msg." > /dev/console");}

function main_entry()
{
	if ($_GLOBALS["CHILDIF"]=="") return "No Child INF !!";
	/* find parent */
	foreach("/inf")
	{
		$child = query("child");
		$childgz = query("childgz");
		/*msg("child uid : ".$child);*/
		if($_GLOBALS["CHILDIF"] == $child || $_GLOBALS["CHILDIF"] == $childgz)
		{
			$parent = query("uid");
			if (isfile("/var/run/".$parent.".UP")==1)
			{
				msg("Found!! Parent(".$parent.") is up");
				$_GLOBALS["CHILD_INFNAME"]=$_GLOBALS["CHILDIF"];
				dophp("load", "/etc/services/INET/inet_child.php");
				return;
			}
		}
	}
	return "No inet profile!!";
}

/*****************************************/
/* Required variables:
 *
 *	CHILDIF:		LAN-4...
 */
$ret = main_entry();
if ($ret!="")	cmd("# ".$ret."\nexit 9\n");
else			cmd("exit 0\n");
?>
