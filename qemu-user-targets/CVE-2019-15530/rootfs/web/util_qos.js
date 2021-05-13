/*
 * do_block_enable()
 *	Enables or disables all <input> elements within the given block element
 *
 * EXAMPLE
 * <fieldset id="blah_settings"><input.../><input..../></fieldset>
 * do_block_enable(mf.blah_settings, false);
 * The above call would set disable to true on all input elements contained within the given fieldset.
 */
function do_block_enable(block, enable)
{
	for (var c = 0; c < block.childNodes.length; ++c) {
		var child_element = block.childNodes[c];

		/*
		 * If the current element has children too, then we recurse into it
		 */
		if (child_element.hasChildNodes()) {
			do_block_enable(child_element, enable);
		}

		/*
		 * We only process input elements
		 */
		var tag_name = new String(child_element.tagName);
		if (tag_name.toUpperCase() != "INPUT") {
			continue;
		}
		child_element.disabled = !enable;
	}
}

/*
 * set_radio
 *	Sets the radio button whose value matches that give (unchecking all others in the group)
 * NOTE: Radio group 
 */
function set_radio(radio_group, value)
{
	/*
	 * Convert value to a string so that it will match properly on the radio.value property
	 */
	value = "" + value;

	/*
	 * Iterate through the radio groups radio buttons, activating the one with the matching value
	 */
	for (var i = 0; i < radio_group.length; i++) {
		if (radio_group[i].value == value) {
			radio_group[i].checked = true;
			return;
		}
	}
}

/*
 * is_number
 *	Return true if a value represents a number, else return false.
 */
function is_number(value)
{
	var str = value + "";
	return str.match(/^-?\d*\.?\d+$/) ? true : false;
}


/*
 * is_blank
 *	Return true if value is a blank (i.e. "").
 */
function is_blank(value)
{
	var str = value + "";
	return str.match(/^\s*$/) ? true : false;
}


/*
 * trim_string
 *	Remove leading and trailing blank spaces from a string.
 */
function trim_string(str)
{
	var trim = str + "";
	trim = trim.replace(/^\s*/, "");
	return trim.replace(/\s*$/, "");
}


/*
 * integer_to_bytearray
 *	Convert an integer value to a byte array of size 'length'.
 */
function integer_to_bytearray(value, length)
{
	var barray = [];
	for (var i = 0; i < length; i++) {
		barray[i] = (value >>> ((length - 1 - i) * 8)) & 0xFF;
	}
	return barray;
}


/*
 * bytearray_to_integer
 *	Convert a byte array to an integer (optional start and end indexes).
 */
function bytearray_to_integer(barray, start, end)
{
	if (typeof(start) == 'undefined') {
		start = 0;
	}
	if (typeof(end) == 'undefined') {
		end = barray.length;
	}
	var num = 0;
	for (var i = start; i < end; i++) {
		num = (num << 8) + barray[i];
	}
	return num;
}


/*
 * ipv4_to_integer
 *	Convert an IPv4 address dotted string to an integer.
 */
function ipv4_to_integer(ipaddr)
{
	var ip = ipaddr + "";
	var got = ip.match (/^\s*(\d+)\s*[.]\s*(\d+)\s*[.]\s*(\d+)\s*[.]\s*(\d+)\s*$/);
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
		x = x | (q << ((4 - i) * 8));
	}
	return x;
}


/*
 * integer_to_ipv4
 *	Convert an integer (signed or not) to an IPv4 address dotted string.
 */
function integer_to_ipv4(num)
{
	var ip = "";
	var q = 0;
	var n = 0;
	for (var i = 3; i >= 0; i--) {
		n = i * 8;
		q = (num & (0xFF << n)) >> n;
		if (q < 0) {
			q = q & 0xFF;
		}
		ip += q.toString(10);
		if (i > 0) {
			ip += ".";
		}
	}
	return ip;
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


/*
 * ipv4_to_bytearray
 *	Convert an IPv4 address dotted string to a byte array
 */
function ipv4_to_bytearray(ipaddr)
{
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


/*
 * bytearray_to_ipv4()
 *	Convert a byte array to an IP address dotted string (optional start index).
 */
function bytearray_to_ipv4(array, start)
{
	if (typeof(start) == 'undefined') {
		start = 0;
	}
	if (array.length < 4) {
		return null;
	}
	var ip = "";
	var q = 0;
	for (var i = 0; i < 4; i++) {
		q = array[i + start];
		if (q < 0 || q > 255) {
			return null;
		}
		ip += q.toString(10);
		if (i < 3) {
			ip += ".";
		}
	}
	return ip;
}


/*
 * is_ipv4_valid
 *	Check is an IP address dotted string is valid.
 */
function is_ipv4_valid(ipaddr)
{
	var ip = ipv4_to_bytearray(ipaddr);
	if (ip === 0) {
		return false;
	}
	return true;
}


/*
 * is_port_valid
 *	Check if a port is valid.
 */
function is_port_valid(port)
{
	return (is_number(port) && port >= 0 && port < 65536);
}

function bigger_than(port1, port2)
{
	return ((port1-0) > (port2-0));
}

/*
 * is_mac_valid()
 *	Check if a MAC address is in a valid form.
 *	Allow 00:00:00:00:00:00 and FF:FF:FF:FF:FF:FF if optional argument is_full_range is true.
 */
function is_mac_valid(mac, is_full_range)
{
	var macstr = mac + "";
	var got = macstr.match(/^[0-9a-fA-F]{2}[:-]?[0-9a-fA-F]{2}[:-]?[0-9a-fA-F]{2}[:-]?[0-9a-fA-F]{2}[:-]?[0-9a-fA-F]{2}[:-]?[0-9a-fA-F]{2}$/);
	if (!got) {
		return false;
	}
	macstr = macstr.replace (/[:-]/g, '');
	if (!is_full_range && (macstr.match(/^0{12}$/) || macstr.match(/^[fF]{12}$/))) {
		return false;
	}

	return true;
}

/*
 * is_mac_null()
 *	Check if a MAC address is 00:00:00:00:00:00 or null.
 */
function is_mac_null(mac)
{
	if (is_blank(mac)) {
		return true;
	}
	if(!is_mac_valid(mac, true)) {
		return false;
	}
	var macstr = mac.replace(/[:-]/g, '');
	return macstr.match(/^[0]{12}$/) ? true : false;
}

/*
 * is_mac_broadcast()
 *	Check if a MAC address is FF:FF:FF:FF:FF:FF.
 */
function is_mac_broadcast(mac)
{
	if(!is_mac_valid(mac, true)) {
		return false;
	}
	var macstr = mac.replace(/[:-]/g, '');
	return macstr.match(/^[fF]{12}$/) ? true : false;
}

/*
 * is_mac_multicast()
 *	Check if a MAC address is a multicast.
 */
function is_mac_multicast(mac)
{
	if(!is_mac_valid(mac)) {
		return false;
	}
	var macstr = mac.replace (/[:-]/g, '');
	var octet = new Number("0x" + mac.substring(0,2));
	return (octet & 1) ? true : false;
}

/*
 * are_values_equal()
 *	Compare values of types boolean, string and number. The types may be different.
 *	Returns true if values are equal.
 */
function are_values_equal(val1, val2)
{
	/* Make sure we can handle these values. */
	switch (typeof(val1)) {
	case 'boolean':
	case 'string':
	case 'number':
		break;
	default:
		// alert("are_values_equal does not handle the type '" + typeof(val1) + "' of val1 '" + val1 + "'.");
		return false;
	}

	switch (typeof(val2)) {
	case 'boolean':
		switch (typeof(val1)) {
		case 'boolean':
			return (val1 == val2);
		case 'string':
			if (val2) {
				return (val1 == "1" || val1.toLowerCase() == "true" || val1.toLowerCase() == "on");
			} else {
				return (val1 == "0" || val1.toLowerCase() == "false" || val1.toLowerCase() == "off");
			}
			break;
		case 'number':
			return (val1 == val2 * 1);
		}
		break;
	case 'string':
		switch (typeof(val1)) {
		case 'boolean':
			if (val1) {
				return (val2 == "1" || val2.toLowerCase() == "true" || val2.toLowerCase() == "on");
			} else {
				return (val2 == "0" || val2.toLowerCase() == "false" || val2.toLowerCase() == "off");
			}
			break;
		case 'string':
			if (val2 == "1" || val2.toLowerCase() == "true" || val2.toLowerCase() == "on") {
				return (val1 == "1" || val1.toLowerCase() == "true" || val1.toLowerCase() == "on");
			}
			if (val2 == "0" || val2.toLowerCase() == "false" || val2.toLowerCase() == "off") {
				return (val1 == "0" || val1.toLowerCase() == "false" || val1.toLowerCase() == "off");
			}
			return (val2 == val1);
		case 'number':
			if (val2 == "1" || val2.toLowerCase() == "true" || val2.toLowerCase() == "on") {
				return (val1 == 1);
			}
			if (val2 == "0" || val2.toLowerCase() == "false" || val2.toLowerCase() == "off") {
				return (val1 === 0);
			}
			return (val2 == val1 + "");
		}
		break;
	case 'number':
		switch (typeof(val1)) {
		case 'boolean':
			return (val1 * 1 == val2);
		case 'string':
			if (val1 == "1" || val1.toLowerCase() == "true" || val1.toLowerCase() == "on") {
				return (val2 == 1);
			}
			if (val1 == "0" || val1.toLowerCase() == "false" || val1.toLowerCase() == "off") {
				return (val2 === 0);
			}
			return (val1 == val2 + "");
		case 'number':
			return (val2 == val1);
		}
		break;
	default:
		return false;
	}
}


/*
 * do_expanse_collapse
 *	Expanse (unhide) or collapse (hide) a block in the page when e.g. clicking on an element,
 *	and change the value of this element (or any other).
 *	elt_id: id of the element that will have its value changed
 *	alt_val: new value for eltid
 *	toggle_id: id of the element that will be hidden or unhidden.
 *
 *	Example of usage:
 *	<input type="button" id="testbt" value="Expand" onclick="do_expanse_collapse(this.id, 'Collapse', 'hidethediv');" />
 *	<div id="hidethediv"> ... </div>
 */
function do_expanse_collapse(elt_id, alt_val, toggle_id)
{
	/* Show or hide the element. */
	var elt = document.getElementById(toggle_id);
	if (!elt) {
		return;
	}
	if (elt.style.display != 'none') {
		elt.style.display = 'none';
	} else {
		elt.style.display = '';
	}

	if (alt_val === "") {
		return;
	}

	elt = document.getElementById(elt_id);
	if (!elt) {
		return;
	}

	/* If this element doesn't have this attribute, create it. */
	if (!elt.getAttribute('alt_value')) {
		var node = document.createAttribute('alt_value');
		node.value = alt_val;
		/* For Safari. */
		if (node.value != alt_val) {
			node.nodeValue = alt_val;
		}
		elt.setAttributeNode(node);
	}

	/* Swap the element's value and alt_value. */
	var attr = elt.getAttribute('alt_value');
	elt.setAttribute('alt_value', elt.value);
	elt.value = attr;
}


/*
 * add_onload_listener
 *	Add a listener function to the window.onload event.
 */
function add_onload_listener(func)
{
	if (typeof(window.addEventListener) != 'undefined') {
		// DOM level 2
		window.addEventListener('load', func, false);
	} else if (typeof(window.attachEvent) != 'undefined') {
		// IE
		window.attachEvent('onload', func);
	} else {
		if (typeof(window.onload) != 'function') {
			window.onload = func;
		} else {
			var oldfunc = window.onload;
			window.onload = function() {
				oldfunc();
				func();
			};
		}
	}
}

/*
 * add_onunload_listener
 *	Add a listener function to the window.onunload event.
 */
function add_onunload_listener(func)
{
	if (typeof(window.addEventListener) != 'undefined') {
		window.addEventListener('unload', func, false);
	} else if (typeof(window.attachEvent) != 'undefined') {
		window.attachEvent('onunload', func);
	} else {
		if (typeof(window.onunload) != 'function') {
			window.onload = func;
		} else {
			var oldfunc = window.onunload;
			window.onunload = function() {
				oldfunc();
				func();
			};
		}
	}
}

/*
 * set_form_always_modified
 *	 Always set the custom attribute "modified" to "true. 
 */
function set_form_always_modified(form_id)
{
	var df = document.forms[form_id];
	if (!df) {
		return;
	}
	df.setAttribute('modified', "true");
}

/*
 * set_form_default_values
 *	Save a form's current values to a custom attribute.
 */
function set_form_default_values(form_id)
{
	var df = document.forms[form_id];
	if (!df) {
		return;
	}
	for (var i = 0, k = df.elements.length; i < k; i++) {
		var obj = df.elements[i];
		if (obj.getAttribute('modified') == 'ignore') {
			continue;
		}
		var name = obj.tagName.toLowerCase();
		if (name == 'input') {
			var type = obj.type.toLowerCase();
			if ((type == 'text') || (type == 'textarea') || (type == 'password') || (type == 'hidden')) {
				obj.setAttribute('default', obj.value);
				/* Workaround for FF error when calling focus() from an input text element. */
				if (type == 'text') {
					obj.setAttribute('autocomplete', 'off');
				}
			} else if ((type == 'checkbox') || (type == 'radio')) {
				obj.setAttribute('default', obj.checked);
			}
		} else if (name == 'select') {
			var opt = obj.options;
			for (var j = 0; j < opt.length; j++) {
				opt[j].setAttribute('default', opt[j].selected);
			}
		}
	}
	df.setAttribute('saved', "true");
}


/*
 * is_form_modified
 *	Check if a form's current values differ from saved values in custom attribute.
 *	Function skips elements with attribute: 'modified'= 'ignore'. 
 */
function is_form_modified(form_id)
{
	var df = document.forms[form_id];
	if (!df) {
		return false;
	}
	if (df.getAttribute('modified') == "true") {
		return true;
	}
	if (df.getAttribute('saved') != "true") {
		return false;
	}

	for (var i = 0, k = df.elements.length; i < k; i++) {
		var obj = df.elements[i];
		if (obj.getAttribute('modified') == 'ignore') {
			continue;
		}
		var name = obj.tagName.toLowerCase();
		if (name == 'input') {
			var type = obj.type.toLowerCase();
			if (((type == 'text') || (type == 'textarea') || (type == 'password') || (type == 'hidden')) &&
					!are_values_equal(obj.getAttribute('default'), obj.value)) {
				return true;
			} else if (((type == 'checkbox') || (type == 'radio')) && !are_values_equal(obj.getAttribute('default'), obj.checked)) {
				return true;
			}
		} else if (name == 'select') {
			var opt = obj.options;
			for (var j = 0; j < opt.length; j++) {
				if (!are_values_equal(opt[j].getAttribute('default'), opt[j].selected)) {
					return true;
				}
			}
		}
	}
	return false;
}


/*
 * reset_form()
 *	Reset a form with previously saved default values.
 *	Function skips elements with attribute: 'modified'= 'ignore'. 
 */
function reset_form(form_id)
{
	var df = document.forms[form_id];
	if (!df) {
		return;
	}
	if (df.getAttribute('saved') != "true") {
		return;
	}

	for (var i = 0, k = df.elements.length; i < k; i++) {
		var obj = df.elements[i];
		if (obj.getAttribute('modified') == 'ignore') {
			continue;
		}
		var name = obj.tagName.toLowerCase();
		var value;
		if (name == 'input') {
			var type = obj.type.toLowerCase();
			if ((type == 'text') || (type == 'textarea') || (type == 'password') || (type == 'hidden')) {
				obj.value = obj.getAttribute('default');
			} else if ((type == 'checkbox') || (type == 'radio')) {
				value = obj.getAttribute('default');
				switch (typeof(value)) {
				case 'boolean':
					obj.checked = value;
					break;
				case 'string':
					if (value == "1" || value.toLowerCase() == "true" || value.toLowerCase() == "on") {
						obj.checked = true;
					}
					if (value == "0" || value.toLowerCase() == "false" || value.toLowerCase() == "off") {
						obj.checked = false;
					}
					break;
				}
			}
		} else if (name == 'select') {
			var opt = obj.options;
			for (var j = 0; j < opt.length; j++) {
				value = obj[j].getAttribute('default');
				switch (typeof(value)) {
				case 'boolean':
					obj[j].selected = value;
					break;
				case 'string':
					if (value == "1" || value.toLowerCase() == "true" || value.toLowerCase() == "on") {
						obj[j].selected = true;
					}
					if (value == "0" || value.toLowerCase() == "false" || value.toLowerCase() == "off") {
						obj[j].selected = false;
					}
					break;
				}
			}
		}
	}
}


/*
 * is_mac_octet_valid
 * 	Check if the MAC address is out of range [00-FF]
 */
function is_mac_octet_valid(mac)
{
	if (mac.value.length == 1) {
		mac.value = "0" + mac.value;
	}

	var d1 = parseInt(mac.value.charAt(0), 16);
	var d2 = parseInt(mac.value.charAt(1), 16);

	if (isNaN(d1) || isNaN(d2)) {
		mac.value = mac.defaultValue;
		return false;
	}
	return true;
}


