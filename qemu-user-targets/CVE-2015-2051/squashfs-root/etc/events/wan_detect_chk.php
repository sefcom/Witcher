<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$dhcp_exist = query("/runtime/detect/dhcp");
$pppoe_exist = query("/runtime/detect/pppoe");

if($pppoe_exist=="yes")
{
	set("/runtime/services/wandetect/wantype", "PPPoE");
	set("/runtime/services/wandetect/desc", "Normal");
}
else if($dhcp_exist=="yes")
{
	set("/runtime/services/wandetect/wantype", "DHCP");
	set("/runtime/services/wandetect/desc", "Global IP");
}
else 
{
	set("/runtime/services/wandetect/wantype", "unknown");
	set("/runtime/services/wandetect/desc", "No Response");
}

//TRACE_error("result : dhcp =".$dhcp_exist.",pppoe=".$pppoe_exist);

?>
