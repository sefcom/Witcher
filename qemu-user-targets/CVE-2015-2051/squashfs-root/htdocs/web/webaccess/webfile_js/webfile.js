function MM_swapImgRestore() { //v3.0
	var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
	var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
	var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function get_file_size(which_size){
	var unit = new Array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB");
	var count = 0;
	var str;
	
	while (which_size > 1024){
		which_size = which_size/1024;
		count++;
	}

	str = parseFloat(which_size).toFixed(2) + unit[count];

	return str;
}

function check_special_char(src_str){
	var dest_src = "";
	
	for (var i = 0; i < src_str.length; i++){
		var ch = src_str.charAt(i);
		
		if (ch == '>'){
			return false;
		}else if (ch == '<'){
			return false;
		}else if (ch == '/'){
			return false;
		}else if (ch == '\\'){
			return false;
		}else if (ch == '?'){
			return false;
		}else if (ch == ':'){
			return false;
		}else if (ch == '|'){
			return false;
		}else if (ch == '*'){
			return false;
		}
	}
	return true;
}
function displayString() {}
displayString.prototype =
{
	write: function(writestr) 
	{
		document.write(writestr)
	}
}

function ctime(mtime){
//jef add + for local timezone support
	var tzo=(new Date().getTimezoneOffset())*(-1)*60;
	mtime = mtime + tzo;
//jef add -
	var time_format = new Date(mtime*1000);
	var weekday = get_week_day(time_format.getUTCDay()+1);
	var month = get_month(time_format.getUTCMonth()+1);
	var date = get_digit_number(time_format.getUTCDate());
	var year = time_format.getUTCFullYear();
	var hour = get_digit_number(time_format.getUTCHours());
	var minute = get_digit_number(time_format.getUTCMinutes());
	var second = get_digit_number(time_format.getUTCSeconds());
	
	return weekday + " " + month + " " + date + " " + year + " " 
			+ hour + ":" + minute + ":" + second;
}