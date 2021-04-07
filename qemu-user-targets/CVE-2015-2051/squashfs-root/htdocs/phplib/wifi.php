<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function WIFI_getchannellist($band)
{
	/**********************************************************************/
	/* New methods to get the channel list.								  */
	/**********************************************************************/
	if ($band == "") $band = "g";
	$list = query("/runtime/freqrule/channellist/".$band);
	if ($list != "") return $list;
	
	/**********************************************************************/
	/* Old methods to get the channel list. Yes, it only supports G band. */
	/**********************************************************************/
	$c = query("/runtime/devdata/countrycode");
	if ($c == "")
	{
		TRACE_error("phplib/WIFI.php - WIFI_getchannellist() ERROR: no Country Code!!! Please check if you board is initialized. Use 'US' as temporary countrycode.");
		$c = "US";
	}
	if (isdigit($c)==1)
	{
		TRACE_error("phplib/WIFI.php - WIFI_getchannellist() ERROR: Country Code (".$c.") is not in ISO Name!! Please use ISO name insteads of Country Number. Use 'US' as temporary countrycode.");
		$c = "US";
	}
	
	if($band == "a")
	{
		if ($c == "US") $list = "36,40,44,48,149,153,157,161,165";
		else if ($c == "JP") $list = "36,40,44,48,52,56,60,64,100,104,108,112,116,120,124,128,132,136,140"; 
		else if ($c == "CN") $list = "149,153,157,161,165"; 
		else if ($c == "LA") $list = "149,153,157,161"; 
		else
		{
			TRACE_error("phplib/WIFI.php - WIFI_getchannellist() ERROR: countrycode (".$c.") doesn't match any list in WIFI_getchannellist(). Please check it. Return the channel list of 'US' instead.");
			$list = "36,40,44,48,149,153,157,161,165";
		}
		return $list;	
	}
	
	if ($c == "US") $list = "1,2,3,4,5,6,7,8,9,10,11";
	else if ($c == "CL" || $c == "GB" || $c == "JP" || $c == "CN" || $c == "LA") $list = "1,2,3,4,5,6,7,8,9,10,11,12,13";
	else
	{
		TRACE_error("phplib/WIFI.php - WIFI_getchannellist() ERROR: countrycode (".$c.") doesn't match any list in WIFI_getchannellist(). Please check it. Return the channel list of 'US' instead.");
		$list = "1,2,3,4,5,6,7,8,9,10,11";
	}
	
	return $list;
}

function WIFI_issupport11n($phy_uid)
{
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy_uid);
	if ($p == "")
	{
		TRACE_error("phplib/WIFI.php - WIFI_issupport11n(): no phyinf (".$phy_uid.")!");
		return 0;
	}
	$parent = query($p."/media/parent");
	if ($parent != "")	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $parent);
	if ($p == "")
	{
		TRACE_error("phplib/WIFI.php - WIFI_issupport11n(): no phyinf (".$parent.")!");
		return 0;
	}
	
	$b = query($p."/media/band");
	TRACE_error("MEDIA BAND:".$b."");
	if (strchr($b,"N") != "")	return 1;
	else						return 0;
}
?>
