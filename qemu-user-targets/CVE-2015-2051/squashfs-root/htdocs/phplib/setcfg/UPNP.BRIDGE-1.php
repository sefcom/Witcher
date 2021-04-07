<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
$infp = XNODE_getpathbytarget("", "inf", "uid", "BRIDGE-1", 0);
if ($infp!="")
{
	$count = query($infp."/upnp/entry#");
	while ($count>0)
	{
		del($infp."/upnp/entry");
		$count--;
	}
	anchor($SETCFG_prefix."/inf/upnp");	
	$cnt = query("count");
	set($infp."/upnp/count", $cnt);
	$i = 0;
	while($i < $cnt)
	{
		$i++;
		set($infp."/upnp/entry:".$i, query("entry:".$i));
	}
}
?>
