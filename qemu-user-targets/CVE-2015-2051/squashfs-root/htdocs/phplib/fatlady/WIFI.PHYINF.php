<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/fatlady/WIFI/wifi.php";
$FATLADY_result = "OK";
//TRACE_debug("FATLADY_prefix = ".$FATLADY_prefix);
foreach($FATLADY_prefix."/phyinf")
{
	//TRACE_debug("FATLADY: uid=".query("uid").", type=".query("type"));
	if (query("type")=="wifi")
	{
		fatlady_wifi($FATLADY_prefix, query("uid"));
		if ($FATLADY_result!="OK")
		{
			set($FATLADY_prefix."/valid", "0");
			break;
		}
	}
}
?>
