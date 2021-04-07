HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
anchor("/runtime/hnap/SetUSBStorageSettings");

$req_enable = get("","Enabled");
$req_RemoteHttp = get("","RemoteHttp");
$req_RemoteHttpPort = get("","RemoteHttpPort");
$req_RemoteHttps = get("","RemoteHttps");
$req_RemoteHttpsPort = get("","RemoteHttpsPort");

if($req_enable=="true") $req_enable = 1; else $req_enable = 0;
if($req_RemoteHttp=="true") $req_RemoteHttp = 1; else $req_RemoteHttp = 0;
if($req_RemoteHttps=="true") $req_RemoteHttps = 1; else $req_RemoteHttps = 0;

anchor("/webaccess");
set("enable",$req_enable);
set("httpenable",$req_RemoteHttp);
set("httpport",$req_RemoteHttpPort);
set("httpsenable",$req_RemoteHttps);
set("httpsport",$req_RemoteHttpsPort);

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
/*
Fixed issue of power led solid orange and DHCP server disappear after enable/disable DLNA server many times.
We just wrokround here and will solve it later.
root cause:
		The order in which execution of events are not dependency.
		(each events are child process forked by servd and running independent)
		In this case will trigger "event DBSAVE" "event LAN-1.DOWN" "event LAN-1.UP"(LAN-1 DOWN/UP triggered by service INET.LAN-1),
		but the actually running order may occur "event DBSAVE" "event LAN-1.UP" "event LAN-1.DOWN",
		and the abnormal condition will happen because "event LAN-1.DOWN" was the last execution. 
		sammy_hsu 2014/6/11
*/		
fwrite("a",$ShellPath, "xmldbc -t \"".scut($ShellPath, 0, "/var/run/").":3:service WEBACCESS restart > /dev/console\"\n");
fwrite("a",$ShellPath, "xmldbc -t \"".scut($ShellPath, 0, "/var/run/").":3:service INET.LAN-1 restart > /dev/console\"\n");
fwrite("a",$ShellPath, "xmldbc -t \"".scut($ShellPath, 0, "/var/run/").":3:service UPNPC restart > /dev/console\"\n");
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetUSBStorageSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetUSBStorageSettingsResult><?=$result?></SetUSBStorageSettingsResult>
</SetUSBStorageSettingsResponse>
</soap:Body>
</soap:Envelope>