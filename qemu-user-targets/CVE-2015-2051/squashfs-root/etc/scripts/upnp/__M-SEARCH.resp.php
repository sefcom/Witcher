<?
if ($MAX_AGE == "") $MAX_AGE=1800;
echo "HTTP/1.1 200 OK\r\n";
echo "CACHE-CONTROL: max-age=".$MAX_AGE."\r\n";
echo "DATE: ".$DATE."\r\n";
echo "EXT:\r\n";
echo "LOCATION: ".$LOCATION."\r\n";
echo "SERVER: ".$SERVER."\r\n";
echo "ST: ".$ST."\r\n";
echo "USN: ".$USN."\r\n";
echo "\r\n";
?>
