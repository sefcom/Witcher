<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

function result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function check_group($prefix)
{
	anchor($prefix);
	$seq = query("seqno");
	$max = query("max");
	$cnt = query("count");
	$num = query("entry#");
	
	if ($cnt > $max) $cnt = $max;
	while ($num > $cnt) {del("entry:".$num); $num--;}

	foreach(entry)
	{
		if ($InDeX > $cnt) break;
		$p = $prefix."/entry:".$InDeX;
		/* check uid */
		$uid = query("uid");
		if ($uid == "")
		{
			$uid = "GRP-".$seq;
			$seq++;
			set("uid", $uid);
		}

		/* check group name */
		$name = query("name");
		if (isalnum($name) != 1) return result("FAILED", $p."/name", "Invalid Group Name.");
		if ($name != "")
		{
			foreach($prefix."/entry")
			{
				$uid2 = query("uid");
				if ($uid == $uid2) continue;
				$name2 = query("name");
				if ($name == $name2) return result("FAILED", $p."/name", "Duplicated Group Name.");
			}
		}

		/* check gid */
		$gid = query("gid");
		if ($gid != "" && isdigit($gid) != 1) return result("FAILED", $p."/gid", "Not Numeric GID.");
		if ($gid != "")
		{
			foreach($prefix."/entry")
			{
				$uid2 = query("uid");
				if ($uid == $uid2) continue;
				$gid2 = query("gid");
				if ($gid == $gid2) return result("FAILED", $p."/gid", "Duplicated GID");
			}
		}

		/* check member */
		$mbr_seq = query("/member/seqno");
		$mbr_max = query("/member/max");
		$mbr_cnt = query("/member/count");
		$mbr_num = query("/member/entry#");

		if ($mbr_cnt > $mbr_max) $mbr_cnt = $mbr_max;
		while ($mbr_num > $mbr_cnt) {del("/member/entry:".$mbr_num); $mbr_num--;}

		foreach($p."/member/entry")
		{
			$q = $p."/member/entry:".$InDeX;
			/* check uid */
			$mbr_uid = query("uid");
			TRACE_debug("GROUP.MEMBER[entry".$InDeX."]: uid=".$mbr_uid);
			if ($mbr_uid == "")
			{
				$mbr_uid = "MBR-".$mbr_seq;
				$mbr_seq++;
				set("uid", $mbr_uid);
			}
			
			/* check account */
			$mbr_name = query("name");
			TRACE_debug("GROUP.MEMBER[entry".$InDeX."]: name=".$mbr_name);
			if (isalnum($mbr_name) != 1) return result("FAILED", $q."/name", "Invalid Member.");
		}
	}
	set("seqno", $seq);
	TRACE_debug("GROUP.MEMBER: END");
	return "OK";
}

function check_account($prefix)
{
	anchor($prefix);
	$seq = query("seqno");
	$max = query("max");
	$cnt = query("count");
	$num = query("entry#");
	
	if ($cnt > $max) $cnt = $max;
	while ($num > $cnt) {del("entry:".$num); $num--;}

	foreach(entry)
	{
		if ($InDeX > $cnt) break;
		$p = $prefix."/entry:".$InDeX;
		/* check uid */
		$uid = query("uid");
		if ($uid == "")
		{
			$uid = "USR-".$seq;
			$seq++;
			set("uid", $uid);
		}

		/* check user name */
		$name = query("name");
		if (isalnum($name) != 1) return result("FAILED", $p."/name", "Invalid User Name.");
		if ($name != "")
		{
			foreach($prefix."/entry")
			{
				$uid2 = query("uid");
				if ($uid == $uid2) continue;
				$name2 = query("name");
				if ($name == $name2) return result("FAILED", $p."/name", "Duplicated User Name.");
			}
		}

		/* check uid */
		$usrid = query("usrid");
		if ($usrid != "" && isdigit($usrid) != 1) return result("FAILED", $p."/usrid", "Not Numeric UID.");
		if ($usrid != "")
		{
			foreach($prefix."/entry")
			{
				$uid2 = query("uid");
				if ($uid == $uid2) continue;
				$usrid2 = query("usrid");
				if ($usrid == $usrid2) return result("FAILED", $p."/usrid", "Duplicated UID.");
			}
		}

		/* check password */
		$passwd = query("password");
		if ($passwd != "" && isprint($passwd) != 1) return result("FAILED", $p."/password", "Invalid Password.");

		/* check group */
		/* check description */
	}
	set("seqno", $seq);
	TRACE_debug("GROUP.MEMBER: END");
	$ret = "OK";
	return $ret;
}

function check_all($prefix)
{
	if (check_group($prefix."/device/group") != "OK") return "FAILED";
	if (check_account($prefix."/device/account") != "OK") return "FAILED";
	return "OK";
}

result("FAILED","","");
if (check_all($FATLADY_prefix) == "OK")
{
	set($FATLADY_prefix."/valid", "1");
	result("OK", "", "");
}
?>
