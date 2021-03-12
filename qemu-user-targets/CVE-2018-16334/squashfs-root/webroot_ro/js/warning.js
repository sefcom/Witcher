var url;
$(function() {
	url = window.location.href;
	$("#close-page").on("click",continuePage);
})

function setUrl() {
	var data = "whiteFlag=0&url="+url.slice(0,url.length-13);
	$.post("goform/InsertWhite",data);
	
}

function continuePage() {
	window.history.back();
}

window.onload = function() {
	setUrl();	
}