<?php
 // check PHP-CLI extension is installed and return to install pages

$output = shell_exec('php -m | grep ctype');
$output = rtrim($output);
if ($output == "ctype") {
	$response = '<strong><font class="good">PHP-CLI Installed</font></strong>';
} else {
	$response = '<strong><font class="bad">PHP-CLI is not installed or working</font></strong>';
}
echo json_encode($response);