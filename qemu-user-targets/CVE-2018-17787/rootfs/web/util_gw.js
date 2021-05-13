/*==============================================================================*/
/*   wlbasic.htm and wizard-wlan1.htm  tcpiplan.htm*/

// for WPS ---------------------------------------------------->>
var wps_warn1='The SSID had been configured by WPS. Any change of the setting ' +
				'may cause stations to be disconnected. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn2='AP Mode had been configured by WPS. Any change of the setting ' +
				'may cause stations to be disconnected. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn3='The security setting had been configured by WPS. Any change of the setting ' +
				'may cause stations to be disconnected. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn4='The WPA Enterprise Authentication cannot be supported by WPS. ' +
				'Use this configuration will cause WPS be disabled. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn5='The 802.1x Authentication cannot be supported by WPS. ' +
				'Use this configuration will cause WPS be disabled. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn6='WDS mode cannot be supported by WPS. ' +
				'Use this configuration will cause WPS be disabled. ' + 
				'Are you sure you want to continue with the new setting?';
var wps_warn7='Adhoc Client mode cannot be supported by WPS. ' +
				'Use this configuration will cause WPS be disabled. ' + 
				'Are you sure you want to continue with the new setting?';
var encrypt_11n = 'Invalid Encryption Mode! WPA or WPA2, Cipher suite AES should be used for 802.11n band.';
var encrypt_basic = 'The Encryption Mode is not suitable for 802.11n band, please modify wlan encrypt setting, or it will not work properly.';
var encrypt_confirm='Are you sure you want to continue with this encrypt mode for 11n band? It may not get good performance while the user is using wlan network!';

var wps_wep_key_old;

function check_wps_enc(enc, radius, auth)
{
	if (enc == 0 || enc == 1) {
		if (radius != 0)
			return 2;
	}		
	else {
		if (auth & 1)
			return 2;
	}
	return 0;
}

function check_wps_wlanmode(mo, type)
{
	if (mo == 2) {
		return 1;
	}
	if (mo == 1 && type != 0) {
		return 1;
	}
	return 0;
}
//<<----------------------------------------------- for WPS
function disableDNSinput()
{
   disableTextField(document.tcpip.dns1);
   disableTextField(document.tcpip.dns2);
   disableTextField(document.tcpip.dns3);
}

function enableDNSinput()
{
   enableTextField(document.tcpip.dns1);
   enableTextField(document.tcpip.dns2);
   enableTextField(document.tcpip.dns3);
}

function autoDNSclicked()
{
  disableDNSinput();
}

function manualDNSclicked()
{
  enableDNSinput();
}

// check whether the str is a right IPv6 address
function checkIPv6(str) {
    var idx = str.indexOf("::");
    // there is no "::" in the ip address
    if (idx == -1) {
        var items = str.split(":");
        if (items.length != 8) {
            return false;
        } else {
            for (i in items) {
                if (!isHex(items[i])) {
                    return false;
                }
            }
            return true;
        }
    } else {
        // at least, there are two "::" in the ip address
        if (idx != str.lastIndexOf("::")) {
            return false;
        } else {
            var items = str.split("::");
            var items0 = items[0].split(":");
            var items1 = items[1].split(":");
            if ((items0.length + items1.length) > 7) {
                return false;
            } else {
                for (i in items0) {
                    if (!isHex(items0[i])) {
                        return false;
                    }
                }
                for (i in items1) {
                    if (!isHex(items1[i])) {
                        return false;
                    }
                }
                return true;
            }
        }
    }
}

function validateNum(str)
{
  for (var i=0; i<str.length; i++) {
   	if ( !(str.charAt(i) >='0' && str.charAt(i) <= '9')) {
		alert("Invalid value. It should be in decimal number (0-9).");
		return false;
  	}
  }
  return true;
}

function isHex(str) {
    if(str.length == 0 || str.length > 4) {
        return false;
    }
    str = str.toLowerCase();
    var ch;
    for(var i=0; i< str.length; i++) {
        ch = str.charAt(i);
        if(!(ch >= '0' && ch <= '9') && !(ch >= 'a' && ch <= 'f')) {
            return false;
        }
    }
    return true;
}

function skip () { this.blur(); }
function disableTextField (field) {
  if (document.all || document.getElementById)
    field.disabled = true;
  else {
    field.oldOnFocus = field.onfocus;
    field.onfocus = skip;
  }
}

function enableTextField (field) {
  if (document.all || document.getElementById)
    field.disabled = false;
  else {
    field.onfocus = field.oldOnFocus;
  }
}

function verifyBrowser() {
	var ms = navigator.appVersion.indexOf("MSIE");
	ie4 = (ms>0) && (parseInt(navigator.appVersion.substring(ms+5, ms+6)) >= 4);
	var ns = navigator.appName.indexOf("Netscape");
	ns= (ns>=0) && (parseInt(navigator.appVersion.substring(0,1))>=4);
	if (ie4)
		return "ie4";
	else
		if(ns)
			return "ns";
		else
			return false;
}

function saveChanges_basic(form, wlan_id)
{
  	var mode =form.elements["mode"+wlan_id] ;

	if (form.name=="wlanSetup") 
	{
		// for support WPS2DOTX  ; ap mode
		var hiddenSSIDEnabled = form.elements["hiddenSSID"+wlan_id];

		if(mode.selectedIndex==0 || mode.selectedIndex==3)
		{
			if ( hiddenSSIDEnabled.selectedIndex==0 )
			{
				if(!confirm("if turn on hiddenSSID; WPS2.0 will be disabled"))
				{
					return false;
				}
			}
		}
	}

  	var ssid =form.elements["ssid"+wlan_id] ;			//mode.selectedIndex=4 means AP+MESH
  // P2P_SUPPORT
  	if((mode.selectedIndex==0 || mode.selectedIndex==3 ) && ssid.value=="") 
  	{
		alert('SSID cannot be empty');

		ssid.value = ssid.defaultValue;
		ssid.focus();
		return false;
   	}

	tx_restrict = form.elements["tx_restrict"+wlan_id];
	if(tx_restrict)
	{
		if (validateNum(tx_restrict.value)==0)
        {
         	tx_restrict.focus();
        	return false;
        }
		if (tx_restrict.value == "") 
		{
			alert('tx_restrict cannot be empty!');
			tx_restrict.focus();
			return false;
		}
		else if(tx_restrict.value < 0 || tx_restrict.value > 1000)
		{
			alert('tx_restrict value between 0 to 1000!');
			tx_restrict.focus();
			return false;
		}
	}
	rx_restrict = form.elements["rx_restrict"+wlan_id];	
	if(rx_restrict)
	{
		if (validateNum(rx_restrict.value)==0)
	        {
	         	rx_restrict.focus();
	        	return false;
	        }
		if (rx_restrict.value == "") 
		{
			alert('rx_restrict cannot be empty!');
			rx_restrict.focus();
			return false;
		}
		else if(rx_restrict.value < 0 || rx_restrict.value > 1000)
		{
			alert('rx_restrict value between 0 to 1000!');
			rx_restrict.focus();
			return false;
		}
	}
   	if (!form.elements["wlanDisabled"+wlan_id].checked)
	{
		var idx_value = form.elements["band"+wlan_id].selectedIndex;
		var band_value = form.elements["band"+wlan_id].options[idx_value].value;
		var band = parseInt(band_value, 10) + 1;

		var wlBandMode =form.elements["wlBandMode"].value ;
			
		if(wlBandMode == 3) // 3:BANDMODESIGNLE
		{
			var selectText=form.elements["band"+wlan_id].options[idx_value].text.substr(0,1);
			var bandOption = form.elements["band"+wlan_id].options.value;
			
			//if(selectText=='2') //match '2'
			if(bandOption == 0 || bandOption == 1 || (bandOption == 7 && selectText=='2') || bandOption == 2 || bandOption == 9 || bandOption == 10)
			{
				form.elements["Band2G5GSupport"].value = 1;//1:PHYBAND_2G
			}
			else if(bandOption == 3 || (bandOption == 7 && selectText=='5') || bandOption == 11)
			{
				form.elements["Band2G5GSupport"].value = 2;//2:PHYBAND_5G
			}
		}

		var basicRate=0;
		var operRate=0;
		if(band & 1)
		{
			basicRate |= 0xf;
			operRate |= 0xf;
		}
		if((band & 2) || (band & 4))
		{
			operRate|=0xff0;
			if (!(band & 1)) 
			{
				if (WiFiTest)
					basicRate=0x15f;
				else
					basicRate=0x1f0;
			}			
		}
		if (band & 8) 
		{
			if (!(band & 3))
				operRate|=0xfff;	
			if (band & 1)
				basicRate=0xf;
			else if (band & 2)			
				basicRate=0x1f0;
			else
				basicRate=0xf;
		}
	
		operRate|=basicRate;
		if (band && band != usedBand[wlan_id]) 
		{
			form.elements["basicrates"+wlan_id].value = basicRate;
			form.elements["operrates"+wlan_id].value = operRate;
		}
		else 
		{
			form.elements["basicrates"+wlan_id].value = 0;
			form.elements["operrates"+wlan_id].value = 0;
		}
	}

	return true;
}
/*==============================================================================*/
function show_div(show,id) {
	var div=document.getElementById(id);
	if(!div) return;
	if(show)
		div.className  = "on" ;
    else	    
    	div.className  = "off" ;
}

/*   tcpipwan.htm */
/*-- keith: add l2tp support. 20080515  */
function wanShowDiv(pptp_bool, dns_bool, dnsMode_bool, pppoe_bool, static_bool, l2tp_bool, USB3G_bool)
{
 	show_div(pptp_bool,"pptp_div");
  	show_div(dnsMode_bool,"dnsMode_div");
  	show_div(dns_bool,"dns_div");
  	show_div(pppoe_bool,"pppoe_div");
  	show_div(static_bool,"static_div"); 
	show_div(l2tp_bool,"l2tp_div"); /*-- keith: add l2tp support. 20080515  */
    show_div(USB3G_bool, "USB3G_div"  );
	show_div(1, "always_div");
  	if (pptp_bool==0 && pppoe_bool==0 && static_bool==0 && dns_bool && l2tp_bool==0 && USB3G_bool==0) /*-- keith: add l2tp support. 20080515  */
  	  	show_div(1,"dhcp_div");  	
  	else
  		show_div(0,"dhcp_div");  
}

function vaildInteger(str)
{   
	for (var i=0; i<str.length; i++) {
    if ( str.charAt(i) >= '0' && str.charAt(i) <= '9')
			continue;
	return 0;
  }
  return 1;
}
//-------------------------------------------------------------------------------------------
function validateInteger(strMsg)//if strMsg contain nonnumeric character,it will return false,else it will return true;
{
	var test=/^(0|[1-9]\d*)$/;
	if(test.exec(strMsg))
	{
		return true;
	}
	else
	{
		return false;
	}
}
//-------------------------------------------------------------------------------------------
function saveChanges_wan(form , MultiPppoeFlag, dynamicWanIP)
{
  	var wanType = form.wanType.selectedIndex;
	if(form.pppoeNumber)
  		var pppoeNumber = form.pppoeNumber.selectedIndex;
  	else
  		var pppoeNumber = 0;
  	
  	var subNetNumber;

  	if (form.lte4g_build.value == 1 && form.lte4g_enable.value == 1)
  	{
		wanType = 1;
  	}
	var form_name=form.name;

  	if ( wanType == 0 )//static IP
	{
	  	if ( checkIpAddr(form.wan_ip, 'Invalid IP address') == false )
	    		return false;

		if(form_name == "wizard" || form_name == "tcpip")
		{
			var i;
			var is_same_net_section=1;  //1:is in the same net section; 0:is not in the same net section; 
			var lan_ip_value;
			var lan_mask_value;
			if(form_name == "wizard" )
			{
				lan_ip_value=form.lan_ip.value;
				lan_mask_value=form.lan_mask.value;
			}
			else if(form_name == "tcpip")
			{
				lan_ip_value=form.lan_ip_address.value;
				lan_mask_value=form.lan_subnet_mask.value;
			}
			var wan_ip_value=form.wan_ip.value;
			var wan_mask_value=form.wan_mask.value;
			var min_mask=wan_mask_value;//record the shorter mask
			for(i=1; i<=4; i++)
			{
				if(getDigit(lan_mask_value,i) < getDigit(wan_mask_value,i))
				{
					min_mask=lan_mask_value;
					break;
				}
			}
			
			for(i=1; i<=4; i++)
			{
				if((getDigit(lan_ip_value,i)&getDigit(min_mask,i))!=(getDigit(wan_ip_value,i)&getDigit(min_mask,i)))
				{
					is_same_net_section=0;
					break;
				}
			}
			if(is_same_net_section==1)
			{
				alert("WAN IP address and LAN IP address can't in the same net section.");
				form.wan_ip.focus();
				return false;
			}
		}
		
  	  	if (checkIPMask(form.wan_mask) == false)
  			return false ;
	  
	  	if(form.wan_ip.value == form.wan_gateway.value)
		{
			alert("wan static ip address can not equal to gateway ip address.");
			form.wan_gateway.focus();
			return false;
		}

	  	if(checkHostIPValid(form.wan_ip,form.wan_mask,'Invalid IP address')== false) 
			return false;

	  	if (form.wan_gateway.value!="" && form.wan_gateway.value!="0.0.0.0") 
		{
			if ( checkIpAddr(form.wan_gateway, 'Invalid default gateway address') == false )
				return false;
			if ( !checkSubnet(form.wan_ip.value,form.wan_mask.value,form.wan_gateway.value))
			{
				alert('Invalid gateway address!\nIt should be located in the same subnet of current IP address.');
				form.wan_gateway.value = form.wan_gateway.defaultValue;
				form.wan_gateway.focus();
				return false;
			}
	  	}
	  	else
			form.wan_gateway.value = '0.0.0.0';

		if(form.dns1.value!="" && form.dns1.value!="0.0.0.0")
		{
			form.dnsMode.value="dnsManual";
			if ( checkIpAddr(form.dns1, 'Invalid DNS address') == false ) 
				return false;
		}
		if (form.fixedIpMtuSize != null)
		{
			/*if(validateInteger(form.fixedIpMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
			{
				alert("You can't input nonnumeric character!");
				form.fixedIpMtuSize.focus();
 				return false;
			}
		 	d2 = getDigit(form.fixedIpMtuSize.value, 1);
	     		if ( validateKey(form.fixedIpMtuSize.value) == 0 ||
				(d2 > 1500 || d2 < 1400) ) {
				alert("Invalid MTU size! You should set a value between 1400-1500.");
				form.fixedIpMtuSize.value = form.fixedIpMtuSize.defaultValue;
				form.fixedIpMtuSize.focus();
				return false;
	     		}*/
			if( checkFieldDigitRange(form.fixedIpMtuSize,1400,1500,"MTU Size") == false)
		  		return false;
	  	}
  	}
  else if ( wanType == 1){ //dhcp wanType
		if (form.dhcpMtuSize != null)
		{
			/*if(validateInteger(form.dhcpMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
			{
				alert("You can't input nonnumeric character!");
				form.dhcpMtuSize.focus();
 				return false;
			}
	   		  d2 = getDigit(form.dhcpMtuSize.value, 1);
	   		  if ( validateKey(form.dhcpMtuSize.value) == 0 ||
				(d2 > 1500 || d2 < 1400) ) {
				alert("Invalid MTU size! You should set a value between 1400-1500.");
				form.dhcpMtuSize.value = form.dhcpMtuSize.defaultValue;
				form.dhcpMtuSize.focus();
				return false;
	    		 }*/
	     if( checkFieldDigitRange(form.dhcpMtuSize,1280,1500,"MTU Size") == false)
		 	return false;
	  } 
	if(form.hostName!=null)
	{ 
	var str = form.hostName.value;
		if(str.length > 63){
  			alert("Invalid Host Name! Length of Domain Name shoule not more than 63");
			form.hostName.focus();
			return false;
  		}
	
	  	for(var i=0; i<str.length; i++){
  			if( (str.charAt(i) >= '0' && str.charAt(i) <= '9') ||
				(str.charAt(i) >= 'a' && str.charAt(i) <= 'z') ||
				(str.charAt(i) >= 'A' && str.charAt(i) <= 'Z') ||
				str.charAt(i) == '-')
					continue;
			alert("Invalid Host Name! Please enter characters in A(a)~Z(z) or 0-9 or '-' without spacing.");	
			form.hostName.focus();
			return false;
  		}
	  	if(str.charAt(0) == '-' ||
	  		str.charAt(str.length - 1) == '-' ||
	  		(str.charAt(0) >= '0' && str.charAt(0) <= '9')){
			alert("Invalid Host Name! Host Name should begin with letter,end with letter or digits .");	
			form.hostName.focus();
			return false;
	  	}
	  
	}
	  
  }
  else if ( wanType == 2){ //pppoe wanType
	   if (form.pppUserName.value=="") {
		  alert('PPP user name cannot be empty!');
		  form.pppUserName.value = form.pppUserName.defaultValue;
		  form.pppUserName.focus();
		  return false;
	   }
	   if (form.pppPassword.value=="") {
		  alert('PPP password cannot be empty!');
		  form.pppPassword.value = form.pppPassword.defaultValue;
		  form.pppPassword.focus();
		  return false;
	   }
	   if ( form.pppConnectType != null){
			if ( form.pppConnectType.selectedIndex == 1 ) 
			{
				if(validateInteger(form.pppIdleTime.value) == false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.pppIdleTime.focus();
					return false;
				}
				d1 = getDigit(form.pppIdleTime.value, 1);
				if(vaildInteger(form.pppIdleTime.value) == 0 || (d1 > 1000 || d1 < 1))
				{
					alert("Invalid idle time value! You should set a value between 1-1000.");
					form.pppIdleTime.focus();
					return false;
				}
			}
	   } 
           
        if ( form.pppMtuSize != null)
		{
			/*if(validateInteger(form.pppMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
			{
				alert("You can't input nonnumeric character!");
				form.pppMtuSize.focus();
 				return false;
			}
	    		 d2 = getDigit(form.pppMtuSize.value, 1);
	    		 if ( vaildInteger(form.pppMtuSize.value) == 0 ||
				(d2 > 1492 || d2 < 1360) ) {
				alert("Invalid MTU size! You should set a value between 1360-1492.");
				form.pppMtuSize.value = form.pppMtuSize.defaultValue;
				form.pppMtuSize.focus();
				return false;
	   		  }*/
	   		 if( checkFieldDigitRange(form.pppMtuSize,1360,1492,"MTU Size") == false)
			 	return false;
	   		  
	   }  // if (pppMtuSize !=null)
	   if(MultiPppoeFlag ==1)
	 	   if (ppp_checkSubNetFormat(form.pppSubNet_1,'Invalid ip input') == false)
	  			return false; 
	  //----------------------first pppoe info check  End--------------------------------	   
	  //----------------------Second pppoe info check  Begin----------------------------- 
	   if(pppoeNumber >= 1)
	   {
		   if (form.pppUserName2.value=="") {
			  alert('2 PPP user name cannot be empty!');
			  form.pppUserName2.value = form.pppUserName2.defaultValue;
			  form.pppUserName2.focus();
			  return false;
		   }	
		   if (form.pppPassword2.value=="") {
			  alert('2 PPP password cannot be empty!');
			  form.pppPassword2.value = form.pppPassword2.defaultValue;
			  form.pppPassword2.focus();
			  return false;
		   }		
		   if ( form.pppConnectType2 != null){
			     if ( form.pppConnectType2.selectedIndex == 1 ) {
					d1 = getDigit(form.pppIdleTime2.value, 1);
					if ( validateKey(form.pppIdleTime2.value) == 0 ||
						(d1 > 1000 || d1 < 1) ) {
						alert("Invalid idle time value! You should set a value between 1-1000.");
						form.pppIdleTime2.focus();
						return false;
					}
			     }
		   }
			if ( form.pppMtuSize2 != null)
			{
				/*
				if(validateInteger(form.pppMtuSize2.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.pppMtuSize2.focus();
	 				return false;
				}
			     d2 = getDigit(form.pppMtuSize2.value, 1);
			     if ( validateKey(form.pppMtuSize2.value) == 0 ||
					(d2 > 1492 || d2 < 1360) ) {
					alert("Invalid MTU size! You should set a value between 1360-1492.");
					form.pppMtuSize2.value = form.pppMtuSize2.defaultValue;
					form.pppMtuSize2.focus();
					return false;
			     }*/

				if( checkFieldDigitRange(form.pppMtuSize2,1360,1492,"MTU Size") == false)
			 		return false;
		   }	
		   if (ppp_checkSubNetFormat(form.pppSubNet_2,'Invalid ip input') == false)
  		   		return false; 
	
	   	}
//----------------------Second pppoe info check  End---------------------------------		  
//----------------------Third pppoe info check  Begin--------------------------------	

	  if(pppoeNumber >= 2)	
	  {
			if (form.pppUserName3.value=="") {
			  alert('3 PPP user name cannot be empty!');
			  form.pppUserName3.value = form.pppUserName3.defaultValue;
			  form.pppUserName3.focus();
			  return false;
		   	}

		   if (form.pppPassword3.value=="") {
			  alert('3 PPP password cannot be empty!');
			  form.pppPassword3.value = form.pppPassword3.defaultValue;
			  form.pppPassword3.focus();
			  return false;
		   }	
		   if ( form.pppConnectType3 != null){
			     if ( form.pppConnectType3.selectedIndex == 1 ) {
					d1 = getDigit(form.pppIdleTime3.value, 1);
					if ( validateKey(form.pppIdleTime3.value) == 0 ||
						(d1 > 1000 || d1 < 1) ) {
						alert("Invalid idle time value! You should set a value between 1-1000.");
						form.pppIdleTime3.focus();
						return false;
					}
			     }
		   } 	   
			if ( form.pppMtuSize3 != null)
			{
				/*
				if(validateInteger(form.pppMtuSize3.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.pppMtuSize3.focus();
	 				return false;
				}
			     d2 = getDigit(form.pppMtuSize3.value, 1);
			     if ( validateKey(form.pppMtuSize3.value) == 0 ||
					(d2 > 1492 || d2 < 1360) ) {
					alert("Invalid MTU size! You should set a value between 1360-1492.");
					form.pppMtuSize3.value = form.pppMtuSize3.defaultValue;
					form.pppMtuSize3.focus();
					return false;
			     }*/
			    if( checkFieldDigitRange(form.pppMtuSize3,1360,1492,"MTU Size") == false)
			 		return false;
			}	
		   if (ppp_checkSubNetFormat(form.pppSubNet_3,'Invalid ip input') == false)
  		   		return false; 		   
	  }
//----------------------Third pppoe info check  End----------------------------------	
//----------------------Fourth pppoe info check  Begin--------------------------------	
		if(pppoeNumber >= 3)
		{
			if (form.pppUserName4.value=="") {
			  alert('4 PPP user name cannot be empty!');
			  form.pppUserName4.value = form.pppUserName4.defaultValue;
			  form.pppUserName4.focus();
			  return false;
		   	}		

		   if (form.pppPassword4.value=="") {
			  alert('4 PPP password cannot be empty!');
			  form.pppPassword4.value = form.pppPassword4.defaultValue;
			  form.pppPassword4.focus();
			  return false;
		   }
		   if ( form.pppConnectType4 != null){
			     if ( form.pppConnectType4.selectedIndex == 1 ) {
					d1 = getDigit(form.pppIdleTime4.value, 1);
					if ( validateKey(form.pppIdleTime4.value) == 0 ||
						(d1 > 1000 || d1 < 1) ) {
						alert("Invalid idle time value! You should set a value between 1-1000.");
						form.pppIdleTime4.focus();
						return false;
					}
			     }
		   } 	   
			if ( form.pppMtuSize4 != null)
			{
				/*if(validateInteger(form.pppMtuSize4.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.pppMtuSize4.focus();
	 				return false;
				}
			     d2 = getDigit(form.pppMtuSize4.value, 1);
			     if ( validateKey(form.pppMtuSize4.value) == 0 ||
					(d2 > 1492 || d2 < 1360) ) {
					alert("Invalid MTU size! You should set a value between 1360-1492.");
					form.pppMtuSize4.value = form.pppMtuSize4.defaultValue;
					form.pppMtuSize4.focus();
					return false;
			     }*/
				 if( checkFieldDigitRange(form.pppMtuSize4,1360,1492,"MTU Size") == false)
			 		return false;
			}
		   if (ppp_checkSubNetFormat(form.pppSubNet_4,'Invalid ip input') == false)
  		   		return false; 		  
		}
		
//----------------------Fourth pppoe info check  End----------------------------------		  
	   
  }
  else if ( wanType == 3){ //pptp wanType
	  	 
	  if(dynamicWanIP == 0)
	  { 
	  	  if ( checkIpAddr(form.pptpIpAddr, 'Invalid IP address') == false )
		    return false;
		  if (checkIPMask(form.pptpSubnetMask) == false)
	  			return false ;

		  if(checkHostIPValid(form.pptpIpAddr,form.pptpSubnetMask,'Invalid IP address')== false) 
			return false;
		  
		  if ( checkIpAddr(form.pptpServerIpAddr, 'Invalid server IP address') == false )
		      return false;
		  if ( !checkSubnet(form.pptpIpAddr.value,form.pptpSubnetMask.value,form.pptpDefGw.value)) {
		      alert('Invalid pptp default gateway IP address!\nIt should be located in the same subnet of local IP address.');
		      form.pptpDefGw.value = form.pptpDefGw.defaultValue;
		      form.pptpDefGw.focus();
		      return false;
		  }
	  }
	if(form.pptpServerDomainName)
	{
		if((!form.pptpServerDomainName.disabled) && 
			!checkFieldEmpty(form.pptpServerDomainName,'domain name can not be emapy'))
		  	return false;
	}
	if((!form.pptpServerIpAddr.disabled)&&
		!checkIpAddr(form.pptpServerIpAddr, 'Invalid IP address'))
		return false;
	
	  if (form.pptpUserName.value=="") {
		  alert('User name cannot be empty!');
		  form.pptpUserName.value = form.pptpUserName.defaultValue;
		  form.pptpUserName.focus();
		  return false;
	  }
	  if (form.pptpPassword.value=="") {
		  alert('Password cannot be empty!');
		  form.pptpPassword.value = form.pptpPassword.defaultValue;
		  form.pptpPassword.focus();
		  return false;
	  }
	   if ( form.pptpConnectType != null){
			if ( form.pptpConnectType.selectedIndex == 1 ) 
			{
				if(validateInteger(form.pptpIdleTime.value) == false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.pptpIdleTime.focus();
					return false;
				}

				var d1 = getDigit(form.pptpIdleTime.value, 1);
				if(validateKey(form.pptpIdleTime.value) == 0 ||(d1 > 1000 || d1 < 1)) 
				{
					alert("Invalid idle time value! You should set a value between 1-1000.");
					form.pptpIdleTime.focus();
					return false;
				}
			}
	   }
		if ( form.pptpMtuSize != null)
		{
			/*if(validateInteger(form.pptpMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
			{
				alert("You can't input nonnumeric character!");
				form.pptpMtuSize.focus();
	 			return false;
			}
	  		var d2 = getDigit(form.pptpMtuSize.value, 1);
	   		if ( validateKey(form.pptpMtuSize.value) == 0 ||
				(d2 > 1460 || d2 < 1400) ) {
				alert("Invalid MTU size! You should set a value between 1400-1460.");
				form.pptpMtuSize.value = form.pptpMtuSize.defaultValue;
				form.pptpMtuSize.focus();
				return false;
	   		}*/
	   		 if( checkFieldDigitRange(form.pptpMtuSize,1400,1460,"MTU Size") == false)
			 		return false;
	  } 
   } 
   /*-- keith: add l2tp support. 20080515  */
   else if ( wanType == 4){ //l2tp wanType
	  
	  
	  if(dynamicWanIP == 0)
	  {
	  	  if ( checkIpAddr(form.l2tpIpAddr, 'Invalid IP address') == false )
		    return false;
		  if (checkIPMask(form.l2tpSubnetMask) == false)
	  			return false ;
		  
		  if(checkHostIPValid(form.l2tpIpAddr,form.l2tpSubnetMask,'Invalid IP address')== false) 
			return false;
		  
		  if ( checkIpAddr(form.l2tpServerIpAddr, 'Invalid server IP address') == false )
		      return false;
		  if ( !checkSubnet(form.l2tpIpAddr.value,form.l2tpSubnetMask.value,form.l2tpDefGw.value)) {
		      alert('Invalid l2tp default gateway IP address!\nIt should be located in the same subnet of local IP address.');
		      form.l2tpDefGw.value = form.l2tpDefGw.defaultValue;
		      form.l2tpDefGw.focus();
		      return false;
		  }
	  }
	if(form.l2tpServerDomainName)
	{
		if((!form.l2tpServerDomainName.disabled) && 
			!checkFieldEmpty(form.l2tpServerDomainName,'domain name can not be emapy'))
		  	return false;
	}
	if((!form.l2tpServerIpAddr.disabled)&&
		!checkIpAddr(form.l2tpServerIpAddr, 'Invalid IP address'))
		return false;
	
	  if (form.l2tpUserName.value=="") {
		  alert('User name cannot be empty!');
		  form.l2tpUserName.value = form.l2tpUserName.defaultValue;
		  form.l2tpUserName.focus();
		  return false;
	  }
	  if (form.l2tpPassword.value=="") {
		  alert('Password cannot be empty!');
		  form.l2tpPassword.value = form.l2tpPassword.defaultValue;
		  form.l2tpPassword.focus();
		  return false;
	  }
	   if ( form.l2tpConnectType != null){
			if ( form.l2tpConnectType.selectedIndex == 1 ) 
			{
				if(validateInteger(form.l2tpIdleTime.value) == false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.l2tpIdleTime.focus();
					return false;
				}
				d1 = getDigit(form.l2tpIdleTime.value, 1);
				if(validateKey(form.l2tpIdleTime.value) == 0 || (d1 > 1000 || d1 < 1)) 
				{
					alert("Invalid idle time value! You should set a value between 1-1000.");
					form.l2tpIdleTime.focus();
					return false;
				}
			}
	   } 
		if ( form.l2tpMtuSize != null)
		{
			/*if(validateInteger(form.l2tpMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
			{
				alert("You can't input nonnumeric character!");
				form.l2tpMtuSize.focus();
	 			return false;
			}
	  		d2 = getDigit(form.l2tpMtuSize.value, 1);
	   		if ( validateKey(form.l2tpMtuSize.value) == 0 ||
				(d2 > 1460 || d2 < 1400) ) {
				alert("Invalid MTU size! You should set a value between 1400-1460.");
				form.l2tpMtuSize.value = form.l2tpMtuSize.defaultValue;
				form.l2tpMtuSize.focus();
				return false;
	   		}*/
			if( checkFieldDigitRange(form.l2tpMtuSize,1400,1460,"MTU Size") == false)
			 	return false;
	  } 
   }
// --------------- USB3G wanType ---------------
   else if ( wanType == 5){
	if(form.wanType.options[5].text == "USB3G")
	{
	        if (form.USB3G_APN.value=="") {
	            alert('APN name cannot be empty!');
	            form.USB3G_APN.value = form.USB3G_APN.defaultValue;
	            form.USB3G_APN.focus();
	            return false;
	        }

	        if (form.USB3G_DIALNUM.value=="") {
	            alert('Dial number cannot be empty!');
	            form.USB3G_DIALNUM.value = form.USB3G_DIALNUM.defaultValue;
	            form.USB3G_DIALNUM.focus();
	            return false;
	        }

	        if ( form.USB3GConnectType != null){
	            if ( form.USB3GConnectType.selectedIndex == 1 ) {
	                d1 = getDigit(form.USB3GIdleTime.value, 1);
	                if ( validateKey(form.USB3GIdleTime.value) == 0 || (d1 > 1000 || d1 < 1) ) {
	                    alert("Invalid idle time value! You should set a value between 1-1000.");
	                    form.USB3GIdleTime.focus();
	                    return false;
	                }
	            }
	        }
	        if ( form.USB3GMtuSize != null)
			{
				/*if(validateInteger(form.USB3GMtuSize.value)==false)//if nonnumeric character was input,a warining will be generated and this text box will be focused.
				{
					alert("You can't input nonnumeric character!");
					form.USB3GMtuSize.focus();
		 			return false;
				}
	           		 d2 = getDigit(form.USB3GMtuSize.value, 1);
	           		 if ( validateKey(form.USB3GMtuSize.value) == 0 || (d2 > 1490 || d2 < 1420) ) {
	             	  	 alert("Invalid MTU size! You should set a value between 1420-1490.");
	              		  form.USB3GMtuSize.value = form.USB3GMtuSize.defaultValue;
	                		form.USB3GMtuSize.focus();
					return false;
		   		}*/
		   	if( checkFieldDigitRange(form.USB3GMtuSize,1420,1490,"MTU Size") == false)
			 	return false;
		  } 
   	}
   } 

	if( wanType != 0 ) // not static IP
   	{
   		if(form_name != "wizard")
		{
		   	var group = form.dnsMode;
		   	//for (var r = 0; r < group.length; r++)
			//  	if (group[r].checked)
			//    	break;
		   	if(wanType == 5)
	   	   	{
	   	   		if(form.wanType.options[5].text != "USB3G")
					r=0;
	   	   	}
			
		   	if (group[1].checked)
		   	{
				if (form.dns1.value=="")
				{
					alert('DNS1 address cannot be empty!');
					return false;
				}	
				if (form.dns1.value=="0.0.0.0")
				{
		      		alert('DNS1 address cannot be 0.0.0.0!');
					return false;
				}
				else
				{
					if ( checkIpAddr(form.dns1, 'Invalid DNS1 address') == false )
			     		return false;
				}		
				if (form.dns2 != null)
				{
					if (form.dns2.value=="")
						form.dns2.value="0.0.0.0";
					if (form.dns2.value!="0.0.0.0") 
					{
						if ( checkIpAddr(form.dns2, 'Invalid DNS2 address') == false )
							return false;
					}			
				}//dns2 != null
				if (form.dns3 != null)
				{	
					if (form.dns3.value=="")
						form.dns3.value="0.0.0.0";
					if (form.dns3.value!="0.0.0.0") 
					{
						if ( checkIpAddr(form.dns3, 'Invalid DNS3 address') == false )
							return false;
					}			
				}// dns3 != null
			}
		}
   	}
	else
	{
		if (form.dns1.value=="")
		{
	  		alert('DNS1 address cannot be empty!');
			return false;
		}
		if (form.dns1.value=="0.0.0.0")
		{
			alert('DNS1 address cannot be 0.0.0.0!');
			return false;
		}
	  	else 
		{
			if ( checkIpAddr(form.dns1, 'Invalid DNS1 address') == false )
				return false;
		}
	  	    
	  if (form.dns2 != null){  
	  	if (form.dns2.value=="")
	    		form.dns2.value="0.0.0.0";
	  	if (form.dns2.value!="0.0.0.0") {
	    		if ( checkIpAddr(form.dns2, 'Invalid DNS2 address') == false )
	      			return false;
	  	}	    		
	  }
	  if (form.dns3 != null){
	  	if (form.dns3.value=="")
	    		form.dns3.value="0.0.0.0";
	  	if (form.dns3.value!="0.0.0.0") {
	    		if ( checkIpAddr(form.dns3, 'Invalid DNS3 address') == false )
	      			return false;
	  	}	    		
	  } 
   }
   if (form.wan_macAddr != null){
   	if (form.wan_macAddr.value == "")
		form.wan_macAddr.value = "000000000000";
	var str = form.wan_macAddr.value;
   	if ( str.length < 12) {
		alert("Input MAC address is not complete. It should be 12 digits in hex.");
		form.wan_macAddr.value = form.wan_macAddr.defaultValue;
		form.wan_macAddr.focus();
		return false;
  	}

	// fixed "All MAC Address field can't reject 00:00:00:00:00:00/ff:ff:ff:ff:ff:ff MAC Address" issue
	if(str == "ffffffffffff")
	{
		alert("Invalid MAC address. It should not be ff:ff:ff:ff:ff:ff.");
		form.wan_macAddr.value = form.wan_macAddr.defaultValue;
		form.wan_macAddr.focus();
		return false;
  	}

	//var reg = /01005[eE][0-7][0-9a-fA-F]{5}/;
	//if(reg.exec(str))
	if(parseInt(str.substr(0, 2), 16) & 0x01 != 0)
	{
		form.wan_macAddr.value = form.wan_macAddr.defaultValue;
		form.wan_macAddr.focus();
		alert("Invalid MAC address. It should not be multicast mac address.");
		return false;
	}
	
   	for (var i=0; i<str.length; i++) {
     		if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') ||
			(str.charAt(i) >= 'a' && str.charAt(i) <= 'f') ||
			(str.charAt(i) >= 'A' && str.charAt(i) <= 'F') )
			continue;
		alert("Invalid MAC address. It should be in hex number (0-9 or a-f).");
		form.wan_macAddr.value = form.wan_macAddr.defaultValue;
		form.wan_macAddr.focus();
		return false;
   	}  	
   }
	if ("undefined" != typeof check_web_access_port_valid && check_web_access_port_valid() == false){
		return false;
	}		

   return true;
}
/*==============================================================================*/
/*   wlbasic.htm */
function enableWLAN(form, wlan_id)
{
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;
	var chan_boundIdx = form.elements["channelbound"+wlan_id].selectedIndex;	
	var mode_idx = form.elements["mode"+wlan_id].selectedIndex; 
	var mode_value =form.elements["mode"+wlan_id].options[mode_idx].value; 	
		
	if(form.elements["multipleAP"+wlan_id] != null) { // for multiple ap
		if (mode_value == 0 || mode_value == 3 || mode_value ==4)
			enableButton(form.elements["multipleAP"+wlan_id]);
		else
			disableButton(form.elements["multipleAP"+wlan_id]);
	}
	
  if (mode_value !=1) {	//mode != client
  	disableTextField(form.elements["type"+wlan_id]); //network type
  	if(form.elements["showMac"+wlan_id]!= null) {
		// mode ==AP or AP+WDS or MPP+AP or MAP
  		if (mode_value ==0 || mode_value ==3 || mode_value ==4){	
  			enableButton(form.elements["showMac"+wlan_id]);
			
			// plus note, just AP or AP+WDS need Multi-AP,under MPP+AP or MAP mode disable multi-AP -> now enabled 29/12/2016
			if (mode_value ==0 || mode_value ==3 || mode_value ==4)	
			if(form ==document.wlanSetup){  	
				if(form.elements["multipleAP"+wlan_id] != null)
					enableButton(form.elements["multipleAP"+wlan_id]);
			}	
			else
			if(form ==document.wlanSetup){  	
				if(form.elements["multipleAP"+wlan_id] != null)
					disableButton(form.elements["multipleAP"+wlan_id]);

			}
  		}else{
  			disableButton(form.elements["showMac"+wlan_id]);
  			if(form ==document.wlanSetup){  	
				if(form.elements["multipleAP"+wlan_id] != null)
					disableButton(form.elements["multipleAP"+wlan_id]);
			}	
  		}
  	}
  	enableTextField(form.elements["chan"+wlan_id]);
  }
  else {	// mode == client
    if (disableSSID[wlan_id])
  		disableTextField(form.elements["type"+wlan_id]);
  	else
   		enableTextField(form.elements["type"+wlan_id]);   	   	
    	
   	if(form.elements["showMac"+wlan_id] != null)
		disableButton(form.elements["showMac"+wlan_id]);
	if(form ==document.wlanSetup){  	
		if(form.elements["multipleAP"+wlan_id] != null)
			disableButton(form.elements["multipleAP"+wlan_id]);
	}	
	if (form.elements["type"+wlan_id].selectedIndex==0) {
		disableTextField(form.elements["chan"+wlan_id]);
	}
	else {
		enableTextField(form.elements["chan"+wlan_id]);
	}

  }
  if (disableSSID[wlan_id]){
	disableTextField(form.elements["ssid"+wlan_id]);
 	disableTextField(form.elements["mode"+wlan_id]);  	
  }
  else {
  	if (mode_value !=2)
  		enableTextField(form.elements["ssid"+wlan_id]);
  	else
  		disableTextField(form.elements["ssid"+wlan_id]);
  		
  	enableTextField(form.elements["mode"+wlan_id]); 
  }  
  enableTextField(form.elements["band"+wlan_id]);

  if(form.elements["mode"+wlan_id].selectedIndex == 1 && opmode != 2) // client mode but not wisp
  {
  	enableCheckBox(form.elements["wlanMacClone"+wlan_id]);
  	
  	if(form.elements["wizardAddProfile"+wlan_id]) //check from wizard
  		enableCheckBox(form.elements["wizardAddProfile"+wlan_id]);
  }
  else
  {
  	disableCheckBox(form.elements["wlanMacClone"+wlan_id]);
		if(form.elements["wizardAddProfile"+wlan_id]) //check from wizard
  		disableCheckBox(form.elements["wizardAddProfile"+wlan_id]);
  }
  	
//ac2g	
	if(band_value == 9 || band_value ==10 || band_value==7 || band_value==11 || band_value==14 || band_value==63 || band_value==71 || band_value==75 || band_value==74 ){ //8812
	  	enableTextField(form.elements["channelbound"+wlan_id]);
	  
	  	
	  	if(chan_boundIdx == 1) //8812
	  		enableTextField(form.elements["controlsideband"+wlan_id]);
	  	else 	
	  		 disableTextField(form.elements["controlsideband"+wlan_id]);
	 }
	if(form ==document.wlanSetup){  	
		enableTextField(form.elements["txRate"+wlan_id]);	
  		enableTextField(form.elements["hiddenSSID"+wlan_id]);	
	}
}
function disableWLAN(form, wlan_id)
{
  disableTextField(form.elements["mode"+wlan_id]);
  disableTextField(form.elements["band"+wlan_id]);
  disableTextField(form.elements["type"+wlan_id]); 
  disableTextField(form.elements["ssid"+wlan_id]);
  disableTextField(form.elements["chan"+wlan_id]);
  disableTextField(form.elements["channelbound"+wlan_id]);
  disableTextField(form.elements["controlsideband"+wlan_id]);
if(form == document.wlanSetup){  
  disableTextField(form.elements["hiddenSSID"+wlan_id]);
  disableTextField(form.elements["txRate"+wlan_id]);
  disableButton(form.elements["multipleAP"+wlan_id]);
}  
  disableCheckBox(form.elements["wlanMacClone"+wlan_id]);

	if(form.elements["wizardAddProfile"+wlan_id]) //check from wizard
  	disableCheckBox(form.elements["wizardAddProfile"+wlan_id]);

  if(form.elements["showMac"+wlan_id]!= null)
  	disableButton(form.elements["showMac"+wlan_id]);
}
function updateIputState(form, wlan_id)
{
  if (form.elements["wlanDisabled"+wlan_id].checked)
 	disableWLAN(form, wlan_id);
  else
  	enableWLAN(form, wlan_id);
}

function disableButton (button) {
  //if (verifyBrowser() == "ns")
  //	return;
  if (document.all || document.getElementById)
    button.disabled = true;
  else if (button) {
    button.oldOnClick = button.onclick;
    button.onclick = null;
    button.oldValue = button.value;
    button.value = 'DISABLED';
  }
}

function enableButton (button) {
  //if (verifyBrowser() == "ns")
  //	return;
  if (document.all || document.getElementById)
    button.disabled = false;
  else if (button) {
    button.onclick = button.oldOnClick;
    button.value = button.oldValue;
  }
}

function showChannel5G(form, wlan_id)
{
	var sideBand=form.elements["controlsideband"+wlan_id].value;
	var dsf_enable=form.elements["dsf_enable"].value;
	var idx=0;
	var wlan_support_8812e=0;
	if(form.elements["wlan_support_8812e"])
		wlan_support_8812e=form.elements["wlan_support_8812e"].value;
	var defChanIdx;
	form.elements["chan"+wlan_id].length=startChanIdx[wlan_id];
	
	if (startChanIdx[wlan_id] == 0)
		defChanIdx=0;
	else
		defChanIdx=1;

	if (startChanIdx[wlan_id]==0) {
		if(dsf_enable == 1)		
			form.elements["chan"+wlan_id].options[0] = new Option("Auto(DFS)", 0, false, false);
		else
			form.elements["chan"+wlan_id].options[0] = new Option("Auto", 0, false, false);
			
		if (0 == defaultChan[wlan_id]) {
			form.elements["chan"+wlan_id].selectedIndex = 0;
			defChanIdx = 0;
		}
		startChanIdx[wlan_id]++;		
	}
	
	idx=startChanIdx[wlan_id];
	

	if(wlan_support_8812e ==1)
	{
		var bound = form.elements["channelbound"+wlan_id].selectedIndex;
		var inc_scale;
		var chan;
		inc_scale = 4;
		var chan_str = 36;
		var chan_end = 64;

		var reg_chan_8812_full =new Array(16);
		var i;
		var ii;
		var iii;
		var iiii;
		var found; 
		var chan_pair;
		var reg_8812 = regDomain[wlan_id];
		
/* FCC */			reg_chan_8812_full[0]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","136","140","149","153","157","161","165");
/* IC */				reg_chan_8812_full[1]= new Array("36","40","44","48","52","56","60","64","149","153","157","161");
/* ETSI */			reg_chan_8812_full[2]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128","132","136","140");                                         
/* SPAIN */			reg_chan_8812_full[3]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128","132","136","140");                                         
/* FRANCE */			reg_chan_8812_full[4]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128","132","136","140");                                          
/* MKK */			reg_chan_8812_full[5]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128","132","136","140");                                          
/* ISRAEL */			reg_chan_8812_full[6]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128","132","136","140");                                           
/* MKK1 */			reg_chan_8812_full[7]= new Array("34","38","42","46");                                                                                                  
/* MKK2 */			reg_chan_8812_full[8]= new Array("36","40","44","48");                                                                                               
/* MKK3 */			reg_chan_8812_full[9]= new Array("36","40","44","48","52","56","60","64");                                                                                        
/* NCC (Taiwan) */	reg_chan_8812_full[10]= new Array("56","60","64","100","104","108","112","116","136","140","149","153","157","161","165");                                                    
/* RUSSIAN */		reg_chan_8812_full[11]= new Array("36","40","44","48","52","56","60","64","132","136","140","149","153","157","161","165");                                                                                                  
/* CN */				reg_chan_8812_full[12]= new Array("36","40","44","48","52","56","60","64","149","153","157","161","165");                                                                                           
/* Global */			reg_chan_8812_full[13]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","136","140","149","153","157","161","165");                                    
/* World_wide */		reg_chan_8812_full[14]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","136","140","149","153","157","161","165");                                       
/* Test */			reg_chan_8812_full[15]= new Array("36","40","44","48","52","56","60","64","100","104","108","112","116","120","124","128"," 132","136","140","144","149","153","157","161","165","169","173","177");		

		if(reg_8812 > 0)
			reg_8812 = reg_8812 - 1;
		if(reg_8812 > 15)
			reg_8812 = 15;

		if(reg_8812==7) //MKK1 are special case
		{
			if(bound <= 2)
			for(i = 0; i < reg_chan_8812_full[reg_8812].length; i++) {
				chan = reg_chan_8812_full[reg_8812][i];
			
			form.elements["chan"+wlan_id].options[idx] = new Option(chan, chan, false, false);
			if (chan == defaultChan[wlan_id]) {
				form.elements["chan"+wlan_id].selectedIndex = idx;
				defChanIdx=idx;
			}
			idx ++;
		}
		}
		else
		{
			for(i = 0; i < reg_chan_8812_full[reg_8812].length; i++) {
				chan = reg_chan_8812_full[reg_8812][i];

				if(reg_8812 != 15 && reg_8812 != 10)
				if((dsf_enable == 0) && (chan >= 52) && (chan <= 144))
					continue;
				
				if( reg_8812 == 10)
					if((dsf_enable == 0) && (chan >= 100) && (chan <= 140))
						continue;

				if(reg_8812 != 15)
				if(bound==1)
				{
					for(ii=0; ii < reg_chan_8812_full[15].length; ii++)
					{
						if(chan == reg_chan_8812_full[15][ii])
							break;
					}
					
					if(ii%2 == 0)
						chan_pair = reg_chan_8812_full[15][ii+1];
					else
						chan_pair = reg_chan_8812_full[15][ii-1];

					found = 0;
					for(ii=0; ii < reg_chan_8812_full[reg_8812].length; ii++)
					{
						if(chan_pair == reg_chan_8812_full[reg_8812][ii])
						{
							found = 1;
							break;
						}
					}

					if(found == 0)
						chan = 0;
		
				}
				else if(bound==2)
				{
					for(ii=0; ii < reg_chan_8812_full[15].length; ii++)
					{
						if(chan == reg_chan_8812_full[15][ii])
							break;
					}

					for(iii=(ii-(ii%4)); iii<((ii-(ii%4)+3)) ; iii++)
					{
						found = 0;
						chan_pair = reg_chan_8812_full[15][iii];
			
						for(iiii=0; iiii < reg_chan_8812_full[reg_8812].length; iiii++)
						{
							if(chan_pair == reg_chan_8812_full[reg_8812][iiii])
							{
								found=1;
								break;
							}
			}

						if(found == 0)
						{
							chan = 0;
							break;
		}

					}

				}
			
				if(chan != 0)
				{
			form.elements["chan"+wlan_id].options[idx] = new Option(chan, chan, false, false);
			if (chan == defaultChan[wlan_id]) {
				form.elements["chan"+wlan_id].selectedIndex = idx;
				defChanIdx=idx;
			}
					idx++;
		}

			}
		}
		
	}
	else{
		reg_chan_plan = new Array(17);
		reg_chan_plan[0] = [2, [36,4],[149,5]]; //FCC
		reg_chan_plan[1] = [2, [36,4],[149,4]]; //IC
		reg_chan_plan[2] = [1, [36,4]]; //ETSI
		reg_chan_plan[3] = [1, [36,4]]; //SPAIN
		reg_chan_plan[4] = [1, [36,4]]; //FRANCE
		reg_chan_plan[5] = [1, [36,4]]; //MKK
		reg_chan_plan[6] = [1, [36,4]]; //ISRAEL
		reg_chan_plan[7] = [1, [34,4]]; //MKK1
		reg_chan_plan[8] = [1, [36,4]]; //MKK2
		reg_chan_plan[9] = [1, [36,4]]; //MKK3
		reg_chan_plan[10] = [2, [56,3],[149,5]]; //NCC
		reg_chan_plan[11] = [2, [36,4],[149,5]]; //RUSSIAN
		reg_chan_plan[12] = [2, [36,4],[149,5]]; //CN 
		reg_chan_plan[13] = [2, [36,4],[149,5]]; //Global
		reg_chan_plan[14] = [2, [36,4],[149,5]]; //World_wide		
		if(!dsf_enable){
			reg_chan_plan[15] = [3, [36,8],[100,12],[149,8]]; //Test
		}else{
			reg_chan_plan[15] = [2, [36,4],[149,8]]; //Test
		}
		reg_chan_plan[16] = [1, [146,170]]; //5M10M

		var index = regDomain[wlan_id] - 1;
		var seg_num = reg_chan_plan[index][0];
		var bandstep;		
		var bound = form.elements["channelbound"+wlan_id].selectedIndex;
		var idx_value= form.elements["band"+wlan_id].selectedIndex;
		var band_value= form.elements["band"+wlan_id].options[idx_value].value;
		
		if(regDomain[wlan_id]>=1 && regDomain[wlan_id]<17){ //step by 4
			bandstep = 4;
		}else if(regDomain[wlan_id]==17){ //step by 1
			bandstep = 1;
		}else{
			return;
		}

		for(var bn=0; bn<seg_num; bn++){
			var base = reg_chan_plan[index][bn+1][0];
			var bandnum = reg_chan_plan[index][bn+1][1];
			var startindex = 0;
			var indexstep = 1;
			if(regDomain[wlan_id]!=17){
				if(band_value!=3){ // not 11a, maybe: 11AC,11n,11a+11n,...
					if(bound==1){//40M
						indexstep = 2;
						if(sideBand==0){ //upper
							startindex = 1;
						}
					}
				}
			}
			for(var bindex=startindex; bindex<bandnum; idx++, bindex+=indexstep){
				var chan = base + bindex*bandstep;
				form.elements["chan"+wlan_id].options[idx] = new Option(chan, chan, false, false);
				if (chan == defaultChan[wlan_id]) {
					form.elements["chan"+wlan_id].selectedIndex = idx;
					defChanIdx=idx;
				}
			}
		}
		reg_chan_plan = null;
	}
		
	form.elements["chan"+wlan_id].length = idx;
	if (defChanIdx==0)
		form.elements["chan"+wlan_id].selectedIndex = 0;
}


function showChannel2G(form, wlan_id, bound_40, band_value)
{
	var start = 1;
	var end = 14;
	if (regDomain[wlan_id]==1 || regDomain[wlan_id]==2 || regDomain[wlan_id]==11) {
		start = 1;
		end = 11;
	}
	if (regDomain[wlan_id]==3 || regDomain[wlan_id]==4 || regDomain[wlan_id]==12 || regDomain[wlan_id]==13 || regDomain[wlan_id]==15) {
		start = 1;
		end = 13;
	}
	if (regDomain[wlan_id]==5) {
		start = 10;
		end = 13;
	}
	if (regDomain[wlan_id]==6  || regDomain[wlan_id]==8  || regDomain[wlan_id]==9  || regDomain[wlan_id]==10  || regDomain[wlan_id]==14  || regDomain[wlan_id]==16 ) {
		start = 1;
		end = 14;
	}
	if (regDomain[wlan_id]==7) {
		start = 3;
		end = 13;
	}


	if(band_value == 9 || band_value == 10 || band_value==7 || band_value==74){
		if(bound_40 ==1){
			var sideBand_idex = form.elements["controlsideband"+wlan_id].selectedIndex;
			var sideBand=form.elements["controlsideband"+wlan_id].options[sideBand_idex].value;
			if(regDomain[wlan_id]==4){
				if(sideBand ==0){  //upper
					start = 11;
					end = 11;
				}else if(sideBand ==1){ //lower
					start = 10;
					end = 10;
				}
			}else if(regDomain[wlan_id]==5){
				if(sideBand ==0){  //upper
					start = 13;
					end = 13;
				}else if(sideBand ==1){ //lower
					start = 10;
					end = 10;
				}
			}else{
				if(sideBand ==0){  //upper
					start = 5;
					if (regDomain[wlan_id]==1 || regDomain[wlan_id]==2 || regDomain[wlan_id]==11)
						end = 11;
					else  				
						end = 13;			
					
				}else if(sideBand ==1){ //lower
					end = 9;
					//end = 7; orig
					if(regDomain[wlan_id]==7)
						start = 3;
					else 
						start = 1;
				}
			}
		}
	}
	var defChanIdx=0;
	form.elements["chan"+wlan_id].length=0;

	idx=0;
	form.elements["chan"+wlan_id].options[0] = new Option("Auto", 0, false, false);
	
	if(wlan_channel[wlan_id] ==0){
		form.elements["chan"+wlan_id].selectedIndex = 0;
		defChanIdx = 0;
	}

	idx++;	
	var chan;
	
//ac2g
{
	for (chan=start; chan<=end; chan++, idx++) {
		form.elements["chan"+wlan_id].options[idx] = new Option(chan, chan, false, false);
		if(chan == wlan_channel[wlan_id]){
			form.elements["chan"+wlan_id].selectedIndex = idx;
			defChanIdx = idx;
		}
	}
}
	form.elements["chan"+wlan_id].length=idx;
	startChanIdx[wlan_id] = idx;
}
function updateChan_channebound(form, wlan_id)
{
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;
	var bound = form.elements["channelbound"+wlan_id].selectedIndex;
	var adjust_chan;
	var Band2G5GSupport=form.elements["Band2G5GSupport"].value;
	var wlBandMode=form.elements["wlBandMode"].value;
	
	
if(form.name == "wizard")
	{
		switch(wlan_id)
		{
			case 0:
				if(form.elements["wlan1_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
				
			case 1:
				if(form.elements["wlan2_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
			
		}
		
	}
var currentBand;		

	if(band_value ==3 || band_value ==11 || band_value ==63|| band_value ==71|| band_value ==75){
		currentBand = 2;
	}
	else if(band_value ==0 || band_value ==1 || band_value ==2 || band_value == 9 || band_value ==10||band_value==74){ //ac2g
		currentBand = 1;
	}
	else if(band_value == 4 || band_value==5 || band_value==6 || band_value==14){
		currentBand = 3;
	}
	else if(band_value == 7) //7:n
	{
		if(Band2G5GSupport == 1) //1:2g
			currentBand = 1;
		else
			currentBand = 2;

		if(wlBandMode == 3)
		{
			if(idx_value != 1)
				currentBand =1;
			else
				currentBand =2;
		}
	}
	if(band_value==9 || band_value==10 || band_value ==7 || band_value==74){	 // 8812 ?? adjust channel ?? //ac2g
		if(bound ==0)
			adjust_chan=0;
		if(bound ==1)
			adjust_chan=1;	
		if(bound ==2)
			adjust_chan=2;
	}else
		adjust_chan=0;	  
    

	if (currentBand == 3) {
		showChannel2G(form, wlan_id, adjust_chan, band_value);
		showChannel5G(form, wlan_id);
	}
  
	if (currentBand == 2) {
		startChanIdx[wlan_id]=0;
		showChannel5G(form, wlan_id);
		Band2G5GSupport = 2;
	}
	
  	if (currentBand == 1) {
		showChannel2G(form, wlan_id, adjust_chan, band_value);
		Band2G5GSupport = 1;
  	}
 	
 	if(band_value==9 || band_value==10 || band_value ==7 || band_value ==11 || band_value ==14){
	  	if(form.elements["chan"+wlan_id].value == 0){ // 0:auto	  
	  		disableTextField(form.elements["controlsideband"+wlan_id]);	
		}
	}
}

function updateChan(form, wlan_id)
{
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;
	var Band2G5GSupport=form.elements["Band2G5GSupport"].value;
	var currentBand;
	if(form.name == "wizard")
	{
		switch(wlan_id)
		{
			case 0:
				if(form.elements["wlan1_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
				
			case 1:
				if(form.elements["wlan2_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
			
		}
		
	}	
//ac2g
	if(band_value ==3|| band_value ==11 || (band_value ==7 && Band2G5GSupport == 2)|| band_value ==63|| band_value ==71 || band_value ==75){ // 3:5g_a 11:5g_an 7:n 2:PHYBAND_5G
		currentBand = 2;
	}
	else if(band_value ==0 || band_value ==1 || band_value ==2 || band_value == 9 || band_value ==10 || band_value == 74 || (band_value ==7 && Band2G5GSupport == 1)){
		currentBand = 1;
	}else if(band_value == 4 || band_value==5 || band_value==6 || band_value==14){
		currentBand = 3;
	}


  if ((lastBand[wlan_id] != currentBand) || (lastRegDomain[wlan_id] != regDomain[wlan_id])) {
  	lastBand[wlan_id] = currentBand;
	lastRegDomain[wlan_id] = regDomain[wlan_id];
	if (currentBand == 3) {
		showChannel2G(form, wlan_id, 0, band_value);
		showChannel5G(form, wlan_id);
	}
	
  if (currentBand == 2) {
		startChanIdx[wlan_id]=0;
		showChannel5G(form, wlan_id);
	}
	
  	if (currentBand == 1)
		showChannel2G(form, wlan_id, 0, band_value);
  }

  	if(band_value==9 || band_value==10 || band_value ==7 || band_value ==11 || band_value ==14){
	  	if(form.elements["chan"+wlan_id].selectedIndex ==0)
	  	{ // 0:auto
	  		disableTextField(form.elements["controlsideband"+wlan_id]);	
		}
	}
}

function showBand_MultipleAP(form, wlan_id, band_root, index_id)
{
  var idx=0;
  var band_value=bandIdx[wlan_id];

  if(band_root ==0){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B)", "0", false, false);
}else if(band_root ==1){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);
}else if(band_root ==2){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B)", "0", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);	
 	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B+G)", "2", false, false);
}else if(band_root ==9){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);	
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G+N)", "9", false, false);
}else if(band_root ==10){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B)", "0", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);	
 	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B+G)", "2", false, false);
 	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (G+N)", "9", false, false);
 	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("2.4 GHz (B+G+N)", "10", false, false);
}else if(band_root ==3){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A)", "3", false, false);
}else if(band_root ==7){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
}else if(band_root ==11){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A)", "3", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A+N)", "11", false, false);
}
else if(band_root == 63){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (AC)", "63", false, false);
}
else if(band_root == 71){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (AC)", "63", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N+AC)", "71", false, false);
}
else if(band_root == 75){
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A)", "3", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A+N)", "11", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (AC)", "63", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (N+AC)", "71", false, false);
	form.elements["wl_band_ssid"+index_id].options[idx++] = new Option("5 GHz (A+N+AC)", "75", false, false);
}


form.elements["wl_band_ssid"+index_id].selectedIndex = 0;
 form.elements["wl_band_ssid"+index_id].length = idx;
}


function showBandAP(form, wlan_id)
{
  var idx=0;
  var band_value=bandIdx[wlan_id];
	var Band2G5GSupport=form.elements["Band2G5GSupport"].value;
	var wlBandMode=form.elements["wlBandMode"].value;
	var i;
	var wlan_support_8812e;
	var wlan_support_8192f;
	var wlan_support_ac2g; //ac2g
	if(form.elements["wlan_support_8812e"])
		wlan_support_8812e=form.elements["wlan_support_8812e"].value;
	if(form.elements["wlan_support_8192f"])
		wlan_support_8192f=form.elements["wlan_support_8192f"].value;
	if(form.elements["wlan_support_ac2g"])
		wlan_support_ac2g=form.elements["wlan_support_ac2g"].value;
if(form.name == "wizard")
{
	switch(wlan_id)
	{
		case 0:
			if(form.elements["wlan1_phyband"].value == "5GHz")
				Band2G5GSupport = 2;
			else
				Band2G5GSupport = 1;
			break;
	
		case 1:
			if(form.elements["wlan2_phyband"].value == "5GHz")
				Band2G5GSupport = 2;
			else
				Band2G5GSupport = 1;
			break;
		
	}

	

}

	
	if(Band2G5GSupport == 2 || wlBandMode == 3) // 2:PHYBAND_5G 3:BANDMODESIGNLE
	{
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A)", "3", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A+N)", "11", false, false);

		if( (wlan_support_8812e==1) &&  (wlan_support_8192f!=1))
		{
			form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (AC)", "63", false, false);
			form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (N+AC)", "71", false, false);
			form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A+N+AC)", "75", false, false); //8812
		}
	}
	
	if(Band2G5GSupport == 1 || wlBandMode == 3) // 1:PHYBAND_2G 3:BANDMODESIGNLE
	{
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B)", "0", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (N)", "7", false, false); 
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G)", "2", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (G+N)", "9", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G+N)", "10", false, false);

		//ac2g
		if(wlan_support_ac2g==1)
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G+N+AC)", "74", false, false);
	}


	for(i=0 ; i<idx ; i++)
	{
		if(form.elements["band"+wlan_id].options[i].value == band_value)
		{			
			if(band_value == 7 && wlBandMode == 3)// 2g and 5g has the same band value in N.
			{
				var selectText=form.elements["band"+wlan_id].options[i].text.substr(0,1);
				
				if( (Band2G5GSupport == 2 && selectText == '5') //2:PHYBAND_5G
				||	(Band2G5GSupport == 1 && selectText == '2') //1:PHYBAND_2G
				) 
				{
					form.elements["band"+wlan_id].selectedIndex = i;
					break;					
				}			
			}
			else
			{	
				form.elements["band"+wlan_id].selectedIndex = i;
				break;
			}
		}				
	}	

 form.elements["band"+wlan_id].length = idx;
}
        
     
function showBandClient(form, wlan_id)
{
  var idx=0;
   var band_value=bandIdx[wlan_id];
var Band2G5GSupport=form.elements["Band2G5GSupport"].value;
	var wlBandMode=form.elements["wlBandMode"].value;
	var i;
	var wlan_support_8812e;
	var wlan_support_8192f;
	var wlan_support_ac2g; //ac2g
	if(form.elements["wlan_support_8812e"])
		wlan_support_8812e=form.elements["wlan_support_8812e"].value;
	if(form.elements["wlan_support_8192f"])
		wlan_support_8192f=form.elements["wlan_support_8192f"].value;
	if(form.elements["wlan_support_ac2g"])
		wlan_support_ac2g=form.elements["wlan_support_ac2g"].value;
if(form.name == "wizard")
	{
		switch(wlan_id)
		{
			case 0:
				if(form.elements["wlan1_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
				
			case 1:
				if(form.elements["wlan2_phyband"].value == "5GHz")
					Band2G5GSupport = 2;
				else
					Band2G5GSupport = 1;
				break;
			
		}
		
	}

	
	if(Band2G5GSupport == 2 || wlBandMode == 3) // 2:PHYBAND_5G 3:BANDMODESIGNLE
	{
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A)", "3", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (N)", "7", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A+N)", "11", false, false);

		if( (wlan_support_8812e==1) && (wlan_support_8192f!=1)){
			form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (AC)", "63", false, false);
			form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (N+AC)", "71", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("5 GHz (A+N+AC)", "75", false, false); //8812
		}
	}

	if(Band2G5GSupport == 1 || wlBandMode == 3) // 1:PHYBAND_2G 3:BANDMODESIGNLE
	{
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B)", "0", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (G)", "1", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (N)", "7", false, false); 
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G)", "2", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (G+N)", "9", false, false);
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G+N)", "10", false, false);

		//ac2g
		if(wlan_support_ac2g==1)
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz (B+G+N+AC)", "74", false, false);
	}

	if (wlBandMode == 3) //ac2g
	{
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4GHz + 5 GHz (A+B+G+N)", "14", false, false);

		if(wlan_support_ac2g==1)
		form.elements["band"+wlan_id].options[idx++] = new Option("2.4 GHz + 5 GHz (A+B+G+N+AC)", "78", false, false);
	}

	for(i=0 ; i<idx ; i++)
	{
		if(form.elements["band"+wlan_id].options[i].value == band_value)
		{
			if(band_value == 7 && wlBandMode == 3)// 2g and 5g has the same band value in N.
			{
				var selectText=form.elements["band"+wlan_id].options[i].text.substr(0,1);
				
				if( (Band2G5GSupport == 2 && selectText == '5') //2:PHYBAND_5G
				||	(Band2G5GSupport == 1 && selectText == '2') //1:PHYBAND_2G
				) 
				{
			form.elements["band"+wlan_id].selectedIndex = i;
			break;
		}				
	}	
			else
			{	
				form.elements["band"+wlan_id].selectedIndex = i;
				break;
			}
		}				
	}	

 form.elements["band"+wlan_id].length = idx;
}

function showBand(form, wlan_id)
{
  if (APMode[wlan_id] != 1)
	showBandAP(form, wlan_id);
  else
 	showBandClient(form, wlan_id);
}
function get_by_id(id){
	with(document){
	return getElementById(id);
	}
}
function get_by_name(name){
	with(document){
	return getElementsByName(name);
	}
}
function updateMode(form, wlan_id)
{
	var chan_boundid;
	var controlsidebandid;
	var wlan_wmm1;
	var wlan_wmm2;
	var networktype;
	var mode_idx =form.elements["mode"+wlan_id].selectedIndex;
	var mode_value = form.elements["mode"+wlan_id].options[mode_idx].value; 
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;
		
	if (form.elements["mode"+wlan_id].selectedIndex != 1) {
  		if (APMode[wlan_id] == 1) {
			if (bandIdxAP[wlan_id] < 0){
				bandIdx[wlan_id]=2;	// set B+G as default
			}else{
				bandIdx[wlan_id]=bandIdxAP[wlan_id];
			}
		}  
	}else {
	  	if (APMode[wlan_id] != 1) {
			if (bandIdxClient[wlan_id] < 0) {
	 			if (RFType[wlan_id] == 10)
					bandIdx[wlan_id]=2;	// set B+G as default
				else
					bandIdx[wlan_id]=6;	// set A+B+G as default
			}
			else{
				bandIdx[wlan_id]=bandIdxClient[wlan_id];
			}
		}	
	}
	APMode[wlan_id] =form.elements["mode"+wlan_id].selectedIndex;
	showBand(form, wlan_id);
  	if(form == document.wlanSetup){
  		wlan_wmm1 = form.elements["wlanwmm"+wlan_id];
  		wlan_wmm2 =  get_by_id("wlan_wmm");
	}

	networktype = form.elements["type"+wlan_id];
	if(mode_value !=1) {
		networktype.disabled = true;
	}else {
		networktype.selectedIndex = networkType[wlan_id];
		networktype.disabled = false;		
	}

	if(form.name=="wlanSetup")
	{
		chan_boundid = get_by_id("channel_bounding");
  		controlsidebandid = get_by_id("control_sideband");
	}else
	{
 		chan_boundid = get_by_id("channel_bounding"+wlan_id);
  		controlsidebandid = get_by_id("control_sideband"+wlan_id);  
	}
	if(bandIdx[wlan_id] == 9 || bandIdx[wlan_id] == 10 ||  bandIdx[wlan_id] == 7 || bandIdx[wlan_id] == 11 || bandIdx[wlan_id] == 14 || bandIdx[wlan_id] == 63 || bandIdx[wlan_id] == 71 || bandIdx[wlan_id] == 75){
		chan_boundid.style.display = "";
	 	controlsidebandid.style.display = "";
		 if(form == document.wlanSetup){
			wlan_wmm1.disabled = true;
		 		//wlan_wmm2.disabled = true;
		}
	}else{
		chan_boundid.style.display = "none";
		controlsidebandid.style.display = "none";
	 	 if(form == document.wlanSetup){
	 		wlan_wmm1.disabled = false;
	 		//wlan_wmm2.disabled = false;
	 	}
	 }
	  updateIputState(form, wlan_id);
	 if(form==document.wizard){
		var chan_number_idx=form.elements["chan"+wlan_id].selectedIndex;
		var chan_number= form.elements["chan"+wlan_id].options[chan_number_idx].value;	
		if(chan_number == 0)
			disableTextField(form.elements["controlsideband"+wlan_id]);	
		else{
			if(form.elements["channelbound"+wlan_id].selectedIndex == "0")
	 			disableTextField(form.elements["controlsideband"+wlan_id]);	
			else if(form.elements["channelbound"+wlan_id].selectedIndex == "2") //8812
				disableTextField(form.elements["controlsideband"+wlan_id]);	
	 		else
				enableTextField(form.elements["controlsideband"+wlan_id]);		
		}
	}
}

function updateBand(form, wlan_id)
{
	var band_index= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[band_index].value;
  if (APMode[wlan_id] != 1){
	bandIdxAP[wlan_id] = band_value;
  }else{
	bandIdxClient[wlan_id] =band_value;
  }	

  updateChan(form, wlan_id);
  
}

function updateRepeaterState(form, wlan_id)
{   
  if(!form.elements["wlanDisabled"+wlan_id].checked &&  	
    ((form.elements["mode"+wlan_id].selectedIndex!=1) ||
       ((form.elements["mode"+wlan_id].selectedIndex==1) &&
     	(form.elements["type"+wlan_id].selectedIndex==0))) 
     ){     	
     	  if(form == document.wlanSetup){	
	enableCheckBox(form.elements["repeaterEnabled"+wlan_id]);
	if (form.elements["repeaterEnabled"+wlan_id].checked)
 		enableTextField(form.elements["repeaterSSID"+wlan_id]);
  	else
  		disableTextField(form.elements["repeaterSSID"+wlan_id]);
  }
  }
  else {
  		 if(form == document.wlanSetup){	
			disableCheckBox(form.elements["repeaterEnabled"+wlan_id]);
			disableTextField(form.elements["repeaterSSID"+wlan_id]);
		}
  }
}

function updateStaControlState(form, wlan_mode)
{   
	disableCheckBox(form.elements["staControlEnabled"]);
	form.elements["staControlPrefer"].disable = true;

 	
	if(wlan_mode==0 ||
	   wlan_mode==3 ||
	   wlan_mode==4
	  )
	{     	
		enableCheckBox(form.elements["staControlEnabled"]);
		if (form.elements["staControlEnabled"].checked)
		{
			form.elements["staControlPrefer"].disable = false;
		}	
	}  	
}

function updateType(form, wlan_id)
{
	var mode_selected=0;
	var Type_selected=0;
	var index_channelbound=0;
  updateChan(form, wlan_id);
  updateIputState(form, wlan_id);
  updateRepeaterState(form, wlan_id);
  Type_selected = form.elements["type"+wlan_id].selectedIndex;
  mode_selected=form.elements["mode"+wlan_id].selectedIndex;
  //if client and infrastructure mode
  	if(mode_selected ==1){
		if(Type_selected == 0){
			disableTextField(form.elements["controlsideband"+wlan_id]);
			disableTextField(form.elements["channelbound"+wlan_id]);
		}else{
			enableTextField(form.elements["channelbound"+wlan_id]);
			index_channelbound=form.elements["channelbound"+wlan_id].selectedIndex;
		if(index_channelbound ==0)
			disableTextField(form.elements["controlsideband"+wlan_id]);	
		else if(index_channelbound ==2)
			disableTextField(form.elements["controlsideband"+wlan_id]);	
		else
			enableTextField(form.elements["controlsideband"+wlan_id]);
		}
	}
	
		var chan_number_idx=form.elements["chan"+wlan_id].selectedIndex;
		var chan_number= form.elements["chan"+wlan_id].options[chan_number_idx].value;	
		if(chan_number == 0)
			disableTextField(form.elements["controlsideband"+wlan_id]);	
		else{
			if(form.elements["channelbound"+wlan_id].selectedIndex == "0")
	 			disableTextField(form.elements["controlsideband"+wlan_id]);	
			else if(form.elements["channelbound"+wlan_id].selectedIndex == "2")
				disableTextField(form.elements["controlsideband"+wlan_id]);	
	 		else
				enableTextField(form.elements["controlsideband"+wlan_id]);		
		}
}
function pskFormatChange(form,wlan_id)
{
	if (form.elements["pskFormat"+wlan_id].selectedIndex ==0){
		form.elements["pskValue"+wlan_id].maxLength = "63";
	}
	else{
		form.elements["pskValue"+wlan_id].maxLength = "64";
	}
}
/*==============================================================================*/
/*   wlwpa.htm */
function disableRadioGroup (radioArrOrButton)
{
  if (radioArrOrButton.type && radioArrOrButton.type == "radio") {
 	var radioButton = radioArrOrButton;
 	var radioArray = radioButton.form[radioButton.name];
  }
  else
 	var radioArray = radioArrOrButton;
 	radioArray.disabled = true;
 	for (var b = 0; b < radioArray.length; b++) {
 	if (radioArray[b].checked) {
 		radioArray.checkedElement = radioArray[b];
 		break;
	}
  }
  for (var b = 0; b < radioArray.length; b++) {
 	radioArray[b].disabled = true;
 	radioArray[b].checkedElement = radioArray.checkedElement;
  }
}

function enableRadioGroup (radioArrOrButton)
{
  if (radioArrOrButton.type && radioArrOrButton.type == "radio") {
 	var radioButton = radioArrOrButton;
 	var radioArray = radioButton.form[radioButton.name];
  }
  else
 	var radioArray = radioArrOrButton;

  radioArray.disabled = false;
  radioArray.checkedElement = null;
  for (var b = 0; b < radioArray.length; b++) {
 	radioArray[b].disabled = false;
 	radioArray[b].checkedElement = null;
  }
}

function preserve () { this.checked = this.storeChecked; }
function disableCheckBox (checkBox) {
  if (!checkBox.disabled) {
    checkBox.disabled = true;
    if (!document.all && !document.getElementById) {
      checkBox.storeChecked = checkBox.checked;
      checkBox.oldOnClick = checkBox.onclick;
      checkBox.onclick = preserve;
    }
  }
}

function enableCheckBox (checkBox)
{
  if (checkBox.disabled) {
    checkBox.disabled = false;
    if (!document.all && !document.getElementById)
      checkBox.onclick = checkBox.oldOnClick;
  }
}

function openWindow(url, windowName, wide, high) {
	if (document.all)
		var xMax = screen.width, yMax = screen.height;
	else if (document.layers)
		var xMax = window.outerWidth, yMax = window.outerHeight;
	else
	   var xMax = 640, yMax=500;
	var xOffset = (xMax - wide)/2;
	var yOffset = (yMax - high)/3;

	var settings = 'width='+wide+',height='+high+',screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+', resizable=yes, toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes';
	window.open( url, windowName, settings );
}
function ppp_getDigit(str, num)
{ 
	i=1; 
	// replace the char '/' with character '.'  
	str = str.replace(/[/]/,".");	  
	if ( num != 1 ) 
	{  	
		while (i!=num && str.length!=0) 
		{		
			if ( str.charAt(0) == '.') 
			{			
				i++;		
			}
			str = str.substring(1);  	
		}
		if ( i!=num )  		
			return -1;  
	}  
	for (i=0; i<str.length; i++) 
	{  	
		if ( str.charAt(i) == '.') 
		{
			str = str.substring(0, i);		
			break;
		}  
	}  
	if ( str.length == 0)  	
		return -1;  
	d = parseInt(str, 10); 
	return d;
}

function ppp_checkDigitRange(str, num, min, max)
{	  
	d = ppp_getDigit(str,num);  
	if ( d > max || d < min )      	
		return false;  
	return true;
}

function ppp_validateKey(str)
{   
	for (var i=0; i<str.length; i++) 
	{    
		if ((str.charAt(i) >= '0' && str.charAt(i) <= '9') 
			||(str.charAt(i) == '.' ) || (str.charAt(i) == '/'))			
			continue;	
		return 0;  
	}  
	return 1;
}
function validateKey(str)
{
 
   for (var i=0; i<str.length; i++) {
    if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') ||
    		(str.charAt(i) == '.' ) )
			continue;
	return 0;
  }

  return 1;
}

function getDigit(str, num)
{
  var i=1;
  if ( num != 1 ) {
  	while (i!=num && str.length!=0) {
		if ( str.charAt(0) == '.' ) {
			i++;
		}
		str = str.substring(1);
  	}
  	if ( i!=num )
  		return -1;
  }
  for (i=0; i<str.length; i++) {
	if ( str.charAt(i) == '.' ) {
		str = str.substring(0, i);
		break;
	}		
}
  if ( str.length == 0)
  	return -1;
  var d = parseInt(str, 10);
  return d;
}

function checkDigitRange(str, num, min, max)
{
  var d = getDigit(str,num);
  if ( d > max || d < min )
      	return false;
  return true;
}


function check_wpa_psk(form, wlan_id)
{
	var str = form.elements["pskValue"+wlan_id].value;
	if (form.elements["pskFormat"+wlan_id].selectedIndex==1) {
		if (str.length != 64) {
			alert('Pre-Shared Key value should be 64 characters.');
			form.elements["pskValue"+wlan_id].focus();
			return false;
		}
		takedef = 0;
		if (defPskFormat[wlan_id] == 1 && defPskLen[wlan_id] == 64) {
			for (var i=0; i<64; i++) {
    				if ( str.charAt(i) != '*')
					break;
			}
			if (i == 64 )
				takedef = 1;
  		}
		if (takedef == 0) {
			for (var i=0; i<str.length; i++) {
    				if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') ||
					(str.charAt(i) >= 'a' && str.charAt(i) <= 'f') ||
					(str.charAt(i) >= 'A' && str.charAt(i) <= 'F') )
					continue;
				alert("Invalid Pre-Shared Key value. It should be in hex number (0-9 or a-f).");
				form.elements["pskValue"+wlan_id].focus();
				return false;
  			}
		}
	}
	else {
		if (str.length < 8) {
			alert('Pre-Shared Key value should be set at least 8 characters.');
			form.elements["pskValue"+wlan_id].focus();
			return false;
		}
		if (str.length > 63) {
			alert('Pre-Shared Key value should be less than 64 characters.');
			form.elements["pskValue"+wlan_id].focus();
			return false;
		}
	}


  
  return true;
}

function saveChanges_wpa(form, wlan_id)
{
  method = form.elements["method"+wlan_id] ;
  wpaAuth= form.elements["wpaAuth"+wlan_id] ;

  if (method.selectedIndex>=2 && (wpaAuth.value == "psk" || wpaAuth[1].checked))
	return check_wpa_psk(form, wlan_id);	
 
    if (form.elements["use1x"+wlan_id].value != "OFF" && form.elements["radiusPort"+wlan_id].disabled == false ) {
	if (form.elements["radiusPort"+wlan_id].value=="") {
		alert("RADIUS Server port number cannot be empty! It should be a decimal number between 1-65535.");
		form.elements["radiusPort"+wlan_id].focus();
		return false;
  	}
	if (validateKey(form.elements["radiusPort"+wlan_id].value)==0) {
		alert("Invalid port number of RADIUS Server! It should be a decimal number between 1-65535.");
		form.elements["radiusPort"+wlan_id].focus();
		return false;
	}
        port = parseInt(form.elements["radiusPort"+wlan_id].value, 10);

 	if (port > 65535 || port < 1) {
		alert("Invalid port number of RADIUS Server! It should be a decimal number between 1-65535.");
		form.elements["radiusPort"+wlan_id].focus();
		return false;
  	}

	if ( checkIpAddr(form.elements["radiusIP"+wlan_id], 'Invalid RADIUS Server IP address') == false )
	    return false;
   } 
   	
   
   
   return true;
}
/*==============================================================================*/
/*   tcpiplan.htm  */
function checkMask(str, num)
{
  var d = getDigit(str,num);
  if(num==1)
  {
  	if( !(d==128 || d==192 || d==224 || d==240 || d==248 || d==252 || d==254 || d==255 ))
  		return false;
  }
  else
  {
  	if( !(d==0 || d==128 || d==192 || d==224 || d==240 || d==248 || d==252 || d==254 || d==255 ))
  		return false;
  }
  return true;
}

function checkWholeMask(str)
{
	if(str.length==0)
		return false;
	var d1 = getDigit(str,1);
	var d2 = getDigit(str,2);
	var d3 = getDigit(str,3);
	var d4 = getDigit(str,4);
	if(d1==-1||d2==-1||d3==-1||d4==-1||d1==0 || d1 !=255)
		return false;
	if(d1!=255&&d2!=0)
		return false;
	if(d2!=255&&d3!=0)
		return false;
	if(d3!=255&&d4!=0)
		return false;
	return true;
}

function checkSubnet(ip, mask, client)
{
  ip_d = getDigit(ip, 1);
  mask_d = getDigit(mask, 1);
  client_d = getDigit(client, 1);
  if ( (ip_d & mask_d) != (client_d & mask_d ) )
	return false;

  ip_d = getDigit(ip, 2);
  mask_d = getDigit(mask, 2);
  client_d = getDigit(client, 2);
  if ( (ip_d & mask_d) != (client_d & mask_d ) )
	return false;

  ip_d = getDigit(ip, 3);
  mask_d = getDigit(mask, 3);
  client_d = getDigit(client, 3);
  if ( (ip_d & mask_d) != (client_d & mask_d ) )
	return false;

  ip_d = getDigit(ip, 4);
  mask_d = getDigit(mask, 4);
  client_d = getDigit(client, 4);
  if ( (ip_d & mask_d) != (client_d & mask_d ) )
	return false;

  return true;
}
function checkIPMask(field)
{

  if (field.value=="") {
      	alert("Subnet mask cannot be empty! It should be filled with 4 digit numbers as xxx.xxx.xxx.xxx.");
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
  
  if(field.value=="0.0.0.0"){
  		alert("Subnet mask cannot be 0.0.0.0!");
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
	
  if ( validateKey( field.value ) == 0 ) {
      	alert("Invalid subnet mask value. It should be the decimal number (0-9).");
      	field.value = field.defaultValue;
	field.focus();
	return false;
  }
  if ( !checkMask(field.value,1) ) {
      	alert('Invalid subnet mask in 1st digit.\nIt should be the number of 128, 192, 224, 240, 248, 252 or 254');
	field.value = field.defaultValue;
	field.focus();
	return false;
  }

  if ( !checkMask(field.value,2) ) {
      	alert('Invalid subnet mask in 2nd digit.\nIt should be the number of 0, 128, 192, 224, 240, 248, 252 or 254');
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
  if ( !checkMask(field.value,3) ) {
      	alert('Invalid subnet mask in 3rd digit.\nIt should be the number of 0, 128, 192, 224, 240, 248, 252 or 254');
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
  if ( !checkMask(field.value,4) ) {
      	alert('Invalid subnet mask in 4th digit.\nIt should be the number of 0, 128, 192, 224, 240, 248, 252 or 254');
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
  if(!checkWholeMask(field.value)){
  		alert("Invalid subnet mask.");
	field.value = field.defaultValue;
	field.focus;
	return false;
  }
  
}  
function checkIpAddr(field, msg)
{
  if (field.value=="") {
	alert("IP address cannot be empty! It should be filled with 4 digit numbers as xxx.xxx.xxx.xxx.");
	field.value = field.defaultValue;
	field.focus();
	return false;
  }
   if ( validateKey(field.value) == 0) {
      alert(msg + ' value. It should be the decimal number (0-9).');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }
   if ( !checkDigitRange(field.value,1,1,223) ) {
      alert(msg+' range in 1st digit. It should be 1-223.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }  
   /*if ( !checkDigitRange(field.value,1,1,223) ) {
      alert(msg+' range in 1st digit. It should be 1-223.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }  */
   if(getDigit(field.value,1)==127){
      alert(msg+' range in 1st digit. It should not be 127.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }
   if ( !checkDigitRange(field.value,2,0,255) ) {
      alert(msg + ' range in 2nd digit. It should be 0-255.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }
   if ( !checkDigitRange(field.value,3,0,255) ) {
      alert(msg + ' range in 3rd digit. It should be 0-255.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }
   
   if ( !checkDigitRange(field.value,4,1,254) ) {
      alert(msg + ' range in 4th digit. It should be 1-254.');
      field.value = field.defaultValue;
      field.focus();
      return false;
   }
   
   return true;
}

/*
 * ipv4_to_unsigned_integer
 *	Convert an IPv4 address dotted string to an unsigned integer.
 */
function ipv4_to_unsigned_integer(ipaddr)
{
	var ip = ipaddr + "";
	var got = ip.match (/^\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*$/);
	if (!got) {
		return null;
	}
	var x = 0;
	var q = 0;
	for (var i = 1; i <= 4; i++) {
		q = parseInt(got[i], 10);
		if (q < 0 || q > 255) {
			return null;
		}
		x = x * 256 + q;
	}
	return x;
}

function checkHostIPValid(ipAddr,mask,msg)
{		
	if(!checkIpAddr(ipAddr,msg))	    return false;
	var ip_int = ipv4_to_unsigned_integer(ipAddr.value);
	var mask_int = ipv4_to_unsigned_integer(mask.value);
	var mask_str = mask_int.toString(2);
	//alert(mask_str);
	var index0 = 32 - mask_str.indexOf('0');
	//alert("mask len:"+index0); 	

	var tmp = Math.pow(2,index0) -1;
	//alert("tmp:"+tmp);

	//var tmp_str = tmp.toString(2);
	//alert("tmp_str:"+tmp_str);

	var host = ip_int & tmp;
	//alert("host:"+host);

	if(host == 0 || host == tmp){
		alert(msg);
		return false;
	}
	return true;

}

//check ipv6 addr available
function checkIpv6DigitRange(ipField)
{
	var reg = /[0-9a-fA-F]{4}/;	
	var value=parseInt(ipField.value,16);
	if(value<0 || value>parseInt("ffff",16) || isNaN(value) || !reg.exec(ipField.value))
	{
		//ipField.value = ipField.defaultValue;
      	ipField.focus();
		ipField.select();
		return false;
	}
	return true;
}

function checkIpv6Addr(ipField0,ipField1,ipField2,ipField3,ipField4,ipField5,ipField6,ipField7,prefixField,msg)
{
	if(!checkIpv6DigitRange(ipField0)||!checkIpv6DigitRange(ipField1)||!checkIpv6DigitRange(ipField2)||
		!checkIpv6DigitRange(ipField3)||!checkIpv6DigitRange(ipField4)||!checkIpv6DigitRange(ipField5)||
		!checkIpv6DigitRange(ipField6)||!checkIpv6DigitRange(ipField7))
		{
			alert(msg+' ipv6 address invalid!');
			return false;	
		}
	var reg = /[^0-9]/;
	if(!prefixField) return false;
	if(reg.exec(prefixField.value)||prefixField.value<0 ||prefixField.value>128)
	{
		//prefixField.value = prefixField.defaultValue;
      	prefixField.focus();
		prefixField.select();
		alert(msg+' ipv6 prefix must between 0-128!');
		return false;
	}
	return true;
}
function isIntVal(strVal)
{
	var reg = /^[1-9][0-9]*$/;
	if(strVal!="0" && !reg.exec(strVal))
		return false;
	else
		return true;
}
function checkFieldDigitRange(field,start,end,msg)
{
	var value = parseInt(field.value,10);
	if(value<start || value > end || isNaN(value)||!isIntVal(field.value))
	{
		//field.value = field.defaultValue;
		field.focus();
		field.select();
		alert(msg+' must between '+start+'-'+end);
		return false;
	}
	return true;
}
function checkSetBothOrNone(fieldStart,fieldEnd)
{
	if(fieldStart.value && !fieldEnd.value)
	{
		fieldEnd.focus();
		alert("The field also Should be set");
		return false;
	}
	if(!fieldStart.value && fieldEnd.value)
	{
		fieldStart.focus();
		alert("The field also Should be set");
		return false;
	}
	return true;
}
function checkFieldEmpty(field,msg)
{
	if(!field) return false;
	if(field.value=="")
	{
		//field.value = field.defaultValue;
		field.focus();
		field.select();
		alert(msg);
		return false;
	}
	return true;
}
// add for "All MAC Address field can't reject 00:00:00:00:00:00/ff:ff:ff:ff:ff:ff MAC Address" issue
function checkMacAddr_is_legal(field)
{
	var reg = /[0-9a-fA-F]{12}/;
	if(!field) return false;
	if(!reg.exec(field.value))
	{
		field.focus();
		field.select();
		alert("Invalid MAC address. It should be in hex number (0-9 or a-f).");
		return false;
	}
	return true;
}
function checkMacAddr_is_zero(field)
{
	if(!field) return false;
	if(field.value == "000000000000")
	{
		field.focus();
		field.select();
		alert("Invalid MAC address. It should not be 00:00:00:00:00:00.");
		return false;
	}
	return true;
}
function checkMacAddr_is_broadcast(field)
{
	if(!field) return false;
	if(field.value == "ffffffffffff")
	{
		field.focus();
		field.select();
		alert("Invalid MAC address. It should not be ff:ff:ff:ff:ff:ff.");
		return false;
	}
	return true;
}
function checkMacAddr_is_muticast(field)
{
	//var reg = /01005[eE][0-7][0-9a-fA-F]{5}/;
	//if(reg.exec(field.value))
	if(parseInt(field.value.substr(0, 2), 16) & 0x01 != 0)
	{
		field.focus();
		field.select();
		alert("Invalid MAC address. It should not be multicast mac address between 01:00:5e:00:00:00 and 01:00:5e:7f:ff:ff.");
		return false;
	}
	return true;
}
function checkMacAddr(field,msg)
{
	return (checkMacAddr_is_legal(field) && checkMacAddr_is_zero(field) && checkMacAddr_is_broadcast(field) && checkMacAddr_is_muticast(field));
}
function ppp_checkSubNetFormat(field,msg)
{
	if (field.value=="") 
	{		
		alert("IP address cannot be empty! It should be filled with 4 digit numbers as xxx.xxx.xxx.xxx.");		
		field.value = field.defaultValue;		
		field.focus();		
		return false;   
	}
	if ( ppp_validateKey(field.value) == 0) 
	{      
		alert(msg + ' value. It should be the decimal number (0-9).');      
		field.value = field.defaultValue;      
		field.focus();      
		return false;   
	}   
	if ( !ppp_checkDigitRange(field.value,1,0,255) ) 
	{      
		alert(msg+' range in 1st digit. It should be 0-255.');      
		field.value = field.defaultValue;      
		field.focus();      
		return false;   
	}   
	if ( !ppp_checkDigitRange(field.value,2,0,255) ) 
	{      
		alert(msg + ' range in 2nd digit. It should be 0-255.');      
		field.value = field.defaultValue;      
		field.focus();      
		return false;   
	}   
	if ( !ppp_checkDigitRange(field.value,3,0,255) ) 
	{      
		alert(msg + ' range in 3rd digit. It should be 0-255.');      
		field.value = field.defaultValue;      
		field.focus();      
		return false;   
	}   
	if ( !ppp_checkDigitRange(field.value,4,0,254) ) 
	{      
		alert(msg + ' range in 4th digit. It should be 1-254.');      
		field.value = field.defaultValue;      
		field.focus();     
		return false;   
	}   
	if ( !ppp_checkDigitRange(field.value,5,1,32) )
	{      
		alert(msg + ' range in 5th digit. It should be 1-32.');      
		field.value = field.defaultValue;      
		field.focus();      
		return false;   
	}      
	return true;
}

/////////////////////////////////////////////////////////////////////////////
/*wlwep.htm*/
function validateKey_wep(form, idx, str, len, wlan_id)
{
 if (idx >= 0) {

  if (str.length ==0)
  	return 1;

  if ( str.length != len) {
  	idx++;
	alert('Invalid length of Key ' + idx + ' value. It should be ' + len + ' characters.');
	return 0;
  }
  }
  else {
	if ( str.length != len) {
		alert('Invalid length of WEP Key value. It should be ' + len + ' characters.');
		return 0;
  	}
  }
  if ( str == "*****" ||
       str == "**********" ||
       str == "*************" ||
       str == "**************************" )
       return 1;

  if (form.elements["format"+wlan_id].selectedIndex==0)
       return 1;

  for (var i=0; i<str.length; i++) {
    if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') ||
			(str.charAt(i) >= 'a' && str.charAt(i) <= 'f') ||
			(str.charAt(i) >= 'A' && str.charAt(i) <= 'F') )
			continue;

	alert("Invalid key value. It should be in hex number (0-9 or a-f).");
	return 0;
  }

  return 1;
}

function setDefaultWEPKeyValue(form, wlan_id)
{
  if (form.elements["length"+wlan_id].selectedIndex == 0) {
	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 5;
		form.elements["key"+wlan_id].value = "*****";
	}
	else {
		form.elements["key"+wlan_id].maxLength = 10;
		form.elements["key"+wlan_id].value = "**********";

	}
  }
  else {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 13;
		form.elements["key"+wlan_id].value = "*************";

	}
	else {
		form.elements["key"+wlan_id].maxLength = 26;
		form.elements["key"+wlan_id].value ="**************************";
	}
  }

// for WPS ---------------------------------------->>
//  wps_wep_key_old =  form.elements["key"+wlan_id].value;
//<<----------------------------------------- for WPS
  
}
function saveChanges_wepkey(form, wlan_id)
{
  var keyLen;
  if (form.elements["length"+wlan_id].selectedIndex == 0) {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0)
		keyLen = 5;
	else
		keyLen = 10;
  }
  else {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0)
		keyLen = 13;
	else
		keyLen = 26;
  }

  if (validateKey_wep(form, 0,form.elements["key"+wlan_id].value, keyLen, wlan_id)==0) {
	form.elements["key"+wlan_id].focus();
	return false;
  }



  return true;
}

function setDefaultKeyValue(form, wlan_id)
{
  if (form.elements["length"+wlan_id].selectedIndex == 0) {
	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 5;
		form.elements["key"+wlan_id].value = "*****";
		

	}
	else {
		form.elements["key"+wlan_id].maxLength = 10;
		form.elements["key"+wlan_id].value = "**********";
		

	}
  }
  else {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 13;		
		form.elements["key"+wlan_id].value = "*************";		


	}
	else {
		form.elements["key"+wlan_id].maxLength = 26;
		form.elements["key"+wlan_id].value ="**************************";		
	
	}
  }


  
}


function setPreKeyValue(form, wlan_id)
{
  if (form.elements["length"+wlan_id].selectedIndex == 0) {
	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 5;
		//form.elements["key"+wlan_id].value = "*****";
		

	}
	else {
		form.elements["key"+wlan_id].maxLength = 10;
		//form.elements["key"+wlan_id].value = "**********";
		

	}
  }
  else {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0) {
		form.elements["key"+wlan_id].maxLength = 13;		
		//form.elements["key"+wlan_id].value = "*************";		


	}
	else {
		form.elements["key"+wlan_id].maxLength = 26;
		//form.elements["key"+wlan_id].value ="**************************";		
	
	}
  }


  
}

function saveChanges_wep(form, wlan_id)
{
  var keyLen;
  if (form.elements["length"+wlan_id].selectedIndex == 0) {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0)
		keyLen = 5;
	else
		keyLen = 10;
  }
  else {
  	if ( form.elements["format"+wlan_id].selectedIndex == 0)
		keyLen = 13;
	else
		keyLen = 26;
  }

  if (validateKey_wep(form, 0,form.elements["key"+wlan_id].value, keyLen, wlan_id)==0) {
	form.elements["key"+wlan_id].focus();
	return false;
  }

  


  return true;
}



function lengthClick(form, wlan_id)
{
  updateFormat(form, wlan_id);
}

///////////////////////////////////////////////////////////////////////
//ntp.htm and wizard-ntp.htm
var ntp_zone_index=4;

function ntp_entry(name, value) { 
	this.name = name ;
	this.value = value ;
} 

var ntp_zone_array=new Array(65);
ntp_zone_array[0]=new ntp_entry("(GMT-12:00)Eniwetok, Kwajalein","12 1");
ntp_zone_array[1]=new ntp_entry("(GMT-11:00)Midway Island, Samoa","11 1");
ntp_zone_array[2]=new ntp_entry("(GMT-10:00)Hawaii", "10 1");
ntp_zone_array[3]=new ntp_entry("(GMT-09:00)Alaska", "9 1");
ntp_zone_array[4]=new ntp_entry("(GMT-08:00)Pacific Time (US & Canada); Tijuana", "8 1");
ntp_zone_array[5]=new ntp_entry("(GMT-07:00)Arizona", "7 1");
ntp_zone_array[6]=new ntp_entry("(GMT-07:00)Mountain Time (US & Canada)", "7 2");
ntp_zone_array[7]=new ntp_entry("(GMT-06:00)Central Time (US & Canada)", "6 1");
ntp_zone_array[8]=new ntp_entry("(GMT-06:00)Mexico City, Tegucigalpa", "6 2");
ntp_zone_array[9]=new ntp_entry("(GMT-06:00)Saskatchewan", "6 3");
ntp_zone_array[10]=new ntp_entry("(GMT-05:00)Bogota, Lima, Quito", "5 1");
ntp_zone_array[11]=new ntp_entry("(GMT-05:00)Eastern Time (US & Canada)", "5 2");
ntp_zone_array[12]=new ntp_entry("(GMT-05:00)Indiana (East)", "5 3");
ntp_zone_array[13]=new ntp_entry("(GMT-04:00)Atlantic Time (Canada)", "4 1");
ntp_zone_array[14]=new ntp_entry("(GMT-04:00)Caracas, La Paz", "4 2");
ntp_zone_array[15]=new ntp_entry("(GMT-04:00)Santiago", "4 3");
ntp_zone_array[16]=new ntp_entry("(GMT-03:30)Newfoundland", "3 1");
ntp_zone_array[17]=new ntp_entry("(GMT-03:00)Brasilia", "3 2");
ntp_zone_array[18]=new ntp_entry("(GMT-03:00)Buenos Aires, Georgetown", "3 3");
ntp_zone_array[19]=new ntp_entry("(GMT-02:00)Mid-Atlantic", "2 1");
ntp_zone_array[20]=new ntp_entry("(GMT-01:00)Azores, Cape Verde Is.", "1 1");
ntp_zone_array[21]=new ntp_entry("(GMT)Casablanca, Monrovia", "0 1");
ntp_zone_array[22]=new ntp_entry("(GMT)Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London", "0 2");
ntp_zone_array[23]=new ntp_entry("(GMT+01:00)Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna", "-1 1");
ntp_zone_array[24]=new ntp_entry("(GMT+01:00)Belgrade, Bratislava, Budapest, Ljubljana, Prague", "-1 2");
ntp_zone_array[25]=new ntp_entry("(GMT+01:00)Barcelona, Madrid", "-1 3");
ntp_zone_array[26]=new ntp_entry("(GMT+01:00)Brussels, Copenhagen, Madrid, Paris, Vilnius", "-1 4");
ntp_zone_array[27]=new ntp_entry("(GMT+01:00)Paris", "-1 5");
ntp_zone_array[28]=new ntp_entry("(GMT+01:00)Sarajevo, Skopje, Sofija, Warsaw, Zagreb", "-1 6");
ntp_zone_array[29]=new ntp_entry("(GMT+02:00)Athens, Istanbul, Minsk", "-2 1");
ntp_zone_array[30]=new ntp_entry("(GMT+02:00)Bucharest", "-2 2");
ntp_zone_array[31]=new ntp_entry("(GMT+02:00)Cairo", "-2 3");
ntp_zone_array[32]=new ntp_entry("(GMT+02:00)Harare, Pretoria", "-2 4");
ntp_zone_array[33]=new ntp_entry("(GMT+02:00)Helsinki, Riga, Tallinn", "-2 5");
ntp_zone_array[34]=new ntp_entry("(GMT+02:00)Jerusalem", "-2 6");
ntp_zone_array[35]=new ntp_entry("(GMT+03:00)Baghdad, Kuwait, Riyadh", "-3 1");
ntp_zone_array[36]=new ntp_entry("(GMT+03:00)Moscow, St. Petersburg, Volgograd", "-3 2");
ntp_zone_array[37]=new ntp_entry("(GMT+03:00)Mairobi", "-3 3");
ntp_zone_array[38]=new ntp_entry("(GMT+03:30)Tehran", "-3 4");
ntp_zone_array[39]=new ntp_entry("(GMT+04:00)Abu Dhabi, Muscat", "-4 1");
ntp_zone_array[40]=new ntp_entry("(GMT+04:00)Baku, Tbilisi", "-4 2");
ntp_zone_array[41]=new ntp_entry("(GMT+04:30)Kabul", "-4 3");
ntp_zone_array[42]=new ntp_entry("(GMT+05:00)Ekaterinburg", "-5 1");
ntp_zone_array[43]=new ntp_entry("(GMT+05:00)Islamabad, Karachi, Tashkent", "-5 2");
ntp_zone_array[44]=new ntp_entry("(GMT+05:30)Bombay, Calcutta, Madras, New Delhi", "-5 3");
ntp_zone_array[45]=new ntp_entry("(GMT+06:00)Astana, Almaty, Dhaka", "-6 1");
ntp_zone_array[46]=new ntp_entry("(GMT+06:00)Colombo", "-6 2");
ntp_zone_array[47]=new ntp_entry("(GMT+07:00)Bangkok, Hanoi, Jakarta", "-7 1");
ntp_zone_array[48]=new ntp_entry("(GMT+08:00)Beijing, Chongqing, Hong Kong, Urumqi", "-8 1");
ntp_zone_array[49]=new ntp_entry("(GMT+08:00)Perth", "-8 2");
ntp_zone_array[50]=new ntp_entry("(GMT+08:00)Singapore", "-8 3");
ntp_zone_array[51]=new ntp_entry("(GMT+08:00)Taipei", "-8 4");
ntp_zone_array[52]=new ntp_entry("(GMT+09:00)Osaka, Sapporo, Tokyo", "-9 1");
ntp_zone_array[53]=new ntp_entry("(GMT+09:00)Seoul", "-9 2");
ntp_zone_array[54]=new ntp_entry("(GMT+09:00)Yakutsk", "-9 3");
ntp_zone_array[55]=new ntp_entry("(GMT+09:30)Adelaide", "-9 4");
ntp_zone_array[56]=new ntp_entry("(GMT+09:30)Darwin", "-9 5");
ntp_zone_array[57]=new ntp_entry("(GMT+10:00)Brisbane", "-10 1");
ntp_zone_array[58]=new ntp_entry("(GMT+10:00)Canberra, Melbourne, Sydney", "-10 2");
ntp_zone_array[59]=new ntp_entry("(GMT+10:00)Guam, Port Moresby", "-10 3");
ntp_zone_array[60]=new ntp_entry("(GMT+10:00)Hobart", "-10 4");
ntp_zone_array[61]=new ntp_entry("(GMT+10:00)Vladivostok", "-10 5");
ntp_zone_array[62]=new ntp_entry("(GMT+11:00)Magadan, Solomon Is., New Caledonia", "-11 1");
ntp_zone_array[63]=new ntp_entry("(GMT+12:00)Auckland, Wllington", "-12 1");
ntp_zone_array[64]=new ntp_entry("(GMT+12:00)Fiji, Kamchatka, Marshall Is.", "-12 2");

function setTimeZone(field, value){
    field.selectedIndex = 4 ;
    for(i=0 ;i < field.options.length ; i++){
    	if(field.options[i].value == value){
		field.options[i].selected = true;
		break;
}
}

}

function setNtpServer(field, ntpServer){
    field.selectedIndex = 0 ;
    for(i=0 ;i < field.options.length ; i++){
    	if(field.options[i].value == ntpServer){
		field.options[i].selected = true;
		break;
	}
    }
}
function updateState_ntp(form)
{
	if(form.enabled.checked){
		enableTextField(form.timeZone);
		enableTextField(form.ntpServerIp1);
		enableCheckBox (form.dlenabled);
		if(form.ntpServerIp2 != null)
			enableTextField(form.ntpServerIp2);
	}
	else{
		disableTextField(form.timeZone);
		disableTextField(form.ntpServerIp1);
		disableCheckBox (form.dlenabled);
		if(form.ntpServerIp2 != null)
			disableTextField(form.ntpServerIp2);
	}
}

function saveChanges_ntp(form)
{
	if(form.ntpServerIp2.value != ""){
		if ( checkIpAddr(form.ntpServerIp2, 'Invalid IP address') == false )
		    return false;
	}
	else
		form.ntpServerIp2.value = "0.0.0.0" ;
	return true;
}
function getRefToDivNest(divID, oDoc) 
{
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
	if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
	for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
		y = getRefToDivNest(divID,oDoc.layers[x].document); }
	return y; } }
  if( document.getElementById ) { return document.getElementById(divID); }
  if( document.all ) { return document.all[divID]; }
  return document[divID];
}

function progressBar( oBt, oBc, oBg, oBa, oWi, oHi, oDr ) 
{
  MWJ_progBar++; this.id = 'MWJ_progBar' + MWJ_progBar; this.dir = oDr; this.width = oWi; this.height = oHi; this.amt = 0;
  //write the bar as a layer in an ilayer in two tables giving the border
  document.write( '<span id = "progress_div" class = "off" > <table border="0" cellspacing="0" cellpadding="'+oBt+'">'+
	'<tr><td>Please wait...</td></tr><tr><td bgcolor="'+oBc+'">'+
		'<table border="0" cellspacing="0" cellpadding="0"><tr><td height="'+oHi+'" width="'+oWi+'" bgcolor="'+oBg+'">' );
  if( document.layers ) {
	document.write( '<ilayer height="'+oHi+'" width="'+oWi+'"><layer bgcolor="'+oBa+'" name="MWJ_progBar'+MWJ_progBar+'"></layer></ilayer>' );
  } else {
	document.write( '<div style="position:relative;top:0px;left:0px;height:'+oHi+'px;width:'+oWi+';">'+
			'<div style="position:absolute;top:0px;left:0px;height:0px;width:0;font-size:1px;background-color:'+oBa+';" id="MWJ_progBar'+MWJ_progBar+'"></div></div>' );
  }
  document.write( '</td></tr></table></td></tr></table></span>\n' );
  this.setBar = resetBar; //doing this inline causes unexpected bugs in early NS4
  this.setCol = setColour;
}

function resetBar( a, b ) 
{
  //work out the required size and use various methods to enforce it
  this.amt = ( typeof( b ) == 'undefined' ) ? a : b ? ( this.amt + a ) : ( this.amt - a );
  if( isNaN( this.amt ) ) { this.amt = 0; } if( this.amt > 1 ) { this.amt = 1; } if( this.amt < 0 ) { this.amt = 0; }
  var theWidth = Math.round( this.width * ( ( this.dir % 2 ) ? this.amt : 1 ) );
  var theHeight = Math.round( this.height * ( ( this.dir % 2 ) ? 1 : this.amt ) );
  var theDiv = getRefToDivNest( this.id ); if( !theDiv ) { window.status = 'Progress: ' + Math.round( 100 * this.amt ) + '%'; return; }
  if( theDiv.style ) { theDiv = theDiv.style; theDiv.clip = 'rect(0px '+theWidth+'px '+theHeight+'px 0px)'; }
  var oPix = document.childNodes ? 'px' : 0;
  theDiv.width = theWidth + oPix; theDiv.pixelWidth = theWidth; theDiv.height = theHeight + oPix; theDiv.pixelHeight = theHeight;
  if( theDiv.resizeTo ) { theDiv.resizeTo( theWidth, theHeight ); }
  theDiv.left = ( ( this.dir != 3 ) ? 0 : this.width - theWidth ) + oPix; theDiv.top = ( ( this.dir != 4 ) ? 0 : this.height - theHeight ) + oPix;
}

function setColour( a ) 
{
  //change all the different colour styles
  var theDiv = getRefToDivNest( this.id ); if( theDiv.style ) { theDiv = theDiv.style; }
  theDiv.bgColor = a; theDiv.backgroundColor = a; theDiv.background = a;
}


function showcontrolsideband_updated(form, band, wlan_id, rf_num, index)
{
  var idx=0;
  var i;
  var controlsideband_str;

  if((band==7 && index==1) || band ==11 || band ==63 || band ==71 || band ==75)
  {
	 form.elements["controlsideband"+wlan_id].options[idx++] = new Option("Auto", "0", false, false);
	 form.elements["controlsideband"+wlan_id].options[idx++] = new Option("Auto", "1", false, false);
  }
  else
  {
	 form.elements["controlsideband"+wlan_id].options[idx++] = new Option("Upper", "0", false, false);
	 form.elements["controlsideband"+wlan_id].options[idx++] = new Option("Lower", "1", false, false);
  }
  	
  form.elements["controlsideband"+wlan_id].length = idx;
  form.elements["controlsideband"+wlan_id].selectedIndex = 0;
 
	 for (i=0; i<idx; i++) {
	 	controlsideband_str = form.elements["controlsideband"+wlan_id].options[i].value;
	 if(wlan_controlsideband[wlan_id]  == controlsideband_str)
	 	form.elements["controlsideband"+wlan_id].selectedIndex = i;
	 }
}

function showchannelbound_updated(form, band, wlan_id, rf_num)
{
  var idx=0;
  var i;
  var channelbound_str;

 form.elements["channelbound"+wlan_id].options[idx++] = new Option("20MHz", "0", false, false);
 form.elements["channelbound"+wlan_id].options[idx++] = new Option("40MHz", "1", false, false);
 
 if(band == 75 || band ==71|| band ==63){ //ac2g
 form.elements["channelbound"+wlan_id].options[idx++] = new Option("80MHz", "2", false, false);
 }
 
 form.elements["channelbound"+wlan_id].length = idx;
 
 for (i=0; i<idx; i++) {
 	channelbound_str = form.elements["channelbound"+wlan_id].options[i].value;
 if(wlan_channelbound[wlan_id]  == channelbound_str)
 	form.elements["channelbound"+wlan_id].selectedIndex = i;
 }


}

function showtxrate_updated(form, band, wlan_id, rf_num)
{
  	var idx=0;
  	var i;
  	var txrate_str;
  	var channel_width_20M_flag = (form.elements["channelbound" + wlan_idx].selectedIndex == 0) ? 1 : 0;
	var channel_width_40M_flag = (form.elements["channelbound" + wlan_idx].selectedIndex == 1) ? 1 : 0;
	var channel_width_80M_flag = (form.elements["channelbound" + wlan_idx].selectedIndex == 2) ? 1 : 0;

 	form.elements["txRate"+wlan_id].options[idx++] = new Option("Auto", "0", false, false);
 
 	if(band == 0 || band ==2 || band ==10)
	{
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("1M", "1", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("2M", "2", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("5.5M", "3", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("11M", "4", false, false);
	}
 	if(band ==9 || band ==10 || band ==1 || band ==2 || band == 11 || band == 3 || band==75 || band == 74)
	{
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("6M", "5", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("9M", "6", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("12M", "7", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("18M", "8", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("24M", "9", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("36M", "10", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("48M", "11", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("54M", "12", false, false);
	}
 	if(band ==9 || band ==10 || band == 7 || band == 11 || band ==71|| band ==75 || band == 74)
	{
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS0", "13", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS1", "14", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS2", "15", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS3", "16", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS4", "17", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS5", "18", false, false);
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS6", "19", false, false); 
 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS7", "20", false, false);
 		if (rf_num >=2)//8812_1t1r || band ==75) {
		{ 
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS8", "21", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS9", "22", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS10", "23", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS11", "24", false, false);
			form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS12", "25", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS13", "26", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS14", "27", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS15", "28", false, false);
 		}
 		if(rf_num >= 3)
		{
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS16", "29", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS17", "30", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS18", "31", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS19", "32", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS20", "33", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS21", "34", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS22", "35", false, false);
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("MCS23", "36", false, false);
 		}
	}

	if(band ==63 || band ==71 || band == 75 || band == 74)
	{
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS0", "37", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS1", "38", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS2", "39", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS3", "40", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS4", "41", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS5", "42", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS6", "43", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS7", "44", false, false);
	 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS8", "45", false, false);
	 	if(!channel_width_20M_flag)
		{
	 		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS1-MCS9", "46", false, false);
		}
	 
	 	if(rf_num >=2)//8812_1t1r
		{
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS0", "47", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS1", "48", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS2", "49", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS3", "50", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS4", "51", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS5", "52", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS6", "53", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS7", "54", false, false);
		 	form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS8", "55", false, false);
			if(!channel_width_20M_flag)
			{
		 		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS2-MCS9", "56", false, false);
		 	}
	 	}
	}
	if(rf_num >= 3 &&( band == 75 || band == 74) && (channel_width_20M_flag == 1 || channel_width_40M_flag == 1))
	{
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS0", "57", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS1", "58", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS2", "59", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS3", "60", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS4", "61", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS5", "62", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS6", "63", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS7", "64", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS8", "65", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS9", "66", false, false);
	}
	if(rf_num >= 3 && (band == 75 || band == 74) && channel_width_80M_flag == 1)
	{
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS0", "57", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS1", "58", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS2", "59", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS3", "60", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS4", "61", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS5", "62", false, false);
		//form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS6", "63", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS7", "64", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS8", "65", false, false);
		form.elements["txRate"+wlan_id].options[idx++] = new Option("NSS3-MCS9", "66", false, false);
	}
 	form.elements["txRate"+wlan_id].length = idx;
 
 	for (i=0; i<idx; i++) 
	{
 		txrate_str = form.elements["txRate"+wlan_id].options[i].value;
 		if(wlan_txrate[wlan_id]  == txrate_str)
 			form.elements["txRate"+wlan_id].selectedIndex = i;
 	}
}
var MultiLanguage = 0;
function mavis_write(string_name)
{
	document.write(eval("string_name[" + MultiLanguage + "]"));
}

function update_controlsideband(form, wlan_id)
{
	var index=form.elements["channelbound"+wlan_id].selectedIndex;
	var wlan_support_8812e=form.elements["wlan_support_8812e"].value;
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;
	
//ac2g
	if(index ==0 || index==2 || (wlan_support_8812e==1 && (band_value==11 || band_value==63 || band_value==71 || band_value==75 ||(band_value==7 && idx_value==1)))) //8812
		disableTextField(form.elements["controlsideband"+wlan_id]);	
	else
		enableTextField(form.elements["controlsideband"+wlan_id]);
	updateChan_channebound(form, wlan_id);
	var chan_number_idx=form.elements["chan"+wlan_id].selectedIndex;
	var chan_number_value=form.elements["chan"+wlan_id].value;	
	
	if(chan_number_idx==0 && chan_number_value==0)
		disableTextField(form.elements["controlsideband"+wlan_id]);	

}

function updateChan_selectedIndex(form, wlan_id)
{
	var chan_number_idx=form.elements["chan"+wlan_id].selectedIndex;
	var chan_number= form.elements["chan"+wlan_id].options[chan_number_idx].value;
	var wlan_support_8812e=form.elements["wlan_support_8812e"].value;
	var idx_value= form.elements["band"+wlan_id].selectedIndex;
	var band_value= form.elements["band"+wlan_id].options[idx_value].value;

	
	wlan_channel[wlan_id] = chan_number;
	if(chan_number == 0)
		disableTextField(form.elements["controlsideband"+wlan_id]);	
	else{
		if(form.elements["channelbound"+wlan_id].selectedIndex == "0")
 			disableTextField(form.elements["controlsideband"+wlan_id]);	
		else if(form.elements["channelbound"+wlan_id].selectedIndex == "2")
 			disableTextField(form.elements["controlsideband"+wlan_id]);
 		else
			enableTextField(form.elements["controlsideband"+wlan_id]);		
		}
	
//ac2g	
	if( ((wlan_support_8812e==1) && (chan_number > 14))) //8812
		disableTextField(form.elements["controlsideband"+wlan_id]);	
}

function isReservedIpAddress(ip)
{
	var num = 0;
	var ipaddr;

	ipaddr = ip.split(".");
	num = Number(ipaddr[0]);

	if (num == 127)
		return true;
	else
		return false;
	
}
