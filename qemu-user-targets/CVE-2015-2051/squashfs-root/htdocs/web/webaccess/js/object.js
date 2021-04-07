var encoding_char = new Array('$', '&', '+', ',', '/', ':', ';', '=', '?', '@', ' ', '"', '<', '>', '#', '%', '{', '}', '|', '\\', '^', '~', '[', ']', '`');
var lang_obj, msg_obj, htm_obj;
var subnet_mask_value = new Array(0, 128, 192, 224, 240, 248, 252, 254, 255);	
var null_mac = "00:00:00:00:00:00";

var tx_11b_rate = [{ value: "1", text: "1M"},
	            	 { value: "2", text: "2M"},
	            	 { value: "3", text: "5.5M"},
	            	 { value: "4", text: "11M"}];	            	
	            	 
var tx_11g_rate = [{ value: "5", text: "6M"},
	            	 { value: "6", text: "9M"},
	            	 { value: "7", text: "12M"},
	            	 { value: "8", text: "18M"},
	            	 { value: "9", text: "24M"},
	            	 { value: "10", text: "36M"},
	            	 { value: "11", text: "48M"},
	            	 { value: "12", text: "54M"}];
	            	 
var tx_11n_rate = [{ value: "13", text: "MCS0"},
	            	 { value: "14", text: "MCS1"},
	            	 { value: "15", text: "MCS2"},
	            	 { value: "16", text: "MCS3"},
	            	 { value: "17", text: "MCS4"},
	            	 { value: "18", text: "MCS5"},
	            	 { value: "19", text: "MCS6"},
	            	 { value: "20", text: "MCS7"},
	            	 { value: "21", text: "MCS8"},
	            	 { value: "22", text: "MCS9"},
	            	 { value: "23", text: "MCS10"},
	            	 { value: "24", text: "MCS11"},
	            	 { value: "25", text: "MCS12"},
	            	 { value: "26", text: "MCS13"},
	            	 { value: "27", text: "MCS14"},
	            	 { value: "28", text: "MCS15"}];

function get_default_lang(which_xml, which_id){	
	var this_lang = load_xml("default_xml/" + which_xml + ".xml");
	
	return get_node_value(this_lang, which_id);
}

function get_default_node(which_xml, which_id){	
	var this_lang = load_xml("default_xml/" + which_xml + ".xml");
	
	return this_lang.getElementsByTagName(which_id)[0];
}
				
/**
 * Lang_Obj() - Constructor for building Lang_Obj Object 
 *
 *	Parameter(s) :
 * 	which_lang : determine which language that user wants to display.
 *
 * Variable(s) :
 * 	my_lang : XML object that contains words 
 *
 **/
function Lang_Obj(which_lang){
	//this.my_lang = load_xml("xml/lang_" + which_lang + ".xml");
	//this.my_lang = load_xml("xml/lang.xml");				
}

/**
 * Lang_Obj.prototype - Prototype of Lang_Obj Object 
 *
 * Methods:
 *  get_word() : get default words
 * 	write()	: display words 
 * 	display(): get words
 **/
Lang_Obj.prototype = {

get_word:function(which_id){
	/*
	var which_word = get_node_value(this.my_lang, which_id);
	
	if (which_word == ""){
		which_word = get_default_lang("lang", which_id);
	}
	*/
	which_word = "get_word=" + which_id
	with(document){				
			write(which_word);
	}
	return which_word;
},

write:function(which_id){
	/*
	var which_word = this.get_word(which_id);
	
	for (var i = 1; i < this.write.arguments.length; i++){
		which_word = which_word.replace("%m", arguments[i]);
	}
	
	with(document){				
			write(which_word);
	}*/
	which_word = "writeID=" + which_id
	with(document){				
			write(which_word);
	}
},	

display:function(which_id){
	which_word = "display=" + which_id
	with(document){				
			write(which_word);
	}
	return which_word;
}
}

/**
 * Msg_Obj() - Constructor for building Msg_Obj Object 
 *
 *	Parameter(s) :
 * 	which_lang : determine which language that user wants to display.
 *
 * Variable(s) :
 * 	my_lang : XML object that contains warning messages  
 *
 **/
function Msg_Obj(which_lang){
 //this.my_lang = load_xml("xml/msg_" + which_lang + ".xml");
	this.my_lang = load_xml("xml/msg.xml");			
}

/**
 * Msg_Obj.prototype - Prototype of Msg_Obj Object 
 *
 * Methods:
 *  get_msg()		: get default message 
 * 	warning_msg()	: alert warning message
 *		confirm_msg()  : confirm message
 *		display_msg()	: display message
 * 	
 **/
Msg_Obj.prototype = {

get_msg:function(which_id){
	var which_msg = get_node_value(this.my_lang, which_id);
	
	if (which_msg == ""){
		which_msg = get_default_lang("msg", which_id);
	}
		
	return which_msg;
},

warning_msg:function(which_id){
	var which_msg = this.get_msg(which_id);
	
	for (var i = 1; i < this.warning_msg.arguments.length; i++){
		which_msg = which_msg.replace("%s", arguments[i]);
	}
	
	alert(which_msg);
},

confirm_msg:function(which_id){
	var which_msg = this.get_msg(which_id);
	
	for (var i = 1; i < this.confirm_msg.arguments.length; i++){
		which_msg = which_msg.replace("%s", arguments[i]);
	}
	
	return confirm(which_msg);	
},

display_msg:function(which_id){
	var which_msg = this.get_msg(which_id);
	
	for (var i = 1; i < this.display_msg.arguments.length; i++){
		which_msg = which_msg.replace("%s", arguments[i]);
	}
	
	return which_msg;	
}
}

/**
 * Html_Obj() - Constructor for building Html_Obj Object 
 *
 *	Parameter(s) :
 * 	
 *
 * Variable(s) :
 * 	my_xml : XML object that contains html name that refer to database's table 
 *
 **/
function Html_Obj(){
	this.my_xml = load_xml("xml/html_info.xml");	
}

/**
 * Html_Obj.prototype - Prototype of Html_Obj Object 
 *
 * Methods: 
 * 	get_value()	: get element's value by id
 * 	
 **/
Html_Obj.prototype = {

get_value:function(which_id){
	return get_node_value(this.my_xml, which_id);
}
}

/**
 * Addr_Obj() - Constructor for building Addr_Obj Object 
 *
 *	Parameter(s) :
 *		field_name	:	the field's name which wants to display in the warning message
 * 	addr 			:	IP address
 *		is_network	:	indicate this IP address a network address or not
 *
 * Variable(s) :
 *		field_name	:	the field's name which wants to display in the warning message
 * 	addr 			:	IP address
 *		is_network	:	indicate this IP address a network address or not
 *
 **/
function Addr_Obj(field_name, addr, is_network){
	this.field_name = field_name;	
	this.addr = addr;	
	this.is_network = is_network;
}

/**
 * Variable() - Constructor for building Varible Object 
 *
 *	Parameter(s) :
 * 	var_value 	:	the value of variable
 *		field_name	:	the field's name which wants to display in the warning message
 *		min			:	the minimal value of variable 
 *		max			:	the maximal value of variable
 *		is_even		:	indicate the variable is even number only or not
 *
 * Variable(s) :
 * 	var_value 	:	the value of variable
 *		field_name	:	the field's name which wants to display in the warning message
 *		min			:	the minimal value of variable 
 *		max			:	the maximal value of variable
 *		is_even		:	indicate the variable is even number only or not
 *
 **/
function Variable(field_name, var_value, min, max, is_even){
	this.field_name = field_name;
	this.var_value = var_value;
	this.min = min;
	this.max = max;
	this.is_even = is_even;
}

/**
 * RADIUS_SERVER( ) - Constructor for building RADIUS_SERVER Object 
 *
 *	Parameter(s) :
 *		ip_field     	: the field name of Radius server IP address which wants to display in the warning message
 *		port_field		: the field name of Radius server port which wants to display in the warning message
 *		secret_field	: the field name of Radius server Shared Secret which wants to display in the warning message
 *		ip_addr	   	: radius server's IP address
 *    port    			: radius server's port
 *    shared_secret 	: radius server's shared_secret
 *
 * Variable(s) :
 *		ip_field     	: the field name of Radius server IP address which wants to display in the warning message
 *		port_field		: the field name of Radius server port which wants to display in the warning message
 *		secret_field	: the field name of Radius server Shared Secret which wants to display in the warning message
 *    ip_addr	   	: radius server's IP address
 *    port    			: radius server's port
 *    shared_secret 	: radius server's shared_secret
 *
 **/
function RADIUS_SERVER(ip_field, port_field, secret_field, addr, port, shared_secret){
	this.ip_field = ip_field;
	this.port_field = port_field;
	this.secret_field = secret_field;
	this.addr = addr;	
	this.port = port;
	this.shared_secret = shared_secret;
}

/**
 * XMLRequest( ) - Constructor for building XMLRequest Object 
 
 *	Parameter(s) :
 * 	onReqComp : the function that you want to perform after receive response from the web server
 *
 * Variable(s) :
 * 	http_req   :  user custom function for performing post response.
 * 	onReqComp  :  user custom function for performing post response.
 *
 **/
function XMLRequest(onReqComp){
	this.http_req = create_http_request();	
	this.onReqComp = onReqComp;	
}

function makestring(size)
{
    if(size<=0 || size > 32);
        size=32;
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < size; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function gen_tok(o_req)
{
    var o_cookie=document.cookie;
	var ran_str=makestring(32);
    var key=o_cookie.substring(4, o_cookie.length);
    var req=o_req+ran_str;
    var digest = hex_hmac_md5(key, req);
    var tok="&tok="+ran_str+digest;
    return tok;
}

function gen_token_req(o_req)
{
    var tok=gen_tok(o_req);
    
    return o_req+tok;
}
/**
 * XMLRequest.prototype - Prototype of XMLRequest Object 
 *
 * Data members:
 * 	READY_STATE_***:  ready state constants
 *
 * Methods:
 * 	loading_xml()		:  get remote xml document
 * 	exec_cgi()			:  requesting server performing CGI command
 *  	get_login_level() :  get user's login level
 * 	onReadyState()		:  callback function while readystate changing
 **/
XMLRequest.prototype = {

READY_STATE_UNINITIALIZED 	: 0,
READY_STATE_LOADING 			: 1,
READY_STATE_LOADED 			: 2,
READY_STATE_INTERACTIVE 	: 3,
READY_STATE_COMPLETE 		: 4,


loading_xml: function(which_url){	
	var req_url = which_url + "?" + Math.random();
	var obj = this;
			
	if (this.http_req){
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};				
		this.http_req.open('GET', req_url, true);
		this.http_req.setRequestHeader('Cache-Control', 'no-cache');
		this.http_req.send(null);	
		return true;	
	}
	
	return false;
},

exec_cgi: function(para){
	var req_url = "my_cgi.cgi?" + Math.random();
	var obj = this;
		
	if (this.http_req){	
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};				
		this.http_req.open('POST', req_url, true);
		this.http_req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		this.http_req.setRequestHeader('Content-length', para.length);	
		this.http_req.setRequestHeader("Connection", "close");
		this.http_req.send(para);		
		
		return true;
	}			
	
	return false;			
},

exec_webfile_cgi: function(para){
	var req_url = "logininfo.xml?" + para;
	var obj = this;
		
	if (this.http_req){	
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};				
		this.http_req.open('GET', req_url, true);
		this.http_req.setRequestHeader('Content-length', para.length);	
		this.http_req.setRequestHeader("Connection", "close");
		this.http_req.send(null);		
		
		return true;
	}			
	
	return false;			
},

exec_auth_cgi: function(para){
	var dummy = new Date().getTime(); //Solve the problem of Ajax GET omitted due to IE cache.
	var req_url = "/dws/api/Login?" + dummy;
	var obj = this;
	
	if (this.http_req){	
				
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};	
								
		if (this.exec_auth_cgi.arguments.length > 0){
			this.http_req.open('POST', req_url, true);
			this.http_req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			this.http_req.setRequestHeader('Content-length', para.length);
		}else{
			this.http_req.open('GET', req_url, true);
			this.http_req.setRequestHeader('Content-length', req_url.length);	
		}
		
		if (this.http_req.overrideMimeType){
			this.http_req.overrideMimeType('application/json');
		}
				
		this.http_req.setRequestHeader("Connection", "close");	

		if (this.exec_auth_cgi.arguments.length > 0){
			this.http_req.send(para);
		}else{
			this.http_req.send(null);	
		}
		
		return true;
	}			
	
	return false;			
},

json_cgi: function(para){
	var req_url = "/dws/api/" + para;
	var obj = this;
	
	req_url = gen_token_req(req_url);
	if (this.http_req){	
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};				
		this.http_req.open('GET', req_url, true);	
		if (this.http_req.overrideMimeType){
			this.http_req.overrideMimeType('application/json');
		}
		this.http_req.setRequestHeader('Content-length', req_url.length);			
		this.http_req.setRequestHeader("Connection", "close");
		this.http_req.send(null);		
		
		return true;
	}			
	
	return false;			
},

get_login_level: function(){	
	var req_url = "my_cgi.cgi?" + Math.random();
	var which_para = "request_cgi=check_user_level";
	var obj = this;
			
	if (this.http_req){
		this.http_req.onreadystatechange = function() {obj.onReadyState.call(obj)};	
		this.http_req.open('POST', req_url, true);
		this.http_req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		this.http_req.setRequestHeader('Content-length', which_para.length);	
		this.http_req.setRequestHeader("Connection", "close");
		this.http_req.send(which_para);							
		return true;	
	}
	
	return false;
},

onReadyState: function(){
	var http_req = this.http_req;
	
	switch (http_req.readyState) {
		case this.READY_STATE_UNINITIALIZED:
			break;
			
		case this.READY_STATE_LOADING:
			break;
			
		case this.READY_STATE_LOADED:
			break;
			
		case this.READY_STATE_INTERACTIVE:
			break;
			
		case this.READY_STATE_COMPLETE:
			if (http_req.status == 200){
				this.onReqComp.call(this, this.http_req);
			}				
			break;
		default:
			break;
	}	
}
}

/**
 * HASH( ) - Constructor for building HASH Object 
 
 *	Parameter(s) :
 * 	
 *
 * Variable(s) :
 * 	obj   :  an Array to store HASH_OBJECT's name and value
 *
 **/
function HASH_TABLE(){	
	this.hash_table = new Array();	
}


HASH_TABLE.prototype = {

clear: function(){
	this.hash_table = new Array();
},

get: function(key){
	return this.hash_table[key];
},

put: function(key, value){
	if (key == null || value == null) {
		throw "NullPointerException {" + key + "},{" + value + "}";
	}else{
		this.hash_table[key] = value;
	}
},

size: function(){
	var size = 0;
    
	for (var i in this.hash_table){
		if (this.hash_table[i] != null){
    		size++;
    	}
	}
   
   return size;
},

isEmpty: function(){
   return (parseInt(this.size()) == 0) ? true : false;
}

}

