HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->VPN download\" > /dev/console\n");
if($result=="OK")
{
	fwrite("a",$ShellPath, "xmldbc -P /htdocs/web/vpnconfig.php > /var/htdocs/web/vpnprofile.xml\n");
	fwrite("a",$ShellPath, "xmldbc -t \"DELETEVP:8:rm /var/htdocs/web/vpnprofile.xml\"\n");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
	<StartVpnDownloadResponse xmlns="http://purenetworks.com/HNAP1/">
		<StartVpnDownloadResult><?=$result?></StartVpnDownloadResult>		
	</StartVpnDownloadResponse>
</soap:Body>
</soap:Envelope>
