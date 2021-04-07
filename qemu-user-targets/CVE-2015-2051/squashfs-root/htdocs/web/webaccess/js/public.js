/**
 *	get_by_id() : get a html element by its id
 *
 *	Parameter(s) :
 *		id : the id of html element
 *
 * Return : html element
 * 	
 **/
function get_by_id(id){
	with(document){
		return getElementById(id);
	}
}

/**
 *	get_by_name() : get a html element array by its name
 *
 *	Parameter(s) :
 *		name : the name of html element
 *
 * Return : html element array
 * 	
 **/
function get_by_name(name){
	with(document){
		return getElementsByName(name);
	}
}

/**
 *	create_http_request() : create a XMLHttpRequest object to communicate with the Web server
 *
 *	Parameter(s) :
 *		NULL
 *
 * Return :	a XMLHttpRequest object
 * 	
 **/
function create_http_request(){
	var http_request = false;
	
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		
		/* For compatibility with some versions of some Mozilla browsers, 
		 * to ensure that browsers can work properly.
		 */
		if (http_request.overrideMimeType){
			http_request.overrideMimeType('text/xml');
		}
		
		return http_request;
	}

	if (window.ActiveXObject){ // IE
		try {
			http_request = new ActiveXObject("Msxml3.XMLHTTP");
		}catch(e){
			try{
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){				
				return false;
			}
		}
		return http_request;
	}

	return false;
}

function disable_right_btn(){
	return false;
}

function key_handler(e){
	var which_key;

   if (document.all) { 
   	which_key = window.event.keyCode; 
   }else{
   	which_key = e.which;
   }
   
   if (which_key == 13){
		return; // do nothing
   }
}

function get_lang_xml(){
	var my_lang = "en";
	var lang_xml;
	
	lang_xml = load_xml("xml/multi_lang.xml");	
	
	return my_lang;
}

/**
 *	load_lang_obj() : create Lang_Obj, Msg_Obj for displaying words, warning messages, and model's information
 *
 *	Parameter(s) :
 *		NULL
 *
 * Variable(s) :
 *		lang_obj	 :	a XML object which contains words displaying on GUI
 *    msg_obj   : a XML object which contains warning messages
 *		html_obj	 : a XML object which contains html information
 * Return :	NULL
 * 	
 **/
function load_lang_obj(){	
	//var which_lang = get_lang_xml();	
	
	lang_obj = new Lang_Obj();
	//msg_obj = new Msg_Obj(which_lang);
	//html_obj = new Html_Obj();
	
	document.oncontextmenu = disable_right_btn;	// disable the mouse's right button
	document.onkeypress = key_handler;
}

/**
 *	load_initial_settings() : send a request to the Web server to obtain values
 *
 *	Parameter(s) :
 *		which_setting : the name of action that you want to execute in cgi
 *
 * Return :	a XML object
 * 	
 **/
function load_initial_settings(which_setting){
	var xml_request = new XMLRequest(get_settings_xml);
	var para = "request=load_settings";

	for (var i = 0; i < arguments.length; i++){
		para += "&table_name=" + arguments[i];
	}
							
	xml_request.exec_cgi(para);
}

function return_to_login(){
	location.href = 'login.htm';
}

function timeup_to_login(){
	setTimeout('return_to_login()', 180000);
}

function return_real_login(http_req){
	my_xml = http_req.responseXML;
	location.href = 'Login_Real.htm';
}

function return_to_index(http_req){
	my_xml = http_req.responseXML;
	location.href = 'index.htm';
}

function return_to_default(http_req){
	my_xml = http_req.responseXML;
	xml_request = new XMLRequest(return_to_index);
	var para;
	
	para = "request=update_entry&table_name=restore_default&rowid=0&factory_default=0";

	xml_request.exec_cgi(para);
}

function get_login_info(which_setting){
	var xml_request = new XMLRequest(get_settings_xml);
	var para = "request=" + arguments[0] + "&request=load_settings";

	for (var i = 1; i < arguments.length; i++){
		para += "&table_name=" + arguments[i];
	}
							
	xml_request.exec_cgi(para);
}
/**
 *	check_user_info() : to check if the user has login yet
 *
 *	Parameter(s) :
 *		redirect_page : a XML's element object which contains a redirect page
 *
 * Return :	True or False
 * 	
 **/
function check_user_info(redirect_page){
	var which_page;
	
	if (redirect_page != null){	
		which_page = redirect_page.firstChild.nodeValue;
		location.href = html_obj.get_value(which_page);				
		return 0;
	}
	
	return 1;
}

/**
 *	check_wizard_user_info() : to check if the user has login yet
 *
 *	Parameter(s) :
 *		redirect_page : a XML's element object which contains a redirect page
 *
 * Return :	True or False
 * 	
 **/
function check_wizard_user_info(redirect_page){
	var which_page;
	
	if (redirect_page != null){	
		which_page = redirect_page.firstChild.nodeValue;
		window.opener.location.href = html_obj.get_value(which_page);				
		window.close();
		return 0;
	}
	
	return 1;
}

/**
 *	redirect_next_page() : redirect the current page to next page
 *
 *	Parameter(s) :
 *		http_req : a XMLHttpRequest object that contains the next page info		
 *
 * Return : NULL
 * 	
 **/
function redirect_next_page(http_req){				
	var xml = http_req.responseXML;		
	var redirect_page = get_node_value(xml, "redirect_page");
	
	location.href = html_obj.get_value(redirect_page);
}

/**
 *	set_html_settings() : to parse the XML element, get its values and display the values on GUI
 *
 *	Parameter(s) :
 *		which_node : a XML element that contains the display values
 *		index		  : the child's index in the XML element
 *
 * Return : NULL
 * 	
 **/
function set_html_settings(which_node){
	for (var i = 0; i < which_node.childNodes.length; i++){
		var node = which_node.childNodes[i];
	
		if (node.nodeType != 3){	// NS6/Mozilla will treat space as an element, so we need to ingore it
			if (node.childNodes.length > 1){
				for (var j = 0; j < node.childNodes.length; j++){
					var child_node = node.childNodes.item(j);					
												
					if (child_node.nodeType != 3){	// NS6/Mozilla will treat space as an element, so we need to ingore it
						set_element_value(child_node, i);					
					}
				}
			}else{				
				set_element_value(node);
			}
		}
	}	
}

function set_element_value(node, index){
	var obj;
				
	if (arguments.length == 2){
		obj = document.getElementById(node.nodeName + index);
	}else{
		obj = document.getElementById(node.nodeName);
	}
			
	if (obj){					
		if (obj.tagName == "INPUT"){												
			if (obj.type == "checkbox"){	// when input field is checkbox						
				if (obj.value == node.firstChild.nodeValue){
					obj.checked = true;
				}else{
					obj.checked = false;
				}				
				
				obj.defaultChecked = obj.checked;
			}else if (obj.type == "text" || obj.type == "password" || obj.type == "hidden"){	// when input field is text or password or textarea			
				if (node.childNodes.length > 0){ // when element has value
					obj.value = node.firstChild.nodeValue;
				}else{
					obj.value = "";
				}				
				
				obj.defaultValue = obj.value;
			}else if (obj.type == "radio"){	// when input field is radio, for IE only
				if (arguments.length == 2){
					obj = document.getElementsByName(node.nodeName + index);
				}else{
					obj = document.getElementsByName(node.nodeName);
				}	
				
				for (var j = 0; j < obj.length; j++){
					if (obj[j].value == node.firstChild.nodeValue){
						obj[j].checked = true;
					}else{
						obj[j].checked = false;
					}
					
					obj[j].defaultChecked = obj[j].checked;
				}	
			}
		}else if (obj.tagName == "SELECT"){						
			for (var j = 0; j < obj.length; j++){
				if(node.firstChild.nodeValue){
					if (obj.options[j].value == node.firstChild.nodeValue){
						obj.selectedIndex = j;
						obj.options[j].defaultSelected = true;									
					}else{
						obj.options[j].defaultSelected = false;
					}
				}
			}						
		}else if (obj.tagName == "TEXTAREA"){	
			if (node.childNodes.length > 0){ // when element has value
				obj.value = node.firstChild.nodeValue;
			}else{
				obj.value = "";
			}			
			
			obj.defaultValue = obj.value;
		}
	}else {	// when the input field is radio, for firefox
		if (arguments.length == 2){
			obj = document.getElementsByName(node.nodeName + index);
		}else{
			obj = document.getElementsByName(node.nodeName);
		}	
							
		for (var j = 0; j < obj.length; j++){
			if (obj[j].value == node.firstChild.nodeValue){
				obj[j].checked = true;
			}else{
				obj[j].checked = false;
			}
			
			obj[j].defaultChecked = obj[j].checked;
		}	
	}
}

/**
 *	get_random_hex_char() : get a random char in hex
 *
 *	Parameter(s) :
 *		none : none
 *
 * Return :random char in number_list
 * 	
 **/
function get_random_hex_char(){
	//var number_list = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	var number_list = "1234567890ABCDEF";	
	var number = Math.floor(Math.random() * number_list.length);	
	
	return(number_list.charAt(number));
}

/**
 *	generate_key_hex() : get a random char in hex
 *
 *	Parameter(s) :
 *		none : none
 *
 * Return :random char in number_list
 * 	
 **/
function generate_key_hex(keyLen){
		var key ="" ;
		for (i=0 ; i < keyLen; i++){
			key += get_random_hex_char();
		}

	return key;
}

/**
 *	get_random_ascii_char() : get a random char in ascii
 *
 *	Parameter(s) :
 *		none : none
 *
 * Return :random char in number_list
 * 	
 **/
function get_random_ascii_char(){
	var number_list = "1234567890abcdefghijklmnopqrstuvwxyz";	
	var number = Math.floor(Math.random() * number_list.length);	
	
	return(number_list.charAt(number));
}

/**
 *	generate_key_ascii() : get a random char in ascii
 *
 *	Parameter(s) :
 *		none : none
 *
 * Return :random char in number_list
 * 	
 **/
function generate_key_ascii(keyLen){
		var key ="" ;
		for (i=0 ; i < keyLen; i++){
			key += get_random_ascii_char();
		}

	return key;
}

function check_wps_pin(pin){
	var sum = 0;
	var result = false;
	
	sum += 3 * (parseInt(pin / 10000000) % 10);
	sum += 1 * (parseInt(pin / 1000000) % 10);
	sum += 3 * (parseInt(pin / 100000) % 10);
	sum += 1 * (parseInt(pin / 10000) % 10);
	sum += 3 * (parseInt(pin / 1000) % 10);
	sum += 1 * (parseInt(pin / 100) % 10);
	sum += 3 * (parseInt(pin / 10) % 10);
	sum += 1 * (parseInt(pin / 1) % 10);
			
	if ((sum % 10) == 0){	// 0 : valid , others : invalid
		result = true;
	}
	
	return result;
}

function compute_pin_checksum(pin){
	var sum = 0; 			
	var digit;
	
	pin *= 10;
	sum += 3 * (parseInt(pin / 10000000) % 10); 
	sum += 1 * (parseInt(pin / 1000000) % 10); 
	sum += 3 * (parseInt(pin / 100000) % 10); 
	sum += 1 * (parseInt(pin / 10000) % 10);
	sum += 3 * (parseInt(pin / 1000) % 10);
	sum += 1 * (parseInt(pin / 100) % 10);
	sum += 3 * (parseInt(pin / 10) % 10); 
	sum += 1 * (parseInt(pin / 1) % 10); 
	
	digit = (sum % 10);
	return ((10 - digit) % 10);
}

function generate_wps_pin(){
	var str = "1";
	var random_num;
	var num;

	while (str.length != 7) {
		random_num = Math.random() * 1000000000; 
		num = parseInt(random_num, 10);	  				
		num %= 10000000;
		str = num.toString();
	}

	num = num*10 + compute_pin_checksum(num);
	num = parseInt(num, 10); 
		 			
	get_by_id("current_pin_number").innerHTML = num;
	set_text("pin_number", num, false); //for checking default value have changed or not
}

/**
 *	get_index_str() : get the represent str according to the argument
 *
 *	Parameter(s) :
 *		index			:	the index number 
 *			 
 * Return : represent str
 * 	
 **/
function get_index_str(index){
	var str = "";
	
	switch(index){
		case 0:
			str = "1st";
			break;
		case 1:
			str = "2nd";
			break;
		case 2:
			str = "3rd";
			break;
		case 3:
			str = "4th";
			break;	
	}
	
	return str;
}

/**
 *	get_digit_number() : get the two digit number
 *
 *	Parameter(s) :
 *		num		 :	number 
 *			 
 * Return : two digit number
 * 	
 **/
function get_digit_number(num){
	if (num <= 9){
		return "0" + num;
	}
	
	return num;
}

/**
 *	get_month() : get the month str according to the argument
 *
 *	Parameter(s) :
 *		index			:	the index number 
 *			 
 * Return : month str
 * 	
 **/
function get_month(index){
	switch (index)
	{
		case 1: return "JAN";
		case 2: return "FEB";
		case 3: return "MAR";
		case 4: return "APR";
		case 5: return "MAY";
		case 6: return "JUN";
		case 7: return "JUL";
		case 8: return "AUG";
		case 9: return "SEP";
		case 10: return "OCT";
		case 11: return "NOV";
		case 12: return "DEC";								
	}
	return "";
}

/**
 *	get_week_day() : get the weekday str according to the argument
 *
 *	Parameter(s) :
 *		index			:	the index number 
 *			 
 * Return : weekday str
 * 	
 **/
function get_week_day(index){
	switch (index)
	{
		case 1: return "MON";
		case 2: return "TUE";
		case 3: return "WED";
		case 4: return "THU";
		case 5: return "FRI";
		case 6: return "SAT";
		case 7: return "SUN";						
	}
	return "";
}

/**
 *	replace_msg() : replace the special character in a message
 *
 *	Parameter(s) :
 *		msg		 :	a message which contains the special character you want to replace
 *    str		 : a str you want to replace
 *			 
 * Return : the replaced message
 * 	
 **/
function replace_msg(msg){

	for (var i = 1; i < arguments.length; i++){
		msg = msg.replace("%s", arguments[i]);
	}

	return msg;
}

/**
 *	check_integer() : check the input value is a integer or not and it's in the range or not
 *
 *	Parameter(s) :
 *		which_int	:	the value you want to check
 *    min			:	the minimum value
 *		max			: 	the maximum value
 *			 
 * Return : if the value is correct, return ture else return false
 * 	
 **/
function check_integer(which_int, min, max){
	var temp_int = which_int.split(" ");
	
	if (temp_int.length > 1){
		return false;
	}
		
	if (isNaN(which_int) || which_int == ""){	// if it is not an integer				
		return false;
	}
	
	if (which_int.charAt(0) == "0" && which_int.length > 1){	// when the value start with 0	
		return false;
	}
	
	if (arguments.length == 3){
		if (parseInt(which_int) < min || parseInt(which_int) > max){	// if it is not in the range
			return false;
		}
	}
	
	return true;
}

/**
 *	check_ip_range() : check each octet of IP address is in the correct range or not 
 *
 *	Parameter(s) :
 *		order			:	the index of IP address
 *    my_obj		:	a Addr_Obj object which contains the IP information that you want to check
 *		mask_obj		:	a Addr_Obj object which contains the Subnet Mask information
 *    checking_ip	:  a IP array which is used to compare with my_obj's IP address
 *
 * Return : if the value is correct, return true else return false
 * 	
 **/
function check_ip_range(order, my_obj, mask, checking_ip, is_broadcast){	
	var which_ip = my_obj.addr[order];
	var temp_ip;
	var temp_msg;
	var start = 0;
	var end = 255;
		
	if(is_broadcast != "BROADCAST_IP_IGNORE"){
	if (order == 0){				// the checking range of 1st address
		start = 1;	
	}
		
	if (mask[order] != 255){		
		if (arguments.length == 4 && checking_ip != null){				
			temp_ip = checking_ip[order];							
		}else{
			temp_ip = which_ip;
		}
						
		if (parseInt(temp_ip) >= 0 && parseInt(temp_ip) <= 255){	
			end = (~mask[order]+256);						
			start = mask[order] & temp_ip;			
			end += start;
	
			if (end > 255){
				end = 255;
			}
		}	
	}
	
	if (order == 3){		
		/* if the 1st, 2nd, 3rd ip address are the network address */
		if (((mask[0] | (~my_obj.addr[0]+256)) == 255) && ((mask[1] | (~my_obj.addr[1]+256)) == 255) && ((mask[2] | (~my_obj.addr[2]+256)) == 255)){
			if (!my_obj.is_network){	// if the address does not support to be the network address
				start += 1;
			}
		}
		
		/* if the 1st, 2nd, 3rd ip address are the broadcast address, the end range must be minus 1 */
		if (((mask[0] | my_obj.addr[0]) == 255) && ((mask[1] | my_obj.addr[1]) == 255) && ((mask[2] | my_obj.addr[2]) == 255)){			
			end -= 1;
		}
		
		if (end == 0){	// when the 4th oct of subnet mask is 254, the end value will be zero
			end = 1;
		}	
	}
	
	if (parseInt(which_ip) < start || parseInt(which_ip) > end){
		if (start != end){	
			msg_obj.warning_msg('MSG012',  get_index_str(order), my_obj.field_name, start, end);
		}else{
			temp_msg = replace_msg(msg_obj.display_msg('MSG013'),  get_index_str(order), my_obj.field_name) + " " + start;
			alert(temp_msg);
		}
		return false;
	}
	}
	
	return true;
}

/**
 *	check_address() : check IP address is in correct or not
 *
 *	Parameter(s) :
 *		my_obj		:	a Addr_Obj object which contains the IP address information
 *		mask_obj		:	a Addr_Obj object which contains the Subnet Mask information
 *    ip_obj		:  a Addr_Obj object which contains the IP address information
 *
 * Return : if the value is correct, return ture else return false
 * 	
 **/
function check_address(my_obj, mask_obj, ip_obj, is_broadcast){	
	var ip = my_obj.addr;
	var count_bcast = 0;
	var mask;
	
	if (ip.length == 4){
		if (ip[0] == "0" && ip[1] == "0" && ip[2] == "0" && ip[3] == "0"){	// when ip is 0.0.0.0	
			msg_obj.warning_msg('MSG009', my_obj.field_name);
			return false;
		}else if ((parseInt(ip[0]) == 127) || (parseInt(ip[0]) >= 224 && parseInt(ip[0]) <= 239)){			
			msg_obj.warning_msg('MSG010', my_obj.field_name);
			return false;
		}else{		// when IP is not 0.0.0.0, checking range. Otherwise no need to check		
					
			if (arguments.length > 1 && mask_obj != null){
				mask = mask_obj.addr;
			}else{
				mask = new Array(255,255,255,0);
			}
						
			for(var i = 0; i < ip.length; i++){						
				if (!check_integer(ip[i])){					
					msg_obj.warning_msg('MSG011', get_index_str(i), my_obj.field_name);
					return false;	
				}
						
				if (arguments.length == 3 && ip_obj != null){
					if (!check_ip_range(i, my_obj, mask, ip_obj.addr)){
						return false;
					}
				}else{					
					if (!check_ip_range(i, my_obj, mask, null, is_broadcast)){
						return false;
					}
				}
			}											
			for (var i = 0; i < 4; i++){	// check the IP address is a broadcast address or not
								
				if ((mask[i] | ip[i]) == 255){
					count_bcast++;
				}
			}
			
			/* broadcast check */
			if (count_bcast == 4){
				if(is_broadcast != "BROADCAST_IP_IGNORE"){
					msg_obj.warning_msg('MSG003', my_obj.field_name);			
					return false;
				}	
			}	
			
		}
	}else{	// if the length of ip is not correct, show invalid ip msg

		if (ip.length == 1 && ip[0] == ""){
			msg_obj.warning_msg('MSG009', my_obj.field_name);
		}else{
			msg_obj.warning_msg('MSG003', my_obj.field_name);
		}
		
		return false;
	}

	return true;
}

function check_routing_address(my_obj, mask_obj){
	var count_zero = 0;
	var ip = my_obj.addr;
	var mask;
	var allow_cast = false;

	if (ip.length == 4){
		// check the ip is not multicast IP (127.x.x.x && 224.x.x.x ~ 239.x.x.x)
		if (ip[0] == "0" && ip[1] == "0" && ip[2] == "0" && ip[3] == "0"){	// when ip is 0.0.0.0	
			msg_obj.warning_msg('MSG009', my_obj.field_name);
			return false;
		
		}else if ((parseInt(ip[0]) == 127) || (parseInt(ip[0]) >= 224 && parseInt(ip[0]) <= 239)){			
			msg_obj.warning_msg('MSG010', my_obj.field_name);
			return false;
		
		}else{		// when IP is not 0.0.0.0, checking range. Otherwise no need to check		
				mask = mask_obj.addr;
				for(var i = 0; i < mask.length; i++){
					if (mask[i] != "255"){
						if (ip[i] != (mask[i] & ip[i])){
							msg_obj.warning_msg('MSG011', get_index_str(i), my_obj.field_name);
							return false;
						}
					}
				}
				
				for(var i = 0; i < ip.length; i++){						
					if (!check_integer(ip[i])){					
						msg_obj.warning_msg('MSG011', get_index_str(i), my_obj.field_name);
						return false;
					}
					
					if (!check_ip_range(i, my_obj, mask)){
						return false;
					}
				}
		}
	}else{	// if the length of ip is not correct, show invalid ip msg
		if (ip.length == 1 && ip[0] == ""){
			msg_obj.warning_msg('MSG009', my_obj.field_name);
		}else{
			msg_obj.warning_msg('MSG003', my_obj.field_name);
		}
		
		return false;
	}

	return true;
}


function check_routing_mask(my_mask){
	var temp_mask = my_mask.addr;
	
	if (temp_mask.length == 4){
		if (temp_mask[0] == "0" && temp_mask[1] == "0" && temp_mask[2] == "0" && temp_mask[3] == "0"){	// when ip is 0.0.0.0			
			msg_obj.warning_msg('MSG009', my_mask.field_name);
			return false;
		}
		
		for (var i = 0; i < temp_mask.length; i++){			
			var mask;
			var range_msg;
			var in_range = false;
			var j = 0;
			
			if (!check_integer(temp_mask[i])){	// check the input value is integer and in well format				
				msg_obj.warning_msg('MSG011', get_index_str(i), my_mask.field_name);
				return false;
			}
						
			mask = parseInt(temp_mask[i]);
			if (i == 0){	// when it's 1st address
				j = 1;		// the 1st address can't be 0
			}
						
			for (; j < subnet_mask_value.length; j++){
				if (mask == subnet_mask_value[j]){
					in_range = true;
					break;
				}else{
					in_range = false;
				}
			}
	
			if (!in_range){	
				range_msg = replace_msg(msg_obj.display_msg('MSG013'), get_index_str(i), my_mask.field_name);
					
				for (var k = 0; k < subnet_mask_value.length; k++){
					if (i == 0 && k == 0){	// the 1st address cannot include 0
						continue;
					}
					
					range_msg += " " + subnet_mask_value[k];
					
					if (k < subnet_mask_value.length - 1){
						range_msg += ",";
					}
				}
				
				alert(range_msg);
				return false;
			}
		
			if ((i != 0) && (mask != 0)){ // when not the 1st range and the value is not 0
				if (parseInt(temp_mask[i-1]) != 255){  // check the previous value is 255 or not
					msg_obj.warning_msg('MSG011', get_index_str(i), my_mask.field_name);
					return false;
				}
			}						
		}
	}else{

		if (temp_mask.length == 1 && temp_mask[0] == ""){
			msg_obj.warning_msg('MSG009', my_mask.field_name);			
		}else{
			msg_obj.warning_msg('MSG003', my_mask.field_name);
		}
		
		return false;
	}

	return true;
}


/**
 *	check_subnet_mask() : check subnet mask is correct or not
 *
 *	Parameter(s) :
 *		my_mask		:	a Addr_Obj object which contains the subnet mask's value and error messages
 *			 
 * Return : if the value is correct, return ture else return false
 * 	
 **/
function check_subnet_mask(my_mask){
	var temp_mask = my_mask.addr;
	
	if (temp_mask.length == 4){
		if (temp_mask[0] == "0" && temp_mask[1] == "0" && temp_mask[2] == "0" && temp_mask[3] == "0"){	// when ip is 0.0.0.0			
			msg_obj.warning_msg('MSG009', my_mask.field_name);
			return false;
		}
		
		for (var i = 0; i < temp_mask.length; i++){			
			var mask;
			var range_msg;
			var in_range = false;
			var j = 0;
			
			if (!check_integer(temp_mask[i])){	// check the input value is integer and in well format				
				msg_obj.warning_msg('MSG011', get_index_str(i), my_mask.field_name);
				return false;
			}
						
			mask = parseInt(temp_mask[i]);
			if (i == 0){	// when it's 1st address
				j = 1;		// the 1st address can't be 0
			}
						
			for (; j < subnet_mask_value.length; j++){
				if (mask == subnet_mask_value[j]){
					in_range = true;
					break;
				}else{
					in_range = false;
				}
			}
			
			if (i == 3 && parseInt(mask) == 255){	// when the last mask address is 255	
				in_range = false;
			}

			if (!in_range){	
				range_msg = replace_msg(msg_obj.display_msg('MSG013'), get_index_str(i), my_mask.field_name);
					
				for (var k = 0; k < subnet_mask_value.length; k++){
					if (i == 0 && k == 0){	// the 1st address cannot include 0
						continue;
					}
					
					range_msg += " " + subnet_mask_value[k];
					
					if (k < subnet_mask_value.length - 1){
						if (i == 3 && k == subnet_mask_value.length - 2){ // the 4th address cannot include 255
							break;
						}
						range_msg += ",";
					}
				}
				
				alert(range_msg);
				return false;
			}
		
			if ((i != 0) && (mask != 0)){ // when not the 1st range and the value is not 0
				if (parseInt(temp_mask[i-1]) != 255){  // check the previous value is 255 or not
					msg_obj.warning_msg('MSG011', get_index_str(i), my_mask.field_name);
					return false;
				}
			}						
		}
	}else{

		if (temp_mask.length == 1 && temp_mask[0] == ""){
			msg_obj.warning_msg('MSG009', my_mask.field_name);			
		}else{
			msg_obj.warning_msg('MSG003', my_mask.field_name);
		}
		
		return false;
	}

	return true;
}

/**
 *	check_same_subnet() : check ip and gateway is in the same Subnet or not
 *
 *	Parameter(s) :
 *		ip		 	:	a Addr_Obj object which contains IP information
 *    mask		:  a Addr_Obj object which contains Subnet Mask information
 *		gateway	:	a Addr_Obj object which contains Gateway information
 *			 
 * Return : if in the same subnet, return true else return false
 * 	
 **/
function check_same_subnet(ip, mask, gateway){
	var temp_ip = ip.addr;
	var temp_mask = mask.addr;
	var temp_gateway = gateway.addr;

	for (var i = 0; i < temp_ip.length - 1; i++){
		if ((temp_ip[i] & temp_mask[i]) != (temp_gateway[i] & temp_mask[i])){
			return false;		// when not in the same subnet mask, return false
		}
	}

	return true;
}

function check_radius_server(radius, which_id){
	var addr_obj = new Addr_Obj(radius.ip_field, (radius.addr).split("."), false);
	var port_obj = new Variable(radius.port_field, radius.port, 1, 65534, false);
		
	if (!check_address(addr_obj)){
		return false;
	}
	
	if (!check_varible(port_obj)){      	
   	return false;
 	}
 	
 	if (radius.shared_secret == ""){
 		msg_obj.warning_msg('MSG002', radius.secret_field);
   	return false;               
	}
	
	return true;
}

function check_multi_ports(which_error, which_ports){
	var port_array = which_ports.split(",");

	for (var i = 0; i < port_array.length; i++){
		var temp_ports = port_array[i].split("-");

		if (temp_ports.length > 2){
			msg_obj.warning_msg("MSG073", port_array[i]);
			return false;
		}else if (temp_ports.length == 2){
			if (temp_ports[0] == "" || temp_ports[1] == ""){
				msg_obj.warning_msg("MSG073", port_array[i]);
				return false;
			}
		}

		for (var j = 0; j < temp_ports.length; j++){
			var temp_varible = new Variable(which_error, temp_ports[j], 1, 65535, false);

			if (!check_varible(temp_varible)){
				return false;
			}
		}

		if (temp_ports.length == 2){
			if (parseInt(temp_ports[0]) >= parseInt(temp_ports[1])){
				msg_obj.warning_msg("MSG074", port_array[i]);
				return false;
			}
		}
	}

	return true;
}

function check_multi_port_conflict(incoming_port, checking_port){
	var incoming_array = incoming_port.split(",");
	var checking_array = checking_port.split(",");

	for (var i = 0; i < incoming_array.length; i++){
		var temp_port = incoming_array[i].split("-");
		var start_port = 0;
		var end_port = 0;
		
		start_port = parseInt(temp_port[0]);
		
		for (var j = 0; j < checking_array.length; j++){
			var temp_checking_port = checking_array[j].split("-")
			var start_checking_port = parseInt(temp_checking_port[0]);
			var end_checking_port = 0;
			
			if (temp_checking_port.length == 2){	// if the checking port is in range
				end_checking_port = parseInt(temp_checking_port[1]);
			}
	
			if (temp_port.length == 1){	// when the incoming port only has one port value	
				if (temp_checking_port.length == 1){	// when the checking port only has one port value
					if (start_port == start_checking_port){ 
						return true;
					}
				}else if (temp_checking_port.length == 2){ // when the checking port is in range, check the incoming port is in the range of the checking port or not
					if (start_checking_port <= start_port && end_checking_port >= start_port){
						return true;
					}
				}
			}else if (temp_port.length == 2){	// when the incoming port is in range
				end_port = parseInt(temp_port[1]);
			
				if (start_port <= start_checking_port && end_port >= start_checking_port){
					return true;
				}
			
				if (checking_array.length == 2){
					if (start_port <= end_checking_port && end_port >= end_checking_port){
						return true;
					}
				
					if (start_checking_port <= start_port && end_checking_port >= start_port){
						return true;
					}								
				}
			}	
		}				
	}
	return false;
}

function check_conflict_virtual_server(server_list, checking_protocol, checking_port){
	
	for (var i = 0; i < server_list.length; i++){
		var obj = server_list[i];
		var protocol = obj.get("protocol");
			
		if (obj.get("server_enable") == "0"){			
			continue;
		}
				
		if ((protocol == checking_protocol) || (protocol == "Both") || (checking_protocol == "Any")){
		
			if (check_multi_port_conflict(obj.get("public_port"), checking_port)){
				return false;
			}		
		}
	}
	
	return true;
}

function check_conflict_application(application_list, checking_protocol, checking_port){
	
	for (var i = 0; i < application_list.length; i++){
		var obj = application_list[i];
		var firewall_protocol = obj.get("firewall_protocol");
		
		if (obj.get("rule_enable") == "0"){
			continue;
		}
		
		if ((firewall_protocol == checking_protocol) || (firewall_protocol == "Any") || (checking_protocol == "Both")){
			
			if (check_multi_port_conflict(checking_port, obj.get("firewall_port"))){
				return false;
			}		
		}
	}
	
	return true;
}

function check_conflict_remote_management(remote_management, checking_port){
	
	if (remote_management.get("remote_enable") == "1"){
		if (check_multi_port_conflict(remote_management.get("remote_port"), checking_port)){
			return false;
		}	
	}
	
	return true;
}

function check_conflict_port_forwarding(forwarding_list, checking_protocol, checking_port){
	
	for (var i = 0; i < forwarding_list.length; i++){
		var obj = forwarding_list[i];
			
		if (obj.get("forwarding_enable") == "0"){			
			continue;
		}
				
		if (checking_protocol == "TCP"){
			if (check_multi_port_conflict(obj.get("tcp_port"), checking_port)){
				return false;
			}
		}
		else if (checking_protocol == "UDP"){
			if (check_multi_port_conflict(obj.get("udp_port"), checking_port)){
				return false;
			}
		}
	}
	
	return true;
}

function get_sub_msg(which_node, index){
	var count_node = 0;
	
	if (which_node.childNodes.length >= index){
		for (var i = 0; i < which_node.childNodes.length; i++){
			var node = which_node.childNodes[i];
						
			if (node.nodeType != 3){ // NS6/Mozilla will treat space as an element, so we need to ingore it				
				if (count_node == index){
					return node.firstChild.nodeValue;
				}else{
					count_node++;
				}
			}
		}
	}
	
	return "";
}

/**
 *	check_varible() : check the varible object is in the range or not and it's a number
 *
 *	Parameter(s) :
 *		obj		 	:	a Varible_Obj object which contains a varible information 
 *			 
 * Return : if the varible object is in the range and it's a number, return true else return false
 * 	
 **/
function check_varible(obj){
	var temp_obj = (obj.var_value).split(" ");
	
	if (obj.var_value == ""){	
		msg_obj.warning_msg('MSG002', obj.field_name);	
		return false;
	}else if ((temp_obj.length > 1) || (isNaN(obj.var_value)) || ((obj.var_value).indexOf(".") != -1)){		
		msg_obj.warning_msg('MSG014', obj.field_name);
		return false;
	}else if (parseInt(obj.var_value,10) < obj.min || parseInt(obj.var_value,10) > obj.max){
		msg_obj.warning_msg('MSG015', obj.field_name, obj.min, obj.max);		
		return false;
	}else if (obj.is_even && (parseInt(obj.var_value,10) % 2 != 0)){
		msg_obj.warning_msg('MSG016', obj.field_name);	
		return false;
	}
	
	return true;
}

/**
 *	check_hex()  :	check the input data is a Hex number or not
 *
 *	Parameter(s) :
 *		data		 :	data that you want to check
 *			 
 * Return : if the input data is a Hex number, return true else return false
 * 	
 **/
function check_hex(data){
	data = data.toUpperCase();
	
	for (var i = 0; i < data.length; i++){	
		var temp_char = data.charAt(i);
		
		if (!(temp_char >= 'A' && temp_char <= 'F') && !(temp_char >= '0' && temp_char <= '9')){	
			return false;
		}
	}
	
	return true;
}

/**
 *	check_ascii()  :	check the input data is in the ascii range or not
 *
 *	Parameter(s) :
 *		data		 :	data that you want to check
 *			 
 * Return : if the input data is in the ascii range, return true else return false
 * 	
 **/
function check_ascii(data){
	
	for (var i = 0; i < data.length; i++){	
		var temp_char = data.charCodeAt(i);
		
		if (temp_char < 32 || temp_char > 126){	// if the character is less than a space(0x20) or greater than ~(0x7F)
			return false;
		}
	}
	
	return true;
}

/**
 *	check_space()  :	check the input data is all space or not
 *
 *	Parameter(s) :
 *		data		 :	data that you want to check
 *			 
 * Return : if the input data is all spaces, return true else return false
 * 	
 **/
function check_space(data){
	var count = 0;
	var result = false;
	
	for (var i = 0; i < data.length; i++){	
		var temp_char = data.charCodeAt(i);
		
		if (temp_char == 32){	// if the character is a space
			count++;
		}
	}
	
	if (count == data.length){	// when the checking data is all space
		result = true;
	}
	
	return result;
}

/**
 *	check_name()  :	check the input data shouldn't include "/"
 *
 *	Parameter(s) :
 *		data		 :	data that you want to check
 *			 
 * Return : if the input data doesn't include "/", return true else return false
 * 	
 **/
function check_name(data){	
	for (var i = 0; i < data.length; i++){	
		var temp_char = data.charCodeAt(i);
		
		if (temp_char == 47){	// if the character is "/"
			return false;
		}
	}
	return true;
}

function hex_to_int(hex){
	var dec = 0;
	var result = 0;
	
	hex = hex.toUpperCase();
	dec = hex.charCodeAt(0);
	
	if ((dec >= 48) && (dec <= 57)){
		result = dec - 48;
	}else if ((dec >= 65) && (dec <= 70)){
		result = dec - 55;
	}
	
	return result;
}

function check_multicase_mac(mac, splinter){
	var temp_mac = mac.split(splinter);
	var nibble = hex_to_int((temp_mac[0]).charAt(1));	// get the last 4 bits of first byte
	var result = false;
				
	if ((nibble % 2) != 0){	// if the last bit of first byte is 1, it means this mac adddress is multicast
		result = true;	   			
	}
	
	return result;
}

/**
 *	check_mac()  :	check the input mac address is correct or not
 *
 *	Parameter(s) :
 *		mac		 :	a mac address that you want to check
 *    splinter	 : a splinter to split the mac address
 *			 
 * Return : if the input mac address is correct, return 1 else return 0 or -1
 * 	
 **/
function check_mac(mac, splinter){
    var temp_mac = mac.split(splinter);
    var error = 0;
	 	
	if (temp_mac.length == 6){
		for (var i = 0; i < 6; i++){
	   		var temp_str = temp_mac[i];
						
			if (temp_str == "" || temp_str.length != 2){
	      		error = 1;	//return 1 means mac format error, it will show different message in GUI. ONLY for DHP-W306AV!
	      	}else{
	      		if (!check_hex(temp_str)){
	         		error = 2;
	       		}
	    	}

	    	if (error != 0){
	      		break;
	   		}
		}
	}else{
		error = 1;	//return 1 means mac format error, it will show different message in GUI. ONLY for DHP-W306AV!
	}
   
   return error;
}

/**
 *	check_host_name()  :	check the host name is valid or not
 *
 *	Parameter(s) :
 *		host_name		 :	a host name that you want to check
 *	   which_name		 : the GUI's field name that you want to display in the warning messages
 *			 
 * Return : if the host name is valid, return true else return false
 * 	
 **/
function check_host_name(host_name, which_name){
	var count_numeric = 0;
	
	host_name = host_name.toUpperCase();	// chagne to uppercase for checking easier
	
	for (var i = 0; i < host_name.length; i++){	
		var temp_char = host_name.charCodeAt(i);
		
		if (temp_char >= 48 && temp_char <= 57){	// if the char is between 0 ~ 9
			if (i == 0){	// if the first char is a number
				msg_obj.warning_msg('MSG018', which_name);
				return false;
			}else{
				count_numeric++;
				continue;
			}
		}
		
		if (temp_char == 32 || temp_char == 45 || temp_char == 46){			
			if (i == 0){	// if the first char is space(0x20), or hyphen(0x2D), or dot(0x2E)				
				msg_obj.warning_msg('MSG019', which_name);
				return false;
			}else if (i == host_name.length - 1){				
				msg_obj.warning_msg('MSG020', which_name);
				return false;
			}else if (temp_char == 46){	// if the current character is dot
				if (host_name.charAt(i+1) == "."){	// check the next character is a dot or not					
					msg_obj.warning_msg('MSG021', which_name);
					return false;
				}
			}
		}
		
		// if the char is not between "A" ~ "Z" or space(0x20) or hyphen(0x2D) or dot(0x2E) 
		if ((temp_char < 65 && temp_char != 32 && temp_char != 45 && temp_char != 46) || (temp_char > 90)){ 			
			msg_obj.warning_msg('MSG022', which_name);
			return false;
		}			
	}
	
	if (count_numeric == host_name.length){	// if the host name is all numeric, it's invalid		
		msg_obj.warning_msg('MSG023', which_name);
		return false;
	}
	
	return true;
}

/**
 *	check_ip_order() : check end ip is greater than start ip or not
 *
 *	Parameter(s) :
 *	start_ip		:	a Addr_Obj object which contains Start IP information
 *    end_ip		:  a Addr_Obj object which contains End IP information 
 *			 
 * Return : if the end ip is greater than the start ip, return true else return false
 * 	
 **/
function check_ip_order(start_ip, end_ip){
	var temp_start_ip = start_ip.addr;
	var temp_end_ip = end_ip.addr;

	for (var i = 0; i < temp_start_ip.length; i++){		
		if (parseInt(temp_start_ip[i]) > parseInt(temp_end_ip[i])){
			return false;
		}
	}

	return true;
}


/**
 * is_ipv4_valid
 *	Check is an IP address dotted string is valid.
 **/
function is_ipv4_valid(ipaddr){
	var ip = ipv4_to_bytearray(ipaddr);
	if (ip == 0) {
		return false;
	}
	return true;
}

/**
 * ipv4_to_bytearray
 *	Convert an IPv4 address dotted string to a byte array
 **/
function ipv4_to_bytearray(ipaddr){
	var ip = ipaddr + "";
	var got = ip.match (/^\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*[.]\s*(\d{1,3})\s*$/);
	if (!got) {
		return 0;
	}
	var a = [];
	var q = 0;
	for (var i = 1; i <= 4; i++) {
		q = parseInt(got[i],10);
		if (q < 0 || q > 255) {
			return 0;
		}
		a[i-1] = q;
	}
	return a;
}


/**
 *	check_email() : check email format is correct or not
 *
 *	Parameter(s)  :
 *	which_error	  : warning message words
 *	email_addr	  :	a email address that the user input
 *			 
 * Return : if the email format is correct, return true else return false
 * 	
 **/
function check_email(which_error, email_addr){
	
	if (!check_ascii(email_addr)){
		return;
	}
	
	if (email_addr == ""){
		msg_obj.warning_msg("MSG002", which_error);						
		return false;
	}
	
	//check email used by Regular Expressions 2010/12/10 Pin
	if (email_addr.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1){
		msg_obj.warning_msg("MSG003", which_error);
		return false;
	}
	
	//Only check email format have included "@" & "." or not, and "@" can be the first word & "." can be the last word.
	
	/* with (email_addr){
		var apos=value.indexOf("@")
		var dotpos=value.lastIndexOf(".")
		if (apos < 1 || dotpos-apos < 2){
			msg_obj.warning_msg("MSG003", which_error);
			return false
		}
	} */
	return true;
}

/**
 *	set_mac_addr()  :	display the input mac address on GUI
 *
 *	Parameter(s) :
 *		mac		 :	a mac address that you want to display
 *    splinter	 : a splinter to split the mac address
 *			 
 * Return : none
 * 	
 **/
function set_mac_addr(mac, splinter){
	var temp_mac = mac.split(splinter);
   
	if (temp_mac.length == 6){
		for (var i = 1; i < 7; i++){
			get_by_id("mac" + i).value = temp_mac[i-1];
	 	}
	}	
}

/**
 *	add_splinter_to_mac()  :	to add splinter into mac address
 *
 *	Parameter(s) :
 *		mac		 :	a mac address that you want to add splinter to
 *    splinter	 : a splinter that you wan to add
 *			 
 * Return : a mac address which contains splinter
 * 	
 **/
function add_splinter_to_mac(mac, splinter){
	var temp_mac = "";
   	
	for (var i = 0; i < 12; i+=2){
		temp_mac += mac.substring(i, i+2);
		
		if (i < 10){
			temp_mac += ":";
		}
	}
		
	return temp_mac;
}
		
function copy_element_values(from, to){
	var from_obj;
	var to_obj
					
	from_obj = document.getElementById(from);	
	to_obj = document.getElementById(to);	
			
	if (from_obj){					
		if (from_obj.tagName == "INPUT"){												
			if (from_obj.type == "checkbox"){	// when input field is checkbox	
				to_obj.checked = from_obj.checked;								
				to_obj.defaultChecked = from_obj.defaultChecked;
			}else if (from_obj.type == "text" || from_obj.type == "password" || from_obj.type == "hidden"){	// when input field is text or password or textarea			
				to_obj.value = from_obj.value;
				to_obj.defaultValue = from_obj.defaultValue;
			}else if (from_obj.type == "radio"){	// when input field is radio, for IE only				
				from_obj = document.getElementsByName(from);	
				to_obj = document.getElementsByName(to);	
			
				for (var j = 0; j < from_obj.length; j++){
					to_obj[j].checked = from_obj[j].checked;
					to_obj[j].defaultChecked = from_obj[j].defaultChecked;
				}	
			}
		}else if (from_obj.tagName == "SELECT"){		
			to_obj.selectedIndex = from_obj.selectedIndex;
			
			for (var j = 0; j < from_obj.length; j++){								
				to_obj.options[j].defaultSelected = from_obj.options[j].defaultSelected;									
			}						
		}else if (from_obj.tagName == "TEXTAREA"){					
			to_obj.value = from_obj.value;
			to_obj.defaultValue = from_obj.defaultValue;
		}
	}else {	// when the input field is radio, for firefox
		from_obj = document.getElementsByName(from);	
		to_obj = document.getElementsByName(to);	
			
		for (var j = 0; j < from_obj.length; j++){
			to_obj[j].checked = from_obj[j].checked;
			to_obj[j].defaultChecked = from_obj[j].defaultChecked;
		}	
	}
}

function get_checkbox_value(which_box){
	var obj = get_by_id(which_box);
	var which_value;
	
	if (obj.checked){
		which_value = obj.value;
	}else{
		if (obj.value == "1"){	// when the checkbox doesn't be checked, return the opposite value
			which_value = "0";
		}else if (obj.value == "0"){
			which_value = "1";
		}
	}
	
	return which_value;
}

function get_radio_value(which_radio){
	var obj = get_by_name(which_radio);
	
	for (var i = 0; i < obj.length; i++){
		if (obj[i].checked){
			return obj[i].value;
		}
	}
	
	return "";
}

function set_radio(which_radio, which_value, set_default){
	var obj = get_by_name(which_radio);
	
	if (set_default){	// clear the previous default checked
		for (var i = 0; i < obj.length; i++){		
			obj[i].defaultChecked = false;
		}
	}
	
	for (var i = 0; i < obj.length; i++){
		if (obj[i].value == which_value){
			obj[i].checked = true;
			
			if (set_default){
				obj[i].defaultChecked = true;
			}
			break;
		}
	}		
}

function set_selection(which_combo, which_value, set_default){
	var obj = get_by_id(which_combo);
	
	if (set_default){	// clear the previous default selected
		for (var i = 0; i < obj.options.length; i++){		
			obj.options[i].defaultSelected = false;
		}
	}
	
	for (var i = 0; i < obj.options.length; i++){
		if (obj.options[i].value == which_value){
			obj.options[i].selected = true;
			if (set_default){
				obj.options[i].defaultSelected = true;
			}
			break;
		}
	}		
}

function set_checkbox(which_box, which_value, set_default){
	var obj = get_by_id(which_box);
	
	if (obj.value == which_value){
		obj.checked = true;
		
		if (set_default){
			obj.defaultChecked = true;
		}
	}else{
		obj.checked = false;
	}
}

function set_text(which_text, which_value, set_default){
	var obj = get_by_id(which_text);

	if(obj == null){
		return;
	}
	obj.value = which_value;
	
	if (set_default){
		obj.defaultValue = which_value;
	}
}

function get_mac_addr(which_mac, splinter){
	var mac_addr = "";
	
	for (var i = 1; i < 7; i++){
		mac_addr += get_by_id(which_mac + i).value;
		
		if (i < 6){
			mac_addr += splinter;
		}
	}
	
	return mac_addr;
}

function clear_mac_addr(){
	for (var i = 1; i < 7; i++){
		get_by_id("mac" + i).value = "";	
	}			
}

function disable_static_ip(which_one){
	var conn_type = get_by_name(which_one + "_conn_type");
	
	get_by_id(which_one + "_ip_addr").disabled = conn_type[0].checked;
	get_by_id(which_one + "_subnet_mask").disabled = conn_type[0].checked;
	get_by_id(which_one + "_gateway").disabled = conn_type[0].checked;
}

function disable_idle_time(which_one){
	var conn_mode = get_by_name(which_one + "_conn_mode");
	
	get_by_id(which_one + "_max_idle_time").disabled = !conn_mode[1].checked;
}
			
function disable_all_btn(is_disable){
	var input_objs = document.getElementsByTagName("input");

	if (input_objs != null){
		for (var i = 0; i < input_objs.length; i++){
			if (input_objs[i].type == "button" || input_objs[i].type == "submit"){
				input_objs[i].disabled = is_disable;
			}
		}
	}
}

function disable_all_items(is_disable){
	var input_objs = document.getElementsByTagName("input");
	var select_objs = document.getElementsByTagName("select");

	if (input_objs != null){
		for (var i = 0; i < input_objs.length; i++){
			input_objs[i].disabled = is_disable;
		}
	}

	if (select_objs != null){
		for (var i = 0; i < select_objs.length; i++){
			select_objs[i].disabled = is_disable;
		}
	}
}

function ascii_to_hex(ascii){
	var hex = "";
						
	for (var i = 0; i < ascii.length; i++){
		var dec = ascii.charCodeAt(i);
		var str = "";
		
		str = parseInt(dec / 16, 10);	
		
		if (str > 9){
			str = String.fromCharCode(str + 55);
		}
						
		if ((dec % 16) > 9){
			str += String.fromCharCode((dec % 16) + 55);				
		}else{
			str += (dec % 16) + "";
		}
			
		hex += str;				
	}			
		
	return hex;
}

function hex_to_ascii(hex){
	var ascii = "";
	
	for (var i = 0; i < hex.length; i += 2){
		var temp_hex = "0x" + hex.substring(i, i+2);	
		ascii += String.fromCharCode(temp_hex);
	}
	
	return ascii;
}

function encode_char(encode_str){
	var str = "";
	
	for (var i = 0; i < encode_str.length; i++){
		var ch = encode_str.substring(i, i+1);
		var find = false;
		
		for (var j = 0; j < encoding_char.length; j++){
			if (ch == encoding_char[j]){
				find = true;
			}		
		}
		
		if (find){
			str += "%" + ascii_to_hex(ch);
		}else{
			str += ch;
		}		
	}
	
	return str;
}

function replace_special_char(src_str){
	var dest_src = "";
	
	for (var i = 0; i < src_str.length; i++){
		var ch = src_str.charAt(i);
		
		if (ch == '>'){
			dest_src += "&gt;";
		}else if (ch == '<'){
			dest_src += "&lt;";
		}else if (ch == ' '){
			dest_src += "&nbsp;";
		}else{
			dest_src += ch;
		}
	}
	
	return dest_src;
}

function remove_array(which_array, index){
	var result = new Array();
	var count = 0;
	
	for (var i = 0; i < which_array.length; i++){
		if (i == index){	// when we find the obj we want to remove in which_array
			continue;		// don't copy to the result array
		}
		
		result[count++] = which_array[i];	// copy the obj to the result array
	}
	
	return result;
}

function update_array(which_array, new_array, index){
	var result = new Array();
	var count = 0;
	
	for (var i = 0; i < which_array.length; i++){
		if (i == index){	// when we find the obj we want to update in which_array
			result[count++] = new_array;
			continue;
		}
		result[count++] = which_array[i];	// copy the obj to the result array
	}
	return result;
}

function set_wireless_channel(wireless_domain){
	var channel = get_by_id("channel");
	var start_channel = 1;
	var end_channel = 11;
	var count = 0;
	var channel_num = 2.412;
	
	/*
		0: channels 1-11
		1: channels 1-13
		2: channels 10-11
		3: channels 10-13
		4: channel 14
		5: channels 1-14
		6: channels 3-9
		7: channels 5-13	
	*/
	switch(parseInt(wireless_domain)){
		case 1:
			end_channel = 13;
			break;
		case 2:
			start_channel = 10;
			break;
		case 3:
			start_channel = 10;
			end_channel = 13;
			break;
		case 4:
			start_channel = 14;
			end_channel = 14;
			break;
		case 5:
			end_channel = 14;
			break;
		case 6:
			start_channel = 3;
			end_channel = 9;
			break;
		case 7:
			start_channel = 5;
			end_channel = 13;
			break;																	
	}
	
	channel.options.length = 0;
					
	for (var i = start_channel; i <= end_channel; i++){
		var oOption = document.createElement("OPTION");						
						
		oOption.text = channel_num + " GHz - CH " + i;										
		oOption.value = i;								
		channel_num = Math.round((channel_num+0.005) * Math.pow(10,3)) / Math.pow(10,3);		
		channel.options[count++] = oOption;	  
	}				
}


function get_obj_by_rowid(obj_list, rowid){
	var obj;
	
	for (var i = 0; i < obj_list.length; i++){
		var temp_obj = obj_list[i];
		var temp_rowid = parseInt(temp_obj.get("rowid"));
		
		if (temp_rowid == rowid){
			obj = temp_obj;
			break;			
		}
	}
	
	return obj;
}

function change_color(table_name, row){
	var obj = get_by_id(table_name);
	
	for (var i = 1; i < obj.rows.length; i++){
		if (row == i){
			obj.rows[i].style.backgroundColor = "#FFFF00";
		}else{
			obj.rows[i].style.backgroundColor = "#DFDFDF";
		}
	}       
}

function change_wan_htm(index){
	var redirect_htm;
	
	switch(parseInt(index)){
		case 0:
			redirect_htm = "WAN_Dhcp.htm";
			break;	
		case 1:	
			redirect_htm = "WAN_Static.htm";
			break;
		case 2:	
			redirect_htm = "WAN_Pppoe.htm";
			break;
		case 3:
			redirect_htm = "WAN_Pptp.htm";
			break;
		case 4:
			redirect_htm = "WAN_L2tp.htm";
			break;
		case 5:
			redirect_htm = "WAN_Usb3g.htm";
			break;
		case 6:
			redirect_htm = "WAN_WISP.htm";
			break;	
	}

	location.href = redirect_htm;
}

function set_mutil_data(which_list){								
	var data_list = new Array();
	
	if (which_list != null){
		for (var i = 0; i < which_list.childNodes.length; i++){
			var node = which_list.childNodes[i];
			var obj = new HASH_TABLE();
			
			if (node.nodeType == 1){ // NS6/Mozilla will treat space(nodeType = 3) as an element, so we need to ingore it														
				for (var j = 0; j < node.childNodes.length; j++){
					var child_node = node.childNodes.item(j);							
																	
					if (child_node.nodeType == 1){ // NS6/Mozilla will treat space(nodeType = 3) as an element, so we need to ingore it																								
						if (child_node.firstChild){					
							obj.put(child_node.nodeName, child_node.firstChild.nodeValue);
						}else{
							obj.put(child_node.nodeName, "");
						}																								
					}
				}												
				
				data_list.push(obj);
			}			
		}		
	}
	return data_list;
}

function set_single_data(root){								
	var obj = new HASH_TABLE();
	
	for (var i = 0; i < root.childNodes.length; i++){
		var node = root.childNodes[i];
		var node_value = "";
				
		if (node.nodeType != 3){ // NS6/Mozilla will treat space as an element, so we need to ingore it														
			if (node.firstChild){
				node_value = node.firstChild.nodeValue;			
			}												
			
			obj.put(node.nodeName, node_value);
		}				
	}		
	
	return obj;
}

function set_time_zone(root, set_default){
	var zone_index = get_node_value(root, "zone_index");
	var time_zone = get_by_id("zone_index");
				
	time_zone.selectedIndex = parseInt(zone_index);
	
	if (set_default){
		time_zone.options[parseInt(zone_index)].defaultSelected = true;	
	}
}
			
function check_user_level(which_level){
	if (which_level == "0"){	// if the level is the user level
		
		is_submit = true;		// prevent users click the apply button
		disable_all_items(true);
		
		if (get_by_id("user_only")){
			get_by_id("user_only").style.display = "";
		}
	}
}
			
function get_max_rule_num(which_id){
	var rule_xml = load_xml("xml/rule_num.xml");
	
	return parseInt(get_node_value(rule_xml, which_id));
}

function check_is_same (database_obj, array_obj){
	var result = false;
	
	if (database_obj.length != array_obj.length){
		result = true;
	}else{
		for (var i = 0; i<database_obj.length; i++){
			if (database_obj[i].get("reserved_enable") == array_obj[i].get("reserved_enable")
			&& database_obj[i].get("reserved_name") == array_obj[i].get("reserved_name")
			&& database_obj[i].get("reserved_ip") == array_obj[i].get("reserved_ip")
			&& database_obj[i].get("reserved_mac") == array_obj[i].get("reserved_mac")){
				
				result = false;
			}else
				result = true;
				
			if (result){
				break;
			}	
		}
	}
	return result;
}

function check_modified_settings(){
	var is_modified = false;
	var input_element;
	var select_element;
	
	input_element = document.getElementsByTagName("INPUT");
		
	for (var i = 0; i < input_element.length; i++){
		var obj = input_element[i];
				
		if (obj.type == "checkbox"){	// when input field is checkbox								
			if (obj.checked != obj.defaultChecked){
				is_modified = true;				
			}
		}else if (obj.type == "text" || obj.type == "password" || obj.type == "hidden"){	// when input field is text or password							
			if (obj.value != obj.defaultValue){
				is_modified = true;					
			}
		}else if (obj.type == "radio"){	// when input field is radio, for IE only
			var radio_obj = document.getElementsByName(obj.name);
						
			for (var j = 0; j < radio_obj.length; j++){				
				if (radio_obj[j].checked != radio_obj[j].defaultChecked){
					is_modified = true;
					break;
				}
			}
		}
		
		if (is_modified){
			break;
		}	
	}

	if (!is_modified){
		select_element = document.getElementsByTagName("SELECT");
		for (var i = 0; i < select_element.length; i++){
			var obj = select_element[i];
				
			for (var j = 0; j < obj.options.length; j++){
				var opt = obj.options[j];
						
				if (opt.defaultSelected){	// if this option is selected by default, check the current index is equal to this option
					if (j != obj.selectedIndex){	// if not, then this selection has been modified
						is_modified = true;
						break;
					}
				}

				if (is_modified){
					break;
				}				
			}
			
			if (is_modified){
				break;
			}	
		}
		if (!is_modified){
			if (arguments.length == 2){
				is_modified = check_is_same (arguments[0], arguments[1]);
			}
		}
	}
	return is_modified;
}
			
function check_is_modified(redirect_page){	
	var result = false;
	
	if (arguments.length == 3){
		result = check_modified_settings(arguments[1], arguments[2]);
		
	}else{
		result = check_modified_settings();
	}

	if (result){
		if (!msg_obj.confirm_msg('MSG028', "\n", "\n")){
			return;
		}	
	}
	
	if (redirect_page == "support.dlink.com"){
		location.href = "http://" + redirect_page;
	}else{
		location.href = redirect_page;
	}
}

function check_is_changed(){
	var result = false;
	
	if (arguments.length == 2){
		result = check_modified_settings(arguments[0], arguments[1]);
		
	}else{
		result = check_modified_settings();
	}
		
	if (!result){	
		return msg_obj.confirm_msg('MSG029');
	}
	
	return result;
}

function check_change_text(which_text){
	var is_modified = false;
	var obj = get_by_id(which_text);
	
	if (obj.value != obj.defaultValue){
		is_modified = true;					
	}
	
	return is_modified;
}

function check_change_selection(which_select){
	var is_modified = false;
	var obj = get_by_id(which_select);
	
	for (var i = 0; i < obj.options.length; i++){
		var opt = obj.options[i];
				
		if (opt.defaultSelected){	// if this option is selected by default, check the current index is equal to this option
			if (i != obj.selectedIndex){	// if not, then this selection has been modified
				is_modified = true;
				break;
			}
		}

		if (is_modified){
			break;
		}				
	}
	
	return is_modified;
}

function check_change_checkbox(which_box){
	var is_modified = false;
	var obj = get_by_id(which_box);
	
	if (obj.checked != obj.defaultChecked){
		is_modified = true;				
	}
	
	return is_modified;
}

function check_change_radio(which_radio){
	var is_modified = false;
	var radio_obj = get_by_name(which_radio);
						
	for (var i = 0; i < radio_obj.length; i++){				
		if (radio_obj[i].checked != radio_obj[i].defaultChecked){
			is_modified = true;
			break;
		}
	}
	
	return is_modified;
}

function load_help_hints(which_hints, check_modify){
	//var which_lang = get_lang_xml();	
   //var help_xml = load_xml("xml/hints_" + which_lang + ".xml");
	var help_xml = load_xml("xml/hints.xml");		
	var root_hint = get_xml_node(help_xml, which_hints);
	var my_hint = null;
	var help_hints = get_by_id("show_help_hints");
	var more_str = "";
	
	
	if(root_hint != null){
		my_hint = get_xml_node(root_hint, "help_hints");
	}
	
	
	if (!root_hint || !my_hint){
		help_xml = load_xml("default_xml/hints.xml");
		root_hint = get_xml_node(help_xml, which_hints);
		my_hint = get_xml_node(root_hint, "help_hints");
	}
			
	more_str = "<strong>" + lang_obj.display('LNG029') + "...</strong>";
	
	for (var i = 0; i < my_hint.childNodes.length; i++){
		var node = my_hint.childNodes[i];
	
		if (node.nodeType != 3){
			if (node.firstChild){
      		more_str += "<p>" + node.firstChild.nodeValue + "</p>";
      	}
      }
  	}
   
   if (get_child_value(root_hint, "more_href").length > 0){
	   more_str += "<p class='more'>";      							
		if (check_modify){   							 
	   	more_str += "<a href=\"javascript:check_is_modified('" + get_child_value(root_hint, "more_href") + "')\">";
	   }else{
	   	more_str += "<a href=\"" + get_child_value(root_hint, "more_href") + "\">";
	   }     
	   
	   more_str += lang_obj.display('LNG030') + "...</a></p>";
   }
   
   help_hints.innerHTML = more_str;  
}


function exit_initial_page(which_page){

	if (check_modified_settings()){
		if (!msg_obj.confirm_msg('MSG076')){
			return;
		}
	}	
	location.href = which_page;	
}

function exit_login_page(){
	xml_request = new XMLRequest(return_real_login);
	var para;
	
	if (check_modified_settings()){
		if (!msg_obj.confirm_msg('MSG076')){
			return;
		}
	}	
	para = "request=update_entry&table_name=restore_default&rowid=0&factory_default=0";
	xml_request.exec_cgi(para);
}

function exit_index_page(){
	xml_request = new XMLRequest(return_to_default);
	var para;
	
	if (check_modified_settings()){
		if (!msg_obj.confirm_msg('MSG076')){
			return;
		}
	}	
	para = "request=login&admin_user_name="+ encode_base64("admin") + "&admin_user_pwd=" + encode_base64("");
	xml_request.exec_cgi(para);
}

function clone_wan_mac(http_req){
	var mac_xml = http_req.responseXML;
	
	if (check_user_info(mac_xml.getElementsByTagName("redirect_page")[0])){														
		get_by_id("wan_mac").value = get_node_value(mac_xml, "remote_mac");	
			
		get_by_id("clone").disabled = false;			
	}		
}

function clone_mac_addr(which_func){
	var xml_request = new XMLRequest(eval(which_func));
	var para = "request=clone_mac_addr";
		
	get_by_id("clone").disabled = true;
	xml_request.exec_cgi(para);
}

function check_resip_order(reserved_ip,start_ip, end_ip){
	var temp_start_ip = start_ip.addr;
	var temp_end_ip = end_ip.addr;
	var temp_res_ip = reserved_ip.addr;
	var total1 = ip_num(temp_start_ip);
	var total2 = ip_num(temp_end_ip);
    var total3 = ip_num(temp_res_ip);
   
   if(total1 <= total3 && total3 <= total2){
        return false;
	}
	 
	return true;
}

function ip_num(IP_array){
	var total1 = 0;
	
	if(IP_array.length > 1){
   		total1 += parseInt(IP_array[3],10);
	    total1 += parseInt(IP_array[2],10)*256;
	    total1 += parseInt(IP_array[1],10)*256*256;
	    total1 += parseInt(IP_array[0],10)*256*256*256;
	}
	
	return total1;
}

function detectBrowser(){ 
  var sAgent = navigator.userAgent.toLowerCase();
  this.isIE = (sAgent.indexOf("msie")!=-1); //IE6.0-7
  this.isFF = (sAgent.indexOf("firefox")!=-1);//firefox
  this.isSa = (sAgent.indexOf("safari")!=-1);//safari
  this.isOp = (sAgent.indexOf("opera")!=-1);//opera
  this.isNN = (sAgent.indexOf("netscape")!=-1);//netscape
  this.isMa = this.isIE;//marthon
  this.isOther = (!this.isIE && !this.isFF && !this.isSa && !this.isOp && !this.isNN && !this.isSa);//unknown Browser
}

function encode (psstrs, iLen) {
	var map1="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
   var oDataLen = (iLen*4+2)/3;
   var oLen = ((iLen+2)/3)*4;
   var out = '';
   var ip = 0;
   var op = 0;
   
   while (ip < iLen) {
      var xx = psstrs.charCodeAt(ip++);
      var yy = ip < iLen ? psstrs.charCodeAt(ip++) : 0;
      var zz = ip < iLen ? psstrs.charCodeAt(ip++) : 0;
      var aa = xx >>> 2;
      var bb = ((xx &   3) << 4) | (yy >>> 4);
      var cc = ((yy & 0xf) << 2) | (zz >>> 6);
      var dd = zz & 0x3F;
      out += map1.charAt(aa);
      op++;
      out += map1.charAt(bb);
      op++;
      out += op < oDataLen ? map1.charAt(cc) : '='; 
      op++;
      out += op < oDataLen ? map1.charAt(dd) : '='; 
      op++; 
   }
   
   return out; 
}

function encode_base64(str) {
	return encode(str, str.length); 
}

function add_dhcp_client(which_list, dhcp_client, which_id, extra_flag){
	var dhcp_client_list = get_by_id(which_list);
	
	dhcp_client_list.options.length = 1;
					
	for (var i = 0; i < dhcp_client.length; i++){
		var obj = dhcp_client[i];
		var oOption = document.createElement("OPTION");						
				
		oOption.value = obj.get(which_id);	// client_mac, client_ip
		
		if (arguments.length == 3){		
			oOption.text = obj.get("client_name");										
		}else if (arguments.length == 4){	
			oOption.text = obj.get("client_name") + "(" + obj.get(extra_flag) + ")";			
		}
			
		dhcp_client_list.options[i+1] = oOption;	  
	}	
}

//add schedule list in UI: virtual_server, port_forwarding, application rules
function add_schedule_option(which_list, schedules){
	var schedule_list = get_by_id(which_list);
	
	schedule_list.options.length = 2;		// Always, Never
	
	for (var i = 0; i < schedules.length; i++){
		var obj = schedules[i];
		var oOption = document.createElement("OPTION");						
		
		oOption.text = obj.get("schedule_name");										
		oOption.value = obj.get("schedule_name");	
		
		schedule_list.options[i+2] = oOption;		  
	}

}

//add inbound filter list in UI: virtual_server, port_forwarding, application rules
function add_inbound_filter(which_list, inbound_filter){
	var inbound_filter_list = get_by_id(which_list);
	
	inbound_filter_list.options.length = 2;	// Allow All, Deny All
					
	for (var i = 0; i < inbound_filter.length; i++){
		var obj = inbound_filter[i];
		var oOption = document.createElement("OPTION");						
				
		oOption.value = obj.get("filter_name");
		oOption.text = obj.get("filter_name");										
				
		inbound_filter_list.options[i+2] = oOption;	  
	}	
}

function convert_protocol_str(which_value){
	var str;
	
	if (which_value == "6"){
		str = "TCP";
	}else if (which_value == "17"){
		str = "UDP";
	}else if (which_value == "256"){
		str = "Both";
	}else if (which_value == "257"){
		str = "All";
	}else{
		str = which_value;
	}
	
	return str;
}

function get_unuse_rowid(which_list, max_num){
	var unuse_rowid;
	var is_used;
	
	for (var i = 0; i < max_num; i++){
		is_used = false;
		
		for (var j = 0; j < which_list.length; j++){
			var obj = which_list[j];
			
			if (i == parseInt(obj.get("rowid"))){
				is_used = true;
				break;
			}
		}
		
		if (!is_used){
			unuse_rowid = i;
			break;
		}
	}
	
	return unuse_rowid;
}

function check_support_bookmark(){
	var isMSIE = (-[1,]) ? false : true;
	var is_support = 0;
	
	if(window.sidebar && window.sidebar.addPanel){	//Firefox
		is_support = 1;
	}else if (isMSIE && window.external) {	//IE favorite
		is_support = 2;
	}
	
	return is_support;
}

function check_ie_version(){
	var version = 6;
	
	if (navigator.appName == 'Microsoft Internet Explorer') {
		var agent = navigator.userAgent;
		var index = agent.indexOf("MSIE");
		
		if (index != -1){
			version = parseFloat(agent.substring(index + 5, index+8));
		}		
	}
	
	    if (version >= 8) {
		return true;	
	    }
	
	return false;
}

function create_favorite_link(url){
	var is_support = check_support_bookmark();
		
	if (is_support > 0){	
		if (msg_obj.confirm_msg('MSG140')){
			if(is_support == 1){	//Firefox
				window.sidebar.addPanel(lang_obj.display('WZ188'),url,"");			
			}else if (is_support == 2) {	//IE favorite
			if (check_ie_version()){	// when IE version is 8 or above				
					window.external.AddToFavoritesBar(url, lang_obj.display('WZ188'));
			}else{				
					window.external.AddFavorite(url, lang_obj.display('WZ188'));
				}
			}
		}
	}	
}