<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

$infp	= XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$phyinf	= query($infp."/phyinf");
$ifname	= PHYINF_getifname($phyinf);

echo 'noauth nodeflate nobsdcomp nodetach noccp\n';
echo 'lcp-echo-failure 3\n';
echo 'lcp-echo-interval 30\n';
echo 'lcp-echo-failure-2 14\n';
echo 'lcp-echo-interval-2 6\n';
echo 'lcp-timeout-1 10\n';
echo 'lcp-timeout-2 10\n';
echo 'ipcp-accept-remote ipcp-accept-local\n';
echo 'mtu 1454\n';
echo 'linkname DISCOVER\n';
echo 'ipparam DISCOVER\n';
echo 'usepeerdns\n';
echo 'defaultroute\n';
echo 'user ""\n';
echo 'password ""\n';
echo 'noipdefault\n';
echo 'kpppoe pppoe_device '.$ifname.'\n';
echo 'pppoe_hostuniq\n';
?>
