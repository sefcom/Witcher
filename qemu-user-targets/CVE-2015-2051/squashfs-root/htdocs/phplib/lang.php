<?
include "/htdocs/phplib/slp.php";
include "/htdocs/phplib/trace.php";

function convert_lcode($primary, $subtag)
{
	$pri = tolower($primary);
	if ($pri=="zh")
	{
		$sub = tolower($subtag);
		if ($sub=="cn")	return "zhcn";
		else			return "zhtw";
	}	
	return $pri;
}
function wiz_load_slp($lcode)
{
	$slp = "/etc/sealpac/wizard/wiz_".$lcode.".slp";
	if (isfile($slp)!="1") return 0;
	sealpac($slp);
	return 1;
}
function load_existed_slp()
{
	$slp = "/var/sealpac/sealpac.slp";
	$slp2 = "/etc/sealpac/en.slp";
	if (isfile($slp)!="1")
	{
		if (isfile($slp2)!="1")
		{
			/*unload language pack*/
			sealpac("");
		}
		else
		{
			sealpac($slp2);
		}
	}
	else
	{
		//+++ Jerry Kao, modified to sync, the language pack info between
		//               /var/sealpac/sealpac.slp and /runtime/device/langcode
		$langcode = sealpac($slp);
		SLP_setlangcode($langcode);
	}
	return 1;
}
function wiz_set_LANGPACK()
{
	$lcode = $_GET["language"];
	//TRACE_error("lcode=".$lcode);
	if ($lcode=="auto" || $lcode=="")
	{
		$count = cut_count($_SERVER["HTTP_ACCEPT_LANGUAGE"], ',');
		$i = 0;
		while ($i < $count)
		{
			$tag = cut($_SERVER["HTTP_ACCEPT_LANGUAGE"], $i, ',');
			$pri = cut($tag, 0, '-');
			$sub = cut($tag, 1, '-');
			$lcode = convert_lcode($pri, $sub);
			//The accept language for Japan from IE is ja-JP and the language code for our language pack is jp.
			if($lcode=="ja") $lcode = "jp";
			if (wiz_load_slp($lcode) > 0) { return $lcode; }
			$i++;
		}
	}
	else { if (wiz_load_slp($lcode) > 0) { return $lcode; } }
	sealpac("/etc/sealpac/wizard/wiz_en.slp");	// Use system default language, en.
	return "en";
}
?>