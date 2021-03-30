<?php
// Update License when 'acceptLicenseChkBox' is checked on /install/license.php page
$id = $_GET['id'];
$filename = "/home/rconfig/www/licenseCheck.txt";
chmod($filename,0666);
// dump array into file & chmod back to RO
$filehandle = fopen($filename, 'w+');
file_put_contents($filename, $id);
fclose($filehandle);
chmod($filename,0444);
$content = file('/home/rconfig/www/licenseCheck.txt');
$checkLicenseValue = intval($content[0]);
if ($checkLicenseValue == 1){
	echo json_encode('success');
} else {
	echo json_encode('failure');
}