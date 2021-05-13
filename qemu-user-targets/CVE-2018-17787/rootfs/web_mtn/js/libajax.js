function XmlDocument(xml)
{
	this.XDoc = xml;
	this.AnchorNode = null;
}
XmlDocument.prototype =
{
	Serialize : function ()
	{
		var xmlString;
		if (window.ActiveXObject) xmlString = this.XDoc.xml;
		else xmlString = (new XMLSerializer()).serializeToString(this.XDoc);
		return xmlString;
	},
	dbgdump : function ()
	{
		var ow = window.open();
		ow.document.open("content-type: text/xml");
		ow.document.write(this.Serialize());
	},
	Find : function (path, create)
	{
		var currnode = this.XDoc;
		var token = path.split("/");
		var i, j;
		var tagname, seq;

		/* User anchor as current node if it exist.
		 * Use the root as current if absolute path. */
		if (this.AnchorNode && token[0]!="") currnode = this.AnchorNode;
		else currnode = this.XDoc;

		/* Walk through the tokens */
		for (i=0; i<token.length; i+=1)
		{
			/* skip the empty token */
			if (token[i] == "") continue;
			/* parse the tag name & seq# */
			if (token[i].indexOf(":")<0)
			{
				tagname = token[i];
				seq = 1;
			}
			else
			{
				var tags = token[i].split(":");
				tagname = tags[0];
				seq = tags[1];
			}

			/* find the matching tag. */
			var tagseq = 0;
			var found = 0;
			var tagnode;
			for (j=0; j<currnode.childNodes.length; j+=1)
			{
				if (currnode.childNodes[j].nodeName == tagname)
				{
					tagseq+=1;
					if (seq == tagseq)
					{
						currnode = currnode.childNodes[j];
						found+=1;
						break;
					}
				}
			}
			if (found) continue;
			if (!create) return null;

			/* create the node */
			for (j=0; j < (seq - tagseq); j+=1)
			{
				tagnode = this.XDoc.createElement(tagname);
				currnode.appendChild(tagnode);
			}
			currnode = tagnode;
		}
		return currnode;
	},
	Anchor : function (path)
	{
		var old = this.AnchorNode;
		if (path && path!=="") this.AnchorNode = this.Find(path, false);
		return old;
	},
	AnchorPop: function (old)
	{
		this.AnchorNode = old;
	},
	GetDOMNodeValue : function (node)
	{
		if (node.hasChildNodes()) return node.firstChild.nodeValue;
		return "";
	},
	GetDOMNodeAttr : function (node, attr)
	{
		if (node.hasChildNodes()) 
		{
			if(node.firstChild.nodeType == 1)
				return node.firstChild.getAttribute(attr);
			else
				return node.getAttribute(attr);
		}
		return "";
	},
	SetDOMNodeValue : function (node, value)
	{
		if (node.hasChildNodes()) node.firstChild.nodeValue = value;
		else
		{
			var valnode = this.XDoc.createTextNode(value);
			node.appendChild(valnode);
		}
	},
	SetDOMNodeAttr : function (node, attr, value)
	{
		if (node.hasChildNodes()) 
		{
			if(node.firstChild.nodeType == 1)
				node.firstChild.setAttribute(attr,value);
			else
				node.setAttribute(attr,value);
		}
	},
	Del : function (path)
	{
		var node = this.Find(path);
		if (node == null) return false;
		var pnode = node.parentNode;
		pnode.removeChild(node);
		return true;
	},
	Get : function (path)
	{
		var lasttagname = path.split("/") [path.split("/").length-1];
		var AES_Desryption = false;
		//if(lasttagname=="Password" || lasttagname=="IPv6_PppoePassword" || lasttagname=="Key" || lasttagname=="AdminPassword" || lasttagname=="AccountPassword" || lasttagname=="RadiusSecret1" || lasttagname=="RadiusSecret2" || lasttagname=="MK")
		//	AES_Desryption = true;

		var node;

		if (path.indexOf("#") < 0)
		{
			/* return the value of the node */
			node = this.Find(path, false);
			if (node)
			{
				if(AES_Desryption)
				{
					return AES_Decrypt128(this.GetDOMNodeValue(node));
				}
				return this.GetDOMNodeValue(node);
			}
			return "";
		}

		/* If the path is end with '#', count the number of node. */
		var count = 0;
		var tokens = path.split("#");
		/* Find the target */
		node = this.Find(tokens[0]);
		if (node)
		{
			var nodeName = node.nodeName;
			node = node.parentNode;
			for (var i=0; i<node.childNodes.length; i+=1)
				if (node.childNodes[i].nodeName == nodeName)
					count+=1;
		}
		return count;
	},
	GetAttr : function (path, attr)
	{
		var node;
		if (path.indexOf("#") < 0)
		{
			/* return the value of the node */
			node = this.Find(path, false);
			if (node) return this.GetDOMNodeAttr(node,attr);
		}
		return "";
	},
	Set : function (path, value)
	{
		var lasttagname = path.split("/") [path.split("/").length-1];
		if(lasttagname=="Password" || lasttagname=="IPv6_PppoePassword" || lasttagname=="Key" || lasttagname=="AdminPassword" || lasttagname=="AccountPassword" || lasttagname=="RadiusSecret1" || lasttagname=="RadiusSecret2" || lasttagname=="MK")
			value = AES_Encrypt128(value);

		var node = this.Find(path, true);
		if (node == null)
		{
			alert("BUG(Set): this should not happen !!");
			return null;
		}
		if (value == null)	{	value = "";	}
		this.SetDOMNodeValue(node, value);
		return node;
	},
	SetAttr : function (path, attr, value)
	{
		var node = this.Find(path, true);
		if (node == null)
		{
			alert("BUG(SetAttr): this should not happen !!");
			return null;
		}
		this.SetDOMNodeAttr(node,attr,value);
		return node;
	},
	Add : function (path, value)
	{
		var node = this.Find(path, false);
		if (node == null)
		{
			node = this.Find(path, true);
			this.SetDOMNodeValue(node, value);
			return node;
		}
		var pnode = node.parentNode;
		var newnode = this.XDoc.createElement(node.nodeName);
		this.SetDOMNodeValue(newnode, value);
		pnode.appendChild(newnode);
		return newnode;
	},
	GetPathByTarget : function (root, node, target, value, create)
	{
		var i, j;
		var pnode, nnode, tnode;
		var found = false;
		var seq = 0;

		/* Get the parent node first. */
		pnode = this.Find(root, create);
		if (pnode == null) return null;
		/* Walk through the 'node' */
		for (i=0; i<pnode.childNodes.length && !found; i+=1)
		{
			if (pnode.childNodes[i].nodeName == node)
			{
				seq+=1;
				nnode = pnode.childNodes[i];
				for (j=0; j<nnode.childNodes.length; j+=1)
				{
					if (nnode.childNodes[j].nodeName == target)
					{
						tnode = nnode.childNodes[j];
						if (this.GetDOMNodeValue(tnode) == value) found+=1;
						break;
					}
				}
				if (found)
				{
					return root+"/"+node+":"+seq;
				}
			}
		}
		if (create)
		{
			seq+=1;
			var newpath = root+"/"+node+":"+seq+"/"+target;
			this.Set(newpath, value);
			return root+"/"+node+":"+seq;
		}
		return null;
	}
}
Object.extend = function(destination,source){
        for (var property in source){
                destination[property] = source[property];
        }
        return destination;
}
function isArray(obj) {
  return Object.prototype.toString.call(obj) === '[object Array]';
}
function isString(obj) {
  return Object.prototype.toString.call(obj) === '[object String]';
}
function StringDoc(xml)
{
	if(xml==undefined || xml==null)
	{
		this.XDoc = "{}";
	}else{
		this.XDoc = xml;		
	}
	this.AnchorNode = null;
}
Object.extend(StringDoc.prototype,
{
	localjson: {},
	
	GetJSONNodeValue : function (node)
	{
		if(isString(node)){
				return node;
		}
		return "";
	},
	Set : function(path,va)
	{
		var curstr = this.XDoc;
		
		var currnode = JSON.parse(curstr);  //字符串转对象
		var snode = currnode;
		var token = path.split("/");
		var value = ((va==undefined)? "":va);
		var seq;
		//if(va==undefined)
		{
			for (i=0; i<token.length; i+=1)
			{
				if (token[i] == "") continue;
				tagname = token[i];
				seq = 0;
				//按层级查找遍历有没有
				for(var j in currnode)
				{
					if(j==tagname)
					{
						if(i < (token.length-1))
						{
							if(currnode[j]==null||currnode[j]=="")currnode[j]={};
							currnode = currnode[j];
						}
						seq = 1;
						break;
					}
				}
				//有就进入下一轮层级遍历
				
				//进入到最后一级赋值跳出
				if(i == (token.length-1))
				{
					currnode[tagname] = value;
				}else{
					//没有就扩充，进入下一轮再扩充
					if(seq == 0)
					{
						currnode[tagname] = {};
						currnode = currnode[tagname];
					}					
				}
			}
		}
		this.XDoc = JSON.stringify(snode);
		return this.XDoc;
	},
	GetObjectNode : function (path)
	{
		var lasttagname_obj = path.split("/") [path.split("/").length-1];
		var node_obj;

		if (path.indexOf("#") < 0)
		{
			node_obj = this.Find(path, false);
			if (node_obj)
			{
				return node_obj;
			}
		}
		return null;
	},
	Get : function(path)
	{
		var lasttagname = path.split("/") [path.split("/").length-1];
		var AES_Desryption = false;
		//if(lasttagname=="Password" || lasttagname=="IPv6_PppoePassword" || lasttagname=="Key" || lasttagname=="AdminPassword" || lasttagname=="AccountPassword" || lasttagname=="RadiusSecret1" || lasttagname=="RadiusSecret2" || lasttagname=="MK")
		//	AES_Desryption = true;

		var node;

		if (path.indexOf("#") < 0)
		{
			/* return the value of the node */
			node = this.Find(path, false);
			if (node)
			{
				if(AES_Desryption)
				{
					return AES_Decrypt128(this.GetJSONNodeValue(node));
				}
				return this.GetJSONNodeValue(node);
			}
			return "";
		}

		/* If the path is end with '#', count the number of node. */
		var count = 0;
		var tokens = path.split("#");
		/* Find the target */
		node = this.Find(tokens[0]);
		if (node)
		{
			if(isArray(node))
			{
				count = node.length;
			}
		}
		return count;		
	},
	Find : function (path, create)
	{
		//console.log("parse string : "+ this.XDoc.toString());
		var currnode = JSON.parse(this.XDoc.toString());//this.XDoc;
		if(currnode) localjson = currnode;
		else return null;
		
		
		var token = path.split("/");
		//console.log("path : "+path);
		//console.log("token : "+token[0] + " token.length : "+token.length);
		var i, j;
		var tagname, seq;

		/* User anchor as current node if it exist.
		 * Use the root as current if absolute path. */
		//if (this.AnchorNode && token[0]!="") currnode = this.AnchorNode;
		//else currnode = this.XDoc;

		/* Walk through the tokens */
		for (i=0; i<token.length; i+=1)
		{
			/* skip the empty token */
			if (token[i] == "") continue;
			/* parse the tag name & seq# */
			if (token[i].indexOf(":")<0)
			{
				tagname = token[i];
				seq = 0;
			}
			else
			{
				var tags = token[i].split(":");
				tagname = tags[0];
				seq = tags[1];
			}

			/* find the matching tag. */
			var tagseq = 0;
			var found = 0;
			var tagnode;
			var k=0;
			for(var j in currnode)
			{
				//console.log("j : "+ j + " type : " +Object.prototype.toString.call(currnode[j]) );
				if(j == tagname)
				{
					if(isArray(currnode[j]))
					{
						if(seq == 0)
						{
							currnode = currnode[j];
						}else
						{
							//console.log("seq : " + seq + " ," + j + " : "+ JSON.stringify(currnode[j]) );
							//console.log("k : " + k + " : "+ currnode[j].length );
							for(k=0; k<currnode[j].length; k+=1)
							{
								if(seq == (k+1))
								{
									currnode = currnode[j][k];
									break;
								}								
							}
						}
					}else{
						//console.log("j : "+ j + " tagname : " +tagname);
						currnode = currnode[j];
					}
					found+=1;
					break;
				}
			}
			if (found) continue;
			if (!create) return null;

			/* create the node */
			/*
			for (j=0; j < (seq - tagseq); j+=1)
			{
				tagnode = this.XDoc.createElement(tagname);
				currnode.appendChild(tagnode);
			}
			currnode = tagnode;
			*/
		}
		//console.log("end currnode: "+ JSON.stringify(currnode) );
		return currnode;
	}
});

function HTTPClient(){}
HTTPClient.prototype =
{
	debug: false,
	__httpRequest : null,
	requestMethod : "POST",
	requestAsyn : true,
	returnXml : true,
	__header : null,
	onSend : null,
	onCallback : null,
	onError : function (msg)
	{
		if (!msg) throw (msg);
	},
	__callback : function()
	{
		if(!this.__httpRequest)
		{
			this.onError("Error : Request return error("+ this.__httpRequest.status +").");
		}
		else
		{
			//alert("this.__httpRequest.readyState ="+this.__httpRequest.readyState);
			//alert("第二个this.__httpRequest.status ="+this.__httpRequest.status);
			
			if (this.__httpRequest.readyState == 2)
			{
				if (this.onSend) this.onSend();
			}
			else if (this.__httpRequest.readyState == 4)
			{
				if (this.__httpRequest.status == 200)
				{
					if (this.onCallback)
					{
						if (this.returnXml)
						{
							var xdoc = new XmlDocument(this.__httpRequest.responseXML);
							if (xdoc != null)
							{
								if (this.debug) xdoc.dbgdump();
								this.onCallback(xdoc);
							}
							else this.onError("Error : unable to create XmlDocument().");
						}
						else this.onCallback(this.__httpRequest.responseText);
					}
				}
				else if (this.__httpRequest.status == 500)
				{
					window.location.href = "/";
				}
				else
				{
					this.onError("Error : Request return error("+ this.__httpRequest.status +").");
				}
			}
		}
	},
	createRequest : function()
	{
		try
		{
			// For Mazilla or Safari or IE7
			this.__httpRequest = new XMLHttpRequest();
		}
		catch (e)
		{
			var __XMLHTTPS = new Array( "MSXML2.XMLHTTP.5.0",
										"MSXML2.XMLHTTP.4.0",
										"MSXML2.XMLHTTP.3.0",
										"MSXML2.XMLHTTP",
										"Microsoft.XMLHTTP" );
			var __Success = false;
			for (var i = 0; i < __XMLHTTPS.length && __Success == false; i+=1)
			{
				try
				{
					this.__httpRequest = new ActiveXObject(__XMLHTTPS[i]);
					__Success = true;
				}
				catch (e) { }
				if (!__Success)
				{
					this.onError("Browser do not support Ajax.");
				}
			}
		}
	},
	sendRequest : function(requestUrl, payload)
	{
		if (!this.__httpRequest) this.createRequest();
		var self = this;
		this.__httpRequest.onreadystatechange = function() {self.__callback();}
		if (!requestUrl)
		{
			this.onError("Error : Invalid request URL.");
			return;
		}
		
		//For IOS 6 Safari browser, Ajax with POST action would cache.
		if(/iPhone/.test(navigator.userAgent) || /iPad/.test(navigator.userAgent) || /iPod/.test(navigator.userAgent))
		{
			var dummy = new Date().getTime();
			var ret = requestUrl.indexOf('?');
			if(ret != -1)
				requestUrl += "&dummy=" + dummy;
			else
				requestUrl += "?dummy=" + dummy;
		}
					//alert(requestUrl);
			//alert(payload);
		this.__httpRequest.open(this.requestMethod, requestUrl, this.requestAsyn);
		if (this.__header)
		{
			for (var i = 0; i < this.__header.length; i+=1)
			{
				if (this.__header[i].value != "")
					this.__httpRequest.setRequestHeader(this.__header[i].name, this.__header[i].value);
			}
		}
		if (this.requestMethod == "GET" || this.requestMethod == "get")
			this.__httpRequest.send(null);
		else
		{
			if (!payload)
			{
				this.onError("Error : Invalid payload for POST.");
				return;
			}
			
			try {

              this.__httpRequest.send(payload);
			 
           } catch (e) {
			
           }		
			
		}
	},
	getResponseHeader : function(header)
	{
		if (!header)
		{
			this.onError("Error : You must assign a header name to get.");
			return "";
		}
		if (!this.__httpRequest)
		{
			this.onError("Error : The HTTP request object is not exist.");
			return "";
		}
		return this.__httpRequest.getResponseHeader(header);
	},
	getAllResponseHeaders : function()
	{
		if (this.__httpRequest) return this.__httpRequest.getAllResponseHeaders();
		else this.onError( "Error : The HTTP request object is not exist." );
	},
	setHeader : function(header, value)
	{
		if (header && value)
		{
			if (!this.__header) this.__header = new Array();
			var tmpHeader = new Object();
			tmpHeader.name = header;
			tmpHeader.value = value;
			this.__header[ this.__header.length ] = tmpHeader;
		}
	},
	clearHeader : function (header)
	{
		if (!this.__header) return;
		if (!header) return;
		for (var i = 0; i < this.__header.length; i+=1)
		{
			if (this.__header[i].name == header)
			{
				this.__header.value = "";
				return;
			}
		}
	},
	clearAllHeaders : function()
	{
		if (!this.__header) return;
		this.__header = null;
	},
	release : function()
	{
		this.__httpRequest = null;
		this.requestMethod = "POST";
		this.requestAsyn = true;
		this.returnXml = true;
		this.__header = null;
		this.onCallback = null;
		this.onSend = null;
	}
};



function GetAjaxObj(name)
{
	var i=0;
	var AJAX_OBJ = new Array();
	var ajax_num = AJAX_OBJ.length;
	if (ajax_num > 0)
	{
		for (i=0; i<ajax_num; i+=1)
		{
			if (AJAX_OBJ[i][0] == name)
			{
				return AJAX_OBJ[i][1];
			}
		}
	}
	AJAX_OBJ[ajax_num] = new Array();
	AJAX_OBJ[ajax_num][0] = name;
	AJAX_OBJ[ajax_num][1] = new HTTPClient();

	return AJAX_OBJ[ajax_num][1];
}

/*function OnunloadAJAX()
{
	var i;
	for (i=0; i<AJAX_OBJ.length; i+=1)
	{
		AJAX_OBJ[i][0]="";
		AJAX_OBJ[i][1].release();
		delete AJAX_OBJ[i][1];
		delete AJAX_OBJ[i];
	}
}*/
