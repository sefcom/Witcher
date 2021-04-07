<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}
/*
	Ref: spec. D-Link HNAP Extension - 20140701v1.20.pdf
	Discussion with D-Link Luke in 20140925
	DISCONNECTED : Cable is disconnected.
	LIMITED_CONNECTION : Cable is connected without WAN IP.
	CONNECTING : Cable is connected and try to get the WAN IP.
	CONNECTED : Cable is connected with WAN IP.
*/
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/webinc/config.php";

$layout = query("/device/layout");
if($layout=="router")
{
	$INF = $WAN1;
}
else
{
	$INF = $BR1;
}

$CableStatus = get("", PHYINF_getphypath($INF)."/linkstatus");
if($CableStatus == "0" || $CableStatus == "")
{	$statusStr = "DISCONNECTED";}
else if(INF_getcfgipaddr($INF) != "" || INF_getcurripaddr($INF) != "")
{ $statusStr = "CONNECTED"; }
else 
{	$statusStr = "LIMITED_CONNECTION";}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetWanStatusResponse xmlns="http://purenetworks.com/HNAP1/">
<GetWanStatusResult>OK</GetWanStatusResult>	
<Status><?=$statusStr ?></Status>
</GetWanStatusResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
