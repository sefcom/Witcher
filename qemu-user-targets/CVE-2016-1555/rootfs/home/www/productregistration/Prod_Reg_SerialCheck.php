<?php
function post_request($url, $data, $referer='') {
    // Convert the data array into URL Parameters like param_serialnumber.

	$data = http_parse_query($data);
//    $data = http_build_query($data);
    // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') { 
        die('Error: Only HTTP request are supported !');
    }
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
    // open a socket connection on port 80 - timeout: 30 sec
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
    if ($fp){
        // send the request headers:
       fputs($fp, "POST /NetgearWS/Product.asmx/IsProductExist HTTP/1.1 \r\n");
        fputs($fp, "Host: my.netgear.com\r\n");

        if ($referer != '')
        fputs($fp, "Referer: $referer\r\n");

        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);
        $result = ''; 
        while(!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
    }
    else { 
        return array(
            'status' => 'err', 
            'error' => "$errstr ($errno)"
        );
    }
    // close the socket connection:
    fclose($fp);
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : ''; 
    // return as structured array:
    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}
	// Submit those variables to the server
	 $serno=exec("printmd /dev/mtd5 | grep serno");
	 $serno = explode("=", $serno);
	 $SerialNo=trim($serno[1]);

	$data = array(
	'param_serialnumber' => $SerialNo
	);
 
//  Send a request to http://my.netgear.com/NetgearWS/Product.asmx/IsProductExist
     $result = post_request('http://my.netgear.com/NetgearWS/Product.asmx/IsProductExist', $data);
 
if ($result['status'] == 'ok'){
    // response xml response from netgear server.
	// Due to xml functionality disable spitling the sting to get httpresutl
	$Serial = strstr($result['content'], 'Product">');
	$Serial=substr($Serial,9);
	if($Serial[0]==0)
	echo "notregistered";
	else if($Serial[0]==1)
	echo "registered";
}
else {
    echo 'A error occured: ' . $result['error']; 
}

//paraser for php 4 support for WN604
function http_parse_query( $array = NULL, $convention = '%s' ){
 if( count( $array ) == 0 ){
	return '';
	} 
  else {
	 if( function_exists( 'http_build_query' ) ){
		 $query = http_build_query( $array );
	 } else {
	 $query = '';
	 foreach( $array as $key => $value ){
		 if( is_array( $value ) ){
			 $new_convention = sprintf( $convention, $key ) . '[%s]';
			 $query .= http_parse_query( $value, $new_convention );
		 } 
		 else {
			 $key = urlencode( $key );
			 $value = urlencode( $value );
			 $query .= sprintf( $convention, $key ) . "=$value&";
		 }
      }
   }
  return $query;
  }
 }

?>
