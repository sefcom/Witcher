<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/DHCPS/dhcpserver.php";
fatlady_dhcps($FATLADY_prefix, "DHCPS6.LAN-3");
?>
