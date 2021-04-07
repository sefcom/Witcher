HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/DeletePortMapping";
$rlt="OK";
$i=0;
anchor($nodebase);
$ExternalPort=query("ExternalPort");
$InDex=1;
foreach("/nat/entry/virtualserver/entry")
{
	$prot=query("protocol");
	if($prot=="1")
	{
		$prot="TCP";
	}
	else if($prot=="2")
	{
		$prot="UDP";
	}
	if(query("external/start")==$ExternalPort && $prot==query($nodebase."/PortMappingProtocol"))
	{
		$i=$InDex;		
	}
	$InDex++;
}
fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
if($i==0)
{
	$rlt="ERROR";
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");
}
else
{
	del("/nat/entry/virtualserver/entry:".$i);
	
	fwrite("a",$ShellPath, "/etc/scripts/dbsave.sh > /dev/console\n");
	fwrite("a",$ShellPath, "service VSVR.NAT-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -W /nat/entry/virtualserver/ > /dev/console\n");
    fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
	$rlt="REBOOT";
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <DeletePortMappingResponse xmlns="http://purenetworks.com/HNAP1/">
      <DeletePortMappingResult><?=$rlt?></DeletePortMappingResult>
    </DeletePortMappingResponse>
  </soap:Body>
</soap:Envelope>
