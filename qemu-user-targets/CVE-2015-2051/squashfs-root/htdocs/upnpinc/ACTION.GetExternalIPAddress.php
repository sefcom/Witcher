<?
include "/htdocs/phplib/xnode.php";

function getipaddr($inf_uid)
{
	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf_uid, 0);
	$t = query($p."/inet/addrtype");
	if (query($p."/inet/".$t."/valid")==1)
	{
		if ($t == "ppp4")	return query($p."/inet/".$t."/local");
		else			return query($p."/inet/".$t."/ipaddr");
	}
	return "";
}

if ($WID=="")	$WID="1";
if (query("/runtime/device/layout")=="router")	$ipaddr = getipaddr("WAN-".$WID);
else											$ipaddr = getipaddr("LAN-1");

?><NewExternalIPAddress><?=$ipaddr?></NewExternalIPAddress>
