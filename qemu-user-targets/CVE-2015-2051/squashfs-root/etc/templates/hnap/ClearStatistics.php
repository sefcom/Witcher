HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$result = "OK";

$interfaceN = 1;
while($interfaceN <= 4)
{
	if($interfaceN == 1) {$interface = "WAN";}
	else if($interfaceN == 2) {$interface = "LAN";}
	else if($interfaceN == 3) {$interface = "WLAN2.4G";}
	else if($interfaceN == 4) {$interface = "WLAN5G";}

	set("/runtime/hnap/GetInterfaceStatistics/Interface", $interface);
	$Remove_XML_Head_Tail = 1;
	include "/etc/templates/hnap/GetInterfaceStatistics.php";
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/Interface", $interface);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/Sent", $tx);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/Received", $rx);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/TXPackets", $tx_pkts);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/RXPackets", $rx_pkts);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/TXDropped", $tx_drop);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/RXDropped", $rx_drop);
	set("/runtime/hnap/LastStatisticsClear/entry:".$interfaceN."/Errors", $error);
	$interfaceN++;
}


fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Clear Statistics\" > /dev/console\n");
if($result == "OK")
{
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
<ClearStatisticsResponse xmlns="http://purenetworks.com/HNAP1/">
	<ClearStatisticsResult><?=$result?></ClearStatisticsResult>
</ClearStatisticsResponse>
</soap:Body>
</soap:Envelope>
