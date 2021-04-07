<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function pfwd_setcfg($prefix, $nat, $svc)
{
	$nat_base = XNODE_getpathbytarget("/nat", "entry", "uid", $nat, 0);
	if ($svc == "PFWD")
	{
		$pfwd_base = $nat_base."/portforward";
		$src_base = $prefix."/nat/entry/portforward/entry";
		$pre_uid = "PFWD-";
		$post_seqno	= query($prefix."/nat/entry/portforward/seqno");
	}
	else if ($svc == "VSVR")
	{
		$pfwd_base = $nat_base."/virtualserver";
		$src_base = $prefix."/nat/entry/virtualserver/entry";
		$pre_uid = "VSVR-";
		$post_seqno  = query($prefix."/nat/entry/virtualserver/seqno");
	}
	$cnt = query($pfwd_base."/entry#");
	/* The seqno is just a serial number, so I use the greater one. */
	$db_seqno = query($pfwd_base."/seqno");
	if ($post_seqno > $db_seqno)	$seqno = $post_seqno;
	else							$seqno = $db_seqno;

	/* delete the old entries (must from tail to head). */
	while ($cnt>0)
	{
		del($pfwd_base."/entry");
		$cnt--;
	}

	/* set the new entries. */
	foreach ($src_base)
	{
		if (query("uid") == "")
		{
			set("uid", $pre_uid.$seqno);
			$seqno++;
		}
		set($pfwd_base."/entry:".$InDeX."/enable",			query("enable")			);
		set($pfwd_base."/entry:".$InDeX."/uid",				query("uid")			);
		set($pfwd_base."/entry:".$InDeX."/schedule",		query("schedule")		);
		set($pfwd_base."/entry:".$InDeX."/description",		query("description")	);
		set($pfwd_base."/entry:".$InDeX."/protocol",		query("protocol")		);
		set($pfwd_base."/entry:".$InDeX."/internal/inf",	query("internal/inf")	);
		set($pfwd_base."/entry:".$InDeX."/internal/hostid",	query("internal/hostid"));
		set($pfwd_base."/entry:".$InDeX."/internal/start",	query("internal/start")	);
		set($pfwd_base."/entry:".$InDeX."/external/start",	query("external/start")	);
		set($pfwd_base."/entry:".$InDeX."/external/end",	query("external/end")	);
	}
	set($pfwd_base."/seqno", $seqno);
	set($pfwd_base."/count", query($pfwd_base."/entry#"));
}
?>
