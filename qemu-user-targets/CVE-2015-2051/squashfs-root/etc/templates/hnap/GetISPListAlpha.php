<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$isp_entry = "/runtime/services/operator/entry";

$result = "OK";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetISPListAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetISPListAlphaResult><?=$result?></GetISPListAlphaResult>
					<?
						foreach($isp_entry)
						{
							echo "			<CountryList>\n";
							echo "				<Country>".get("x","country")."</Country>\n";
							foreach($isp_entry.":".$InDeX."/entry")
							{
								echo "				<ISPList>\n";
								echo "					<DialNumber>".get("x","dialno")."</DialNumber>\n";
								echo "					<APN>".get("x","apn")."</APN>\n";
								echo "					<ProfileName>".get("x","profilename")."</ProfileName>\n";
								echo "				</ISPList>\n";
							}
							echo "			</CountryList>\n";
						}
					?>
		</GetISPListAlphaResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
