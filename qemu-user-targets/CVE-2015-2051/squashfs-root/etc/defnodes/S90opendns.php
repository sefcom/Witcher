<?
include "/htdocs/phplib/xnode.php";

$wan1_infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
set($wan1_infp."/open_dns/adv_dns_srv/dns1", "204.194.232.200");
set($wan1_infp."/open_dns/adv_dns_srv/dns2", "204.194.234.200");
set($wan1_infp."/open_dns/family_dns_srv/dns1", "208.67.222.123");
set($wan1_infp."/open_dns/family_dns_srv/dns2", "208.67.220.123");
?>
