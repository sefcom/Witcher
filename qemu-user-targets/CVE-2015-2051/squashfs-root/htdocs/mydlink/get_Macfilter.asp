<?
include "/htdocs/mydlink/header.php";
if(query("/acl/macctrl/policy")=="DISABLE")
{
	$enabled="0";
}
else if(query("/acl/macctrl/policy")=="ACCEPT")
{
	$enabled="1";
}
else if(query("/acl/macctrl/policy")=="DROP")
{
    $enabled="2";
}

?>
<macfilter>
<enable><?=$enabled?></enable>
<?
    foreach("/acl/macctrl/entry")
    {
		echo "<mac>\n";
		echo "<addr>".query("mac")."</addr>\n";
		echo "<hostname></hostname>\n";
		echo "<enable>".query("enable")."</enable>\n";
		echo "</mac>\n";
    }
?></macfilter>
