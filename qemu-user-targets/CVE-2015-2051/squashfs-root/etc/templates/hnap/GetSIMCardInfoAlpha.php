<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";

$PINsts = get("x","/runtime/device/SIM/PINsts");

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSIMCardInfoAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetSIMCardInfoAlphaResult><?=$result?></GetSIMCardInfoAlphaResult>
			<PINsts><?=$PINsts?></PINsts>
		</GetSIMCardInfoAlphaResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
