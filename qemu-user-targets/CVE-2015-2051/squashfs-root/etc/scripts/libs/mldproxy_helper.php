#!/bin/sh
<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

/*
we got these from igmpproxyd :
$ACTION 
$GROUP		--> group ip
$IF		--> interface
$SRC		--> client ip
$GROUPMAC	--> group mac
$SRCMAC		--> client mac
*/

$igmp = "/runtime/services/mldproxy";	
if ($ACTION=="add_member")
{
	$found = 0;
	foreach ($igmp."/group") if ($VaLuE==$GROUP) $found=1;
	if ($found == 0)
	{
		add($igmp."/group", $GROUP);		
	}
	echo 'echo "add '.$GROUPMAC.' '.$SRCMAC.'" > /proc/alpha/multicast_'.$IF.'\n';	
}
else if ($ACTION=="del_member")
{
	$found = 0;
	foreach ($igmp."/group") if ($VaLuE==$GROUP) $found=$InDeX;
	if ($found > 0)
	{		
		del($igmp."/group:".$found);
	}
	echo 'echo "remove '.$GROUPMAC.' '.$SRCMAC.'" > /proc/alpha/multicast_'.$IF.'\n';
}
?>
