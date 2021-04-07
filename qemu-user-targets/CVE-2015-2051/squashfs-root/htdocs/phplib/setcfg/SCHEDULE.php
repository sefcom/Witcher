<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
$cnt = query("/schedule/entry#");
while ($cnt>0)
{
	del("/schedule/entry");
	$cnt--;
}

$cnt = query($SETCFG_prefix."/schedule/count");
$seqno = query($SETCFG_prefix."/schedule/seqno");

foreach ($SETCFG_prefix."/schedule/entry")
{
	if ($InDeX > $cnt) break;
	if (query("uid")=="")
	{
		set("uid", "SCH-".$seqno);
		$seqno++;
	}
	set("/schedule/entry:".$InDeX."/uid",			query("uid")		);
	set("/schedule/entry:".$InDeX."/description",	query("description"));
	set("/schedule/entry:".$InDeX."/exclude",		query("exclude")	);
	set("/schedule/entry:".$InDeX."/sun",			query("sun")		);
	set("/schedule/entry:".$InDeX."/mon",			query("mon")		);
	set("/schedule/entry:".$InDeX."/tue",			query("tue")		);
	set("/schedule/entry:".$InDeX."/wed",			query("wed")		);
	set("/schedule/entry:".$InDeX."/thu",			query("thu")		);
	set("/schedule/entry:".$InDeX."/fri",			query("fri")		);
	set("/schedule/entry:".$InDeX."/sat",			query("sat")		);
	set("/schedule/entry:".$InDeX."/start",			query("start")		);
	set("/schedule/entry:".$InDeX."/end",			query("end")		);
}

set("/schedule/seqno", $seqno);
set("/schedule/count", $cnt);
?>
