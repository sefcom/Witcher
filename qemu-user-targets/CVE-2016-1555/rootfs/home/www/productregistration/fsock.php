<?php
$fp = @fsockopen("www.netgear.com", 80, $errno, $errstr, 1);
if($fp)
echo "ok";
else
echo "notok";
?>