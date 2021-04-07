<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
$count = query($FATLADY_prefix."/inf/upnp/count");
$cidx = 0;
$result = "OK";
$node = "";
$message = "";
while($cidx < $count)
{
	$cidx++;
	if(query($FATLADY_prefix."/inf/upnp/entry:".$cidx)=="")
	{
		$result = "FAILED";
		$node = $FATLADY_prefix."/inf/upnp/entry:".$cidx;
		$message = i18n("Entry is empty.");
	}
}
set($FATLADY_prefix."/valid", "1");
$_GLOBALS["FATLADY_result"]  = $result;
$_GLOBALS["FATLADY_node"]    = $node;
$_GLOBALS["FATLADY_message"] = $message;
?>
