<?php
@include('sessionCheck.inc');
if (empty($_REQUEST['action'])) {
	echo 'failed';
}
else
{
	if($_REQUEST['action']=='linkTestStart'){
		conf_set_buffer($_REQUEST['totalString']);
	}
	else if($_REQUEST['action']=='linkTestStatus') {
			$wpsStatus = explode(' ',conf_get("system:monitor:wdsLinkTestStatus"));				//This will work on Board...
			//$wpsStatus = explode(' ',"system:monitor:linkTestStatus 12");						//This will work on Localhost...
			echo $wpsStatus[1];
	}
	//echo "ok";
}

?>
