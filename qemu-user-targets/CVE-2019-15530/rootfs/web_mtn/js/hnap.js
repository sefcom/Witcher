function changText(str)
{
	var ar = str.split('');
	for(var i=0;i<ar.length;i++)
	{
		if(ar[i].charCodeAt(0)>=65&&ar[i].charCodeAt(0)<=90)	{	ar[i]= ar[i].toLowerCase();		}
		else	{	ar[i] = ar[i].toUpperCase();	}
	}
	str = "";
	for(var i=0;i<ar.length;i++)	{	str += ar[i];	}
	return str;
}
function onError(msg)
{
	if (!msg) throw (msg);
}
function GetJsonData(xml, hnap)
{
	
	try{
		var json = JSON.parse(xml);			
	}catch(e){
		try{
			var json  = eval('('+xml+')');			
		}catch(E){
			//console.log("Form : "+hnap+". Browser can not parse cgi data : " + xml);
			onError("Form : "+hnap+". Browser can not parse cgi data : " + xml);
			window.location.href = "/";
			return new StringDoc("{}");//xml;
		}
	}
	for(var j in json)
	{
		//console.log(j + " json : "+ JSON.stringify(json[j]));
		if((j=="Envelope"))
		{
			for(var k in json[j])
			{
				//console.log(k + " json : "+ JSON.stringify(json[j][k]));
				if((k=="Body"))
				{						
					json = json[j][k];break;								
				}
			}
		}
	}
	xml = JSON.stringify(json);
	//console.log(j + " xml : "+ xml);
	return new StringDoc(xml);//xml;
}

function HNAP_XML(){}
HNAP_XML.prototype =
{
	XML_hnap: null,
	GetXML: function(hnap, input_array)
	{
		var self = this;
		var ajaxObj = GetAjaxObj("GetXML");
        ajaxObj.createRequest();
        ajaxObj.onCallback =
        function (xml)
        {
			if(ajaxObj.returnXml)
			{
				xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
				if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];
				if(input_array != null)
				{           	
					for(var i=0; i < input_array.length; i=i+2)
					{xml.Set(hnap+"/"+input_array[i], input_array[i+1]);}
				}
			}else{
				xml = GetJsonData(xml, hnap);				
			}
			self.XML_hnap = xml;
			ajaxObj.release();
        }
        ajaxObj.setHeader("Content-Type", "text/xml");
        ajaxObj.requestMethod = "GET";
		ajaxObj.returnXml = false; //return JSON data
        ajaxObj.requestAsyn = false;
        ajaxObj.sendRequest("/hnap/"+ hnap + ".json?v=TimeStamp_QzwsxDcRfvTGByHn");
		if(hnap.substr(0,3)=="Get" && hnap!="GetMultipleHNAPs")
		{
            var ajaxObj = GetAjaxObj("GetXMLFromHNAP");
            ajaxObj.createRequest();
            ajaxObj.onCallback =
            function (xml)
            {
				if(ajaxObj.returnXml)
				{
					xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
					if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];					
				}else{
					xml = GetJsonData(xml, hnap);
				}
				self.XML_hnap = xml;
				ajaxObj.release();			
				//CallbackFunc(xml);
            }
			ajaxObj.returnXml = false; //return JSON data
            ajaxObj.requestAsyn = false;
            ajaxObj.clearAllHeaders();
			ajaxObj.setHeader("Content-Type", "application/json");
            //ajaxObj.setHeader("Content-Type", "text/xml");
            ajaxObj.setHeader("Accept", "text/xml");
            ajaxObj.setHeader("SOAPACTION", '"http://purenetworks.com/HNAP1/'+hnap+'"');
			/*try {
              var PrivateKey = localStorage.getItem('PrivateKey');
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   var PrivateKey=$.cookie('PrivateKey'); 
			
			if(PrivateKey == null) PrivateKey = "withoutloginkey"; //For login action or another action without login.
			//The current time length should fit the size of integer in Code. The period of the current time is almost 30 years.
			var current_time = new Date().getTime();
			current_time = Math.floor(current_time) % 2000000000000;
			var URI = '"http://purenetworks.com/HNAP1/'+hnap+'"';
			var auth = hex_hmac_md5(PrivateKey, current_time.toString()+URI);
			auth = changText(auth);
			ajaxObj.setHeader("HNAP_AUTH", auth + " " + current_time);
            ajaxObj.sendRequest("/HNAP1/", this.XML_hnap.XDoc);	
			return 	this.XML_hnap;
		}		
		return 	this.XML_hnap;
	},
	GetXMLAsync: function(hnap, input_array, behavior, CallbackFunc)
	{
		var self = this;
		var ajaxObj = GetAjaxObj("GetXMLAsync");
        ajaxObj.createRequest();
        ajaxObj.onCallback =
        function (xml)
        {
			if(ajaxObj.returnXml)
			{
				xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
				if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];
				if(input_array != null)
				{           	
					for(var i=0; i < input_array.length; i=i+2)
					{xml.Set(hnap+"/"+input_array[i], input_array[i+1]);}
				}
			}else{
				xml = GetJsonData(xml, hnap);
			}
			self.XML_hnap = xml;
			ajaxObj.release();
        	switch (behavior)
        	{
        		case "GetXML":		CallbackFunc(xml);	break;
        		case "GetValue":	GetXML_Async(self.XML_hnap, hnap, input_array, CallbackFunc);	break;
        	}
        }
		ajaxObj.returnXml = false; //return JSON data
        ajaxObj.setHeader("Content-Type", "text/xml");
        ajaxObj.requestMethod = "GET";
        ajaxObj.requestAsyn = true;
        ajaxObj.sendRequest("/hnap/"+ hnap + ".json?v=TimeStamp_QzwsxDcRfvTGByHn");
        
        function GetXML_Async(selfhnap, hnap, input_array, CallbackFunc)
        {
        	var self = this;
			var ajaxObj = GetAjaxObj("GetXMLAsync");
	        ajaxObj.createRequest();
	        ajaxObj.onCallback =
	        function (xml)
	        {
				if(ajaxObj.returnXml)
				{
					xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
					if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];
					if(input_array != null)
					{           	
						for(var i=0; i < input_array.length; i=i+2)
						{xml.Set(hnap+"/"+input_array[i], input_array[i+1]);}
					}
					self.XML_hnap = xml;					
				}else{
					xml = GetJsonData(xml, hnap);
				}
				ajaxObj.release();			
				CallbackFunc(xml);
	        }
			ajaxObj.returnXml = false; //return JSON data
	        ajaxObj.requestAsyn = true;
            ajaxObj.clearAllHeaders();
			ajaxObj.setHeader("Content-Type", "application/json");
            //ajaxObj.setHeader("Content-Type", "text/xml");
            //ajaxObj.setHeader("Accept", "text/xml");
			ajaxObj.setHeader("Accept", "application/json");
		
            ajaxObj.setHeader("SOAPACTION", '"http://purenetworks.com/HNAP1/'+hnap+'"');
			/*try {
               var PrivateKey = localStorage.getItem('PrivateKey');
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   var PrivateKey=$.cookie('PrivateKey');  
			
			if(PrivateKey == null) PrivateKey = "withoutloginkey"; //For login action or another action without login.
			//The current time length should fit the size of integer in Code. The period of the current time is almost 30 years.
			var current_time = new Date().getTime();
			current_time = Math.floor(current_time) % 2000000000000;
			var URI = '"http://purenetworks.com/HNAP1/'+hnap+'"';
			var auth = hex_hmac_md5(PrivateKey, current_time.toString()+URI);
			auth = changText(auth);
			ajaxObj.setHeader("HNAP_AUTH", auth + " " + current_time);
            ajaxObj.sendRequest("/HNAP1/", selfhnap.XDoc);
        }
	},
	SetXML: function(hnap, xml)
	{
		var self = this;
        var ajaxObj = GetAjaxObj("SetXML");
        ajaxObj.createRequest();
        ajaxObj.onCallback =
        function (xml)
        {
			if(ajaxObj.returnXml)
			{
				xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
				if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];
				self.XML_hnap = xml;
			}else{
				xml = GetJsonData(xml, hnap);
				self.XML_hnap = xml;
			}
			ajaxObj.release();
		}
		ajaxObj.returnXml = false; //return JSON data
        ajaxObj.requestAsyn = false;
        ajaxObj.clearAllHeaders();
		ajaxObj.setHeader("Content-Type", "application/json");
        //ajaxObj.setHeader("Content-Type", "text/xml");
        ajaxObj.setHeader("Accept", "text/xml");
        ajaxObj.setHeader("SOAPACTION", '"http://purenetworks.com/HNAP1/'+hnap+'"');
		
		/*try {
              var PrivateKey = localStorage.getItem('PrivateKey');
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   var PrivateKey=$.cookie('PrivateKey');  
		if(PrivateKey == null) PrivateKey = "withoutloginkey"; //For login action or another action without login.
		//The current time length should fit the size of integer in Code. The period of the current time is almost 30 years.
		var current_time = new Date().getTime();
		current_time = Math.floor(current_time) % 2000000000000;
		var URI = '"http://purenetworks.com/HNAP1/'+hnap+'"';
		var auth = hex_hmac_md5(PrivateKey, current_time.toString()+URI);
		auth = changText(auth);
		ajaxObj.setHeader("HNAP_AUTH", auth + " " + current_time);
        ajaxObj.sendRequest("/HNAP1/", xml.XDoc);
        return 	this.XML_hnap;
	},
	SetXMLAsync: function(hnap, xml, CallbackFunc)
	{
		var self = this;
        var ajaxObj = GetAjaxObj("SetXMLAsync");
        ajaxObj.createRequest();
        ajaxObj.onCallback =
        function (xml)
        {
			if(ajaxObj.returnXml)
			{
				xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
				if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];				
			}else{
				xml = GetJsonData(xml, hnap);
			}
			ajaxObj.release();			
			CallbackFunc(xml);
        }
		ajaxObj.returnXml = false; //return JSON data
        ajaxObj.requestAsyn = true;
        ajaxObj.clearAllHeaders();
		ajaxObj.setHeader("Content-Type", "application/json");
       // ajaxObj.setHeader("Content-Type", "text/xml");
		//ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        //ajaxObj.setHeader("Accept", "text/xml");
       ajaxObj.setHeader("Accept", "application/json");		
        ajaxObj.setHeader("SOAPACTION", '"http://purenetworks.com/HNAP1/'+hnap+'"');
		/*try {
              var PrivateKey = localStorage.getItem('PrivateKey');
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }*/
		   //alert(xml.XDoc);
		 var PrivateKey=$.cookie('PrivateKey');  
		
		if(PrivateKey == null) PrivateKey = "withoutloginkey"; //For login action or another action without login.
		//The current time length should fit the size of integer in Code. The period of the current time is almost 30 years.
		var current_time = new Date().getTime();
		current_time = Math.floor(current_time) % 2000000000000;
		var URI = '"http://purenetworks.com/HNAP1/'+hnap+'"';
		var auth = hex_hmac_md5(PrivateKey, current_time.toString()+URI);
		auth = changText(auth);
		ajaxObj.setHeader("HNAP_AUTH", auth + " " + current_time);
        ajaxObj.sendRequest("/HNAP1/", xml.XDoc);
	},
	SetJSONAsync: function(hnap, xml, json, CallbackFunc)
	{
		var self = this;
        var ajaxObj = GetAjaxObj("SetXMLAsync");
        ajaxObj.createRequest();
        ajaxObj.onCallback =
        function (xml)
        {
			if(ajaxObj.returnXml)
			{
				xml.AnchorNode = xml.XDoc.getElementsByTagName("soap:Body")[0];
				if(xml.AnchorNode==undefined || xml.AnchorNode==null) xml.AnchorNode = xml.XDoc.getElementsByTagName("Body")[0];			
			}else{
				xml = GetJsonData(xml, hnap);
			}
			ajaxObj.release();			
			CallbackFunc(xml);
        }
		ajaxObj.returnXml = false; //return JSON data
        ajaxObj.requestAsyn = true;
        ajaxObj.clearAllHeaders();
        ajaxObj.setHeader("Content-Type", "application/json");
		//ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.setHeader("Accept", "text/xml");
        ajaxObj.setHeader("SOAPACTION", '"http://purenetworks.com/HNAP1/'+hnap+'"');
		/*try {
               var PrivateKey = localStorage.getItem('PrivateKey');
           } catch (e) {
              alert("您的浏览器属于无痕浏览模式，无法进行正常配置，请您将您的浏览器切换成非无痕浏览模式再进行登录");
			  return ;
           }
		*/
		var PrivateKey=$.cookie('PrivateKey');  
		if(PrivateKey == null) PrivateKey = "withoutloginkey"; //For login action or another action without login.
		//The current time length should fit the size of integer in Code. The period of the current time is almost 30 years.
		var current_time = new Date().getTime();
		current_time = Math.floor(current_time) % 2000000000000;
		var URI = '"http://purenetworks.com/HNAP1/'+hnap+'"';
		var auth = hex_hmac_md5(PrivateKey, current_time.toString()+URI);
		auth = changText(auth);
		ajaxObj.setHeader("HNAP_AUTH", auth + " " + current_time);
		ajaxObj.sendRequest("/HNAP1/", JSON.stringify( json ));
	}
};
