<?
function HTML_gen_301_header($host, $uri)
{
	echo "HTTP/1.1 301 Moved Permanently\r\n";
	echo "Location: http://";
	if ($host == "")	echo $_SERVER["HTTP_HOST"].$uri;
	else				echo $host.$uri;
	echo "\r\n\r\n";
}

function HTML_hnap_200_header()
{
	echo "HTTP/1.1 200 OK\r\n";
	echo "Content-Type: text/xml; charset=utf-8\r\n";
}

function HTML_hnap_xml_header()
{
	echo "<\?xml version='1.0' encoding='utf-8'\?>\r\n";
	echo '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">\r\n';
	echo "<soap:Body>";
}

function HTML_hnap_xml_tail()
{
	echo "</soap:Body>\r\n";
	echo "</soap:Envelope>\r\n";
}
?>
