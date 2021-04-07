HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
include "/htdocs/phplib/trace.php"; 
include "/htdocs/phplib/xnode.php";

echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetMACFilters2/";
$Enabled=query($nodebase."Enabled");
$DefaultAllow=query($nodebase."IsAllowList");
$rlt="OK";
$i=0;
foreach($nodebase."MACList/MACInfo")
{
	$i++;
}

if($i==0 && query($nodebase."MACList/MACInfo/MacAddress")!="") //-----Maybe only one node, set it to array
{
	set($nodebase."MACList/MACInfo:1/MacAddress", query($nodebase."MACList/MACInfo/MacAddress"));
	set($nodebase."MACList/MACInfo:1/DeviceName", query($nodebase."MACList/MACInfo/DeviceName"));
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
	set("allow", "false");
	if($Enabled=="false" && $DefaultAllow=="true")
	{
		set("allow", "true");
	}
	if($Enabled=="false")
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
	$pol = get("","policy");
	if(get("","policy") != "DISABLE")
	{
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
			set("/acl/macctrl/entry:".$j."/enable", "1"); //sammy
			set("/acl/macctrl/entry:".$j."/mac", query($nodebase."MACList/MACInfo:".$j."/MacAddress"));
			set("/acl/macctrl/entry:".$j."/description", query($nodebase."MACList/MACInfo:".$j."/DeviceName"));
			set("/acl/macctrl/entry:".$j."/schedule", XNODE_getscheduleuid(query($nodebase."MACList/MACInfo:".$j."/ScheduleName")));
			$j++;
		}
	}
	
	$cnt = get("x", "/acl/macctrl/entry#");
	set("/acl/macctrl/seqno", $cnt+1);
	set("/acl/macctrl/count", $cnt);
	
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service MACCTRL restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetMACFilters2Response xmlns="http://purenetworks.com/HNAP1/">
      <SetMACFilters2Result><?=$rlt?></SetMACFilters2Result>
    </SetMACFilters2Response>
  </soap:Body>
</soap:Envelope>
