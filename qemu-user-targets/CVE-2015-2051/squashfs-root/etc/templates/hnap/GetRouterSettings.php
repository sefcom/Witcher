<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
anchor($path_inf_wan1);
$remoteMng = query("web");
if( $remoteMng != "" && $remoteMng != "0" ) { $remoteMngStr = "true"; } else { $remoteMngStr = "false"; }
$remotePort = query("web");
$remoteSSL = "false";
$remoteName = get("","/ddns4/entry/hostname");

if( query("/device/qos/enable") == "1" )
{ $wireQos = "true"; }
else
{ $wireQos = "false"; }

$mngWlan = query("/hnap/SetRouterSettings/ManageWireless");
if( $mngWlan == "" )
{ $mngWlan = "true"; }

//$pinCode = query("/wireless/wps/pin"); 
//if($pinCode == ""){ $pinCode = query("/runtime/wps/pin"); }
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetRouterSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetRouterSettingsResult>OK</GetRouterSettingsResult>
      <ManageRemote><?=$remoteMngStr?></ManageRemote>
      <ManageWireless><?=$mngWlan?></ManageWireless>
      <RemotePort><?=$remotePort?></RemotePort>
	  <RemoteSSL><?=$remoteSSL?></RemoteSSL>
	  <DomainName><? echo escape("x",$remoteName); ?></DomainName>
	  <WiredQoS><?=$wireQos?></WiredQoS>
	  <WPSPin><?=$pinCode?></WPSPin>
    </GetRouterSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
