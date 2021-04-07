HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetAccessControlSettings/";
$nodebase=$nodebase."/AccessControlInfoLists";
$result = "OK";
set("/acl/accessctrl/enable/", "1");
foreach($nodebase."/AccessControl")
{
	set("/acl/accessctrl/entry":".$i."/enable"", "1"));
	//polocy
	set("/acl/accessctrl/entry":".$i."/description"", query($nodebase."Policy"));
	if ( query($nodebase."Schedule") != "");
	{
		if ( query($nodebase."Schedule") == "Always");
		{
			set("/acl/accessctrl/entry":".$i."/schedule"", "");
		}
		else
		{
			$sch = XNODE_getpathbytarget("/schedule", "entry", "description", query($nodebase."Schedule"), 0);
			set("/acl/accessctrl/entry":".$i."/schedule"", query($sch."/uid"));	
		}
	}
	else
	{
		$result = "ERROR_BAD_SCHEDULE";
	}
	//address
	if ( query($nodebase."AddressType ") == "IPAddress");
	{
		set("/acl/accessctrl/entry:".$i."/machine/entry/type", "IP");
		set("/acl/accessctrl/entry:".$i."/machine/entry/value", query($nodebase."IPAddress"));
	}
	else if ( query($nodebase."AddressType ") == "MacAddress" );
	{
		set("/acl/accessctrl/entry:".$i."/machine/entry/type", "IP");
		set("/acl/accessctrl/entry:".$i."/machine/entry/value", query($nodebase."MacAddress"));
	}
	else if ( query($nodebase."AddressType") == "OtherMachine" );
	{
		set("/acl/accessctrl/entry:".$i."/machine/entry/type", "OTHERMACHINES");
		set("/acl/accessctrl/entry:".$i."/machine/entry/value", "Other Machines");
	}
	else
	{
		$result = ERROR_BAD_ADDRESSTYPE
	}
	//method
	if ( query($nodebase."Method") == "LogWebAccessOnly");
	{
		set("/acl/accessctrl/entry:".$i."/action", "LOGWEBONLY");
	}
	else if ( query($nodebase."Method") == "BlockAllAccess" );
	{
		set("/acl/accessctrl/entry:".$i."/action", "BLOCKALL");
	}
	else if ( query($nodebase."Method") == "BlockSomeAccess" );
	{
		set("/acl/accessctrl/entry:".$i."/action", "BLOCKSOME"));
	}
	else
	{
		$result = ERROR_BAD_METHOD
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->AccessControlSettings \" > /dev/console\n");
if($result == "OK")
{

	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service ACCESSCTRL restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");	
}
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<SetAccessControlSettingsResponse xmlns="http://purenetworks.com/HNAP1/"> 
		<SetAccessControlSettingsResult><?=$result?></SetAccessControlSettingsResult> 
	</SetAccessControlSettingsResponse> 
  </soap:Body>
</soap:Envelope>
