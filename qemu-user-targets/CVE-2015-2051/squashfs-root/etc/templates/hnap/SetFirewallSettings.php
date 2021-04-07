HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";

$nodebase = "/runtime/hnap/SetFirewallSettings/";
$alg_path = "/device/passthrough";
$result = "OK";

if (get("x", $nodebase."SPIIPv4") == "true") { set("/acl/spi/enable", "1"); }
else { set("/acl/spi/enable", "0"); }

if (get("x", $nodebase."AntiSpoof") == "true") { set("/acl/anti_spoof/enable", "1"); }
else { set("/acl/anti_spoof/enable", "0"); }

if (get("x", $nodebase."ALGPPTP") == "true") { set($alg_path."/pptp", "1"); }
else { set($alg_path."/pptp", "0");}

if (get("x", $nodebase."ALGIPSec") == "true") { set($alg_path."/ipsec", "1"); }
else { set($alg_path."/ipsec", "0"); }

if (get("x", $nodebase."ALGRTSP") == "true") { set($alg_path."/rtsp", "1"); }
else { set($alg_path."/rtsp", "0"); }

if (get("x", $nodebase."ALGSIP") == "true") { set($alg_path."/sip", "1"); }
else { set($alg_path."/sip", "0"); }

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Firewall Settings\" > /dev/console\n");

if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service ACL restart > /dev/console\n");
	fwrite("a",$ShellPath, "service FIREWALL restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetFirewallSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetFirewallSettingsResult><?=$result?></SetFirewallSettingsResult>
		</SetFirewallSettingsResponse>
	</soap:Body>
</soap:Envelope>