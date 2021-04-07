<?
include "/htdocs/phplib/xnode.php";

function get_wl_physical_medium()	{ return "2"; }
function get_max_op_rate()			{ return "108"; }
function get_link_speed()			{ return "540000"; }
function get_machine_name()			{ return query("/device/hostname"); }


if		($PARAM=="get_max_op_rate")		$ret=get_max_op_rate();
else if	($PARAM=="get_link_speed")		$ret=get_link_speed();
else if	($PARAM=="get_machine_name")	$ret=get_machine_name();
else if	($PARAM=="get_wl_physical_medium") $ret=get_wl_physical_medium();
echo $ret;
?>
