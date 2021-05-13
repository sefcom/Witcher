var globalSortCol;
//var tblId = "avblStationList";
//alert($(tableId));
var headerClickedVal = -1;
var headerClickedDir ="";

function getNumHeaders(tableId) {
	var numHeaders = fetchObjectById(tableId).rows[0].cells.length;
	return numHeaders;
}
function getNumHeaderRows(tableId) {
    if (fetchObjectById(tableId).rows[1] != undefined)
        var numHeaderRows = (fetchObjectById(tableId).rows[1].cells[0].tagName == 'TH')?2:1;
	return (numHeaderRows)?numHeaderRows:1;
}
function headerClicked(tableId, id_num) {
	var numHeaders = getNumHeaders(tableId);
	for(var i = 0; i < numHeaders; i++) {
		$(tableId+'_down_'+i).style.display='none';
		$(tableId+'_up_'+i).style.display='none';
	}
	
	if(headerClickedVal == id_num && headerClickedDir == "d") {
		$(tableId+'_up_'+id_num).style.display='';
		headerClickedDir = "u";
		reverseTable(tableId);
	}
	else {
		$(tableId+'_down_'+id_num).style.display='';
		headerClickedVal = id_num;
		headerClickedDir = "d";
		sortTableOnColumn(tableId,id_num);
	}
}
function doSearch(search_box) {

	var q = search_box.value;

	q = q.toLowerCase();
	var q_ars = new Array();
	q_ars = q.split(' ');

	for(var d = 0; d < q_ars.length; d++) {
		if(q_ars[d]=="") {
			q_ars.splice(d,1);
		}
	}

	var tbl = $(tableId);
	var rows = tbl.rows;
	for(var i=1; i<rows.length; i++) {
		var cells = rows[i].cells;
		var q_parts_found = "";
		for(var j=0; j<cells.length; j++) {	
			var val = cells[j].innerHTML;
			val = val.toLowerCase();

			for(var k =0; k < q_ars.length; k++) {
				if(q_ars[k]!="" && val.indexOf(q_ars[k]) != -1) {
					q_parts_found += k + " ";
					rows[i].style.display='';
				}
			}
		}	

		var foundCell = 1;
		for(var n = 0; n < q_ars.length; n++) {
			if(q_parts_found.indexOf(n)==-1) {
				foundCell = 0;
			}
		}
		if(foundCell == 1) {
			rows[i].style.display='';
		}
		else {
			rows[i].style.display='none';
		}
	}
	reAlternateLines(tableId);
}

function sortTableOnColumn(tableId,col) {
	var numHeaders = getNumHeaders(tableId);
    var numHeaderRows = getNumHeaderRows(tableId);
    var tbl = $(tableId);
	var rows = tbl.rows;
	var row_array = new Array();
	for(var i=numHeaderRows; i<rows.length; i++) {
		row_array[i-numHeaderRows] =  rows[i];
	}
	globalSortCol = col;
	row_array.sort(sortRowArrayOnColumn);
	globalSortCol = "";
	
	var innerHTMLArray = new Array();
	for(var l = 0; l < row_array.length; l++) {
		var innerHTMLArray2 = new Array();
		for(var c = 0; c < numHeaders; c++) {
			innerHTMLArray2[c] = row_array[l].cells[c].innerHTML;
		}
		innerHTMLArray[l] = innerHTMLArray2;
	}
	for(var l = 0; l < innerHTMLArray.length; l++) {
		for(var c = 0; c < numHeaders; c++) {
			tbl.rows[l+numHeaderRows].cells[c].innerHTML = innerHTMLArray[l][c];
		}
        tbl.rows[l+numHeaderRows].setAttribute('id',tbl.rows[l+numHeaderRows].cells[1].innerHTML.replace(/:/g,'-'));
    }
	
	//doSearch($('search'));
	reAlternateLines(tableId);
}
function sortRowArrayOnColumn(a,b) {	
	var x = a.cells[globalSortCol].innerHTML.toLowerCase();
	var y = b.cells[globalSortCol].innerHTML.toLowerCase();
	if(isNumeric(x) && isNumeric(y)) {
		x = x * 1;
		y = y * 1;
	}
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));	
}
function reverseTable(tableId) {
	var numHeaders = getNumHeaders(tableId);
    var numHeaderRows = getNumHeaderRows(tableId);
	var tbl = $(tableId);
	var rows = tbl.rows;
	var row_array = new Array();
    for(var i=numHeaderRows; i<rows.length; i++) {
		row_array[i-numHeaderRows] =  rows[i];
	}

	var innerHTMLArray = new Array();
	for(var l = 0; l < row_array.length; l++) {
		var innerHTMLArray2 = new Array();
		for(var c = 0; c < numHeaders; c++) {
			innerHTMLArray2[c] = row_array[l].cells[c].innerHTML;
		}
		innerHTMLArray[l] = innerHTMLArray2;
	}
	for(var l = 0; l < innerHTMLArray.length; l++) {
		for(var c = 0; c < numHeaders; c++) {
			tbl.rows[l+numHeaderRows].cells[c].innerHTML = innerHTMLArray[innerHTMLArray.length-1-l][c];
		}
        tbl.rows[l+numHeaderRows].setAttribute('id',tbl.rows[l+numHeaderRows].cells[1].innerHTML.replace(/:/g,'-'));
    }
	
	//doSearch($('search'));
	reAlternateLines(tableId);
}
function reAlternateLines(tableId) {
	var tbl = $(tableId);
	var rows = tbl.rows;
    var numHeaderRows = getNumHeaderRows(tableId);
	var counter = 1;
	for(var i=numHeaderRows; i<rows.length; i++) {
		if(rows[i].style.display!='none' && rows[i].cells[0].tagName != 'TH') {
			if (rows[i].style.backgroundColor) {
				rows[i].style.backgroundColor='';
			}
			applyAlternateClass(rows[i],counter%2);
			counter++;
		}
	}
	try {
	window.onload();
	}
	catch(e) {
		
	}
}

function applyAlternateClass(row,isAlt) {
	if (isAlt == 1) {
		row.className='';
		for (var k=0; k < row.cells.length; k++) {
			row.cells[k].className='';
		}
	}
	else {
		row.className="Alternate";
		for (var k=0; k < row.cells.length; k++) {
			row.cells[k].className="Alternate";
		}
	}
	//alert("Row Class="+row.style);
}

function isNumeric(sText) {
   var validChars = "0123456789.";
   for (var i = 0; i < sText.length; i++) { 
      if (validChars.indexOf(sText.charAt(i)) == -1) {
         return false;
      }
   }
   return true;
}

//call this to get our tables sorted. It also alternates row colors for us
//headerClicked(0);



/*
function showAllRows() {
	var tbl = $(tableId);
	var rows = tbl.rows;

	for(var i=1; i<rows.length; i++) {
		rows[i].style.display='';
	}
}
*/
