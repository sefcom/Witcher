
<html>
	<head>
		<meta http-equiv="Pragma" content="no-cache"> <!--for HTTP 1.1-->
		<meta http-equiv="Cache-Control" content="no-cache"> <!--for HTTP 1.0--> 
		<meta http-equiv="Expires" content="0"> <!--prevents caching at the proxy server-->
		<script type="text/javascript" charset="utf-8" src="./js/libajax.js"></script>
		<script type="text/javascript" charset="utf-8" src="./js/hnap.js"></script>
		<script type="text/javascript">	
			var HNAP = new HNAP_XML();
			
			function SetDelStorageUser() 
			{
				var xml_SetDelStorageUser = HNAP.GetXML("SetDelStorageUser");
				xml_SetDelStorageUser.Set("SetDelStorageUser/StorageUserInfo/StorageUser/UserName", "yao");
				xml_SetDelStorageUser.Set("SetDelStorageUser/StorageUserInfo/StorageUser/Password", "12345");
				xml_SetDelStorageUser.Set("SetDelStorageUser/StorageUserInfo/StorageUser/AccessPath", "Kingston_DataTraveler_032F0:/Test");
				xml_SetDelStorageUser.Set("SetDelStorageUser/StorageUserInfo/StorageUser/Permission", "True");
				var xml_SetDelStorageUserResult = HNAP.SetXML("SetDelStorageUser", xml_SetDelStorageUser);
			}
			function SetAddStorageUser() 
			{
				var xml_SetAddStorageUser = HNAP.GetXML("SetAddStorageUser");
				xml_SetAddStorageUser.Set("SetAddStorageUser/StorageUserInfo/StorageUser/UserName", "yao");
				xml_SetAddStorageUser.Set("SetAddStorageUser/StorageUserInfo/StorageUser/Password", "12345");
				xml_SetAddStorageUser.Set("SetAddStorageUser/StorageUserInfo/StorageUser/AccessPath", "Kingston_DataTraveler_032F0:/Test");
				xml_SetAddStorageUser.Set("SetAddStorageUser/StorageUserInfo/StorageUser/Permission", "True");
				var xml_SetAddStorageUserResult = HNAP.SetXML("SetAddStorageUser", xml_SetAddStorageUser);
			}
		</script>
	</head>
	<body>
        <div class="textinput">
                <span class="name"></span>
                <span class="delimiter"></span>
                <input type="button" value="SetDelStorageUser" onclick="SetDelStorageUser();" />
                <input type="button" value="SetAddStorageUser" onclick="SetAddStorageUser();" />
        </div>		
	</body>
</html>
