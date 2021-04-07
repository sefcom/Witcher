<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/INET/inet.php";
foreach ($FATLADY_prefix."/inf")
{
	$inf = query("uid");
	$needgw = 0;
	if (scut_count($inf, "WAN")>0) $needgw = 1;
	if ($_GLOBALS["FATLADY_result"]=="OK") fatlady_inet($FATLADY_prefix, $inf, $needgw);
}
?>
