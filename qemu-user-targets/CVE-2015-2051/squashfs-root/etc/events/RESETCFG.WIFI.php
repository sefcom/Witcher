<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/setcfg/libs/wifi.php";

wifi_setcfg("/runtime/default");
/*we should set configured to unconfigure,bc the default maybe configured.*/
foreach ("/wifi/entry") 
{
	set("wps/configured","0");	
}

?> 
