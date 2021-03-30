<?php
// check HTTP version and return to install pages
function find_HTTPD_Version() {
    $output = apache_get_version();
    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
    return $version[0];
}
if (find_HTTPD_Version() >= 2.2) {
    $response = '<strong><font class="good">Yes</font></strong>';
} else {
    $response = '<strong><font class="bad">No</font></strong>';
}
echo json_encode($response);