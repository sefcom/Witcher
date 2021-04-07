<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

/***********************************************************************/
function SOAP_act_resp_200($svr_name, $svc_type, $act_name, $soap_body)
{
	echo "HTTP/1.1 200 OK\r\n";
	echo "CONTENT-TYPE: text/xml; charset=\"utf-8\"\r\n";
	echo "CONTENT-LENGTH:\r\n";
	echo "EXT:\r\n\r\n";

	echo "<?xml version=\"1.0\"?>\n";
	echo "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\" s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n";
	echo "\t<s:Body>\n";
	echo "\t\t<u:".$act_name."Response xmlns:u=\"".$svc_type."\">";
	if ($soap_body!="")
	{
		echo "\n";
		dophp("load", "/htdocs/upnpinc/".$soap_body);
	}
	echo "</u:".$act_name."Response>\n";
	echo "\t</s:Body>\n";
	echo "</s:Envelope>\n";
}

function SOAP_act_resp_500($error_code, $error_desc)
{
	echo "HTTP/1.1 500 Internal Server Error\r\n";
	echo "CONTENT-TYPE: text/xml; charset=\"utf-8\"\r\n";
	echo "CONTENT-LENGTH:\r\n";
	echo "EXT:\r\n\r\n";

	echo "<?xml version=\"1.0\"?>\n";
	echo "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\" s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n";
	echo "\t<s:Body>\n";
	echo "\t\t<s:Fault>\n";
	echo "\t\t\t<faultcode>s:Client</faultcode>\n";
	echo "\t\t\t<faultstring>UPnPError</faultstring>\n";
	echo "\t\t\t<detail>\n";
	echo "\t\t\t\t<UPnPError xmlns=\"urn:schemas-upnp-org:control-1-0\">\n";
	echo "\t\t\t\t\t<errorCode>".$error_code."</errorCode>\n";
	echo "\t\t\t\t\t<errorDescription>".$error_desc."</errorDescription>\n";
	echo "\t\t\t\t</UPnPError>\n";
	echo "\t\t\t</detail>\n";
	echo "\t\t</s:Fault>\n";
	echo "\t</s:Body>\n";
	echo "</s:Envelope>\n";
}
/***********************************************************************/

?>
