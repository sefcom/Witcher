HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetMACFilters/";
$Enabled=query($nodebase."Enabled");
$DefaultAllow=query($nodebase."DefaultAllow");
$rlt="OK";
$i=0;
foreach($nodebase."MACList/string")
{
	$i++;
}

if($i==0 && query($nodebase."MACList/string")!="") //-----Maybe only one node, set it to array
{
	set($nodebase."MACList/string", query($nodebase."MACList/string"));
	$i++;
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
if($i>32)
{
	$rlt="TOOMANY";
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}
else
{
	anchor("/acl/macctrl");
	if($Enabled=="true" && $DefaultAllow=="true")
	{
		set("policy", "ACCEPT");
	}
	else if($Enabled=="false")
	{
		set("policy", "DISABLE");
	}
	if($DefaultAllow=="true" && $Enabled=="true")
	{
		set("policy", "ACCEPT");
	}
	else if($DefaultAllow=="false" && $Enabled=="true")
	{
		set("policy", "DROP");
	}
	//-----Clear entry
	$j=0;
	foreach("entry")
	{
		$j++;
	}
	while($j>0)
	{
		del("entry:".$j);
		$j--;
	}

	$j=1;
	while($j<=$i)
	{
		set("/acl/macctrl/entry:".$j."/mac", query($nodebase."MACList/string:".$j));
		set("/acl/macctrl/entry:".$j."/description", "NetworkMagic".$j);
		$j++;
	}

	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service MACCTRL start > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");	
	set("/runtime/hnap/dev_status", "ERROR");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetMACFiltersResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetMACFiltersResult><?=$rlt?></SetMACFiltersResult>
    </SetMACFiltersResponse>
  </soap:Body>
</soap:Envelope>
