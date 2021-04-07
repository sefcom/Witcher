<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php"; 
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/wifi.php";

$wifiverify = get("","/runtime/devdata/wifiverify");
//if ($wifiverify==1) {$FEATURE_NOACMODE=1;}

$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
$path_phyinf_wlan2 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0);
$path_phyinf_wlan3 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN3, 0);
$CountryCode = get("", "/runtime/devdata/countrycode");
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetWLanRadiosResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetWLanRadiosResult>OK</GetWLanRadiosResult>
		<RadioInfos>
		<?if($path_phyinf_wlan1==""){echo "<!--";}?>
			<RadioInfo>
			<RadioID>2.4GHZ</RadioID>
			<Frequency>2</Frequency>
			<SupportedModes>
			<?if ($wifiverify==1)
				{
					echo "<string>802.11b</string>\n";
					echo "<string>802.11g</string>\n";
					echo "<string>802.11n</string>\n";
					echo "<string>802.11bg</string>\n";
					echo "<string>802.11gn</string>\n";
					echo "<string>802.11bgn</string>\n";
				}
				else
				{
					echo "<string>802.11n</string>\n";
					echo "<string>802.11gn</string>\n";
					echo "<string>802.11bgn</string>\n";
				}
			?>
			</SupportedModes>
			<Channels><?
			echo "\n";
			$clist = WIFI_getchannellist("g");
			$count = cut_count($clist, ",");
			$i = 0;
			while($i < $count)
			{
				$channel = cut($clist, $i, ',');
				echo "\t\t\t\t<int>".$channel."</int>";
				$i++;
				if($i < $count) echo "\n";
			}?>
			</Channels>
			<WideChannels>
			<?
			$bandwidth = query($path_phyinf_wlan1."/media/dot11n/bandwidth");     			
			if ($bandWidth != "20")
			{
				$startChannel = 3;
				while( $startChannel <= 9 )
				{
					echo "<WideChannel>\n";
					echo "	<Channel>".$startChannel."</Channel>\n";
					echo "	<SecondaryChannels>\n";
					$secondaryChnl = $startChannel - 2;
					echo "		<int>".$secondaryChnl."</int>\n";	
					$secondaryChnl = $startChannel + 2;
					echo "		<int>".$secondaryChnl."</int>\n";
					echo "	</SecondaryChannels>\n";
					echo "</WideChannel>\n";	
					$startChannel++;	
				}
			}
			?>
			</WideChannels>
			<SupportedSecurity>
			<?
				if ($wifiverify==1)
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA2-AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA+WPA2-TKIP+AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
				else
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WEP</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>WEP-64</string>\n";
					echo "		<string>WEP-128</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA-Personal</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
			?>
			</SupportedSecurity>
			</RadioInfo>
			<?if($path_phyinf_wlan1==""){echo "-->";}?>
			<?if($path_phyinf_wlan2==""){echo "<!--";}?>
			<RadioInfo>
			<RadioID>5GHZ</RadioID>
			<Frequency>5</Frequency>
			<SupportedModes>
				<string>802.11a</string>
				<string>802.11n</string>
				<string>802.11an</string><?
				if($FEATURE_NOACMODE!=1)
				{
					echo "\n";
					echo "\t\t\t\t<string>802.11ac</string>\n";
					echo "\t\t\t\t<string>802.11nac</string>\n";
					echo "\t\t\t\t<string>802.11anac</string>";
				}?>
			</SupportedModes>
			<Channels><?
			echo "\n";
			$clist = WIFI_getchannellist("a0");
			if($CountryCode=="EU")	$clist="36,40,44,48";
			$count = cut_count($clist, ",");
			$i = 0;
			while($i < $count)
			{
				$channel = cut($clist, $i, ',');
				echo "\t\t\t\t<int>".$channel."</int>";
				$i++;
				if($i < $count) echo "\n";
			}?>
			</Channels>
			<WideChannels>
			<?
			$bandwidth = query($path_phyinf_wlan2."/media/dot11n/bandwidth");     			
			if ($bandWidth != "20")
			{
				$startChannel = 44;
				while( $startChannel <= 56 )
				{
					echo "<WideChannel>\n";
					echo "	<Channel>".$startChannel."</Channel>\n";
					echo "	<SecondaryChannels>\n";
					$secondaryChnl = $startChannel - 8;
					echo "		<int>".$secondaryChnl."</int>\n";	
					$secondaryChnl = $startChannel + 8;
					echo "		<int>".$secondaryChnl."</int>\n";
					echo "	</SecondaryChannels>\n";
					echo "</WideChannel>\n";	
					$startChannel=$startChannel+4;	
				}
				echo "<WideChannel>\n";
		    	echo "	<Channel>157</Channel>\n";
				echo "	<SecondaryChannels>\n";
				echo "		<int>149</int>\n";	
				echo "		<int>165</int>\n";
				echo "	</SecondaryChannels>\n";
				echo "</WideChannel>\n";
			}
			?>
			</WideChannels>
			<SupportedSecurity>
			<?
				if ($wifiverify==1)
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA2-AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA+WPA2-TKIP+AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
				else
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WEP</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>WEP-64</string>\n";
					echo "		<string>WEP-128</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA-Personal</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
			?>
			</SupportedSecurity>
			</RadioInfo>
			<?if($path_phyinf_wlan2==""){echo "-->";}?>
			<!-- it does not support 5G high band if channel list is empty. -->
			<?if($path_phyinf_wlan3=="" || query("/runtime/freqrule/channellist/a1")==""){echo "<!--";}?>
			<RadioInfo>
			<RadioID>RADIO_5GHz_2</RadioID>
			<Frequency>5</Frequency>
			<SupportedModes>
				<string>802.11a</string>
				<string>802.11n</string>
				<string>802.11an</string><?
				if($FEATURE_NOACMODE!=1)
				{
					echo "\n";
					echo "\t\t\t\t<string>802.11ac</string>\n";
					echo "\t\t\t\t<string>802.11nac</string>\n";
					echo "\t\t\t\t<string>802.11anac</string>";
				}?>
			</SupportedModes>
			<Channels><?
			echo "\n";
			$clist = WIFI_getchannellist("a1");
			if($CountryCode=="EU")	$clist="";
			$count = cut_count($clist, ",");
			$i = 0;
			while($i < $count)
			{
				$channel = cut($clist, $i, ',');
				echo "\t\t\t\t<int>".$channel."</int>";
				$i++;
				if($i < $count) echo "\n";
			}?>
			</Channels>
			<WideChannels>
			<?
			$bandwidth = query($path_phyinf_wlan2."/media/dot11n/bandwidth");     			
			if ($bandWidth != "20")
			{
				$startChannel = 44;
				while( $startChannel <= 56 )
				{
					echo "<WideChannel>\n";
					echo "	<Channel>".$startChannel."</Channel>\n";
					echo "	<SecondaryChannels>\n";
					$secondaryChnl = $startChannel - 8;
					echo "		<int>".$secondaryChnl."</int>\n";	
					$secondaryChnl = $startChannel + 8;
					echo "		<int>".$secondaryChnl."</int>\n";
					echo "	</SecondaryChannels>\n";
					echo "</WideChannel>\n";	
					$startChannel=$startChannel+4;	
				}
				echo "<WideChannel>\n";
		    	echo "	<Channel>157</Channel>\n";
				echo "	<SecondaryChannels>\n";
				echo "		<int>149</int>\n";	
				echo "		<int>165</int>\n";
				echo "	</SecondaryChannels>\n";
				echo "</WideChannel>\n";
			}
			?>
			</WideChannels>
			<SupportedSecurity>
			<?
				if ($wifiverify==1)
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA2-AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA+WPA2-TKIP+AES</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
				else
				{
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WEP</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>WEP-64</string>\n";
					echo "		<string>WEP-128</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
					echo "<SecurityInfo>\n";
					echo "	<SecurityType>WPA-Personal</SecurityType>\n";
					echo "	<Encryptions>\n";
					echo "		<string>TKIP</string>\n";
					echo "		<string>AES</string>\n";
					echo "		<string>TKIPORAES</string>\n";
					echo "	</Encryptions>\n";
					echo "</SecurityInfo>\n";
				}
			?>
			</SupportedSecurity>
			</RadioInfo>
			<?if($path_phyinf_wlan3=="" || query("/runtime/freqrule/channellist/a1")==""){echo "-->";}?>
		</RadioInfos>
    </GetWLanRadiosResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
