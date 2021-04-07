<?
//include "/htdocs/phplib/trace.php";
function addevent($name,$handler)
{
	$cmd = $name." add \"".$handler."\"";
	event($cmd);
} 
function runservice($cmd)
{
	addevent("PHPSERVICE","service ".$cmd." &");
	event("PHPSERVICE");
	event("PHPSERVICE remove default");
}
function get_valid_mac($value)
{
	$mac_len = strlen($value);
	$mac_idx = 0;
	$valid_mac = "";
	$char = "";
	$delimiter = ":";
	while($mac_idx < $mac_len)
	{
		$valid_mac = $valid_mac.substr($value,$mac_idx,2);
		$mac_idx = $mac_idx + 2;
		$char = charcodeat($value,$mac_idx);
		if($char != "")
		{
			if($char == $delimiter){$mac_idx++;}
			$valid_mac = $valid_mac.$delimiter;
		}
	}
	
	return $valid_mac;
}
//runservice("HTTP restart");
?>

