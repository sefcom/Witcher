<?
echo "HTTP/1.1 200 OK\r\n";
if($AUTHORIZED_GROUP < 0)
{
	echo '\r\nAuthetication Fail!\r\n';
}
else
{
	echo "Content-Type: application/octet-stream\r\n";
	echo "Content-Disposition: attachment; filename=vpnprofile.mobileconfig\r\n";
	echo "<\?xml version='1.0' encoding='utf-8'\?>\r\n";

	include "/htdocs/phplib/xnode.php";
	include "/htdocs/webinc/config.php";
	include "/htdocs/phplib/trace.php";

	$username = get("x", "/vpn/ipsec/username");
	$password = get("x", "/vpn/ipsec/password");
	$psk = get("x", "/vpn/ipsec/psk");

	$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, 0);
	$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
	if(get("x", $path_inf_wan1."/ddns4") != "")
	{
		$ipaddr = get("x", "/ddns4/entry:1/hostname");
	}
	else	$ipaddr = get("x",$path_run_inf_wan1."/inet/ipv4/ipaddr");

	echo '\r\n';
	echo '<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">\r\n';
	echo '<plist version="1.0">\r\n';
	echo '<dict>\r\n';
	echo '\t<key>PayloadContent</key>\r\n';
	echo '\t<array>\r\n';
	echo '\t\t<dict>\r\n';
	echo '\t\t\t<key>EAP</key>\r\n';
	echo '\t\t\t<dict/>\r\n';
	echo '\t\t\t<key>IPSec</key>\r\n';
	echo '\t\t\t<dict>\r\n';
	echo '\t\t\t\t<key>AuthenticationMethod</key>\r\n';
	echo '\t\t\t\t<string>SharedSecret</string>\r\n';
	echo '\t\t\t\t<key>SharedSecret</key>\r\n';
	echo '\t\t\t<data>'.$psk.'</data>\r\n';
	echo '\t\t\t</dict>\r\n';
	echo '\t\t\t<key>IPv4</key>\r\n';
	echo '\t\t\t<dict>\r\n';
	echo '\t\t\t\t<key>OverridePrimary</key>\r\n';
	echo '\t\t\t\t<integer>1</integer>\r\n';
	echo '\t\t\t</dict>\r\n';
	echo '\t\t\t<key>PPP</key>\r\n';
	echo '\t\t\t<dict>\r\n';
	echo '\t\t\t\t<key>AuthName</key>\r\n';
	echo '\t\t\t\t<string>'.$username.'</string>\r\n';
	echo '\t\t\t\t<key>AuthPassword</key>\r\n';
	echo '\t\t\t\t<string>'.$password.'</string>\r\n';
	echo '\t\t\t\t<key>CommRemoteAddress</key>\r\n';
	echo '\t\t\t\t<string>'.$ipaddr.'</string>\r\n';
	echo '\t\t\t</dict>\r\n';
	echo '\t\t\t<key>PayloadDescription</key>\r\n';
	echo '\t\t\t<string>Configures VPN settings, including authentication.</string>\r\n';
	echo '\t\t\t<key>PayloadDisplayName</key>\r\n';
	echo '\t\t\t<string>VPN (Aaa)</string>\r\n';
	echo '\t\t\t<key>PayloadIdentifier</key>\r\n';
	echo '\t\t\t<string>com.leo.profile.vpn1</string>\r\n';
	echo '\t\t\t<key>PayloadOrganization</key>\r\n';
	echo '\t\t\t<string></string>\r\n';
	echo '\t\t\t<key>PayloadType</key>\r\n';
	echo '\t\t\t<string>com.apple.vpn.managed</string>\r\n';
	echo '\t\t\t<key>PayloadUUID</key>\r\n';
	echo '\t\t\t<string>32F74854-40D2-4607-97A9-64D2EE68FEF0</string>\r\n';
	echo '\t\t\t<key>PayloadVersion</key>\r\n';
	echo '\t\t\t<integer>1</integer>\r\n';
	echo '\t\t\t<key>Proxies</key>\r\n';
	echo '\t\t\t<dict/>\r\n';
	echo '\t\t\t<key>UserDefinedName</key>\r\n';
	echo '\t\t\t<string>Aaa</string>\r\n';
	echo '\t\t\t<key>VPNType</key>\r\n';
	echo '\t\t\t<string>L2TP</string>\r\n';
	echo '\t\t</dict>\r\n';
	echo '\t</array>\r\n';
	echo '\t<key>PayloadDescription</key>\r\n';
	echo '\t<string>temp description</string>\r\n';
	echo '\t<key>PayloadDisplayName</key>\r\n';
	echo '\t<string>leo VPN profile</string>\r\n';
	echo '\t<key>PayloadIdentifier</key>\r\n';
	echo '\t<string>com.leo.profile</string>\r\n';
	echo '\t<key>PayloadOrganization</key>\r\n';
	echo '\t<string></string>\r\n';
	echo '\t<key>PayloadRemovalDisallowed</key>\r\n';
	echo '\t<false/>\r\n';
	echo '\t<key>PayloadType</key>\r\n';
	echo '\t<string>Configuration</string>\r\n';
	echo '\t<key>PayloadUUID</key>\r\n';
	echo '\t<string>BAFBB3A1-FEEA-4C9E-95E3-7D39EAF30A26</string>\r\n';
	echo '\t<key>PayloadVersion</key>\r\n';
	echo '\t<integer>1</integer>\r\n';
	echo '</dict>\r\n';
	echo '</plist>\r\n';
}
?>
