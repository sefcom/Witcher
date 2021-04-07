HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_wan1 = XNODE_getpathbytarget("", "inf", "uid", $WAN1, 0);
$nodebase="/runtime/hnap/SetRouterSettings/";
$result = "OK";
if( query($nodebase."WiredQoS") == "true" )
{
	$wireQos = "1";
}
else
{
	$wireQos = "0";
}
if( query($nodebase."RemoteSSL") == "true" )
{ $result = "ERROR_REMOTE_SSL_NOT_SUPPORTED"; }
if(query($nodebase."ManageRemote") == "true" )
{
	$remoteMng = "1";
}
else
{ $remoteMng = "0"; }

$hostName = query($nodebase."DomainName");

$remotePort = query($nodebase."RemotePort");
if( $remoteMng == "" || $remotePort == "" )
{
	$result = "ERROR";
}
$mngWlan = query($nodebase."ManageWireless");
set("/hnap/SetRouterSettings/ManageWireless",	$mngWlan);

//$wpsPin = query($nodebase."WPSPin");
//$wpsEn = query("/runtime/func/wps");
//if( $wpsPin == "" && $wpsEn == "1" )
//{
//	$result = "ERROR";
//}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->RouterSettings Change\" > /dev/console\n");
if($result == "OK")
{
	set("/device/qos/enable",$wireQos);
	set("/ddns4/entry/hostname", $hostName);
	set($path_inf_wan1."/web", $remotePort);
	set("/runtime/devdata/pin", $wpsPin);
	//fwrite("a",$ShellPath, "submit COMMIT > /dev/console\n");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	if( $remotePort != "" && $remotePort != "0" )
	{ fwrite("a",$ShellPath, "service HTTP.WAN-1 start > /dev/console\n");}
	if( query("/ddns/enable") == "1" )
	{ fwrite("a",$ShellPath, "service DDNS4.WAN-1 start > /dev/console\n"); }
	//if( $wpsEn == "1" )
	//{ fwrite("a",$ShellPath, "submit WLAN > /dev/console\n"); }
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetRouterSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetRouterSettingsResult><?=$result?></SetRouterSettingsResult>
    </SetRouterSettingsResponse>
  </soap:Body>
</soap:Envelope>
