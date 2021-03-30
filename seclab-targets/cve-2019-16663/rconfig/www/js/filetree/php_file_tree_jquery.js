$(document).ready( function() {
	 // var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
	 
	// custom - ss - hide bottom show and hide and top buttons if in a div called $("#bottomButtons").show();
	// can be show if show all button is click on a given page
	$("#bottomButtons").hide();
	
	// Hide all subfolders at startup
	$(".php-file-tree").find("UL").hide();
	
	
	// Expand/collapse on click
	$(".pft-directory A").click( function() {
		$(this).parent().find("UL:first").toggle("medium");
		if( $(this).parent().attr('className') == "pft-directory" ) return false;
	});

});
