<?
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function copy_bwc($from, $to)
{
    $cnt = query($to."/count");
	$seqno = query($to."/seqno");

	while($cnt>0)
	{
		if(XNODE_getpathbytarget($from, "entry", "uid", query($to."/entry:".$cnt."/uid"), 0) == "")
		{
			del($to."/entry:".$cnt);
		}
		$cnt--;
    }

    set($from."/count", 0);
    $cnt = 0;
    foreach($from."/entry")
    {
        $cnt++;
		$bwc_index = $InDeX;

		if(query("uid") == "")
		{
	        set($to."/entry:".$bwc_index."/uid", "BWC-".$seqno);
			$seqno++;
		}
		else
		{
			$rules_cnt = query($to."/entry:".$bwc_index."/rules/count");
			while($rules_cnt>0)
		    {
				if(XNODE_getpathbytarget($from."entry:".$bwc_index."rules", "entry", "uid", query($to."/entry:".$bwc_index."/rules:".$rules_cnt."/uid"), 0) == "") 
					del($to."/entry:".$bwc_index."rules/entry:".$rules_cnt);
				$rules_cnt--;
		    }
	        set($to."/entry:".$bwc_index."/uid", query("uid"));
		}
		set($to."/entry:".$bwc_index."/autobandwidth",		query("autobandwidth"));
		set($to."/entry:".$bwc_index."/bandwidth",		query("bandwidth"));
		set($to."/entry:".$bwc_index."/flag",			query("flag"));
		set($to."/entry:".$bwc_index."/enable",		query("enable"));

		set($from."/entry:".$bwc_index."/rules/count", 0);
		$rules_cnt = 0;
		$rules_seqno = query($to."/entry:".$bwc_index."/rules/seqno");
		foreach($from."/entry:".$bwc_index."/rules/entry")
		{
			$rules_cnt++;
			if(query("uid") == "")
			{
				set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/uid", "BWR-".$rules_seqno);
				$rules_seqno++;
			}
			else
			{
				set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/uid", query("uid"));
			}
			set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/enable", query("enable"));
			set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/description", query("description"));
			set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/bwcf", query("bwcf"));
			set($to."/entry:".$bwc_index."/rules/entry:".$InDeX."/bwcqd", query("bwcqd"));
		}
		set($to."/entry:".$bwc_index."/rules/count",	$rules_cnt);
		set($to."/entry:".$bwc_index."/rules/seqno",	$rules_seqno);
		$InDeX = $bwc_index;
    }
    set($to."/count",	$cnt);
    set($to."/seqno",	$seqno);
}

function copy_bwcf($from, $to)
{
    $cnt = query($to."/count");
    while($cnt>0)
    {
        del($to."/entry");
        $cnt--;
    }
    set($from."/count", 0);
    $cnt = 0;
    foreach($from."/entry")
    {
        $cnt++;
		if(query("uid") == "")
		{
	        set($to."/entry:".$InDeX."/uid", "BWCF-".$cnt);
		}
		else
		{
	        set($to."/entry:".$InDeX."/uid", query("uid"));
		}
        set($to."/entry:".$InDeX."/description", query("description"));
        set($to."/entry:".$InDeX."/flag", query("flag"));
        set($to."/entry:".$InDeX."/ipv4/start", query("ipv4/start"));
        set($to."/entry:".$InDeX."/ipv4/end", query("ipv4/end"));
        set($to."/entry:".$InDeX."/port/start", query("port/start"));
        set($to."/entry:".$InDeX."/port/end", query("port/end"));
	set($to."/entry:".$InDeX."/mac", query("mac"));
    }
    set($to."/count",	$cnt);

}

function copy_bwcqd($from, $to)
{
    $cnt = query($to."/count");
    while($cnt>0)
    {
        del($to."/entry");
        $cnt--;
    }
    set($from."/count", 0);
    $cnt = 0;
    foreach($from."/entry")
    {
        $cnt++;
		if(query("uid") == "")
		{
	        set($to."/entry:".$InDeX."/uid", "BWCQD-".$cnt);
		}
		else
		{
	        set($to."/entry:".$InDeX."/uid", query("uid"));
		}
        set($to."/entry:".$InDeX."/description", query("description"));
        set($to."/entry:".$InDeX."/flag", query("flag"));
        set($to."/entry:".$InDeX."/priority", query("priority"));
        set($to."/entry:".$InDeX."/bandwidth", query("bandwidth"));
    }
    set($to."/count",	$cnt);
}

function bwc_entry_setcfg($prefix, $inf)
{
	/* get bwc of inf */
	$base = XNODE_getpathbytarget($prefix, "inf", "uid", $inf, 0);
	if($base=="")
	{
		TRACE_error("SETCFG/BWC: no inf entry for [".$inf."] found!");
		return;
	}
	$bwc_uid = query($base."/bwc");

	/* copy the dhcp profile. */
	$spath = XNODE_getpathbytarget($prefix."/bwc", "entry", "uid", $bwc_uid, 0);
	$bwc = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_uid, 0);
	if ($bwc!="")
	{
		TRACE_debug("SETCFG: from ".$spath." to ".$bwc);
		copy_bwc($spath, $bwc);
	}
	else TRACE_error("SETCFG/BWC: no bwc entry for [".$bwc_uid."] found!");
}

function bwc_setcfg($prefix)
{
    TRACE_debug("SETCFG: $prefix=".$prefix);
    copy_bwc($prefix."/bwc", "/bwc");
    copy_bwcf($prefix."/bwc/bwcf", "/bwc/bwcf");
    copy_bwcqd($prefix."/bwc/bwcqd", "/bwc/bwcqd");
}

?>
