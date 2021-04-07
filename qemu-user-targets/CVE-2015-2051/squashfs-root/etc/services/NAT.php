<?
include "/htdocs/phplib/xnode.php";

fwrite(w,$START,"#!/bin/sh\n");
fwrite(w,$STOP, "#!/bin/sh\nexit 100\n");

fwrite(a,$START,
	"service IPTPFWD  restart\n".
	"service IPTVSVR  restart\n".
	"service IPTPORTT restart\n".
	"service IPTDMZ   restart\n"
	);

/* refresh the chain of WAN interfaces. */
foreach ("/inf")
{
	$uid = query("uid");
	$prefix = cut($uid,0,'-');
	if ($prefix == "WAN") fwrite("a",$START,"service IPT.".$uid." restart\n");
}

/* Always return error, so this service will never be in the running state. */
fwrite(a,$START,"exit 100\n");
?>
