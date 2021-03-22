// JavaScript Document

var url, mac;
$(function() {
	var url_str = window.location.href.split("?")[1].split("&");
	//http://192.168.0.1/yun_safe.html?mac=>>>&domain=
	mac = url_str[0].split("=")[1];
	url = url_str[1].split("=")[1];
	$("#continue_open").on("click",continuePage);
})

function returnBack(data) {
	//alert("http://" + url + "?" + Math.random());
	window.location = "http://" + url + "?" + Math.random();
}



function continuePage() {
	var data = "mac=" + mac + "&domain=" + url;
	$.post("goform/InsertWhite",data, returnBack);
}

