<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

/*This HNAP action is refer the /htdocs/web/wpsstate.php in dlob.hans */
include "/htdocs/webinc/config.php";
$result = "OK";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetWPSStatusResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetWPSStatusResult><?=$result?></GetWPSStatusResult>
				<WPSStatusLists>
				<?
				foreach ("/runtime/phyinf")
				{
					if (get("", "type")=="wifi")
					{
						$uid = get("", "uid");
						if($uid == $WLAN1)
						{$uid = "RADIO_2.4GHz";}
						else if($uid == $WLAN2)
						{$uid = "RADIO_5GHz";}
						$status = get("", "media/wps/enrollee/state");
						if($status=="WPS_IN_PROGRESS" || $status=="")
						{$status = "WPS_IN_PROGRESS";}
						else if($status=="WPS_SUCCESS")
						{$status = "WPS_SUCCESS";}
						else
						{$status = "ERROR";}

						echo "					<WPSStatus>\n";
						echo "						<RadioID>".$uid."</RadioID>\n";
						echo "						<Status>".$status."</Status>\n";
						echo "					</WPSStatus>\n";
					}
				}
				?>
				</WPSStatusLists>
		</GetWPSStatusResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>