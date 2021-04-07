<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$pro_entry = "internetprofile/entry";
$pro_run_entry = "runtime/internetprofile/entry";

$result = "OK";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetInternetProfileAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetInternetProfileAlphaResult><?=$result?></GetInternetProfileAlphaResult>
			<?
				foreach($pro_run_entry)
				{
					$active = query("active");
					if($active==1)
					{
						$active_uid = query("profileuid");
						break;
					}
				}      
				
				$profile_path = XNODE_getpathbytarget("/internetprofile", "entry", "uid", $active_uid, 0);
				$active_pro = query($profile_path."/profilename");
				echo "				<ActiveProfile>".$active_pro."</ActiveProfile>\n";
			?>
				<InternetProfileLists>
					<?
						foreach($pro_entry)
						{
							$profile_name = get("x", "profilename");
							$profile_type = get("x", "profiletype");
							
							echo "			<InternetProfile>\n";
							echo "				<ProfileName>".$profile_name."</ProfileName>\n";
							echo "				<ProfileType>".$profile_type."</ProfileType>\n";
							
							if($profile_type=="DHCP")
							{
								$hostname = get("x", "config/hostname");
								$mac = get("x", "config/mac");
								echo "				<HostName>".$hostname."</HostName>\n";
								echo "				<MAC>".$mac."</MAC>\n";
							}
							else if($profile_type=="STATIC")
							{
								$ipaddr = get("x", "config/ipaddr");
								$mask = get("x", "config/mask");
								$submask = ipv4int2mask($mask);
								$gateway = get("x", "config/gateway");
								$dns_cnt = get("x", "config/dns/count");
								if($dns_cnt > 0) { $dns1 = get("x", "config/dns/entry:1"); }
								if($dns_cnt > 1) { $dns2 = get("x", "config/dns/entry:2"); }
								
								$mtu = get("x", "config/mtu");
								
								echo "				<IPAddress>".$ipaddr."</IPAddress>\n";
								echo "				<SubnetMask>".$submask."</SubnetMask>\n";
								echo "				<Gateway>".$gateway."</Gateway>\n";
								echo "				<DNS>\n";
								echo "					<Primary>".$dns1."</Primary>\n"; 
								echo "					<Secondary>".$dns2."</Secondary>\n"; 
								echo "				</DNS>\n";
								echo "				<MTU>".$mtu."</MTU>\n"; 
							}
							else if($profile_type=="PPPoE")
							{
								$username = get("x", "config/username");
								$password = get("x", "config/password");
								$static = get("x", "config/static");
								$addrmode = "";
								if($static==1)
								{ 
									$addrmode = "StaticPPPoE";
									$ipaddr = get("x", "config/ipaddr");
								}
								else if($static==0)
								{
									$addrmode = "DHCPPPPoE";
									$ipaddr = "";
								}
								
								$servicename = get("x", "config/servicename");
								$dialmode = get("x", "config/dialup/mode");
								$maxidle_t = get("x", "config/dialup/idletimeout");
								$dns_cnt = get("x", "config/dns/count");
								if($dns_cnt > 0) { $dns1 = get("x", "config/dns/entry:1"); }
								if($dns_cnt > 1) { $dns2 = get("x", "config/dns/entry:2"); }
								$mtu = get("x", "config/mtu");
								
								echo "				<UserName>".$username."</UserName>\n"; 
								echo "				<Password>".$password."</Password>\n";
								echo "				<AddressMode>".$addrmode."</AddressMode>\n"; 
								echo "				<IPAddress>".$ipaddr."</IPAddress>\n";
								echo "				<ServiceName>".$servicename."</ServiceName>\n"; 
								echo "				<ReconnectMode>".$dialmode."</ReconnectMode>\n";
								echo "				<MaxIdleTime>".$maxidle_t."</MaxIdleTime>\n";
								echo "				<DNS>\n";
								echo "					<Primary>".$dns1."</Primary>\n"; 
								echo "					<Secondary>".$dns2."</Secondary>\n"; 
								echo "				</DNS>\n";
								echo "				<MTU>".$mtu."</MTU>\n";
							}
							else if($profile_type=="WISP")
							{
								$authtype = get("x", "config/authtype");
								
								if($authtype=="OPEN") { $authtype = "NONE"; }
								else if($authtype=="WEPAUTO") { $authtype = "WEP"; }
								
								echo "				<SecurityType>".$authtype."</SecurityType>\n";
								
								if($authtype=="WEP")
								{
									$size = get("x", "config/wep/size");
									$ascii = get("x", "config/wep/ascii");
									
									if($size==64)
									{
										if($ascii==0) { $passwdlen = "64Hex"; }
										else if($ascii==1) { $passwdlen = "64ASCII"; }
									}
									else if($size==128)
									{
										if($ascii==0) { $passwdlen = "128Hex"; }
										else if($ascii==1) { $passwdlen = "128ASCII"; }
									}
									
									echo "				<PasswordLength>".$passwdlen."</PasswordLength>\n";
									$passwd = get("x", "config/wep/key");
								}
								else if($authtype=="WPAPSK" || $authtype=="WPA2PSK" || $authtype=="WPA+2PSK")
								{
									$passwd = get("x", "config/psk/key");
								}
								echo "				<Password>".$passwd."</Password>\n";
							}
							else if($profile_type=="USB3G")
							{
								$dialno = get("x", "config/dialno");
								$apn = get("x", "config/apn");
								$country = get("x", "config/country");
								$isp = get("x", "config/isp");
								$username = get("x", "config/username");
								$passwd = get("x", "config/password");
								$authprotocol = get("x", "config/authprotocol");
								$simstatus = get("x", "config/simcardstatus");
								$dialmode = get("x", "config/dialup/mode");
								$maxidle_t = get("x", "config/dialup/idletimeout");
								$mtu = get("x", "config/mtu");
								
								echo "				<DialupNumber>".$dialno."</DialupNumber>\n"; 
								echo "				<APN>".$apn."</APN>\n"; 
								echo "				<Country>".$country."</Country>\n"; 
								echo "				<ISP>".$isp."</ISP>\n";
								echo "				<UserName>".$username."</UserName>\n"; 
								echo "				<Password>".$passwd."</Password>\n";
								echo "				<AuthProtocol>".$authprotocol."</AuthProtocol>\n"; 
								echo "				<SIMCardStatus>".$simstatus."</SIMCardStatus>\n";
								echo "				<ReconnectMode>".$dialmode."</ReconnectMode>\n";
								echo "				<MaxIdleTime>".$maxidle_t."</MaxIdleTime>\n";
								echo "				<MTU>".$mtu."</MTU>\n";
							}
							echo "			</InternetProfile>\n";
						}
					?>	
				</InternetProfileLists>
		</GetInternetProfileAlphaResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
