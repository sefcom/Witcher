HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/encrypt.php";

$nodebase = "/runtime/hnap/SetQuickVPNSettings/";
$rlt = "OK";

$enable = get("x", $nodebase."Enabled");
$username = get("x", $nodebase."Username");
$password = get("x", $nodebase."Password");
$password = AES_Decrypt128($password);
$psk = get("x", $nodebase."PSK");
$AuthProtocol = get("x", $nodebase."AuthProtocol");
$MPPE = get("x", $nodebase."MPPE");

if($enable == "true")
{
	set("/vpn/ipsec/enable",	"1");
}
else
{
	set("/vpn/ipsec/enable",	"0");
}

set("/vpn/ipsec/username", $username);
set("/vpn/ipsec/password", $password);
set("/vpn/ipsec/psk", $psk);


if($AuthProtocol == "MSCHAPv2")		set("/vpn/ipsec/auth", "MSCHAPv2");
else if($AuthProtocol == "PAP")		set("/vpn/ipsec/auth", "PAP");
else if($AuthProtocol == "CHAP")	set("/vpn/ipsec/auth", "CHAP");

if($MPPE == "none")		set("/vpn/ipsec/mppe", "none");
else if($MPPE == "RC4-40")		set("/vpn/ipsec/mppe", "RC4-40");
else if($MPPE == "RC4-128")	set("/vpn/ipsec/mppe", "RC4-128");


fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->VPN Change\" > /dev/console\n");
if($rlt == "OK")
{
	fwrite("a",$ShellPath, "/etc/scripts/dbsave.sh > /dev/console\n");
	fwrite("a",$ShellPath, "service IPSEC restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetQuickVPNSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
<SetQuickVPNSettingsResult><?=$rlt?></SetQuickVPNSettingsResult>
</SetQuickVPNSettingsResponse>
</soap:Body>
</soap:Envelope>
