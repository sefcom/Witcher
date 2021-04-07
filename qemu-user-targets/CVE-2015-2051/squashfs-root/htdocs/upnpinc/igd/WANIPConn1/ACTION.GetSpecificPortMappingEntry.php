<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
$target = query($_GLOBALS["ACTION_NODEBASE"]."/target");
anchor("/runtime/upnpigd/portmapping/entry:".$target);
?>
<NewInternalPort><?		echo query("internalport");?></NewInternalPort>
<NewInternalClient><?	echo query("internalclient");?></NewInternalClient>
<NewEnabled><?			echo query("enable");?></NewEnabled>
<NewPortMappingDescription><?echo query("description");?></NewPortMappingDescription>
<NewLeaseDuration>0</NewLeaseDuration>
