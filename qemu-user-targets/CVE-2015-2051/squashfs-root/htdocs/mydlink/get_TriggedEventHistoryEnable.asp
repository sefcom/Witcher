<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$TRIP	="/device/log/mydlink/eventmgnt/trigger";
$ENABLE	=query($TRIP);

?>
<enable><?=$ENABLE?></enable>
