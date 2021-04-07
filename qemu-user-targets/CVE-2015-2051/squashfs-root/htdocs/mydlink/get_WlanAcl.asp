<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
$WIFIP	= XNODE_getpathbytarget("/wifi", "entry", "uid", "WIFI-1", 0);
if(query($WIFIP."/acl/policy")=="DISABLED"){
	$enabled="0";
}else if(query($WIFIP."/acl/policy")=="ACCEPT"){
	$enabled="2";
}else if(query($WIFIP."/acl/policy")=="DROP"){
    $enabled="1";
}

?>
<mydlink_wlan_acl>
<mode><?=$enabled?></mode>
<?
    foreach($WIFIP."/acl/entry")
    {
		echo "<mac>\n";
		echo "<enable>".query("enable")."</enable>\n";
		echo "<addr>".query("mac")."</addr>\n";
		echo "</mac>\n";
    }
?></mydlink_wlan_acl>
