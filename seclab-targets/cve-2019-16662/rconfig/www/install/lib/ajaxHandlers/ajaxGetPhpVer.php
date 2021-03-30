<?php 
 // check PHP version and return to install pages
$phpVersion = phpversion();
if ($phpVersion >= 5.3) {
	$response = '<strong><font class="good">Yes</font></strong>';
} else {
	$response = '<strong><font class="bad">No</font></strong>';
}
echo json_encode($response);