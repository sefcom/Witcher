<?
/*include "/htdocs/mydlink/header.php";*/
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$EVENTLOGP	= "/runtime/log/mydlink";
$TRIEVENT	= query("/device/log/mydlink/eventmgnt/trigger");
?>
<mydlink_triggedevent_history>
<?
if($TRIEVENT!="0"){
	$RECCNT=query($EVENTLOGP."/entry#");
	$i=1;
	while($i<=$RECCNT){
		echo "\t<record>";
		echo get("TIME.MYDLINK", $EVENTLOGP."/entry:".$i."/time").";";
		echo query($EVENTLOGP."/entry:".$i."/message");
		echo "</record>\n";
		$i++;
	}
}
?></mydlink_triggedevent_history>
