<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";

function writescript($mode, $message)
{
	if ($_GLOBALS["SHELL"] != "")
		fwrite($mode, $_GLOBALS["SHELL"], $message);
}

$dirtysvcp = "/runtime/services/dirty/service";

if ($ACTION=="SETCFG")
{
	//TRACE_debug("WAND dump ===============\n".dump(0,$PREFIX));
	//TRACE_debug("WAND: SETCFG, PREFIX=".$PREFIX);
	foreach($PREFIX."/postxml/module")
	{
		$svc = query("service");
		TRACE_debug("SETCFG: [".$svc."]");
		/* record the dirty service to runtime node. */
		if (query("ACTIVATE") != "ignore")
		{
			$hit = 0;
			foreach ($dirtysvcp)
			{
				if ($svc == query("name"))	{$hit++; break;}
			}
			if ($hit == 0)
			{
				$c = query($dirtysvcp."#");
				$c++;
				set($dirtysvcp.":".$c."/name",				$svc);
				set($dirtysvcp.":".$c."/ACTIVATE",			query("ACTIVATE"));
				set($dirtysvcp.":".$c."/ACTIVATE_DELAY",	query("ACTIVATE_DELAY"));
				set($dirtysvcp.":".$c."/ACTIVATE_EVENT",	query("ACTIVATE_EVENT"));
			}
			//TRACE_debug("WAND: SETCFG, dump ===============\n".dump(0, "/runtime/services/dirty"));
		}
		if (query("valid")!=1)
		{
			if (query("SETCFG")!="ignore")	TRACE_error("SETCFG: [".$svc."] is invalid.");
			continue;
		}
		if (query("SETCFG")=="ignore") continue;
		$file = "/htdocs/phplib/setcfg/".$svc.".php";
		//TRACE_debug("SETCFG: ".$file);
		$SETCFG_prefix = $PREFIX."/postxml/module:".$InDeX;
		if (isfile($file)==1) dophp("load", $file);
		else TRACE_error("SETCFG: [".$file."] is not found!");

	}
	/* clear session data. */
	del($PREFIX."/postxml");
}
else if ($ACTION=="ACTIVATE")
{
	writescript("w", "#!/bin/sh\n");
	//writescript("a", "cat $0\n");
	//TRACE_debug("WAND: ACTIVATE, dump ===============\n".dump(0, "/runtime/services/dirty"));
	foreach($dirtysvcp)
	{
		$svc = query("name");
		$act = query("ACTIVATE");
		if ($svc == "" || $act=="ignore") continue;

		if ($act=="delay")
		{
			$delay = query("ACTIVATE_DELAY") + 0;
			TRACE_debug('WAND: delay active ['.'xmldbc -t "wand:'.$delay.':service '.$svc.' restart"'.']');
			writescript(a, 'xmldbc -t "wand:'.$delay.':service '.$svc.' restart"\n');
		}
		else if ($act=="event")
		{
			$event = query("ACTIVATE_EVENT");
			$delay = query("ACTIVATE_DELAY") + 0;
			TRACE_debug('WAND: event active ['.$event.'], delay='.$delay);
			writescript(a, 'xmldbc -t "wand:'.$delay.':event '.$event.'"\n');
		}
		else
		{
			writescript("a", "service ".$svc." restart\n");
		}
	}
	del("/runtime/services/dirty");

	/* remove this shell script. */
	writescript("a", "rm -f $0\n");
}
?>
