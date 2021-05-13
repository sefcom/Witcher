<?php
@include('sessionCheck.inc');
$str="";
$ApList = explode(',',$_POST['ApList']);
foreach ($ApList as $mac) {
	if (!empty($mac))
		$str .= $mac . "\n";
}

header('Content-type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"macList.txt\"");
echo $str;
?>
