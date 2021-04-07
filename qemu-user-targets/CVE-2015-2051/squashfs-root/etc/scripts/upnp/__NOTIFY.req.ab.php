<?
if ($MAX_AGE=="") $MAX_AGE = 1800;
echo "NOTIFY * HTTP/1.1\r\n";
echo "HOST: 239.255.255.250:1900\r\n";
echo "CACHE-CONTROL: max-age=".$MAX_AGE."\r\n";
echo "LOCATION: ".$LOCATION."\r\n";
echo "NT: ".$NT."\r\n";
echo "NTS: ".$NTS."\r\n";
echo "SERVER: ".$SERVER."\r\n";
echo "USN: ".$USN."\r\n";
echo "\r\n";
?>
