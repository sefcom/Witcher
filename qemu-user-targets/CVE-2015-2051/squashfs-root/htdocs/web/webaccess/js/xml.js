/**
 *	xmlhttp_getxml() : obtain XML object from Web Server (using XMLHttpRequest)
 *
 *	Parameter(s) :
 *		which_one : the name of XML object that you want to obtain
 *
 * Return :	a XMLHttpRequest object
 * 	
 **/

function xmlhttp_getxml(which_one){
	var xml_request=new XMLHttpRequest();
	
	if (xml_request!=null){  
  	xml_request.open("GET",which_one,false);
  	xml_request.send(null);
  }else{
  	alert("Your browser does not support XMLHTTP.11");
  	return false;
  }
	return(xml_request);
}


/**
 *	load_xml() : obtain XML object from Web Server
 *
 *	Parameter(s) :
 *		which_one : the name of XML object that you want to obtain
 *
 * Return :	a XML object
 * 	
 **/
function load_xml(which_one){
	var my_doc;
	
	if (window.ActiveXObject){	// code for IE
		my_doc = new ActiveXObject("Microsoft.XMLDOM");
		my_doc.async = false;
		my_doc.load(which_one);
	}else if (document.implementation && document.implementation.createDocument && document.implementation.createDocument.load){	// code for Mozilla, Firefox, Opera, etc.
		my_doc = document.implementation.createDocument("","",null);
		my_doc.async = false;
		my_doc.load(which_one);
	}else if(window.XMLHttpRequest){	//code for Apple Safari, Google Chrome, etc.
		my_doc=xmlhttp_getxml(which_one).responseXML;
	}else{
		alert('Your browser cannot handle this script');
	}
	
	return my_doc;
}

/**
 *	get_xml_node() : obtain an element from a XML object
 *
 *	Parameter(s) :
 *		which_doc : a XML object that you want to obtain an element from
 *		which_id	 :	the element's name that you want to obtain
 *
 * Return : element object
 * 	
 **/
function get_xml_node(which_doc, which_id){
	return which_doc.getElementsByTagName(which_id)[0];
}

/**
 *	get_node_value() : obtain an element's value from a XML object
 *
 *	Parameter(s) :
 *		which_doc : a XML object that you want to obtain an element's value from
 *		which_id	 :	the element's name that you want to obtain its value
 *
 * Return : element's value
 * 	
 **/
function get_node_value(which_doc, which_id){		
	var node = get_xml_node(which_doc, which_id);
	var node_value = "";
	
	if (node != null){
		if (node.nodeType != 3){	// NS6/Mozilla will treat space as an element, so we need to ingore it
			if (node.childNodes.length > 0){
				node_value = node.childNodes[0].nodeValue;	
			}
		}
	}
	
	return node_value;
}


/**
 *	get_child_value() : obtain a node's child value from a XML object
 *
 *	Parameter(s) 	:
 *		which_node 	: a parent element that you want to obtain its child's value from
 *		which_id	 	: the child's name
 *
 * Return : child's value
 * 	
 **/
function get_child_value(which_node, which_id){
		
	for (var i = 0; i < which_node.childNodes.length; i++){
		var node = which_node.childNodes[i];
					
		if (node.nodeType != 3){ // NS6/Mozilla will treat space as an element, so we need to ingore it				
			if (node.nodeName == which_id){
				if (node.childNodes.length > 0){ // when element has value
					return node.firstChild.nodeValue;
				}else{
					break;
				}
			}
		}
	}
		
	return "";
}
