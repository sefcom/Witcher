HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase	= "/runtime/hnap/AddPortMapping";
$max_rules	= query("/nat/entry/virtualserver/max");
if ($max_rules=="") { $max_rules=32; }

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");

$rlt="OK";
$exist=0;
$i=0;
$j=0;
$InDex=1;
foreach("/nat/entry/virtualserver/entry/")
{
	$prot=query("protocol");
	if	($prot=="1") { $prot="TCP"; }
	else if	($prot=="2") { $prot="UDP"; }

	if (	query("internal/hostid")==query($nodebase."/InternalClient") &&
		query("external/start")==query($nodebase."/ExternalPort") &&
		$prot==query($nodebase."/PortMappingProtocol")	)
	{ $exist=1; }

	if ($i==0 && query("description")=="") { $i=$InDex; }
	$j++;
	$InDex++;
} 
if($i==0) { $i=$j+1; }
if($i > $max_rules || $exist==1)
{
	fwrite("a",$ShellPath, "echo \"max_rules=".$max_rules.", i=".$i."exist=".$exist."\" > /dev/console\n");
	$rlt="ERROR";
}
else
{
	anchor($nodebase);
	$PortMappingProtocol="TCP+UDP";
	$Protocol=query("PortMappingProtocol");
	if($Protocol=="tcp" || $Protocol=="TCP")
	{
		$PortMappingProtocol="TCP";
	}
	else if($Protocol=="udp" || $Protocol=="UDP")
	{
		$PortMappingProtocol="UDP";
	}
	$PrivateIP=query("InternalClient");
	if($PrivateIP=="")
	{
		$PrivateIP="0.0.0.0";
	}

	set("/nat/entry/virtualserver/entry:".$i."/enable", "1");
	set("/nat/entry/virtualserver/entry:".$i."/description", query("PortMappingDescription"));
	set("/nat/entry/virtualserver/entry:".$i."/internal/hostid", $PrivateIP);
	set("/nat/entry/virtualserver/entry:".$i."/protocol", $PortMappingProtocol);
	set("/nat/entry/virtualserver/entry:".$i."/internal/start", query("InternalPort"));
	set("/nat/entry/virtualserver/entry:".$i."/external/start", query("ExternalPort"));
	set("/nat/entry/virtualserver/entry:".$i."/schedule", "");

	fwrite("a",$ShellPath, "/etc/scripts/dbsave.sh > /dev/console\n");
	fwrite("a",$ShellPath, "service VSVR.NAT-1 restart > /dev/console\n");
    fwrite("a",$ShellPath, "xmldbc -W /nat/entry/virtualserver/ > /dev/console\n");
    fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
	$rlt="OK";
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <AddPortMappingResponse xmlns="http://purenetworks.com/HNAP1/">
      <AddPortMappingResult><?=$rlt?></AddPortMappingResult>
    </AddPortMappingResponse>
  </soap:Body>
</soap:Envelope>
