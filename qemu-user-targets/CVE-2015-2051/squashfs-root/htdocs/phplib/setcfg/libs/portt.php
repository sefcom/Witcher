<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function portt_setcfg($prefix, $nat)
{
	$nat_base = XNODE_getpathbytarget("/nat", "entry", "uid", $nat, 0);
	$portt_base = $nat_base."/porttrigger";
	$src_base = $prefix."/nat/entry/porttrigger/entry";
	$prefix	= "PORTT-";
	$cnt	= query($portt_base."/entry#");
	$seqno	= query($portt_base."/seqno");

	/* delete the old entries (must from tail to head). */
	while ($cnt>0)
	{
		del($portt_base."/entry");
		$cnt--;
	}

	/* set the new entries. */
	foreach ($src_base)
	{
		if (query("uid") == "")
		{
			set("uid", $prefix.$seqno);
			$seqno++;
		}
		set($portt_base."/entry:".$InDeX."/enable",				query("enable")				);
		set($portt_base."/entry:".$InDeX."/uid",				query("uid")				);
		set($portt_base."/entry:".$InDeX."/schedule",			query("schedule")			);
		set($portt_base."/entry:".$InDeX."/description",		query("description")		);
		set($portt_base."/entry:".$InDeX."/trigger/protocol",	query("trigger/protocol")	);
		set($portt_base."/entry:".$InDeX."/trigger/start",		query("trigger/start")		);
		set($portt_base."/entry:".$InDeX."/trigger/end",		query("trigger/end")		);
		set($portt_base."/entry:".$InDeX."/external/protocol",	query("external/protocol")	);
		set($portt_base."/entry:".$InDeX."/external/portlist",	query("external/portlist")	);
	}
	set($portt_base."/seqno", $seqno);
	set($portt_base."/count", query($portt_base."/entry#"));
}
?>
