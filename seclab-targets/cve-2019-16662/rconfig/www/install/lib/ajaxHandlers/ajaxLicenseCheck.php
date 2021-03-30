<?php
$content = file('/home/rconfig/www/licenseCheck.txt');
$checkLicenseValue = intval($content[0]);
echo json_encode($checkLicenseValue);