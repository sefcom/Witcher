<?php
@include('sessionCheck.inc');
if (empty($_REQUEST['action'])) {
	echo 'failed';
}
else {
	if ($_REQUEST['action'] == 'start'){
		//call script to start packet capture
		proc_close(proc_open('/usr/local/bin/pktCapture start  > /dev/null &',array(),$res));
		sleep(2);
		$pktCaptureStatus = explode(' ',conf_get("system:monitor:pktCaptureStatus"));
		$response = ($pktCaptureStatus[1] == 1)?('success'):('failure');
		echo $response;
	}
	else if ($_REQUEST['action'] == 'stop'){
		//call script to stop packet capture
		system('/usr/local/bin/pktCapture stop &');
		sleep(2);
		$pktCaptureStatus = explode(' ',conf_get("system:monitor:pktCaptureStatus"));
		$response = ($pktCaptureStatus[1] == 2)?('success'):('failure');
		echo $response;
	}
}

?>
