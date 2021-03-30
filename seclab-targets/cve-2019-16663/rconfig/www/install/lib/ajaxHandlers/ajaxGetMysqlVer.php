 <?php
 // check MySQL version and return to install pages
function find_SQL_Version() {
   $output = shell_exec('mysql -V');
   preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
   return $version[0];
}
if (find_SQL_Version() >= 5.1) {
	$response = '<strong><font class="good">Yes</font></strong>';
} else {
	$response = '<strong><font class="bad">No</font></strong>';
}
echo json_encode($response);