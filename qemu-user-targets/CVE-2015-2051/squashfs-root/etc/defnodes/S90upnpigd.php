<? /* vi: set sw=4 ts=4: */

function remove_nondigit($src, $is_model)
{
	$ret="";
	$idx=0;
	$len=strlen($src);
	$found=0;
	while ($idx < $len)
	{
		$sub=substr($src, $idx, 1);
		if(isdigit($sub) == 1)
		{
			$ret=$ret.$sub;
		    $found=1;
		 }
		 else if($found==1 && $is_model==1)  //non continued digit string
		 {
			 break;
		 }
		 $idx=$idx+1;
	 }
	 return $ret;
}

$vendor		= query("/runtime/device/vendor");
$model		= query("/runtime/device/modelname");
$url		= query("/runtime/device/producturl");
$ver		= query("/runtime/device/firmwareversion");
$modeldesc	= query("/runtime/device/description");
$version         = fread("s", "/etc/config/buildver");
$version         = remove_nondigit($version, 0);
if($version == "") {$version = "100";}
$sn         = $version;  //jef add to fit dlink upnp spec v.106
//$sn			= "None";	/* Modified from "01234567" to "None", by jerry_kao, 110810. */

$Genericname = query("/runtime/device/upnpmodelname");
if($Genericname == ""){ $Genericname = $model; }

$Genericvendor = query("/runtime/device/upnpvendor");
if($Genericvendor == ""){ $Genericvendor = $vendor; }
if($Genericvendor == "D-Link"){$Genericvendor=$Genericvendor." Corporation";} //jef add to fit dlink upnp spec v.106
/* find out the root device path. */
$pbase		= "/runtime/upnp/dev";
$i			= query($pbase."#") + 1;
$dev_root	= $pbase.":".$i;
$dtype		= "urn:schemas-upnp-org:device:InternetGatewayDevice:1";

/********************************************************************/
/* root device: Internet Gateway Device */
/* create $dev_root */
set($dev_root, "");			anchor($dev_root);
/* set extension nodes. */
setattr("mac",  "get", "devdata get -e wanmac");
setattr("guid", "get", "genuuid -s \"".$dtype."\" -m \"".query("mac")."\"");
//setattr("guid", "get", "genuuid -s \"".$dtype."\" -r");
$udn = "uuid:".query("guid");

/* set IGD nodes. */
set("UDN",					$udn);
set("deviceType",			$dtype);
set("port",					"49152");
set("location", 			"InternetGatewayDevice.xml");
set("maxage",				"1800");
set("server",				"Linux, UPnP/1.0, ".$model." Ver ".$ver);

/* set the description file names */
add("xmldoc",				"InternetGatewayDevice.xml");
add("xmldoc",				"Layer3Forwarding.xml");
add("xmldoc",				"OSInfo.xml");
add("xmldoc",				"WANCommonInterfaceConfig.xml");
add("xmldoc",				"WANEthernetLinkConfig.xml");
add("xmldoc",				"WANIPConnection.xml");

/********************************************************************/
/* set the device description nodes */
$desc_root = $dev_root."/devdesc";

/* devdesc/specVersion */
set($desc_root."/specVersion",	"");	anchor($desc_root."/specVersion");
	set("major",				"1");
	set("minor",				"0");

/* devdesc/URLBase */
set($desc_root."/URLBase",		"");

/* devdesc/device */
/* root device */
set($desc_root."/device",		"");	anchor($desc_root."/device");
	set("deviceType",			$dtype);
	set("friendlyName",			$modeldesc);
	set("manufacturer",			$Genericvendor);	// Modified from $vendor
	set("manufacturerURL",		$url);
	set("modelDescription",		$Genericname);
	set("modelName",			$Genericname);
	set("modelNumber",			$model);			// Modified from "1" to $model
	set("modelURL",				$url);
	set("serialNumber",			$sn);
	set("UDN",					$udn);

/* devdesc/device/iconList */
$sub_root = $desc_root."/device/iconList/icon:1";
set($sub_root, "");				anchor($sub_root);
	set("mimetype",				"image/gif");
	set("width",				"118");
	set("height",				"119");
	set("depth",				"8");
	set("url",					"/ligd.gif");

/* devdesc/device/serviceList */
$sub_root = $desc_root."/device/serviceList/service:1";
set($sub_root, "");				anchor($sub_root);
	set("serviceType",			"urn:schemas-microsoft-com:service:OSInfo:1");
	set("serviceId",			"urn:microsoft-com:serviceId:OSInfo1");
	set("controlURL",			"/soap.cgi?service=OSInfo1");
	set("eventSubURL",			"/gena.cgi?service=OSInfo1");
	set("SCPDURL",				"/OSInfo.xml");
$sub_root = $desc_root."/device/serviceList/service:2";
set($sub_root, "");				anchor($sub_root);
	set("serviceType",			"urn:schemas-upnp-org:service:Layer3Forwarding:1");
	set("serviceId",			"urn:upnp-org:serviceId:L3Forwarding1");
	set("controlURL",			"/soap.cgi?service=L3Forwarding1");
	set("eventSubURL",			"/gena.cgi?service=L3Forwarding1");
	set("SCPDURL",				"/Layer3Forwarding.xml");

/* devdesc/device/deviceList */
/* WANDevice */
$sub_root = $desc_root."/device/deviceList/device:1";
set($sub_root, "");				anchor($sub_root);
	set("deviceType",			"urn:schemas-upnp-org:device:WANDevice:1");
	set("friendlyName",			"WANDevice");
	set("manufacturer",			$vendor);
	set("manufacturerURL",		$url);
	set("modelDescription",		"WANDevice");
	set("modelName",			$model);
	set("modelNumber",			"1");
	set("modelURL",				$url);
	set("serialNumber",			$sn);
	set("UDN",					$udn);

/* devdesc/device/deviceList/device:1/serviceList */
$sub_root = $desc_root."/device/deviceList/device:1/serviceList/service:1";
set($sub_root, "");				anchor($sub_root);
	set("serviceType",			"urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1");
	set("serviceId",			"urn:upnp-org:serviceId:WANCommonIFC1");
	set("controlURL",			"/soap.cgi?service=WANCommonIFC1");
	set("eventSubURL",			"/gena.cgi?service=WANCommonIFC1");
	set("SCPDURL",				"/WANCommonInterfaceConfig.xml");

/* devdesc/device/deviceList/device:1/deviceList */
/* WANConnectionDevice */
$sub_root = $desc_root."/device/deviceList/device:1/deviceList/device:1";
set($sub_root, "");	anchor($sub_root);
	set("deviceType",			"urn:schemas-upnp-org:device:WANConnectionDevice:1");
	set("friendlyName",			"WANConnectionDevice");
	set("manufacturer",			$vendor);
	set("manufacturerURL",		$url);
	set("modelDescription",		"WanConnectionDevice");
	set("modelName",			$model);
	set("modelNumber",			"1");
	set("modelURL",				$url);
	set("serialNumber",			$sn);
	set("UDN",					$udn);

/* devdesc/device/deviceList/device:1/deviceList/device:1/serviceList */
$sub_root = $desc_root."/device/deviceList/device:1/deviceList/device:1/serviceList/service:1";
set($sub_root, "");	anchor($sub_root);
	set("serviceType",			"urn:schemas-upnp-org:service:WANEthernetLinkConfig:1");
	set("serviceId",			"urn:upnp-org:serviceId:WANEthLinkC1");
	set("controlURL",			"/soap.cgi?service=WANEthLinkC1");
	set("eventSubURL",			"/gena.cgi?service=WANEthLinkC1");
	set("SCPDURL",				"/WANEthernetLinkConfig.xml");
$sub_root = $desc_root."/device/deviceList/device:1/deviceList/device:1/serviceList/service:2";
set($sub_root, "");	anchor($sub_root);
	set("serviceType",			"urn:schemas-upnp-org:service:WANIPConnection:1");
	set("serviceId",			"urn:upnp-org:serviceId:WANIPConn1");
	set("controlURL",			"/soap.cgi?service=WANIPConn1");
	set("eventSubURL",			"/gena.cgi?service=WANIPConn1");
	set("SCPDURL",				"/WANIPConnection.xml");

/* devdesc/device/presentationURL */
/* We keep the 'presentationURL' & 'URLBase' empty here,
 * and set the real value in when 'elbox/progs.template/htdocs/upnpdevdesc/InternetGatewayDevice.xml.php' is called. */
set($desc_root."/device/presentationURL","");

?>
