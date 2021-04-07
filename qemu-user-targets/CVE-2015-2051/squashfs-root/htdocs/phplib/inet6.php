<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

/* return 1 if the ipaddr is a valid ipv6 colon-hexadecimal notation. */
function INET_validv6addr($ipaddr)
{
	if ( ipv6checkip($ipaddr)=="1" ) return 1;
	else return 0;
}

function INET_globalv6addr($ipaddr)
{
	if ( ipv6globalip($ipaddr)=="1" ) return 1;
	else return 0;
}

function INET_v6addrtype($ipaddr)
{
	return ipv6addrtype($ipaddr);
}
?>
