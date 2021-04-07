HTTP/1.1 200 OK

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SharePort Web Access</title>	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache"/>
		<meta http-equiv="Pragma" content="no-cache"/>
		<meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT"/>
		<link href="webfile_css/layout.css" rel="stylesheet" type="text/css" />
		<script language="JavaScript" src="webfile_js/webfile.js"></script>

		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		<script type="text/javascript" src="fancybox/jquery-1.4.3.min.js"></script>
<!--	<script type="text/javascript" src="fancybox/jquery.mousewheel-3.0.4.pack.js"></script> -->
		<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<script type="text/javascript" src="fancybox/json2.js"></script>
		<script language="JavaScript" src="js/md5.js" tppabs="/webaccess/js/md5.js"></script>
		<script language="JavaScript" src="js/object.js"></script>
		<script language="JavaScript" src="js/xml.js"></script>
		<script language="JavaScript" src="js/i18n.js"></script>
		<script language="JavaScript" src="js/libajax.js"></script>
		<script language="JavaScript" src="js/public.js"></script>
		<script type="text/javascript">	
			var media_info;
			var storage_user = new HASH_TABLE();
			
			load_lang_obj();	// you have to load language object for displaying words in each html page and load html object for the redirect or return page
			// Detect Language which could be set in share port login page.
			if (localStorage.getItem('language') === null) InitLANG("en-us");
			else InitLANG(localStorage.language);
										
			function show_media_list(which_action){
				var media_list = get_by_id("media_list");
				var str;
								
				if (media_info.status == "ok" && media_info.errno == null){
								
					str = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
											
					for (var i = 0; i < media_info.count; i++){
						var obj = media_info.files[i];
						var file_name = obj.name;
						var search_value = get_by_id("search_box").value;
						
						if (search_value.length > 0){
							if (which_action){	// check the search box							
								if(file_name.indexOf(search_value) != 0){
									continue;
								}
							}
						}
					
						var req="/dws/api/GetFile?id=" + storage_user.get("id")+"&volid="+obj.volid+"&path="+encodeURIComponent(obj.path)+"&filename="+encodeURIComponent(obj.name);
						req=gen_token_req(req);
						str += "<tr onMouseOver=\"this.style.background='#D8D8D8'\" onMouseOut=\"this.style.background=''\">"
							 + "<td width=\"36\" height=\"36\" class=\"tdbg\">"
							 + "<img src=\"webfile_images/icon_files.png\" width=\"36\" height=\"36\" border=\"0\">"
							 + "</td>"
							 + "<td width=\"868\" class=\"text_2\">"
							 //+ "<a href=\"/" + obj.path + "\" target=\"_blank\">"
							 + "<a  href=\""+req+"\" title=\"" + obj.name + "\">"
							 + "<div>"
							 + file_name+ "<br>" + get_file_size(obj.size) + ", " + obj.mtime
							 + "</div>"
							 + "</a>"								 
							 + "</td></tr>";
					}
					
					str += "</table>"
						+ "<div id=\"footer\"><img src=\"webfile_images/dlink.png\" width=\"77\" height=\"22\" /></div>";
					
					media_list.innerHTML = str;
				}else if(media_info.errno == "5002"){
					alert("No HardDrive Connected");
					location.href = "category_view.php";
				}else
					location.href = "index.php";						
			}
			
			function get_settings_xml(http_req){
				var my_txt = http_req.responseText;
											
				try {
					media_info = JSON.parse(my_txt);
				} catch(e) {
					get_media_list('document');
					return;
				}				
				
				show_media_list(false);
			}
			
			function get_media_list(which_media){		
				var xml_request = new XMLRequest(get_settings_xml);
				var dummy = new Date().getTime(); //Solve the problem of Ajax GET omitted due to IE cache.
				var para = "ListCategory?id=" + storage_user.get("id")
						 + "&pageoffset=0&maxcount=0" + "&path=&filter=" + which_media + "&dummy=" + dummy;
								
				xml_request.json_cgi(para);			
			}
			
			function clear_search_box(){				
				get_by_id("search_box").value = "";	
				show_media_list(false);				
			}	

			function reset_search_box(){
				get_by_id("search_box").value = I18N("j", "Search Documents")+"...";	
				show_media_list(false);	
			}
			
			function get_login_info_result(http_req){
				var my_xml = http_req.responseXML;
				
				if (check_user_info(my_xml.getElementsByTagName("redirect_page")[0])){
				
					storage_user.put("id", get_node_value(my_xml, "user_name"));
					
					storage_user.put("volid", get_node_value(my_xml, "volid"));

					get_media_list('document');										
				}
			}
			
			function get_login_info(){
				var xml_request = new XMLRequest(get_login_info_result);
				var para = "request=get_login_info";
								
				xml_request.exec_webfile_cgi(para);
			}
			function load_value(){
				get_by_id("search_box").value = I18N("j", "Search Documents")+"...";
			}
		</script>
	</head>
	<body onload="load_value();MM_preloadImages('webfile_images/btn_home_.png','webfile_images/backBtn_.png','webfile_images/x.png');get_login_info();">
		<div id="wrapper">
			<div id="header">
				<div align="right">
					<table width="100%" border="0" cellspacing="0">
						<tr>
							<th width="224" rowspan="2" scope="row"><img src="webfile_images/index_01.png" width="220" height="55" /></th>
							<th width="715" height="30" scope="row"><a href="category_view.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image6','','webfile_images/btn_home_.png',1)"><img src="webfile_images/btn_home_.png" name="Image6" width="25" height="25" border="0" align="right" id="Image6" /></a></th>
							<th width="15" scope="row"></th>
						</tr>
						<tr>
							<th scope="row"></th>
							<th scope="row"></th>
						</tr>
					</table>		 
					<a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image6','','webfile_images/btn_home_.png',1)"></a>
				</div>
				</div>
				<div id="navi">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<th width="9%" height="42" scope="row">
								<div align="right">

									<a href="category_view.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('back','','webfile_images/backBtn_.png',1)"><img src="webfile_images/backBtn.png" name="back" width="70" height="30" border="0" id="back" /></a>
								</div>
							</th>
							<td width="82%" class="text_2Copy"><script>I18N("h", "Documents");</script></td>
							<td width="10%"></td>
									</tr>
								</table>
				</div>

				<div id="search">
					<table width="100%" height="49" border="0" border-color:transparent;align="left" cellpadding="0" cellspacing="0">
						<tr>
							<th width="40" height="49"  scope="row"></th>
							<th width="880" class="search2" scope="row">							
								<input type="text" id="search_box" name="search_box" class="search2" value="Search Documents..." onfocus="clear_search_box()" onkeyup="show_media_list(true)" size="105" />
							</th>
							<th width="40" scope="row"><a href="javascript:reset_search_box()" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('reset','','webfile_images/x.png',1)"><img src="webfile_images/x.png" name="reset" width="15" height="15" border="0" align="left" id="reset" /></a></th>
						</tr>
					</table>
				</div>
				<div id="media_list">
					<div id="progress_bar"><img src="webfile_images/progress_bar.gif"/></div>
					<div id="footer"><img src="webfile_images/dlink.png" width="77" height="22" /></div>
				</div>
		</div>
	</body>
</html>
