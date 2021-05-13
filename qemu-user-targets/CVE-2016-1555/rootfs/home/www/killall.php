<?php
@include('sessionCheck.inc');
exec("/usr/local/bin/firmware-upgrade-web",$dummy, $res);
sleep(20);
?>

