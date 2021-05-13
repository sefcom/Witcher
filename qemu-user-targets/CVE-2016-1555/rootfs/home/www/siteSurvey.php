<?php
	@include('sessionCheck.inc');
	require_once 'include/libs/Smarty.class.php';
	
	function getFormatData($str)
	{
		$contArray=explode("\n",$str);
		$tempStr=str_replace('--',':','system:monitor:apList:detectedApTable:wlan0:iterate');
		$dataList=explode(':',$tempStr);
		
		foreach($contArray as $line) {
			$line=trim($line);
			if (!empty($line)) {
				$nameVal=explode(" ",$line);
				$tmpArr=explode(":",$nameVal[0]);
				if (strpos('ssid',$nameVal[0])!==false)	 {	
					str_replace('&nbsp;', ' ',$nameVal[1]);
					str_replace('&amp;', '&',$nameVal[1]);
				}
				if ((strtoupper($tmpArr[count($tmpArr)-1]) == 'ITERATE') || (strtoupper($tmpArr[count($tmpArr)-1]) == 'CONFIGITERATE')) {
				    continue;
				}
				if (array_search($tmpArr[1],$dataList)!==false && $nameVal[1] != 't') {
					if (count($tmpArr) > 2) {
		                for($k = 1 ; $k < count($tmpArr); $k++) {
		                    $par_str .= "['".$tmpArr[$k]."']";
		                }
	                    eval('$data'.$par_str.'=$nameVal[1];');
	                    //echo('$parentStr'.$par_str.'=$tmpArr[0].$par_str;');
						$par_str = '';
		            }
					
				}
			}
		}
		return $data;
	}
	
	function getSiteSurveyData()
	{
		$confdEnable = true;
		$str = '';
		if ($confdEnable) {
			$str = conf_get('system:monitor:apList:detectedApTable:wlan0:iterate');
		}
		else {
			$str = "system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A1 t
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A1:apSsid Test&nbsp;AP1
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A1:apAuthProto open
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A1:apPairwiseCipher none
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A1:apChannel 5
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A2 t
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A2:apSsid Test&amp;AP2
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A2:apAuthProto wep
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A2:apPairwiseCipher NA
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A2:apChannel 9
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A3 t
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A3:apSsid Test&nbsp;&amp;AP3
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A3:apAuthProto wpapsk
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A3:apPairwiseCipher tkip
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A3:apChannel 10
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A4 t
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A4:apSsid TestAP4
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A4:apAuthProto wpa2psk
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A4:apPairwiseCipher ccmp
system:monitor:apList:detectedApTable:wlan0:00-01-AB-CD-EF-A4:apChannel 12";
		}
        $str = str_replace('\\:',':',$str);
        $str = str_replace('\\ ',' ',$str);
        $str = str_replace('\\\\','\\',$str);
        $str = str_replace("\\\n","\n", $str);
		return $str;
	}
	
	function getEncryptionTypeList()
	{
        return array(	'0' =>	array(	"0"	=>	"None",
		                                "64"	=>	"64 bit WEP",
		                                "128"	=>	"128 bit WEP",
		                                "152"	=>	"152 bit WEP"
		                            ),
						'1'	=>	array(	"64"	=>	"64 bit WEP",
		                                "128"	=>	"128 bit WEP",
		                                "152"	=>	"152 bit WEP"
		                            ),
						'16'	=>	array(	"2"	=>	"TKIP"	),
						'32'	=>	array(	"4"	=>	"AES"	));
    }
	function getClientAuthenticationTypeList()
    {
        return array(	"0"	=>	"Open System",
                        "1"	=>	"Shared Key",
                        "16"	=>	"WPA-PSK",
                        "32"	=>	"WPA2-PSK"
                    );
    }

	$template->assign("clientEncryptionTypeList",getEncryptionTypeList());
	$template->assign("clientAuthenticationTypeList",getClientAuthenticationTypeList());
	$template->assign("data", getFormatData(getSiteSurveyData()));
	$template->display('siteSurvey.tpl');
?>
