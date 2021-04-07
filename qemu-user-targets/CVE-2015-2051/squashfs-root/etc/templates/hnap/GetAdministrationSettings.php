<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$nodebase= "/runtime/hnap/GetAdministrationSettings";
$inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);

$result = "OK";
$HTTPS = query($inf_lan1."/stunnel"); 
if($HTTPS==1) $HTTPS = "true"; else $HTTPS = "false";

if (query($inf_wan1."/web") == "") { $RemoteMgt = 0; }
else
{
  $RemoteMgt = 1;
  $RemoteMgtPort = query($inf_wan1."/web");
  //if($RemoteMgt==1) $RemoteMgt = "true"; else $RemoteMgt = "false";
}


if(query($inf_wan1."/https_rport") == "") { $RemoteMgtHTTPS = 0; }
else
{
	$RemoteMgt = 1;
  $RemoteMgtHTTPS = 1;
  $RemoteMgtPort = query($inf_wan1."/https_rport");
  //if($RemoteMgtHTTPS==1) $RemoteMgtHTTPS = "true"; else $RemoteMgtHTTPS = "false";
}

if($RemoteMgt==1) $RemoteMgt = "true"; else $RemoteMgt = "false";
if($RemoteMgtHTTPS==1) $RemoteMgtHTTPS = "true"; else $RemoteMgtHTTPS = "false";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetAdministrationSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetAdministrationSettingsResult><?=$result?></GetAdministrationSettingsResult> 
			<HTTPS><?=$HTTPS?></HTTPS> 
			<RemoteMgt><?=$RemoteMgt?></RemoteMgt> 
			<RemoteMgtPort><?=$RemoteMgtPort?></RemoteMgtPort> 
			<RemoteMgtHTTPS><?=$RemoteMgtHTTPS?></RemoteMgtHTTPS> 
			<InboundFilter><? echo query($inf_wan1."/inbfilter");?></InboundFilter> 
		</GetAdministrationSettingsResponse> 
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>