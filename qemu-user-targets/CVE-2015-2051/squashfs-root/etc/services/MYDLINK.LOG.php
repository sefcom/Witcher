<?

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"],  $cmd."\n");} 

$path = "/device/log/mydlink/eventmgnt";
anchor($path);
$reg_st = query("/mydlink/register_st");
$enable = query("pushevent/enable");
$new_dev = query("pushevent/types/userlogin");
$wifi_intru = query("pushevent/types/wirelessintrusion");
$new_fw = query("pushevent/types/firmwareupgrade");
function restart_arpmonitor($enable,$new_dev)
{
	
	if($enable == "1" && $new_dev == "1")
	{
		
		startcmd("if [ -f /var/run/arpmonitor.pid ]; then\n");
		startcmd("	echo \"arpmonitor is started ,do nothing\"\n");
		startcmd("else\n");
		startcmd( "arpmonitor -i br0 &\n");
		startcmd( "echo $$ > /var/run/arpmonitor.pid\n");
		startcmd("fi\n");  
	}
	else
	{
		startcmd( "killall arpmonitor\n");
		startcmd( "rm -f /var/run/arpmonitor.pid\n");
	}
	
}

function restart_eventlog($enable,$new_dev,$wifi_intru,$new_fw)
{
	if($enable == "1")
	{
		$param = "-l ";
		$dact = "-x ";
		$dact_mem = "";
		if($new_dev != "1")
		{
			$dact_mem = $dact_mem."NEW_DEVICE";
		}
		
		if($wifi_intru != "1") 
		{
			if($dact_mem!="")
			{
				$dact_mem = $dact_mem.",";
			}
			$dact_mem = $dact_mem."WIFI_INTRU";
		}
		
		if($new_fw != "1") 
		{
			if($dact_mem!="")
			{
				$dact_mem = $dact_mem.",";
			}
			$dact_mem = $dact_mem."NEW_FW";
		}
		
		if($dact_mem != "")
		{
			$param = $param.$dact.$dact_mem;
		}
	
		startcmd("mydlinkeventd ".$param." &\n");
		
			
	}
	stopcmd("killall mydlinkeventd\n");
}

/* Main */
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n"); 
if($reg_st == "1")
{
	//restart_arpmonitor($enable,$new_dev);
	restart_eventlog($enable,$new_dev,$wifi_intru,$new_fw);
	startcmd( "service DNS restart \n");
}
	
?>
