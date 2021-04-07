<?
function AES_Encrypt128($input)
{
	$key_hex = $_GLOBALS["PrivateKey"];
	if($key_hex=="")
	{ return escape("x", $input);}
	else
	{
		$input_hex = ascii($input);
		return encrypt_aes($key_hex, $input_hex);
	}
}

function AES_Decrypt128($encrypted)
{
	$key_hex = $_GLOBALS["PrivateKey"];
	if(strlen($encrypted) < 128 || $key_hex=="")
	{ return $encrypted;}
	else
	{ return hex2ascii(decrypt_aes($key_hex, $encrypted));}
}
?>
