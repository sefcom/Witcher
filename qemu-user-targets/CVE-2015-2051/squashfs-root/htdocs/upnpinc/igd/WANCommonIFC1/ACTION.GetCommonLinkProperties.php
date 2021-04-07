<NewWANAccessType>Ethernet</NewWANAccessType>
<NewLayer1UpstreamMaxBitRate>100000000</NewLayer1UpstreamMaxBitRate>
<NewLayer1DownstreamMaxBitRate>100000000</NewLayer1DownstreamMaxBitRate>
<NewPhysicalLinkStatus><?

include "/htdocs/phplib/xnode.php";

$phyinf = query(XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0)."/phyinf");
$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
if (query($p."/linkstatus")=="") echo "Down";	else  echo "Up";

?></NewPhysicalLinkStatus>
