<?
include "/htdocs/phplib/xnode.php";

echo '#!/bin/sh\n';

//$ip = query("/runtime/services/wandetect/dhcp/".$INF."/ip");
$wantype = query("/runtime/services/wandetect6/wantype");
if ($wantype!="PPPoE")
{
	echo 'event WANV6.AUTOCONF.DETECT\n';
}
echo 'exit 0\n';
?>
