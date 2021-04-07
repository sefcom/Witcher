<?
/*include "/htdocs/mydlink/header.php";*/
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$DNSLOGP="/runtime/DNSqueryhistory";
?>
<mydlink_logdnsquery>
<?
$MACCNT=query($DNSLOGP."/entry#");
$i=1;
while($i<=$MACCNT){
	$ENTRYCNT=query($DNSLOGP."/entry:".$i."/entry#");
	$j=1;
	while($j<=$ENTRYCNT){
		echo "	<record>";
		echo query($DNSLOGP."/entry:".$i."/entry:".$j."/date")." ";
		echo query($DNSLOGP."/entry:".$i."/entry:".$j."/time").";";
		echo "DNSQUERY;";
		$mac = query($DNSLOGP."/entry:".$i."/macaddr");
		echo toupper($mac).";";
		echo query($DNSLOGP."/entry:".$i."/entry:".$j."/domain").";";
		echo "</record>\n";
		$j++;
	}
	$i++;
}
?></mydlink_logdnsquery>
