<? /* vi: set sw=4 ts=4: */
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function verify_setting($path)
{
	anchor($path);
	if (query("hostname")=="")
		return set_result(
						"FAILED",
						$path."/hostname",
						i18n("Please input the host name.")
						);
	if (isdomain(query("hostname"))=="0")
		return set_result(
						"FAILED",
						$path."/hostname",
						i18n("Invalid host name.")
						);
	if (query("username")=="")
		return set_result(
						"FAILED",
						$path."/username",
						i18n("Please input the user account or email address.")
						);
	if (query("password")=="")
		return set_result(
						"FAILED",
						$path."/password",
						i18n("Please input the password.")
						);
	return "OK";
}

if (verify_setting($FATLADY_prefix."/runtime/inf/ddns4")=="OK")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK", "", "");
}

?>
