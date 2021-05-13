var CORRECT=1;
var SAMESUBNET=2;

/*IP地址错误*/
var ERR_IP_NOTNUMBER=	-10001;/*IP只能为数字*/
var ERR_IP_FORMAT=		-10002;/*IP格式错误*/
var ERR_IP_INVALID=		-10003;/*IP地址网段非法*/
var ERR_IP_LOOP=		-10004;/*IP地址为回环地址*/
var ERR_IP_EMPTY=		-10005;/*IP地址为空*/
var ERR_IP_FIRSTZERO=	-10006;/*IP以0开头*/
var ERR_IP_GROUP=		-10007;/*组播IP地址*/
var ERR_IP_ALLZERO=		-10008;/*IP全为0*/
var ERR_IP_ALLONE=		-10009;/*IP全为255*/

/*MAC地址错误*/
var ERR_MAC_FORMAT=		-20001;/*MAC地址格式不正确*/
var ERR_MAC_GROUP=		-20002;/*MAC地址为组播地址*/
var ERR_MAC_ZERO=		-20003;/*MAC地址为全零*/
var ERR_MAC_BROAD=		-20004;/*MAC地址为广播地址*/
var ERR_MAC_EMPTY=		-20005;/*MAC地址为空*/

/*子网掩码*/
var ERR_MASK_ILLEGAL=	-30001;/*子网掩码不合法*/
var ERR_MASK_ZERO=		-30002;/*子网掩码全零*/
var ERR_MASK_ONE=		-30003;/*子网掩码全1*/
var ERR_MASK_FORMAT=	-30004;/*子网掩码格式错误*/
var ERR_MASK_EMPTY=		-30005;/*子网掩码为空*/
var ERR_IPMASK_MATCH=	-30006;/*IP址和掩码不匹配*/
var ERR_NETMASK_MATCH=	-30007;/*网络地址和掩码不匹配*/
var ERR_IP_DIFFNET=		-30008;/*不在同一个网段*/
var ERR_NETID_INV=		-30009;/*网络号全0或者1*/
var ERR_HOSTID_INV=		-30010;/*主机号全0或者1*/

/*网关错误*/
var ERR_GW_ADDR=		-40001;/*网关地址非法*/
var ERR_GW_LAN=			-40002;/*网关不在LAN网段*/
var ERR_GW_EMPTY=		-40003;/*网关地址为空*/

/*端口*/
var ERR_PORT_ILLEGAL=	-50001;/*端口值非法*/
var ERR_PORT_FORMAT=	-50003;/*端口格式不正确*/
var ERR_PORT_NULL=		-50004;/*端口值为空*/
var ERR_PORT_RANGE=		-50005;/*端口范围不正确*/
var ERR_PORT_OUTRANGE=	-50006;/*端口超出范围*/

/*无线名称*/
var ERR_SSID_NULL=		-60001;/*不能为空*/
var ERR_SSID_SPACE=     -60002;/*首尾不能有空格*/
var ERR_SSID_LEN=		-60003;/*无线SSID长度为1-32个字符*/
var ERR_SSID_IVALID=	-60004;/*无线SSID含非法字符*/

/*终端列表名称*/
var ERR_DEVICE_EMPTY=	-70001
var ERR_DEVICE_SPACE=   -70002;/*不能含有空格*/
var ERR_DEVICE_LEN=		-70003;/*名称长度为1-64个字符*/
var ERR_DEVICE_IVALID=	-70004;/*输入含非法字符*/

/*输入框*/
var ERR_INPUT_EMPTY=	-80001;/*输入框为空*/
var ERR_INPUT_INVALID=	-80002;/*输入框中含有非法字符*/
var ERR_QoS_FIRSTZERO=	-80003;/*Qos输入格式错误xxx.xx*/
var ERR_QoS_FORMAT=		-80004;/*Qos输入格式错误xxx.xx*/
var ERR_QoS_OUTSMALL=	-80005;/*Qos输入值小于规定值*/
var ERR_QoS_OUTLARGE=	-80006;/*Qos输入值大于规定值*/

/*MTU值错误*/
var ERR_MTU=			-90001;/*MTU值错误*/


/*公共函数：检测输入框的输入字符个数长度是否在规定范围之内*/
function checkInputLength(str,minlen,maxlen)
{
	if(!checkDigitRange(str.length,1,minlen,maxlen))
	{
		return false;
	}
	return true;
		
}

/*公共函数：检测输入框的输入字符是否只含16进制*/
function checkStrAllHex(str,minlen,maxlen)
{
	var result=/^[0-9a-fA-F]+$/g.test(str);
	if(result)
	{
		return true;
	}
	else 
	{
		return false;
	}
		
}
function funcChina(str) {
    if (/.*[\u4e00-\u9fa5]+.*/.test(str)) {
        return true
    } else {
        return false;
    }
}
/*公共函数：检测双字节字符，；%￥。。（包括汉字）----用来测试密码包含非法字符*/
function checkDoubleByteStr(str)
{
	var result=/[^\x00-\xFF]/g.test(str);
	if(result)
	{
		return true;//含有双字节符
	}
	else 
	{
		return false;//不含双字节符
	}	
}

/*公共函数：检查input输入框是否含有空格，开头结尾中间都不能含有*/
function checkInputStr(str)
{
	var i,len=str.length;
	if(0==len)
	{
		return ERR_INPUT_EMPTY;
	}
	var result=/\s/g.test(str);
	if(result)
	{
		return ERR_INPUT_INVALID;
	}
	return CORRECT;
}

/*公共函数：检查input输入框是否以空格开头或结尾，中间可有，如密码设置*/
function checkInputStrBeginendSpace(str)
{
	var i,len=str.length;
	if(0==len)
	{
		return ERR_INPUT_EMPTY;
	}
	var result=/(^\s)|(\s$)/g.test(str);
	if(result)
	{
		return ERR_INPUT_INVALID;
	}
	return CORRECT;
}

/*检测输入框是否含有；，,;*/
function checkSpecialInputStr(str)
{
	for(i=0;i<str.length;i++)
	{
		if(','==str.charAt(i) || ';'==str.charAt(i) || '，'==str.charAt(i) || '；'==str.charAt(i))
		{
			return ERR_INPUT_INVALID;
		}
	}
	return CORRECT;
}

/*检测是否是合法的键盘输入（Å等非法字符）*/
function isKeyboardInput(Str)
{
    var i, len = Str.length;

    for(i = 0; i < len; i++)
    {
        if(Str.charAt(i) < ' ' || Str.charAt(i) > '~')
            return false;
    }
    
    return true;
}

/*检测是否全部是数字或.*/
function validateKey(str)
{
   for (var i=0; i<str.length; i++) 
   {
    if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') || (str.charAt(i) == '.' ) )
			continue;
	return 0;
   }
  return 1;
}

function checkIpFormat(value)
{
	//var result = /^([0-9]{1,3}\.){3}([0-9]{1,3})$/g.test(value);
	var result = /^((0|([1-9][0-9]?[0-9]?))\.){3}(0|([1-9][0-9]?[0-9]?))$/g.test(value);
	return (result == true ? CORRECT : ERR_IP_FORMAT);
}

function getDigit(str, num)
{
  i=1;
  if ( num != 1 ) 
  {
  	while (i!=num && str.length!=0) 
	{
		if ( str.charAt(0) == '.' ) 
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
  	if ( str.charAt(i) == '.' ) 
	{
		str = str.substring(0, i);
		break;
	}
  }
  if ( str.length == 0)
  	return -1;
  var d = parseInt(str, 10);
  return d;
}

/*判断地址是否在范围内*/
function checkDigitRange(str, num, min, max)
{
  var d = getDigit(str,num);
  if ( d > max || d < min )
      	return false;
  return true;
}

/* --------------公共函数：检查IP地址是否正确 ------------------*/
function checkIpAddr(value)
{
	if(""==value)
	{
		return ERR_IP_EMPTY;/*IP为空*/
	}
	if(!validateKey(value))
	{
		return ERR_IP_NOTNUMBER;//IP只能为数字*/
	}
	if( CORRECT!=checkIpFormat(value))
	{
		return ERR_IP_FORMAT;//IP格式错误
	}
	if("0.0.0.0"==value)
	{
		return	ERR_IP_ALLZERO;/*IP全为0*/
	}
	if("255.255.255.255"==value)
	{
		return	ERR_IP_ALLONE;/*IP全为255*/
	}
	for(var i=1;i<=4;i++)
	{
		if(!checkDigitRange(value,i,0,255))
		{
			return ERR_IP_FORMAT;//IP格式错误
		}
	}
	if(getDigit(value,1)>0xE0)
	{
		return ERR_IP_INVALID;//网段非法
	}
	if(getDigit(value,1)==0)
	{
		return ERR_IP_FIRSTZERO;//以0开头
	}
	if( getDigit(value,1) ==127)
	{
		return ERR_IP_LOOP;//回环地址
	}
	if( getDigit(value,1) ==0xE0)
	{
		return ERR_IP_GROUP;//组播地址
	}
	return CORRECT;
}



/* 检查MAC地址范围是否合法 */
function verifyMacAddr(str)
{
	var charSet = "0123456789abcdef";
	var macAddr = str.toLowerCase();

	if ( "00:00:00:00:00:00" == macAddr)
	{
		return ERR_MAC_ZERO;
	}

	if ( "ff:ff:ff:ff:ff:ff" == macAddr)
	{
		return ERR_MAC_BROAD;
	}

	if (1 == charSet.indexOf(macAddr.charAt(1)) % 2)
	{
		return ERR_MAC_GROUP;
	}
		return CORRECT;
}

/* 检查MAC地址格式是否合法 */
function verifyMacFormat(str)
{
	var colonResult = /^([0-9a-f]{2}:){5}([0-9a-f]{2})+$/gi.test(str);
	if(colonResult)
	{
		return CORRECT;
	}
	else 
	{
		return ERR_MAC_FORMAT;
	}
}

/* --------------公共函数：检查MAC地址是否正确 ------------------*/
function checkMacAddr(str)
{
	var result = CORRECT;
	if(""==str.length)
	{
		return ERR_MAC_EMPTY;
	}
	if (CORRECT != (result = verifyMacFormat(str)))
	{
		return result;
	}

	if (CORRECT != (result = verifyMacAddr(str)))
	{
		return result;
	}
	
		return result;
}

/*检测Mask地址是否合法*/
function checkMask(str, num)
{
  var d = getDigit(str,num);
  if( !(d==0 || d==128 || d==192 || d==224 || d==240 || d==248 || d==252 || d==254 || d==255 ))
  	return false;
  return true;
}

function isValidMask2(str)
{
	
	var _value = str.split('.');
	var _numValue=0;
	var _flag=0;
	
	for(var _i=0; _i<_value.length; _i++)
	{
		var _numValue = parseInt(_value[_i],10);

		for(var _j=0; _j<8; _j++)
		{
			if(parseInt(_numValue&128,10)==128)
			{
				if(_flag==1)
				{
					return false;
				}
			}
			else if(parseInt(_numValue&128,10)!=128)
			{
				_flag=1;
			}
			
			_numValue = _numValue << 1;
			
		}

	}

	return true;

}

/* --------------公共函数：检查子网掩码是否正确 ------------------*/
function isValidMask(_value)
{
	if(_value=="")
	{
		return ERR_MASK_EMPTY;
	}
	if (_value=="..." || CORRECT!=checkIpFormat(_value) ) 
	{
		//alertError('CheckMaskValue1',1);
		return ERR_MASK_FORMAT;
	 }

	if (_value == "255.255.255.255")
	{
		//alertError('CheckMaskValue2',1);
		return ERR_MASK_ONE;
	
	  }
	  
	if (_value == "0.0.0.0")
	{
		//alertError('CheckMaskValue3',1);
		return ERR_MASK_ZERO;
	
	 }

  	if ( validateKey(_value) == 0 ) 
	{
      	//alertError('CheckMaskValue4',1);
		return ERR_IP_NOTNUMBER;
	 }
	 
	  if ( !checkMask(_value,1)) 
	  {
		//alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }

	  if ( !checkMask(_value,2)) {
		// alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }
	  
	  if ( !checkMask(_value,3)) {
		//alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }
	  
	  if ( !checkMask(_value,4)) {
		//alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }
	
	  if (! ( (getDigit(_value,1) >= getDigit(_value,2))
		&&  (getDigit(_value,2) >= getDigit(_value,3))
		&& (getDigit(_value,3) >= getDigit(_value,4)) ) )
	  {
		//alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }
	  
	   if (!isValidMask2(_value)) {
		//alertError('CheckMaskValue5',1);
		return ERR_MASK_ILLEGAL;
	  }

	return CORRECT;
}

/* 根据IP和掩码获取网络地址*/
function getNetwork(ip, mask)
{
	var ipByte = ip.split(".");
	var maskByte = mask.split(".");
	var netByte = new Array();
	for(var i = 0, len = ipByte.length; i < len; i++)
	{
		var temp = ipByte[i] & maskByte[i];
		netByte.push(temp);
	}
		
	return netByte.join(".");
}

/* ----------------公共函数：检查网络号是否与掩码匹配 ---------------*/
function checkNetMaskMatch(net,mask)
{
	if( getNetwork(net,mask)!=net)
	{
		return ERR_NETMASK_MATCH;/*1.168.1.0/128.0.0.0*/
	}
	return CORRECT;
}

/* ---------------公共函数：使用掩码判断两个IP是否处于同一网段 --------*/
function isSameNet(srcIp, dstIp, srcMask)
{
	var srcNet = getNetwork(srcIp, srcMask);
	var dstNet = getNetwork(dstIp, srcMask);
	if(srcNet != dstNet)
	{
		return ERR_IP_DIFFNET;
	}
	return SAMESUBNET;
}

/* 获取IP的网络号类型*/
function getIpClass(value)
{
	var ipByte = value.split(".");
	if (ipByte[0] <= 127)
	{
		return 'A';
	}
	if (ipByte[0] <= 191)
	{
		return 'B';
	}
	if (ipByte[0] <= 223)
	{
		return 'C';
	}
	if (ipByte[0] <= 239)
	{
		return 'D';
	}
	return 'E';
}
/* 检查Ip类型是否合法 */
function checkIpClass(ip, mask)
{
	var netId = getIpClass(ip);
	var ipVal = transIptoInt(ip);
	var maskVal = transIptoInt(mask);
	switch(netId)
	{
		case 'A':
			ipVal &= 0xFF000000;
			break;
		case 'B':
			ipVal &= 0xFFFF0000;
			break;
		case 'C':
			ipVal &= 0xFFFFFF00;
			break;
		}
		return (maskVal > ipVal ? CORRECT : ERR_IPMASK_MATCH);
}
/* 将IP转换为整数 */
function transIptoInt(str)
{
	var value = str.split(".");
	return (0x1000000 * value[0] + 0x10000 * value[1] + 0x100 * value[2] + 1 * value[3]);
}

/*检测主机号和网络号是否全是0/1*/
function verifyIPNetHost(ipVal, maskVal)
{
	/* 网络号全0/1 */
	if (0x0 == (ipVal & maskVal) || maskVal == (ipVal & maskVal))
	{
		return ERR_NETID_INV;
	}

	/* 主机号全0/1(源地址/广播地址) */
	if (0x0 == (ipVal & (~maskVal)) || (~maskVal) == (ipVal & (~maskVal)))
	{
		return ERR_HOSTID_INV;
	}

	return CORRECT;
}

/* ---------------公共函数：检查是否为网络地址 ------------------------*/
function verifyRouteNet(ip, mask)
{
	var maskVal=transIptoInt(mask);
	var ipVal=transIptoInt(ip);
	if (0x0 != (ipVal & (~maskVal)))
	{
		return false;
	}
	return true;
}

/* ---------------公共函数：/* 使用掩码检查IP是否合法 ------------------------*/
/////返回结果：CORRECT、（ERR_NETID_INV、ERR_HOSTID_INV）、ERR_IPMASK_MATCH
function checkIpMaskMatch(ip, mask)
{
	var maskVal=transIptoInt(mask);
	var ipVal=transIptoInt(ip);
	
	/* 网络号全0/1*/
	if (0x0 == (ipVal & maskVal) || maskVal == (ipVal & maskVal))
	{
		return ERR_NETID_INV;
	}
	/* 主机号全0/1*/
	if (0x0 == (ipVal & (~maskVal)) || (~maskVal) == (ipVal & (~maskVal)))
	{
		return ERR_HOSTID_INV;
	}
	var result=checkIpClass(ip, mask);
	if(result != CORRECT)//若IP网络号大于mask
	{
		return result;
	}
	return CORRECT;
}

/*检查输入字符串是否符合正整数格式*/
function isPositiveInteger(str)
{
	var result=/^[1-9]\d*$/.test(str);
	if(result)
	{
		return true;//正整数
	}
	else
	{
		return false;//非正整数
	}
}

/*检查输入字符串是否符合整数或xxxxxx.xx格式，QoS验证输入*/
function verifyQosInput(value,minvalue,maxvalue)
{
	var spaceresult=checkInputStr(value);
	var Rexptest1=/^([0-9])+(\.([0-9]{1,2}))?$/g.test(value);
	var Rexptest2=/^(0|0\.([0-9]{1,2}))$/g.test(value);
	if(CORRECT!=spaceresult)
	{
		return spaceresult;
	}
	if(!Rexptest1)
	{
		return ERR_QoS_FORMAT;
	}
	else if(value.charAt(0)=='0' && !Rexptest2)//以0开头且不符合小数格式
	{
		return ERR_QoS_FIRSTZERO;
	}
	else if(parseFloat(value) < minvalue)
	{
		return ERR_QoS_OUTSMALL;
	}
	else if(parseFloat(value) > maxvalue)
	{
		return ERR_QoS_OUTLARGE;
	}
	return CORRECT;
}

/* ----------------公共函数：检查端口输入值是否合法 ---------------*/
function verifyPort(port)
{
	if( ""==port )
	{
		return ERR_PORT_NULL;
	}
	if( !isPositiveInteger(port) )
	{
		return ERR_PORT_FORMAT;
	}
	if( parseInt(port,10)>65535 )
	{
		return ERR_PORT_RANGE;
	}
	return CORRECT;
}

/* ----------------公共函数：检查端口范围X-X的是否合法 ---------------*/
function verifyPortRange(port)
{
	var isIntegerPort=/^[1-9]\d*$/.test(port);//是否只为正整数
	var isRangePort=/^([1-9]\d*$|[1-9]\d*\-[1-9]\d*$)/.test(port);//端口格式8000-8000或8000
	if( ""==port )
	{
		return ERR_PORT_NULL;
	}
	else if(isIntegerPort)//只为一个正整数值
	{
		if(parseInt(port,10)>65535)
		{
			return ERR_PORT_OUTRANGE;
		}
		return CORRECT;
	}
	else if(isRangePort)//8000-9000范围的格式
	{
		var subPort=port.split("-");
		if(parseInt( subPort[0],10)>65535 || parseInt(subPort[1],10)>65535 )
		{
			return ERR_PORT_OUTRANGE;
		}
		else if(parseInt(subPort[0],10) > parseInt(subPort[1],10))
		{
			return ERR_PORT_RANGE;//后者大于前者
		}
		return CORRECT;
	}
	else
	{
		return ERR_PORT_FORMAT;
	}
	
}

/* ----------------公共函数：检查SSID是否合法：1-32个字符 ---------------*/
function verifySSID(ssid)
{
	var result=checkInputStrBeginendSpace(ssid);
	switch(result)
	{
		case ERR_INPUT_EMPTY:
			return ERR_SSID_NULL;//SSid为空
		case ERR_INPUT_INVALID:
			//return ERR_SSID_IVALID;//首尾含有空格
			return ERR_SSID_SPACE;//首尾含有空格
		default:
			break;
	}
	if(CORRECT!=checkSpecialInputStr(ssid))
	{
		return ERR_SSID_IVALID;//含有分号和逗号
	}
	var len = ssid.replace(/[^\x00-\xFF]/g, "xxx").length;//UTF-8编码的非英文字符是三个字节
	if (len < 1 || len > 32)
	{
		return ERR_SSID_LEN;/* 长度不符合要求 */
	}
	return CORRECT;
}

/* ----------------公共函数：检查终端列表中名称否合法（1-64个字符）---------------*/
function verifyDeviceName(name)
{
	if(""==name)
	{
		return ERR_DEVICE_EMPTY;//为空
	}
	if(ERR_INPUT_INVALID == checkInputStr(name))
	{
		return ERR_DEVICE_SPACE;//含有空格
	}
	if(ERR_INPUT_INVALID == checkSpecialInputStr(name))
	{
		return ERR_DEVICE_IVALID;//含有分号和逗号
	}
	var len = name.replace(/[^\x00-\xFF]/g, "xxx").length;//UTF-8编码的非英文字符是三个字节
	if (len < 1 || len > 64)
	{
		return ERR_DEVICE_LEN;/* 长度不符合要求 */
	}
	return CORRECT;
}

/* 检查是否为数字格式的字符 */
function checkNum(value)
{
	var result=/^[1-9]\d*$/g.test(value);
	if(result)
		return true;
	else
		return false;
}
/* --------------公共函数：检查MTU是否在规定的范围之内 ------------------*/
function verifyMTU(value, min, max)
{
	var result = CORRECT;

	if (checkNum(value) == false)
	{
		return ERR_MTU;
	}

	if (max == undefined)
	{
		max = 1500;
		min = 576;
	}

	if (false == checkDigitRange(parseInt(value), 1, min, max))
	{
		return ERR_MTU;
	}
	
	return CORRECT;
}

/* --------------公共函数：检查是否为正整数（不包含负整数） ------------------*/
function verifyInteger(value)
{
	//var regEpx=/((^0$)|(^[1-9]\d*$))/g;
	var regEpx=/^[1-9]\d*$/g;
	if (regEpx.test(value))
	{
		return true;
	}
	return false;
}
/* --------------检查是否为IP格式 ------------------*/
function checkParentControlIpFormat(value)
{
	var result = /^([0-9]+\.){3}([0-9]+)$/g.test(value);
	if(result)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/* --------------公共函数：检查域名是否合法 ------------------*/
function verifyDomain(value)
{
	//var regEpx=/^((http|https|ftp|rtsp|mms)+:\/\/)?(\w+(-\w+)*)((\.|\/)(\w+(-\w+)*)){2,}(\?\S*)?(\/)?$/gi;
	var regEpx=/^((\w)+:\/\/)?(\w+(-\w+)*)((\.|\/)(\w+(-\w+)*))*(\?\S*)?(\/)?$/gi;
	if (regEpx.test(value))
	{
		return true;
	}
	return false;
}

/* --------------公共函数：检查DDNS域名是否合法 ------------------*/
function verifyDDNSDomain(value)
{
	//var regEpx=/^((http|https|ftp|rtsp|mms)+:\/\/)?(\w+(-\w+)*)((\.|\/)(\w+(-\w+)*)){2,}(\?\S*)?(\/)?$/gi;
	var regEpx=/^((\w)+:\/\/)?(\w+(-\w+)*)((\.)(\w+(-\w+)*)){1,}(\?\S*)?(\/)?$/gi;
	if (regEpx.test(value))
	{
		return true;
	}
	return false;
}

/* --------------公共函数：设备名超过长度时显示... ------------------*/
function subDeviceNameDisplay(devicename,maxlen)
{
	var devicename_display=devicename;
	var len=0;
	for(var i=0;i<devicename.length;i++)
	{
		len+=devicename.charAt(i).replace(/[^\x00-\xFF]|[A-Z]/g, "xx").length;//大写字母和中文为两个长度
		if(len > maxlen)
		{
			devicename_display=devicename.substring(0,i)+"...";
			break;
		}
	}
	return devicename_display;
}