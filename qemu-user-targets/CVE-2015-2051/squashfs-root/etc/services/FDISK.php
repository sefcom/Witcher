<?
$fpath = "/var/run";
$xmlbase = "/runtime/fdisk";

fwrite(w, $START, "#!/bin/sh\n");
fwrite(a, $START, "for i in `ls ".$fpath."|grep '^SD'|grep 'conf$'`; do rm ".$fpath."/\$i; done\n");
$cnt = query($xmlbase."/entry#");
$i = 1;
while ($i <= $cnt)
{
	$uid = query($xmlbase."/entry:".$i."/uid");
	$line_cnt = query($xmlbase."/entry:".$i."/args#");
	if ($line_cnt>0)
	{
		fwrite(w, $fpath."/".$uid.".conf", query($xmlbase."/entry:".$i."/args:1")."\n");
		$j = 2;
		while ($j <= $line_cnt)
		{
			fwrite(a, $fpath."/".$uid.".conf", query($xmlbase."/entry:".$i."/args:".$j)."\n");
			$j++;
		}
		fwrite(a, $START, "event FDISK.".$uid."\n");
	}
	$i++;
}
fwrite(a, $START, "exit 0\n");

fwrite(w, $STOP, "#!/bin/sh\n");
fwrite(a, $STOP, "exit 0\n");
del($xmlbase);
?>
