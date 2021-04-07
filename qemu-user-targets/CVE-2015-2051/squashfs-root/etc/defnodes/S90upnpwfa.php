<? /* vi: set sw=4 ts=4: */
$vendor		= query("/runtime/device/vendor");
$model		= query("/runtime/device/modelname");
$url		= query("/runtime/device/producturl");
$ver		= query("/runtime/device/firmwareversion");
$modeldesc	= query("/runtime/device/description");
$sn			= "01234567";

/* find out the root device path. */
$pbase		= "/runtime/upnp/dev";
$i			= query($pbase."#") + 1;
$dev_root	= $pbase.":".$i;
$dtype		= "urn:schemas-wifialliance-org:device:WFADevice:1";

/********************************************************************/
/* root device: WFADevice */
/* create $dev_root */
set($dev_root, "");						anchor($dev_root);
/* set extension nodes. */
setattr("mac",  "get", "devdata get -e wanmac");
setattr("guid", "get", "genuuid -s \"".$dtype."\" -m \"".query("mac")."\"");
$udn = "uuid:".query("guid");
/* set WFA nodes. */
set("UDN",				$udn);
set("deviceType",		$dtype);
set("port",				"49152");
set("location",			"WFADevice.xml");
set("maxage",			"1800");
set("server",			"Linux, UPnP/1.0, ".$model." Ver ".$ver);

/* set the description file names */	
add("xmldoc",			"WFADevice.xml");
add("xmldoc",			"WFAWLANConfig.xml");

/* set the device description nodes */
$desc_root = $dev_root."/devdesc";	
/* devdesc/specVersion */
set($desc_root."/specVersion",	"");	anchor($desc_root."/specVersion");
	set("major",				"1");
	set("minor",				"0");

/* devdesc/URLBase */
set($desc_root."/URLBase",		"");

/* devdesc/device */
set($desc_root."/device",		"");	anchor($desc_root."/device");
	set("deviceType",			$dtype);
	set("friendlyName",			$model);
	set("manufacturer",			$vendor);
	set("manufacturerURL",		$url);
	set("modelDescription",		$modeldesc);
	set("modelName",			$model);
	set("modelNumber",			"1");
	set("modelURL",				$url);
	set("serialNumber",			$sn);
	set("UDN",					$udn);
	/* devdesc/device/serviceList */
	set($desc_root."/device/serviceList/service:1", "");
	anchor($desc_root."/device/serviceList/service:1");
		set("serviceType",			"urn:schemas-wifialliance-org:service:WFAWLANConfig:1");
		set("serviceId",			"urn:wifialliance-org:serviceId:WFAWLANConfig1");
		set("controlURL",			"/wfadev.cgi?service=WFAWLANConfig1");
		set("eventSubURL",			"/gena.cgi?service=WFAWLANConfig1");
		set("SCPDURL",				"/WFAWLANConfig.xml");

/* devdesc/device/presentationURL */
/* We keep the 'presentationURL' & 'URLBase' empty here,
 * and set the real value in when 'elbox/progs.template/htdocs/upnpdevdesc/WFADevice.xml.php' is called. */
set($desc_root."/device/presentationURL","");
?>
