<?php

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}


if (isset($_REQUEST['json']) && $_REQUEST['json'] == 'true') {
    if (getProductID() == 'WG102') {
        echo "var config=".json_encode($config)."; var config2=".json_encode($config2 =       Array(
		'WG102'      =>      array('status'  =>      true)));
    }
    else {
        echo "var config=".json_encode($config)."; var config2=".json_encode($config2 =       Array(
		'WG102'      =>      array('status'  =>      false)));
    }
}

function getProductID() {
	$confdEnable = true;
	if ($confdEnable) {
		$productIdArr = explode(' ', conf_get('system:monitor:productId'));
		$productId = $productIdArr[1];
	}
	else {
		$productId = "WG102";
	}
	return $productId;
}

?>
