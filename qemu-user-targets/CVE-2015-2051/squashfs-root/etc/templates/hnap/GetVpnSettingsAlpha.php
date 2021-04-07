HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/encrypt.php";

if(get("x", "/vpn/ipsec/enable") == "1")	$enable = true;
else	$enable = false;

$username = get("x", "/vpn/ipsec/username");
$password = get("x", "/vpn/ipsec/password");
$psk = get("x", "/vpn/ipsec/psk");

$auth = get("x", "/vpn/ipsec/auth");
if($auth == "MSCHAPv2")		$AuthProtocol = "MSCHAPv2";
else if($auth == "PAP")		$AuthProtocol = "PAP";
else if($auth == "CHAP")	$AuthProtocol = "CHAP";

$mppe = get("x", "/vpn/ipsec/mppe");
if($mppe == "none")					$MPPE = "none";
else if($mppe == "RC4-40")	$MPPE = "RC4-40";
else if($mppe == "RC4-128")	$MPPE = "RC4-128";

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<GetVpnSettingsAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetVpnSettingsResult>OK</GetVpnSettingsResult>
			<Enabled><?=$enable?></Enabled>
			<Username><?=$username?></Username>
			<Password><? echo AES_Encrypt128($password);?></Password>
			<PSK><? echo AES_Encrypt128($psk);?></PSK>
			<Auth><?=$AuthProtocol?></Auth>
			<Mppe><?=$MPPE?></Mppe>
			<CountryCode><? echo get("", "/runtime/devdata/countrycode");?></CountryCode>
		</GetVpnSettingsAlphaResponse>
	</soap:Body>
</soap:Envelope>
