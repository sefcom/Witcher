<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be breaked for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/inet6.php";

function result($result, $node, $msg)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $msg;
	return $result;
}

function verify_dynamic_setting($path)
{
	$rip = query($path."/rip");
	TRACE_debug("FATLADY: ROUTE6.DYNAMIC: rip=".$rip);
	return "OK";
}

/*************************************************/
if (verify_dynamic_setting($FATLADY_prefix."/route6/dynamic")=="OK")
{
	set($FATLADY_prefix."/valid", 1);
	result("OK", "", "");
}

?>
