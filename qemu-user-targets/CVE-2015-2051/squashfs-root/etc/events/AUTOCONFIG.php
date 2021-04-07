<?
include "/htdocs/phplib/xnode.php";

$ninf = XNODE_getpathbytarget("","inf","uid","WAN-3",0);
if($ninf =="") exit;
$ninet = query($ninf."/inet");
$ndev = XNODE_getpathbytarget("/inet","entry","uid",$ninet,0);
if($ndev =="") exit;
$automode = query($ndev."/ppp4/tty/auto_config/mode");

//$mcc = query("/runtime/auto_config/mcc");
//$mnc1 = query("/runtime/auto_config/mnc_1");
//$mnc2 = query("/runtime/auto_config/mnc_2");

/*XNODE_pri_getpathbytarget();*/
function XNODE_pri_getpathbytarget($base,$node,$target,$value,$create,$pri)
{
    foreach($base."/".$node)
    {
 	$src_value=query($target);
	if(strchr($src_value,',') == "")
	{
		$dst_value = $src_value;	
	        if ($dst_value == $value)
	        {
	            if($pri != "0")
	            {
	                if(query("priority") == $pri)
	                    return $base."/".$node.":".$InDeX;
	            }
	            else
	                return $base."/".$node.":".$InDeX;
	        }
	}
	else
	{
		$a = cut($src_value,0,",");	
		$b = cut($src_value,1,",");	
		$c = cut($src_value,2,",");	
		if($a == $value)
		{
                    if($pri != "0")
                    {
                        if(query("priority") == $pri)
                            return $base."/".$node.":".$InDeX;
                    }
	            else
	                return $base."/".$node.":".$InDeX;
		}
                else if($b == $value)
                {
                    if($pri != "0")
                    {
                        if(query("priority") == $pri)
                            return $base."/".$node.":".$InDeX;
                    }
	            else
	                return $base."/".$node.":".$InDeX;
                }
                else if($c == $value)
                {
                    if($pri != "0")
                    {
                        if(query("priority") == $pri)
                            return $base."/".$node.":".$InDeX;
                    }
                    else
                        return $base."/".$node.":".$InDeX;
                }	
	}
    }

    if ($create > 0)
    {
        $i = query($base."/".$node."#")+1;
        $path = $base."/".$node.":".$i;
        set($path."/".$target, $value);
        return $path;
    }

    return "";

}

$dev = XNODE_getpathbytarget("/runtime/services/operator", "entry", "mcc", $mcc, 0);
if($dev != "")
{
	$opt = XNODE_getpathbytarget($dev,"entry", "mnc", $mnc2, 0);
	$mnc = "";
	if($opt != "")
	{
		$mnc = $mnc2;
	}
	else
	{
		$opt = XNODE_getpathbytarget($dev,"entry", "mnc", $mnc1, 0);
		if($opt != "")
		{
			$mnc = $mnc1;
		}
	}
	if($mnc != "")
	{
		$sign = query($opt."/repeat_sign");
		$priority = query($opt."/priority");
		if($sign == "1")
		{
			if($priority != "1")
			{
				$opt=XNODE_pri_getpathbytarget($dev, "entry", "mnc", $mnc, 0,"1");
			}
		}
		$apn	  = query($opt."/apn");	
		$dialno	  = query($opt."/dialno");
		$user	  = query($opt."/username");
		$password = query($opt."/password");
		$isp	  = query($opt."/profilename");
		$country  = query($dev."/country");
	
		set("/runtime/auto_config/mcc",$mcc);		
		set("/runtime/auto_config/mnc",$mnc);		
		set("/runtime/auto_config/mnc_1",$mnc1);		
		set("/runtime/auto_config/mnc_2",$mnc2);		
                set("/runtime/auto_config/apn",$apn);
                set("/runtime/auto_config/dialno",$dialno);
                set("/runtime/auto_config/username",$user);
                set("/runtime/auto_config/password",$password);
                set("/runtime/auto_config/country",$country);
        	set("/runtime/auto_config/isp",$isp);		
	}
}

?>
