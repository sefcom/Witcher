/*+-----------------------------------------+
  | FileName : Javascript Common Web Check Function |
  | Edtion   : To Trunk R2                  |
  | Author   : TBS Software                 |
  +-----------------------------------------+*/
  
/*检测是否是合法字符*/
function validVisibleStr(Str)
{
    var i, len = Str.length;

    for(i = 0; i < len; i++)
    {
        if(Str.charAt(i) < ' ' || Str.charAt(i) > '~')
            return false;
    }
    
    return true;
}

/*检测是否全部是数字*/
function validateKey(str)
{
   for (var i=0; i<str.length; i++) {
    if ( (str.charAt(i) >= '0' && str.charAt(i) <= '9') || (str.charAt(i) == '.' ) )
			continue;
	return 0;
  }
  return 1;
}

function getDigit(str, num)
{
  i=1;
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

/*判断地址是否在范围内*/
function checkDigitRange(str, num, min, max)
{
  var d = getDigit(str,num);
  if ( d > max || d < min )
      	return false;
  return true;
}

function checkIpAddr1(_value)
{
	if (_value == "...") {
		return false;
	}

	if ( validateKey(_value) == 0) {
		return false;
	}
	if ( !checkDigitRange(_value,1,1,223) ) {
		return false;
	}

	if ( getDigit(_value,1) ==127) {
		return false;
	}

	if ( !checkDigitRange(_value,2,0,255) ) {
		return false;
	}
	if ( !checkDigitRange(_value,3,0,255) ) {
		return false;
	}
	if ( !checkDigitRange(_value,4,1,254) ) {
		return false;
	}

	return true;
}


function checkIpAddr(_value)
{
	if (_value == "") {
		//alertError('CheckIpValue1',1);
		return false;
	}

	if ( validateKey(_value) == 0) {
		//alertError('CheckIpValue2',1);
		return false;
	}
	if ( !checkDigitRange(_value,1,1,223) ) {
		//alertError('CheckIpValue3',1);
		return false;
	}

	if ( getDigit(_value,1) ==127) {
		//alertError('CheckIpValue4',1);
		return false;
	}

	if ( !checkDigitRange(_value,2,0,255) ) {
		//alertError('CheckIpValue5',1);
		return false;
	}
	if ( !checkDigitRange(_value,3,0,255) ) {
		//alertError('CheckIpValue6',1);
		return false;
	}
	if ( !checkDigitRange(_value,4,1,254) ) {
		//alertError('CheckIpValue7',1);
		return false;
	}

	return true;
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


/*判断子网掩码的合法性*/
function isValidMask1(_value)
{
	
	if (_value=="...") 
	{
		return false;
	 }


	if (_value == "255.255.255.255")
	{
		return false;
	
	  }
	  
	if (_value == "0.0.0.0")
	{
		return false;
	
	 }

  	if ( validateKey(_value) == 0 ) 
	{

		return false;
	 }
	 
	  if ( !checkMask(_value,1)) 
	  {
		return false;
	  }

	  if ( !checkMask(_value,2)) {
		return false;
	  }
	  
	  if ( !checkMask(_value,3)) {
		return false;
	  }
	  
	  if ( !checkMask(_value,4)) {
		return false;
	  }
	
	  if (! ( (getDigit(_value,1) >= getDigit(_value,2))
		&&  (getDigit(_value,2) >= getDigit(_value,3))
		&& (getDigit(_value,3) >= getDigit(_value,4)) ) )
	  {
		return false;
	  }
	  
	   if (!isValidMask2(_value)) {
		return false;
	  }

	return true;
}

function isValidMask(_value)
{
	
	if (_value=="...") 
	{
		//alertError('CheckMaskValue1',1);
		return false;
	 }


	if (_value == "255.255.255.255")
	{
		//alertError('CheckMaskValue2',1);
		return false;
	
	  }
	  
	if (_value == "0.0.0.0")
	{
		//alertError('CheckMaskValue3',1);
		return false;
	
	 }

  	if ( validateKey(_value) == 0 ) 
	{

      	//alertError('CheckMaskValue4',1);
		return false;
	 }
	 
	  if ( !checkMask(_value,1)) 
	  {
		//alertError('CheckMaskValue5',1);
		return false;
	  }

	  if ( !checkMask(_value,2)) {
		// alertError('CheckMaskValue5',1);
		return false;
	  }
	  
	  if ( !checkMask(_value,3)) {
		//alertError('CheckMaskValue5',1);
		return false;
	  }
	  
	  if ( !checkMask(_value,4)) {
		//alertError('CheckMaskValue5',1);
		return false;
	  }
	
	  if (! ( (getDigit(_value,1) >= getDigit(_value,2))
		&&  (getDigit(_value,2) >= getDigit(_value,3))
		&& (getDigit(_value,3) >= getDigit(_value,4)) ) )
	  {
		//alertError('CheckMaskValue5',1);
		return false;
	  }
	  
	   if (!isValidMask2(_value)) {
		//alertError('CheckMaskValue5',1);
		return false;
	  }

	return true;
}

//for lan.htm page checking Ip & Mask is valid value
function checkIp_Mask(lanIp,lanMask)
{

   var count = 0;
   var count2 = 0;
   var l1a_n,l1m_n;

   var _lanIp = lanIp.split('.');
   var _lanMask = lanMask.split('.');

   for (i = 0; i < 4; i++) {
      l1a_n = parseInt(_lanIp[i]);
      l1m_n = parseInt(_lanMask[i]);
      if ((l1a_n & l1m_n)==0)
         count++;
	  else if((l1a_n & l1m_n)==1)
	  	 count2++;
   }
   if (count == 4)
   {
	 // alertError('CheckIp_Mask',1);
      return false;
   }
   else if(count2 == 4)
   {
	 // alertError('CheckIp_Mask',1);
      return false;
   }
   else
      return true;
}


/*判断网关地址是否合法*/
function checkGatewayAddr(_value)
{

	if (_value == "...") {
		//alertError('CheckGatewayValue1',1);
		return false;
	}

	   if ( validateKey(_value) == 0) {
     // alertError('CheckGatewayValue2',1);
      return false;
   }
   if ( !checkDigitRange(_value,1,1,223) ) {
   // alertError('CheckGatewayValue3',1);
      return false;
   }
   if ( getDigit(_value,1) ==127)
   	{
	 // alertError('CheckGatewayValue4',1);
      return false;

   }

   if ( !checkDigitRange(_value,2,0,255) ) {
     // alertError('CheckGatewayValue5',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,3,0,255) ) {
     // alertError('CheckGatewayValue6',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,4,1,254) ) {
    // alertError('CheckGatewayValue7',1);
      return false;
   }
	return true;
}


/*判断DNS地址是否合法*/
function checkPrimaryDNS(_value)
{
	if (_value == "...") {
		//alertError('CheckPrimaryDnsValue1',1);
		return false;
	}

	  if ( validateKey(_value) == 0) {
     // alertError('CheckPrimaryDnsValue2',1);
      return false;
   }
   if ( !checkDigitRange(_value,1,1,223) ) {
     // alertError('CheckPrimaryDnsValue3',1);
      return false;
   }
   if ( getDigit(_value,1) ==127)
   	{
	 // alertError('CheckPrimaryDnsValue4',1);
      return false;

   }
   
   if ( !checkDigitRange(_value,2,0,255) ) {
     // alertError('CheckPrimaryDnsValue5',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,3,0,255) ) {
     // alertError('CheckPrimaryDnsValue6',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,4,1,254) ) {
     // alertError('CheckPrimaryDnsValue7',1);
      return false;
   }
	return true;
}

function checkSecondaryDNS(_value)
{

  if ( validateKey(_value) == 0) {
  //alertError('CheckSecondaryDnsValue2',1);
  return false;
   }
   
   if ( !checkDigitRange(_value,1,1,223) ) {
     // alertError('CheckSecondaryDnsValue3',1);
      return false;
   }
   
   if ( getDigit(_value,1) ==127)
   	{
	 // alertError('CheckSecondaryDnsValue4',1);
      return false;

   }
   
   if ( !checkDigitRange(_value,2,0,255) ) {
     // alertError('CheckSecondaryDnsValue5',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,3,0,255) ) {
     // alertError('CheckSecondaryDnsValue6',1);
      return false;
   }
   
   if ( !checkDigitRange(_value,4,1,254) ) {
     // alertError('CheckSecondaryDnsValue7',1);
      return false;
   }
	return true;
}

function checkHex(str)
{
	for(var i=0; i<str.length; i++)
		{
			if((str.charAt(i) >= '0' && str.charAt(i) <= '9')||
				(str.charAt(i) >= 'a' && str.charAt(i) <= 'f')||
				(str.charAt(i) >= 'A' && str.charAt(i) <= 'F'))
				{continue;}
			else {return false;}
		}
}

function checkSecurityKey(str)
{
	if(getId('SELECT_Encryptstrength').value == '64bits')
		{
			if(checkHex(str) == false || str.length != 10)
				{
					//alertError("CheckSecurityKey1");
					return false;
				}
		}
	else if(getId('SELECT_Encryptstrength').value == '128bits')
		{
			if(checkHex(str) == false || str.length != 26)
				{
					//alertError("CheckSecurityKey2");
					return false;
				}
		}

}

function checkWord(s)
{ 

	var patrn=/(\|)|(\\)|(\`)/;
	if(patrn.test(s)==true)
	{
		//alertError('CheckPassphrase');
		return false;
	}
	else
	{
		return true;
	}

}

function checkBlank(str)
{
	if(str.charAt(str.length-1) == " " || str.charAt(0) == " ")
		{
			//alertError('CheckBlank');
		}
	str = str.replace(/(^\s*)|(\s*$)/g,"");
	return str;
}

function checkPassphrase(str)
{
	
	
	if(str.length == 64)
		{
			if(checkHex(str) == false)
				{
					///alertError("CheckSecurityPwd1");
					return false;
				}
		}
	if(str.length < 8)
		{
			//alertError("CheckSecurityPwd2");
			return false;
		}
		
	if(checkWord(str)==false)
		return false;
	else
		return true;
}

function checkNameBlank(str)
{
	var patrn = /^\S{1,100}$/;
	var tmp = str;
	if(str == "")
	{
		return false;
	}
	else
	{   
	    if(!patrn.exec(str)) 
		{
			str = str.replace(/\s/g,"");
		}
		if (tmp == str) 
		{
		    return true;
		}
		else
		{
			return str;
		}
	}
}


function isSameSubNet(lan1Ip, lan1Mask, lan2Ip, lan2Mask)
{

   var count = 0;

   lan1a = lan1Ip.split('.');
   lan1m = lan1Mask.split('.');
   lan2a = lan2Ip.split('.');
   lan2m = lan2Mask.split('.');

   for (i = 0; i < 4; i++) {
      l1a_n = parseInt(lan1a[i]);
      l1m_n = parseInt(lan1m[i]);
      l2a_n = parseInt(lan2a[i]);
      l2m_n = parseInt(lan2m[i]);
      if ((l1a_n & l1m_n) == (l2a_n & l2m_n))
         count++;
   }
   if (count == 4)
      return true;
   else
      return false;
}


function checkURL(s)
{ 

	var patrn=/^\w+$/;
	var patrn2=/\./;
	var patrn3=/\//;

	for (var i = 0; i < s.length; i++)
	{ 
		if (!patrn.exec(s.substr(i, 1)))
		{
			if(!patrn2.exec(s.substr(i, 1)))
			{
				if(!patrn3.exec(s.substr(i, 1)))
				{
					alert('###nomatch');
					return false;
				}
			}
		}
		
	}
	
	return true;
}

function checkSSID(s)
{ 

	var patrn=/[^A-Za-z0-9-_]/;

    if(patrn.test(s)==true)
	{
		//alertError('CheckSSID',1);
		return false;
	}
	else
		return true;

}

function checkDomain(s)
{ 

	var patrn=/^[^ !"#$%&'()*+,\/:;<=>?@\[\\\]^`{|}~]*$/;

    if(patrn.test(s)==false)
	{
		return false;
	}
	else
		return true;

}

function checkMac(macValue)
{
	var re_f = /[fF]{2}:[fF]{2}:[fF]{2}:[fF]{2}:[fF]{2}:[fF]{2}/;
	var re_z = /[0]{2}:[0]{2}:[0]{2}:[0]{2}:[0]{2}:[0]{2}/;
	if ( re_f.test(macValue)){
		return false;
	}
	else if(re_z.test(macValue))
	{
		return false;
	}

	return true;
}

function isSameSubNet(lan1Ip, lan1Mask, lan2Ip, lan2Mask)
{

   var count = 0;

   lan1a = lan1Ip.split('.');
   lan1m = lan1Mask.split('.');
   lan2a = lan2Ip.split('.');
   lan2m = lan2Mask.split('.');

   for (i = 0; i < 4; i++) {
      l1a_n = parseInt(lan1a[i]);
      l1m_n = parseInt(lan1m[i]);
      l2a_n = parseInt(lan2a[i]);
      l2m_n = parseInt(lan2m[i]);
      if ((l1a_n & l1m_n) == (l2a_n & l2m_n))
         count++;
   }
   if (count == 4)
      return true;
   else
      return false;
}
