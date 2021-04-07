<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

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
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetQuickVPNSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetQuickVPNSettingsResult>OK</GetQuickVPNSettingsResult>
	<Enabled><?=$enable?></Enabled>
	<Username><?=$username?></Username>
	<Password><? echo AES_Encrypt128($password);?></Password>
	<PSK><?=$psk?></PSK>
	<AuthProtocol><?=$AuthProtocol?></AuthProtocol>
	<MPPE><?=$MPPE?></MPPE>
</GetQuickVPNSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
