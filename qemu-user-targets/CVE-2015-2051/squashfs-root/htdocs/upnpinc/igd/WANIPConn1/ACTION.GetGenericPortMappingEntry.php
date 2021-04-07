<?
include "/htdocs/phplib/trace.php";
$NewPortMappingIndex = query($_GLOBALS["ACTION_NODEBASE"]."/GetGenericPortMappingEntry/NewPortMappingIndex");
$index = $NewPortMappingIndex + 1;
anchor("/runtime/upnpigd/portmapping/entry:".$index);
?>
<NewRemoteHost><?		echo query("remotehost");		?></NewRemoteHost>
<NewExternalPort><?		echo query("externalport");		?></NewExternalPort>
<NewProtocol><?			echo map("protocol","TCP","TCP","*","UDP");	?></NewProtocol>
<NewInternalPort><?		echo query("internalport");		?></NewInternalPort>
<NewInternalClient><?	echo query("internalclient");	?></NewInternalClient>
<NewEnabled><?			echo query("enable");			?></NewEnabled>
<NewPortMappingDescription><? echo query("description");	?></NewPortMappingDescription>
<NewLeaseDuration>0</NewLeaseDuration>
