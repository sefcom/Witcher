<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/INET/inet.php";
fatlady_inet($FATLADY_prefix, "WAN-3", "1");
?>
