<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$nodebase= "/runtime/hnap/GetSysLogSettings";
$result = OK;
$SysLog = query("/device/log/remote/enable");
$IPAddress = query("/device/log/remote/ipv4/ipaddr");

if($SysLog==1) $SysLog = "true";
else $SysLog = "false";


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSysLogSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetSysLogSettingsResult><?=$result?></GetSysLogSettingsResult> 
      <SysLog><?=$SysLog?></SysLog> 
      <IPAddress><?=$IPAddress?></IPAddress>
    </GetSysLogSettingsResponse> 
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>