<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
$path_wifi_wifi1 = XNODE_getpathbytarget("wifi", "entry", "uid", "WIFI-1", 0);
$auth=query($path_wifi_wifi1."/authtype");
$encrypt=query($path_wifi_wifi1."/encrtype");
$key="";
if($encrypt!="NONE")
{
	$enabled="true";
	if($auth != "OPEN" && $auth != "SHARED")
	{
		$auth="WPA";
		$key=get("x",$path_wifi_wifi1."/nwkey/eap/secret");    
	}
	else
	{
		$auth="WEP";
		$id=query($path_wifi_wifi1."/nwkey/wep/defkey");     
		$key=get("x",$path_wifi_wifi1."/nwkey/wep/key:".$id);
	}
}
else
{
	$enabled="false";
	$auth="WEP";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetWLanSecurityResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetWLanSecurityResult>OK</GetWLanSecurityResult>
      <Enabled><?=$enabled?></Enabled>
      <Type><?=$auth?></Type>
      <WEPKeyBits><?echo map($path_wifi_wifi1."/nwkey/wep/size", "", "64");?></WEPKeyBits>
      <SupportedWEPKeyBits>
        <int>64</int>
        <int>128</int>
      </SupportedWEPKeyBits>
      <Key><?=$key?></Key>
      <RadiusIP1><?echo query($path_wifi_wifi1."/nwkey/eap/radius");?></RadiusIP1>
      <RadiusPort1><?echo map($path_wifi_wifi1."/nwkey/eap/port", "", "0");?></RadiusPort1>
      <RadiusIP2><?echo query($path_wifi_wifi1."/nwkey/eap/radius");?></RadiusIP2>
      <RadiusPort2><?echo map($path_wifi_wifi1."/nwkey/eap/port", "", "0");?></RadiusPort2>
    </GetWLanSecurityResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
