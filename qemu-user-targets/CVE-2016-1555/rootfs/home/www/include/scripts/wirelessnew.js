	var	AutoCellStatus = 0; //[TRI_AUTOCELL_STATUS];
	var	CountryCode = 840; // USA
	var CountryCodeAlpha = "us";

    /*var channel11a	=	new Array(new Array("Auto","36 / 5.180GHz","40 / 5.200GHz","42 / 5.210GHz","44 / 5.220GHz","48 / 5.240GHz","50 / 5.250GHz","52 / 5.260GHz","56 / 5.280GHz","58 / 5.290GHz","60 / 5.300GHz","64 / 5.320GHz","149 / 5.745GHz","152 / 5.760GHz","153 / 5.765GHz","157 / 5.785GHz","160 / 5.800GHz","161 / 5.805GHz","165 / 5.825GHz"),new Array("Best","6 Mbps","9 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps"));
    var channel11b	=	new Array(new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),new Array("Best","1 Mbps","2 Mbps","5.5 Mbps","11 Mbps"));
    var channel11g	=	new Array(new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),new Array("Best","1 Mbps","2 Mbps","5.5 Mbps","6 Mbps","9 Mbps","11 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps"));
    var channel11na=	new Array(new Array("Auto","36 / 5.180GHz","40 / 5.200GHz","42 / 5.210GHz","44 / 5.220GHz","48 / 5.240GHz","50 / 5.250GHz","52 / 5.260GHz","56 / 5.280GHz","58 / 5.290GHz","60 / 5.300GHz","64 / 5.320GHz","149 / 5.745GHz","152 / 5.760GHz","153 / 5.765GHz","157 / 5.785GHz","160 / 5.800GHz","161 / 5.805GHz","165 / 5.825GHz"),new Array("Best","6 Mbps","9 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps"));
    var channel11ng=	new Array(new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),new Array("Best","1 Mbps","2 Mbps","5.5 Mbps","6 Mbps","9 Mbps","11 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps"));*/


	var local_ssid;
	var remote_ssid;
	var str;
//	var gChnlMatrix = new CountryMap();
	var curChnl, curRate;

	var getDataRateInfo=function(temp, bandwidth, gi)
	{

		// var chnlList11=new Array();
		var chnlList = new Array();
		chnlList['3']= new Array(
				   new Array("Auto","36 / 5.180GHz","40 / 5.200GHz","42 / 5.210GHz","44 / 5.220GHz","48 / 5.240GHz","50 / 5.250GHz","52 / 5.260GHz","56 / 5.280GHz","58 / 5.290GHz","60 / 5.300GHz","64 / 5.320GHz","149 / 5.745GHz","152 / 5.760GHz","153 / 5.765GHz","157 / 5.785GHz","160 / 5.800GHz","161 / 5.805GHz","165 / 5.825GHz"),
				   new Array("Best","6 Mbps","9 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps")
				   );

		chnlList['0']= new Array(
				   new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),
				   new Array("Best","1 Mbps","2 Mbps","5.5 Mbps","11 Mbps")
				   );

		chnlList['1']= new Array(
				   new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),
				   new Array("Best","1 Mbps","2 Mbps","5.5 Mbps","6 Mbps","9 Mbps","11 Mbps","12 Mbps","18 Mbps","24 Mbps","36 Mbps","48 Mbps","54 Mbps")
				   );

		chnlList['4']= new Array(
				   new Array("Auto","36 / 5.180GHz","40 / 5.200GHz","42 / 5.210GHz","44 / 5.220GHz","48 / 5.240GHz","50 / 5.250GHz","52 / 5.260GHz","56 / 5.280GHz","58 / 5.290GHz","60 / 5.300GHz","64 / 5.320GHz","149 / 5.745GHz","152 / 5.760GHz","153 / 5.765GHz","157 / 5.785GHz","160 / 5.800GHz","161 / 5.805GHz","165 / 5.825GHz"),
				   new Array(
							 new Array(
		//for 20MHz and 400ms GI
										new Array("Best", "7.2 Mbps", "14.4 Mbps", "21.7 Mbps", "28.9 Mbps", "43.3 Mbps", "57.8 Mbps", "65 Mbps", "72.2 Mbps", "14.44 Mbps", "28.88 Mbps", "43.33 Mbps", "57.77 Mbps", "86.66 Mbps", "115.56 Mbps", "130 Mbps", "144.44 Mbps"),
		//for 20MHz and 800ms GI
										new Array("Best", "6.5 Mbps", "13 Mbps", "19.5 Mbps", "26 Mbps", "39 Mbps", "52 Mbps", "58.5 Mbps", "65 Mbps", "13 Mbps", "26 Mbps", "39 Mbps", "52 Mbps", "78 Mbps", "104 Mbps", "117 Mbps", "130 Mbps")
									),
							 new Array(
		//for 40MHz and 400ms GI
										new Array("Best", "15 Mbps", "30 Mbps", "45 Mbps", "60 Mbps", "90 Mbps", "120 Mbps", "135 Mbps", "150 Mbps", "30 Mbps", "60 Mbps", "90 Mbps", "120 Mbps", "180 Mbps", "240 Mbps", "270 Mbps", "300 Mbps"),
		//for 40MHz and 800ms GI
										new Array("Best", "13.5 Mbps", "27 Mbps", "40.5 Mbps", "54 Mbps", "81 Mbps", "108 Mbps", "121.5 Mbps", "135 Mbps", "27 Mbps", "54 Mbps", "81 Mbps", "108 Mbps", "162 Mbps", "216 Mbps", "243 Mbps", "270 Mbps")
									)
							)
				   );

		chnlList['2']= new Array(
				   new Array("Auto","1 / 2.412GHz","2 / 2.417GHz","3 / 2.422GHz","4 / 2.427GHz","5 / 2.432GHz","6 / 2.437GHz","7 / 2.442GHz","8 / 2.447GHz","9 / 2.452GHz","10 / 2.457GHz","11 / 2.462GHz"),
				   new Array(
							 new Array(
		//for 20MHz and 400ms GI
										new Array("Best", "7.2 Mbps", "14.4 Mbps", "21.7 Mbps", "28.9 Mbps", "43.3 Mbps", "57.8 Mbps", "65 Mbps", "72.2 Mbps", "14.44 Mbps", "28.88 Mbps", "43.33 Mbps", "57.77 Mbps", "86.66 Mbps", "115.56 Mbps", "130 Mbps", "144.44 Mbps"),
		//for 20MHz and 800ms GI
										new Array("Best", "6.5 Mbps", "13 Mbps", "19.5 Mbps", "26 Mbps", "39 Mbps", "52 Mbps", "58.5 Mbps", "65 Mbps", "13 Mbps", "26 Mbps", "39 Mbps", "52 Mbps", "78 Mbps", "104 Mbps", "117 Mbps", "130 Mbps")
									),
							 new Array(
		//for 40MHz and 400ms GI
										new Array("Best", "15 Mbps", "30 Mbps", "45 Mbps", "60 Mbps", "90 Mbps", "120 Mbps", "135 Mbps", "150 Mbps", "30 Mbps", "60 Mbps", "90 Mbps", "120 Mbps", "180 Mbps", "240 Mbps", "270 Mbps", "300 Mbps"),
		//for 40MHz and 800ms GI
										new Array("Best", "13.5 Mbps", "27 Mbps", "40.5 Mbps", "54 Mbps", "81 Mbps", "108 Mbps", "121.5 Mbps", "135 Mbps", "27 Mbps", "54 Mbps", "81 Mbps", "108 Mbps", "162 Mbps", "216 Mbps", "243 Mbps", "270 Mbps")
									)
							)
				   );

               if(config.WN604.status){
                    for(i = 16; i>=9; i--){
                        chnlList['4']['1']['0']['0'].splice(i, 1);
                        chnlList['4']['1']['1']['0'].splice(i, 1);
                        chnlList['4']['1']['1']['1'].splice(i, 1);

                        chnlList['2']['1']['0']['0'].splice(i, 1);
                        chnlList['2']['1']['0']['1'].splice(i, 1);

                        chnlList['2']['1']['1']['0'].splice(i, 1);
                        chnlList['2']['1']['1']['1'].splice(i, 1);
                    }
                    for(i = 16; i>=8; i--){
                        chnlList['4']['1']['0']['1'].splice(i, 1);
                    }
                }


		if (temp.length != 0) {
			var b;
			var g;
			if (temp == '2' || temp == '4') {
				if (bandwidth!='0')
					b='1';
				else
					b='0';
				if (gi=='800')
					g='1';
				else
					g='0';
				return (chnlList[temp][1][b][g]);
			}
			else
				return (chnlList[temp][1]);
		}
		else
			return '';
	}

	var getChannelInfo = function(temp, bandwidth)
	{
		//'2'=>'Dynamic 20/40 MHz','0'=>'20 MHz','1'=>'40 MHz'
		switch(temp) {
			case '0':
                return ChannelList_0;
                break;
			case '1':
				return ChannelList_1;
				break;
			case '3':
				return ChannelList_3;
				break;
			case '2':
				eval ('var x = ChannelList_0_'+((bandwidth=='0')?'20':'40')+';');
				return x;
			case '4':
				eval ('var x = ChannelList_1_'+((bandwidth=='0')?'20':'40')+';');
				return x;
		}
	}

	var  DispChannelList=function(block,str)
	{
        block = String(block);
        str = String(str);
		updateChannelInfo(block, str);
		DispRatelist(block,str);
		//setActiveRadioStatus(str,block);
	}

	var updateChannelInfo=function(block,tmp) {
		if (tmp == '4' || tmp == '2')
		{
			var tmpChannelList=eval("document.dataForm.ChannelList"+block);
			curChnl=tmpChannelList.value;

			do {
				tmpChannelList.options[0] = null;
			} while (tmpChannelList.length > 0);

			var bandwidth = $('Bandwidth'+block).value;

			var channelArr =	getChannelInfo(tmp, bandwidth);
			var x = 0;
			//alert($H(channelArr).inspect())
			$H(channelArr).each(function(item, key)
			{
				tmpChannelList.options[x] = new Option(item.value,item.key);
				if (item.key == curChnl) {
					tmpChannelList.options[x].selected = true;
				}
				x++;
			});
		}
		else
		{
			var tmpChannelList=eval("document.dataForm.ChannelList"+block);
			curChnl=tmpChannelList.value;

			do {
				tmpChannelList.options[0] = null;
			} while (tmpChannelList.length > 0);

			var channelArr =	getChannelInfo(tmp);

			var x = 0;
			//alert($H(channelArr).inspect())
			$H(channelArr).each(function(item, key)
			{
				tmpChannelList.options[x] = new Option(item.value,item.key);
				if (item.key == curChnl) {
					tmpChannelList.options[x].selected = true;
				}
				x++;
			});
		}
	}

	var setActiveRadioStatus = function(tmp,block)
	{
        if (block == 2) {
            var id = 'chkRadio1';
            if (config.DUAL_CONCURRENT.status) {
        		var activeMode=$('activeMode1').value;
                var modeName = 'WirelessMode2';
            }
            else {
           		var activeMode=$('activeMode').value;
                var modeName = 'WirelessMode1';
            }
        }
        else {
            var id = 'chkRadio0';
            if (config.DUAL_CONCURRENT.status) {
        		var activeMode=$('activeMode0').value;
            }
            else {
           		var activeMode=$('activeMode').value;
            }
            var modeName = 'WirelessMode1';
        }
		if ($('cb_'+id)) {
			if (activeMode==tmp) {
				$('cb_'+id).checked=true;
			}
			else {
				$('cb_'+id).checked=false;
			}
			setActiveMode($('cb_'+id),modeName, false);
		}
	}


	var DispRatelist=function(block,tmp)
	{
		if (tmp == '4' || tmp == '2')
		{
			var tmpDatarateList = eval("document.dataForm.MCSrateList"+block);
			var curRate=tmpDatarateList.value;
			do {
				tmpDatarateList.options[0] = null;
			} while (tmpDatarateList.length > 0);

			var bandwidth = $('Bandwidth'+block).value;
			var gi = $('GI'+block).value;

			var ratearr =	getDataRateInfo(tmp, bandwidth, gi);
			for (cnt in ratearr)
			{
				if (typeof(ratearr[cnt])!='function') {
					if (cnt==0) {
						var rateStr = ratearr[cnt];
						var val = '99';
					}
					else {
						var rateStr = (cnt-1)+' / '+ratearr[cnt];
						var val = (cnt-1);
					}
					tmpDatarateList.options[cnt] = new Option(rateStr,val);
					if (val == curRate) {
						tmpDatarateList.options[cnt].selected = true;
					}
				}
			}
		}
		else
		{
			var tmpDatarateList = eval("document.dataForm.DatarateList"+block);
			var curRate=tmpDatarateList.value;
			do {
				tmpDatarateList.options[0] = null;
			} while (tmpDatarateList.length > 0);

			var ratearr =	getDataRateInfo(tmp);
			for (cnt in ratearr) {
				if (typeof(ratearr[cnt])!='function')  {
					if (cnt==0) {
						var val = '0';
					}
					else {
						var val = String(Number(ratearr[cnt].split(' ')[0])*2);
					}
					tmpDatarateList.options[cnt] = new Option(ratearr[cnt],val);
					if (val == curRate) {
						tmpDatarateList.options[cnt].selected = true;
					}
				}
			}
		}
	}

	var Display11nMode = function(block, bandwidth, gi)
	{

	}

	function ChanneltoStr(chnlnum)
	{
		if (chnlnum == "Auto")
			return "Auto";

		var chnlband = (chnlnum == 14) ? 2484 : (5 * chnlnum + ((chnlnum > 14) ? 5000 : 2407));
		var result = chnlnum + " / " + chnlband;
		var len = result.length-3;
		return result.slice(0, len) + "." + result.slice(len) + "GHz";
	}

	function RatetoStr(ratenum)
	{
		return (ratenum == 0) ? "Best" : ratenum + " Mbps";
	}
	function chgChannel()
	{
		var radio_mode = "g";
		if (radio_mode == "dynamic-turbo-g")
		{
			if (document.dataForm.ChannelList.value != 6)
			{
				alert("The SuperG mode is enabled and operates on channel 6 only. To select any other channel, please disable SuperG mode on Advanced Wireless Settings Page.");
				DispChnllist();
				return;
			}
		}

		curChnl = document.dataForm.ChannelList.value;
		DispRatelist();
	}


	function chgDatarate()
	{
		// Called on submit.  Prepare the datarate settings to be passed to the backend.

		// Store the current selection from the data rate dropdown.
		curRate = document.dataForm.DatarateList.value;

		var list = document.dataForm.DatarateList;
		var mode = document.getElementById ('WirelessMode').value;

		// Clear out our hidden input fields for setting.
		for (i = 0; i < 16; i++)
		{
			var basicrate = document.getElementById ("basic-rate-" + i);
			var supportedrate = document.getElementById ("supported-rate-" + i);

			basicrate.name = "datarates";
			basicrate.value = "";
			supportedrate.name = "datarates";
			supportedrate.value = ""
		}

		// Cycle through supported-rates, adding values that are selected to the input fields.
		var rates = gChnlMatrix.getRatelist(document.dataForm.WirelessMode.value, document.dataForm.ChannelList.value);
		var selectedRates = new Array();		// store the selected rates in an array for basic-rate processing below
		for (cnt in rates)
		{
			// If the value in the array is less then or equal to the selected value, pass it to the backend.
			//	Do this by setting a hidden field.
			if ((rates[cnt] <= Number(list.value)) || (list.value == 0))
			{
				if (rates[cnt] != 0)
				{
					var rateObj = document.getElementById ("supported-rate-" + cnt);
					rateObj.name = "supported-rate.wlan0." + cnt;
					rateObj.value = rates[cnt];
					selectedRates.push (rates[cnt]);
				}
			}
		}

		// Cycle through basic-rates.  All basic-rates MUST be in the supported rates set.
		var basicRates = [1,2,3];
		for (i in basicRates)
		{
			var found = false;
			for (j in selectedRates)
			{
				if (basicRates[i] == selectedRates[j])
					found = true;
			}

			// If we found it, add it to a hidden field so it gets passed to the backend.
			if (found)
			{
				var rateObj = document.getElementById ("basic-rate-" + i);
				rateObj.name = "basic-rate.wlan0." + i
				rateObj.value = basicRates[i];
			}
		}
	}





	///security profile page functions
var bFirstLoad = true;
var	encrypt_11g_none= "selected";
var	encrypt_11g_64	= "";
var	encrypt_11g_128	= "";
var	encrypt_11g_152	= "";
var	encrypt_11g_TKIP="";
var showit = "block";
var hideit = "none";






var wpapsk = "";
var disp_wpapsk = "";
for (j=0;j<wpapsk.length;j++)
	disp_wpapsk = disp_wpapsk + "*";

function VerifyVlanIdNumber(WarnText, EditBox, MinValue, MaxValue)
{
	if (EditBox.value == "")
	{
		alert("Please specify VLAN ID, must be in range ["+ MinValue + "-" + MaxValue + "].");
		return false;
	}
	if (""!=EditBox.value && isNaN(EditBox.value))
	{
		alert(WarnText + " must be a legal decimal number.");
		EditBox.select();
		return false;
	}
	if (parseInt(EditBox.value)<MinValue || parseInt(EditBox.value)>MaxValue)
	{
		alert(WarnText + " must be in range [" + MinValue + "-" + MaxValue + "].");
		EditBox.select();
		return false;
	}
}

function genesis()
{

	encrypt_11g_128= "selected";


	var	list = document.dataForm.key_size_11g;
	do
		list.options[0] = null;
	while (list.length);
	var isel = select_getv(document.dataForm.auth_11g);
	//switch (select_getv(document.dataForm.auth_11g))

	switch (isel)
	{
	default:
	case 0:
		list.options[0] = new Option("None", 0);
		list.options[1] = new Option("64 bits WEP", 40);
		list.options[2] = new Option("128 bits WEP", 104);
		list.options[3] = new Option("152 bits WEP", 128);

		if (bFirstLoad)
		{
			list.options[0].selected = true;
			document.dataForm.wep_key_length.value = "none";
		}
		break;
	case 2:
		list.options[0] = new Option("None", 0);
		if (bFirstLoad)
		{
		}
		break;
	case 1:
		list.options[0] = new Option("64 bits WEP", 40);
		list.options[1] = new Option("128 bits WEP", 104);
		list.options[2] = new Option("152 bits WEP", 128);
		if (bFirstLoad)
		{
			if (encrypt_11g_64 == "selected")
				{
					list.options[0].selected = true;
					document.dataForm.szKey1_11g.value = "**********";
					document.dataForm.szKey2_11g.value = "**********";
					document.dataForm.szKey3_11g.value = "**********";
					document.dataForm.szKey4_11g.value = "**********";
				}
			else if (encrypt_11g_128 == "selected")
				{
					list.options[1].selected = true;
					document.dataForm.szKey1_11g.value = "**************************";
					document.dataForm.szKey2_11g.value = "**************************";
					document.dataForm.szKey3_11g.value = "**************************";
					document.dataForm.szKey4_11g.value = "**************************";
				}
			else if (encrypt_11g_152 == "selected")
				{
					list.options[2].selected = true;
					document.dataForm.szKey1_11g.value = "********************************";
					document.dataForm.szKey2_11g.value = "********************************";
					document.dataForm.szKey3_11g.value = "********************************";
					document.dataForm.szKey4_11g.value = "********************************";
				}
		}
		break;
	case 4:
		list.options[0] = new Option("TKIP+AES", 253);
		list.options[1] = new Option("TKIP", 255);
		if (bFirstLoad)
		{
		}
		break;
	case 16:
		list.options[0] = new Option("TKIP+AES", 253);
		list.options[1] = new Option("TKIP", 255);
		if (bFirstLoad)
		{
			document.dataForm.wpa_psk.value = disp_wpapsk;
		}
		break;
	case 8:
		list.options[0] = new Option("TKIP+AES", 253);
		list.options[1] = new Option("AES", 254);
		if (bFirstLoad)
		{
		}
		break;
	case 32:
		list.options[0] = new Option("TKIP+AES", 253);
		list.options[1] = new Option("AES", 254);
		if (bFirstLoad)
		{
			document.dataForm.wpa_psk.value = disp_wpapsk;
		}
		break;
	case 12:
		list.options[0] = new Option("TKIP+AES", 253);
		break;
	case 48:
		list.options[0] = new Option("TKIP+AES", 253);
		if (bFirstLoad)
			document.dataForm.wpa_psk.value = disp_wpapsk;
		break;
	}

	if (bFirstLoad)
		bFirstLoad = false;

	graysomething();
	show_hide("wepDIV", (isel==0|isel==1|isel==2));
	show_hide("wpapskDIV",(isel==16|isel==32|isel==48));
}

function doKeyValue(val, flag)
{
	var i;
	val.maxLength = flag ? document.dataForm.key_size_11g.value / 4 : 39;
	for (i = 0; i < val.value.length; i++)
		if (val.value.charAt(i) != '*')
			return;
	val.value = "";
}

function wrongKeySyntax(val)
{
	var i = 0, s = 0;
	for (; i < val.length; i++)
		if (val.charAt(i) != '*')
			s++;
	if (s == 0)
		return false;
	for (i = 0; i < val.length; i++)
	{
		if (val.charAt(i) >= '0' && val.charAt(i) <= '9')
			continue;
		if (val.charAt(i) >= 'A' && val.charAt(i) <= 'F')
			continue;
		if (val.charAt(i) >= 'a' && val.charAt(i) <= 'f')
			continue;
		return true;
	}
	return false;
}

function verifykey(wep, keyname, keysize, key)
{
	var klen = select_getv(keysize), len = key.value.length;
	if (wep)
	{
		if (len == 0)
			return true;
		if (klen == 40	&& len == 10	||
			klen == 104 && len == 26	||
			klen == 128 && len == 32)
		{
			if (wrongKeySyntax(key.value))
			{
				alert("Invalid value for "+keyname+", please enter hexadecimal 0-9 A-F");
				return false;
			}
			return true;
		}
		else if (klen == 40 && len != 10)
		{
			alert("Invalid length for "+keyname+", please enter 10 digits");
			return false;
		}
		else if (klen == 104 && len != 26)
		{
			alert("Invalid length for "+keyname+", please enter 26 digits");
			return false;
		}
		else if (klen == 128 && len != 32)
		{
			alert("Invalid length for "+keyname+", please enter 32 digits");
			return false;
		}
	}
	return true;
}


function validate()
{
	genesis3();

	if (document.dataForm.szSsid_11a.value == "")
	{
		alert("The SSID can not be empty");
		return false;
	}
	if (document.dataForm.profileName.value == "")
	{
		alert("The Profile Name can not be empty");
		return false;
	}
	var v = document.dataForm.wpa_psk.value;
	var auth=document.dataForm.auth_11g.value;
	if ((auth== 16) && (v.length < 8 || v.length > 63))
	{
		alert("The Passphrase of WPA-PSK can only be fit the value between 8-63 characters. Over these range is against the WiFi WPA rule.");
		return false;
	}
	if ((auth==32) && (v.length < 8 || v.length > 63))
	{
		alert("The Passphrase of WPA2-PSK can only be fit the value between 8-63 characters. Over these range is against the WiFi WPA rule.");
		return false;
	}
	if ((auth==48) && (v.length < 8 || v.length > 63))
	{
		alert("The Passphrase of WPA-PSK & WPA2-PSK can only be fit the value between 8-63 characters. Over these range is against the WiFi WPA rule.");
		return false;
	}
	if (! verifykey(document.dataForm.key_size_11g, "Key 1", document.dataForm.key_size_11g, document.dataForm.szKey1_11g))	return false;
	if (! verifykey(document.dataForm.key_size_11g, "Key 2", document.dataForm.key_size_11g, document.dataForm.szKey2_11g))	return false;
	if (! verifykey(document.dataForm.key_size_11g, "Key 3", document.dataForm.key_size_11g, document.dataForm.szKey3_11g))	return false;
	if (! verifykey(document.dataForm.key_size_11g, "Key 4", document.dataForm.key_size_11g, document.dataForm.szKey4_11g))	return false;
	if((document.dataForm.szKey1_11g.value == "" && document.dataForm.keyno_11g[0].checked ||
		document.dataForm.szKey2_11g.value == "" && document.dataForm.keyno_11g[1].checked ||
		document.dataForm.szKey3_11g.value == "" && document.dataForm.keyno_11g[2].checked ||
		document.dataForm.szKey4_11g.value == "" && document.dataForm.keyno_11g[3].checked)&&(
		document.dataForm.key_size_11g.value == 40	||
		document.dataForm.key_size_11g.value == 104	||
		document.dataForm.key_size_11g.value == 128))
	{
		alert("The key is not completed.");
		return false;
	}

    if ((document.dataForm.szKey1_11g.value == "") || (document.dataForm.szKey1_11g.value == "**********") || (document.dataForm.szKey1_11g.value == "**************************") || (document.dataForm.szKey1_11g.value == "********************************"))
		document.dataForm.szKey1_11g.name = "a";
    if ((document.dataForm.szKey2_11g.value == "") || (document.dataForm.szKey2_11g.value == "**********") || (document.dataForm.szKey2_11g.value == "**************************") || (document.dataForm.szKey2_11g.value == "********************************"))
		document.dataForm.szKey2_11g.name = "a";
    if ((document.dataForm.szKey3_11g.value == "") || (document.dataForm.szKey3_11g.value == "**********") || (document.dataForm.szKey3_11g.value == "**************************") || (document.dataForm.szKey3_11g.value == "********************************"))
		document.dataForm.szKey3_11g.name = "a";
    if ((document.dataForm.szKey4_11g.value == "") || (document.dataForm.szKey4_11g.value == "**********") || (document.dataForm.szKey4_11g.value == "**************************") || (document.dataForm.szKey4_11g.value == "********************************"))
		document.dataForm.szKey4_11g.name = "a";
	if (document.dataForm.wpa_psk.value == disp_wpapsk)
		document.dataForm.wpa_psk.name ="a";

	VerifyVlanIdNumber("VLAN", document.dataForm.vlan_id, 1, 0x0fff-1);

	return true;
}

function integer(n)
{
	return n % (0xffffffff+1);
}

function shr(a, b)
{
	a = integer(a);
	b = integer(b);
	if (a - 0x80000000 >= 0)
	{
		a = a % 0x80000000;
		a >>= b;
		a += 0x40000000 >> (b-1);
	}
	else
		a >>= b;
	return a;
}

function shl1(a)
{
	a = a % 0x80000000;
	if (a & 0x40000000 == 0x40000000)
	{
		a -= 0x40000000;
		a *= 2;
		a += 0x80000000;
	}
	else
		a *= 2;
	return a;
}

function shl(a, b)
{
	a = integer(a);
	b = integer(b);
	for (var i = 0; i < b; i++)
		a = shl1(a);
	return a;
}

function and(a, b)
{
	a = integer(a);
	b = integer(b);
	var t1 = a-0x80000000, t2 = b-0x80000000;
	if (t1 >= 0)
		if (t2 >= 0)
			return (t1 & t2) + 0x80000000;
		else
			return t1 & b;
	else
		if (t2 >= 0)
			return a & t2;
		else
			return a & b;
}

function or(a, b)
{
	a = integer(a);
	b = integer(b);
	var t1 = a - 0x80000000, t2 = b - 0x80000000;
	if (t1 >= 0)
		if (t2 >= 0)
			return (t1 | t2) + 0x80000000;
		else
			return (t1 | b) + 0x80000000;
	else
		if (t2 >= 0)
			return (a | t2) + 0x80000000;
		else
			return a | b;
}

function xor(a, b)
{
	a = integer(a);
	b = integer(b);
	var t1 = a - 0x80000000, t2 = b - 0x80000000;
	if (t1 >= 0)
		if (t2 >= 0)
			return t1 ^ t2;
		else
			return (t1 ^ b) + 0x80000000;
	else
		if (t2 >= 0)
			return (a ^ t2) + 0x80000000;
		else
			return a ^ b;
}

function not(a)
{
	a = integer(a);
	return 0xffffffff - a;
}

var state = new Array(4), count = new Array(0, 0), buffer = new Array(64), transformBuffer = new Array(16), digestBits = new Array(16),
	S11 = 7, S12 = 12, S13 = 17, S14 = 22, S21 = 5, S22 = 9, S23 = 14, S24 = 20, S31 = 4, S32 = 11, S33 = 16, S34 = 23,
	S41 = 6, S42 = 10, S43 = 15, S44 = 21;

function F(x, y, z)
{
	return or(and(x, y), and(not(x), z));
}

function G(x, y, z)
{
	return or(and(x, z), and(y, not(z)));
}

function H(x, y, z)
{
	return xor(xor(x, y), z);
}

function I(x, y, z)
{
	return xor(y ,or(x , not(z)));
}

function rotateLeft(a, n)
{
	return or(shl(a, n), shr(a, 32-n));
}

function FF(a, b, c, d, x, s, ac)
{
	a += F(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a += b;
	return a;
}

function GG(a, b, c, d, x, s, ac)
{
	a += G(b, c, d) +x + ac;
	a = rotateLeft(a, s);
	a += b;
	return a;
}

function HH(a, b, c, d, x, s, ac)
{
	a += H(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a += b;
	return a;
}

function II(a, b, c, d, x, s, ac)
{
	a += I(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a += b;
	return a;
}

function transform(buf, offset)
{
	var a = 0, b = 0, c = 0, d = 0, x = transformBuffer;
	a = state[0];
	b = state[1];
	c = state[2];
	d = state[3];
	for (i = 0; i < 16; i++)
	{
		x[i] = and(buf[i*4+offset], 0xff);
		for (j = 1; j < 4; j++)
			x[i] += shl(and(buf[i*4+j+offset], 0xff), j*8);
	}
	a = FF(a, b, c, d, x[ 0], S11, 0xd76aa478);	/* 1 */
	d = FF(d, a, b, c, x[ 1], S12, 0xe8c7b756);	/* 2 */
	c = FF(c, d, a, b, x[ 2], S13, 0x242070db);	/* 3 */
	b = FF(b, c, d, a, x[ 3], S14, 0xc1bdceee);	/* 4 */
	a = FF(a, b, c, d, x[ 4], S11, 0xf57c0faf);	/* 5 */
	d = FF(d, a, b, c, x[ 5], S12, 0x4787c62a);	/* 6 */
	c = FF(c, d, a, b, x[ 6], S13, 0xa8304613);	/* 7 */
	b = FF(b, c, d, a, x[ 7], S14, 0xfd469501);	/* 8 */
	a = FF(a, b, c, d, x[ 8], S11, 0x698098d8);	/* 9 */
	d = FF(d, a, b, c, x[ 9], S12, 0x8b44f7af);	/* 10 */
	c = FF(c, d, a, b, x[10], S13, 0xffff5bb1);	/* 11 */
	b = FF(b, c, d, a, x[11], S14, 0x895cd7be);	/* 12 */
	a = FF(a, b, c, d, x[12], S11, 0x6b901122);	/* 13 */
	d = FF(d, a, b, c, x[13], S12, 0xfd987193);	/* 14 */
	c = FF(c, d, a, b, x[14], S13, 0xa679438e);	/* 15 */
	b = FF(b, c, d, a, x[15], S14, 0x49b40821);	/* 16 */
	a = GG(a, b, c, d, x[ 1], S21, 0xf61e2562);	/* 17 */
	d = GG(d, a, b, c, x[ 6], S22, 0xc040b340);	/* 18 */
	c = GG(c, d, a, b, x[11], S23, 0x265e5a51);	/* 19 */
	b = GG(b, c, d, a, x[ 0], S24, 0xe9b6c7aa);	/* 20 */
	a = GG(a, b, c, d, x[ 5], S21, 0xd62f105d);	/* 21 */
	d = GG(d, a, b, c, x[10], S22, 0x02441453);	/* 22 */
	c = GG(c, d, a, b, x[15], S23, 0xd8a1e681);	/* 23 */
	b = GG(b, c, d, a, x[ 4], S24, 0xe7d3fbc8);	/* 24 */
	a = GG(a, b, c, d, x[ 9], S21, 0x21e1cde6);	/* 25 */
	d = GG(d, a, b, c, x[14], S22, 0xc33707d6);	/* 26 */
	c = GG(c, d, a, b, x[ 3], S23, 0xf4d50d87);	/* 27 */
	b = GG(b, c, d, a, x[ 8], S24, 0x455a14ed);	/* 28 */
	a = GG(a, b, c, d, x[13], S21, 0xa9e3e905);	/* 29 */
	d = GG(d, a, b, c, x[ 2], S22, 0xfcefa3f8);	/* 30 */
	c = GG(c, d, a, b, x[ 7], S23, 0x676f02d9);	/* 31 */
	b = GG(b, c, d, a, x[12], S24, 0x8d2a4c8a);	/* 32 */
	a = HH(a, b, c, d, x[ 5], S31, 0xfffa3942);	/* 33 */
	d = HH(d, a, b, c, x[ 8], S32, 0x8771f681);	/* 34 */
	c = HH(c, d, a, b, x[11], S33, 0x6d9d6122);	/* 35 */
	b = HH(b, c, d, a, x[14], S34, 0xfde5380c);	/* 36 */
	a = HH(a, b, c, d, x[ 1], S31, 0xa4beea44);	/* 37 */
	d = HH(d, a, b, c, x[ 4], S32, 0x4bdecfa9);	/* 38 */
	c = HH(c, d, a, b, x[ 7], S33, 0xf6bb4b60);	/* 39 */
	b = HH(b, c, d, a, x[10], S34, 0xbebfbc70);	/* 40 */
	a = HH(a, b, c, d, x[13], S31, 0x289b7ec6);	/* 41 */
	d = HH(d, a, b, c, x[ 0], S32, 0xeaa127fa);	/* 42 */
	c = HH(c, d, a, b, x[ 3], S33, 0xd4ef3085);	/* 43 */
	b = HH(b, c, d, a, x[ 6], S34, 0x04881d05);	/* 44 */
	a = HH(a, b, c, d, x[ 9], S31, 0xd9d4d039);	/* 45 */
	d = HH(d, a, b, c, x[12], S32, 0xe6db99e5);	/* 46 */
	c = HH(c, d, a, b, x[15], S33, 0x1fa27cf8);	/* 47 */
	b = HH(b, c, d, a, x[ 2], S34, 0xc4ac5665);	/* 48 */
	a = II(a, b, c, d, x[ 0], S41, 0xf4292244);	/* 49 */
	d = II(d, a, b, c, x[ 7], S42, 0x432aff97);	/* 50 */
	c = II(c, d, a, b, x[14], S43, 0xab9423a7);	/* 51 */
	b = II(b, c, d, a, x[ 5], S44, 0xfc93a039);	/* 52 */
	a = II(a, b, c, d, x[12], S41, 0x655b59c3);	/* 53 */
	d = II(d, a, b, c, x[ 3], S42, 0x8f0ccc92);	/* 54 */
	c = II(c, d, a, b, x[10], S43, 0xffeff47d);	/* 55 */
	b = II(b, c, d, a, x[ 1], S44, 0x85845dd1);	/* 56 */
	a = II(a, b, c, d, x[ 8], S41, 0x6fa87e4f);	/* 57 */
	d = II(d, a, b, c, x[15], S42, 0xfe2ce6e0);	/* 58 */
	c = II(c, d, a, b, x[ 6], S43, 0xa3014314);	/* 59 */
	b = II(b, c, d, a, x[13], S44, 0x4e0811a1);	/* 60 */
	a = II(a, b, c, d, x[ 4], S41, 0xf7537e82);	/* 61 */
	d = II(d, a, b, c, x[11], S42, 0xbd3af235);	/* 62 */
	c = II(c, d, a, b, x[ 2], S43, 0x2ad7d2bb);	/* 63 */
	b = II(b, c, d, a, x[ 9], S44, 0xeb86d391);	/* 64 */
	state[0] += a;
	state[1] += b;
	state[2] += c;
	state[3] += d;
}

function init()
{
	count[0] = count[1] = 0;
	state[0] = 0x67452301;
	state[1] = 0xefcdab89;
	state[2] = 0x98badcfe;
	state[3] = 0x10325476;
	for (i = 0; i < digestBits.length; i++)
		digestBits[i] = 0;
}

function update(b)
{
	var index, i;
	index = and(shr(count[0], 3), 0x3f);
	if (count[0] < 0xffffffff-7)
		count[0] += 8;
	else
	{
		count[1]++;
		count[0] -= 0xffffffff+1;
		count[0] += 8;
	}
	buffer[index] = and(b, 0xff);
	if (index >= 63)
		transform(buffer, 0);
}

function finish()
{
	var bits = new Array(8), padding, i = 0, index = 0, padLen = 0;
	for (i = 0; i < 4; i++)
		bits[i] = and(shr(count[0], i*8), 0xff);
	for (i = 0; i < 4; i++)
		bits[i+4] = and(shr(count[1], i*8), 0xff);
	index = and(shr(count[0], 3), 0x3f);
	padLen = (index < 56) ? 56 - index : 120 - index;
	padding = new Array(64);
	padding[0] = 0x80;
	for (i = 0; i < padLen; i++)
		update(padding[i]);
	for (i = 0; i < 8; i++)
		update(bits[i]);
	for (i = 0; i < 4; i++)
		for (j = 0; j < 4; j++)
			digestBits[i*4+j] = and(shr(state[i], j*8), 0xff);
}

function hexa(n)
{
	var hexa_h = "0123456789abcdef", hexa_c = "", hexa_m = n;
	for (hexa_i = 0; hexa_i < 8; hexa_i++)
	{
		hexa_c = hexa_h.charAt(Math.abs(hexa_m) % 16) + hexa_c;
		hexa_m = Math.floor(hexa_m / 16);
	}
	return hexa_c;
}

function hexb(n)
{
	var hexa_h = "0123456789ABCDEF", hexa_c = "", hexa_m = n;
	for (hexa_i = 0; hexa_i < 2; hexa_i++)
	{
		hexa_c = hexa_h.charAt(Math.abs(hexa_m) % 16) + hexa_c;
		hexa_m = Math.floor(hexa_m / 16);
	}
	return hexa_c;
}

var ascii = "01234567890123456789012345678901" +
			" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
			"[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";

function MD5(entree)
{
	var l, s, k, ka, kb, kc, kd;
	init();
	for (k = 0; k < entree.length; k++)
	{
		l = entree.charAt(k);
		update(ascii.lastIndexOf(l));
	}
	finish();
	ka = kb = kc = kd = 0;
	for (i = 0;	i < 4;	i++)	ka += shl(digestBits[15-i], i*8);
	for (i = 4;	i < 8;	i++)	kb += shl(digestBits[15-i], (i-4)*8);
	for (i = 8;	i < 12;	i++)	kc += shl(digestBits[15-i], (i-8)*8);
	for (i = 12;i < 16;	i++)	kd += shl(digestBits[15-i], (i-12)*8);
	s = hexa(kd) + hexa(kc) + hexa(kb) + hexa(ka);
	return s;
}


function ltrim(str) {
	for (var i=0; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i++);
	return str.substring(i,str.length);
}
function rtrim(str) {
	for (var i=str.length-1; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i--);
	return str.substring(0,i+1);
}
function trim(str) {
	return ltrim(rtrim(str));
}

function gen_11g_keys()
{
	var v = trim(document.dataForm.wepPassPhrase.value),
	klen = document.dataForm.key_size_11g.value,
	seed = 0, keybyte, pp64, pseed = new Array(4),
	key = new Array(4), i, j, k;
	if (v == '') {
		alert('WEP Passphrase cannot be empty!');
		return false;
	}
	else if ((/^\*{1,}$/.test(v) == true)) {
		if (document.dataForm.wepPassPhrase_hidden.value != '') {
			var v = document.dataForm.wepPassPhrase_hidden.value;
		}
		else {
			return false;
		}
	}

	if ($('wepKey')!=undefined) {
		document.dataForm.wepKey.value = "";
	}
	else {
		document.dataForm.wepKey1.value = "";
		document.dataForm.wepKey2.value = "";
		document.dataForm.wepKey3.value = "";
		document.dataForm.wepKey4.value = "";
	}
	if (0 == v.length || v.length > 64 || klen == 0)
		return;
	if (klen == 64)
	{
		for (i = 0; i < v.length; i++)
			pseed[i%4] ^= ascii.lastIndexOf(v.charAt(i));
		seed += pseed[0];
		seed += pseed[1] << 8;
		seed += pseed[2] << 16;
		seed += pseed[3] << 24;
		for (j = 0; j < 4; j++)
		{
			k = "";
			for(i=0; i<5; i++)
			{
				seed = (214013 * seed + 0x269EC3) & 0xFFFFFF;
				keybyte = hexb(seed >> 16);
				if (keybyte.length == 1)
					k += "0" + keybyte;
				else
					k += keybyte;
			}
			key[j] = k;
		}
	}
	else
	{
		k = "";
		pp64 = "";
		for (i = 0; i < 64; i++)
			pp64 += v.charAt(i%(v.length));
		k = MD5(pp64);
		key[0] = k.toUpperCase().substr(0, (klen-24)/4);
		key[1] = key[0];
		key[2] = key[0];
		key[3] = key[0];
	}
	if ($('wepKey')!=undefined) {
		document.dataForm.wepKey.setAttribute('masked',false);
		document.dataForm.wepKey.value = key[0];
	}
	else {
		document.dataForm.wepKey1.setAttribute('masked',false);
		document.dataForm.wepKey2.setAttribute('masked',false);
		document.dataForm.wepKey3.setAttribute('masked',false);
		document.dataForm.wepKey4.setAttribute('masked',false);
		document.dataForm.wepKey1.value = key[0];
		document.dataForm.wepKey2.value = key[1];
		document.dataForm.wepKey3.value = key[2];
		document.dataForm.wepKey4.value = key[3];
	}
	setActiveContent();
}

function show_hide(el,shownow)  // IE & NS6; shownow = true, false
{
	if (document.all)
		document.all(el).style.display = (shownow) ? showit : hideit ;
	else if (document.getElementById)
		document.getElementById(el).style.display = (shownow) ? showit : hideit ;
}
