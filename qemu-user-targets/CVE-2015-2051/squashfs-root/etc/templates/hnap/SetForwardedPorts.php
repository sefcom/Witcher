HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetForwardedPorts/ForwardedPorts/ForwardedPort";
$max_rules=query("/nat/entry/virtualserver/max");
if($max_rules=="") { $max_rules=32; }
$rlt="OK";
$i=0;
foreach($nodebase)
{
	$i++;
}

if($i==0 && query($nodebase."/Enabled")!="") //-----Maybe only one node, set it to array
{
	$PrivateIP=query($nodebase."/PrivateIP");
	set($nodebase.":1/Enabled", query($nodebase."/Enabled"));
	set($nodebase.":1/Name", query($nodebase."/Name"));
	if($PrivateIP=="")
	{
		$PrivateIP="0.0.0.0";
	}
	set($nodebase.":1/PrivateIP", $PrivateIP);
	set($nodebase.":1/Protocol", query($nodebase."/Protocol"));
	set($nodebase.":1/Port", query($nodebase."/Port"));
	$i++;
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
if($i>$max_rules)
{
	$rlt="TOOMANY";
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}
else
{
	$i=0;
	//-----Clear
	anchor("/nat/entry/virtualserver");
	foreach("entry")
	{
		$i++;
	}
	while($i>0)
	{
//-----work around for purenetworks(with postfix //)
if(query("/nat/entry/virtualserver/entry:".$i."/publicPort")=="8008")//
{//
		del("entry:".$i);
}//
		$i--;
	}
$InDex=1;
foreach("entry")//
{//
	if($i==0 && query("enable")==0) { $i=$InDex; }//
	$InDex++;
}//
if($i==0) { $i=1; }
//	for($nodebase)//
//	{//
//		$i++;//
	anchor($nodebase);
	if(query("privateip")!="")//
	{//
		$Enabled="0";
		if(query("Enabled")=="true")
		{
			$Enabled="1";
		}

		$Protocol="0";
		if(query("Protocol")=="tcp" || query("Protocol")=="TCP")
		{
			$Protocol="TCP";
		}
		else if(query("Protocol")=="udp" || query("Protocol")=="UDP")
		{
			$Protocol="UDP";
		}
		$PrivateIP=query("privateip");
		if($PrivateIP=="")
		{
			$PrivateIP="0.0.0.0";
		}
		set("/nat/entry/virtualserver/entry:".$i."/enable", $Enabled);
		set("/nat/entry/virtualserver/entry:".$i."/description", query("Name"));
		set("/nat/entry/virtualserver/entry:".$i."/internal/hostid", $PrivateIP);
		set("/nat/entry/virtualserver/entry:".$i."/protocol", $Protocol);
		set("/nat/entry/virtualserver/entry:".$i."/internal/start", query("Port"));
		set("/nat/entry/virtualserver/entry:".$i."/external/start", query("Port"));
		set("/nat/entry/virtualserver/entry:".$i."/schedule", "");
	}
	//fwrite("a",$ShellPath, "/etc/scripts/misc/profile.sh put > /dev/console\n");
	//fwrite("a",$ShellPath, "/etc/templates/rg.sh vrtsrv > /dev/console\n");
	//fwrite("a",$ShellPath, "rgdb -i -s /runtime/hnap/dev_status '' > /dev/console");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service VSVR.NAT-1 restart > /dev/console\n");
    fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
	$rlt="OK";
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetForwardedPortsResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetForwardedPortsResult><?=$rlt?></SetForwardedPortsResult>
    </SetForwardedPortsResponse>
  </soap:Body>
</soap:Envelope>
