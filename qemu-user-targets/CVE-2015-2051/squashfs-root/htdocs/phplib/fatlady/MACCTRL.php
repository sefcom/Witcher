<? /* vi: set sw=4 ts=4: */
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be breaked for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

//////////////////////////////////////////////////////////////////////////////

$root = "/acl/macctrl";

/* The default MAX. value. */
$max = query($root."/max"); if ($max=="") $max=32;
$count = query($FATLADY_prefix.$root."/count"); if ($count=="") $count=0;
$seqno = query($FATLADY_prefix.$root."/seqno");
if ($count > $max) $count = $max;

TRACE_debug("FATLADY: MACCTRL: max=".$max.", count=".$count.", seqno=".$seqno);

/* MAC filter default policy */
$policy = query($FATLADY_prefix.$root."/policy");
if ($policy!="DROP" && $policy!="ACCEPT") set($FATLADY_prefix.$root."/policy", "DISABLE");

/* Delete the extra entries. */
$cnt = query($FATLADY_prefix.$root."/entry#");
while ($cnt>$count) { del($FATLADY_prefix.$root."/entry:".$cnt); $cnt--; }

$ret = "OK";
/* Verify each entries */
set($FATLADY_prefix.$root."/count", $count);
foreach ($FATLADY_prefix.$root."/entry")
{
	if ($InDeX > $count) break;

	/* The current entry path. */
	$entry = $FATLADY_prefix.$root."/entry:".$InDeX;

	/* Check empty UID */
	$uid = query("uid");
	if ($uid=="")
	{
		$uid = "MACF-".$seqno;
		set("uid", $uid);
		$seqno++;
		set($FATLADY_prefix.$root."/seqno", $seqno);
	}
	/* Check duplicated UID */
	if ($$uid == "1")
	{
		$ret = set_result("FAILED", $entry."/uid", "Duplicated UID - ".$uid);
		break;
	}
	$$uid = "1";

	/* Enable is boolean type. */
	if (query("enable")!="1") set("enable", "0");

	/* verify MAC address */
	$mac = query("mac");
	if ($mac == "")
	{
		$ret = set_result("FAILED", $entry."/mac", i18n("No MAC address value."));
		break;
	}
	if (PHYINF_validmacaddr($mac) != 1)
	{
		$ret = set_result("FAILED", $entry."/mac", i18n("Invalid MAC address value."));
		break;
	}

	/* duplication check */
	$i = $InDeX + 1;
	while ($i <= $count)
	{
		if ($mac == query($FATLADY_prefix.$root."/entry:".$i."/mac"))
		{
			$ret = set_result("FAILED", $entry."/mac", i18n("Duplicate MAC addresses."));
			break;
		}
		$i++;
	}
	if ($ret=="FAILED") break;

	$ret = "OK";
}

TRACE_debug("FATLADY: MACCTRL: ret = ".$ret);
if ($ret=="OK") set($FATLADY_prefix."/valid", "1");
?>
