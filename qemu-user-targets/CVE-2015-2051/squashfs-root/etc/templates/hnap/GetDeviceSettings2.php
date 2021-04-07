<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

function get_timezone_zoneindex($uid)
{
	foreach("/runtime/services/timezone/zone")
	{
		$zone_uid = query("uid");
		if ($zone_uid == $uid)
			return $InDeX;
	}
}
$tzInx = get("","/device/time/timezone");
$zone_idx = get_timezone_zoneindex($tzInx);
if( $zone_idx != "" )
{
	$tzStr = get("","/hnap/timezone:".$zone_idx."/name");
}
$ds = query("/time/timezone/dst");
if($ds == "1")
{ $autoAdj = "true"; }
else
{ $autoAdj = "false"; }
$locale  = query("/hnap/Locale");
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetDeviceSettings2Response xmlns="http://purenetworks.com/HNAP1/">
      <GetDeviceSettings2Result>OK</GetDeviceSettings2Result>
      <SerialNumber><?
		echo "00000001";
      ?></SerialNumber>
      <TimeZone><?=$tzStr?></TimeZone>
      <AutoAdjustDST><?=$autoAdj?></AutoAdjustDST>
      <Locale><?=$locale?></Locale>
      <SupportedLocales><string></string></SupportedLocales>
      <SSL><?echo "false";?></SSL>
    </GetDeviceSettings2Response>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
