HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$Status=query("/runtime/hnap/dev_status");
if($Status != "ERROR")
{
	$Status="OK";
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <IsDeviceReadyResponse xmlns="http://purenetworks.com/HNAP1/">
      <IsDeviceReadyResult><?=$Status?></IsDeviceReadyResult>
    </IsDeviceReadyResponse>
  </soap:Body>
</soap:Envelope>
