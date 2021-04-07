HTTP/1.1 200 OK
Content-Type: text/xml

<?
include "/htdocs/phplib/trace.php";

/* get modules that send from hedwig */
/* call $target to do error checking, 
 * and it will modify and return the variables, '$FATLADY_XXXX'. */
$FATLADY_result	= "OK";
$FATLADY_node	= "";
$FATLADY_message= "No modules for Hedwig";	/* this should not happen */

//TRACE_debug("FATLADY dump ====================\n".dump(0, "/runtime/session"));

foreach ($prefix."/postxml/module")
{
	del("valid");
	if (query("FATLADY")=="ignore") continue;
	$service = query("service");
	if ($service == "") continue;
	TRACE_debug("FATLADY: got service [".$service."]");
	$target = "/htdocs/phplib/fatlady/".$service.".php";
	$FATLADY_prefix = $prefix."/postxml/module:".$InDeX;
	$FATLADY_base	= $prefix."/postxml";
	if (isfile($target)==1) dophp("load", $target);
	else
	{
		TRACE_debug("FATLADY: no file - ".$target);
		$FATLADY_result = "FAILED";
		$FATLADY_message = "No implementation for ".$service;
	}
	if ($FATLADY_result!="OK") break;
}
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<hedwig>\n";
echo "\t<result>".	$FATLADY_result.	"</result>\n";
echo "\t<node>".	$FATLADY_node.		"</node>\n";
echo "\t<message>".	$FATLADY_message.	"</message>\n";
echo "</hedwig>\n";
?>
