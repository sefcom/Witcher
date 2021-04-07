<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$DNSLOGP	="/device/log/mydlink/dnsquery";
$ENABLE		=query($DNSLOGP);
if($ENABLE != 1)
{
	$ENABLE = 0;
}

?>
<config.log_dnsquery_enable><?=$ENABLE?></config.log_dnsquery_enable>
