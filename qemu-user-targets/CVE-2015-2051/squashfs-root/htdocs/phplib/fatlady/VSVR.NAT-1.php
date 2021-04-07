<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/PFWD/pfwd.php";
fatlady_pfwd($FATLADY_prefix, "NAT-1", "VSVR");
?>
