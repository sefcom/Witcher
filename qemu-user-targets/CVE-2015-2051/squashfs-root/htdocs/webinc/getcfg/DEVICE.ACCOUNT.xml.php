<module>
	<service><?=$GETCFG_SVC?></service>
	<device>
		<account>
<?
$cnt = query("/device/account/count");
if ($cnt=="") $cnt=0;
echo "\t\t\t<seqno>".query("/device/account/seqno")."</seqno>\n";
echo "\t\t\t<max>".query("/device/account/max")."</max>\n";
echo "\t\t\t<count>".$cnt."</count>\n";
foreach("/device/account/entry")
{
	if ($InDeX > $cnt) break;
	echo "\t\t\t<entry>\n";
	echo "\t\t\t\t<uid>".		get("x","uid").	"</uid>\n";
	echo "\t\t\t\t<name>".		get("x","name").	"</name>\n";
	echo "\t\t\t\t<usrid>".		get("x","usrid").	"</usrid>\n";
	echo "\t\t\t\t<password>".	get("x","password")."</password>\n";
	echo "\t\t\t\t<group>".		get("x", "group").	"</group>\n";
	echo "\t\t\t\t<description>".get("x","description")."</description>\n";
	echo "\t\t\t</entry>\n";
}
?>		</account>
		<group>
<?
$cnt = query("/device/group/count");
if ($cnt=="") $cnt=0;
echo "\t\t\t<seqno>".query("/device/group/seqno")."</seqno>\n";
echo "\t\t\t<max>".query("/device/group/max")."</max>\n";
echo "\t\t\t<count>".$cnt."</count>\n";
$b = "/device/group/entry";
function gen_member($s,$p)
{
	$cnt = query($p."/count");
	echo $s."<member>\n";
	echo $s."\t<seqno>".query($p."/seqno")."</seqno>\n";
	echo $s."\t<max>".query($p."/max")."</max>\n";
	echo $s."\t<count>".$cnt."</count>\n";
	foreach($p."/entry")
	{
		if ($InDeX > $cnt) break;
		echo $s."\t<entry>\n";
		echo $s."\t\t<uid>".	get("x","uid"). "</uid>\n";
		echo $s."\t\t<name>".	get("x","name"). "</name>\n";
		echo $s."\t</entry>\n";
	}
	echo $s."</member>\n";
}
foreach($b)
{
	if ($InDeX > $cnt) break;
	echo "\t\t\t<entry>\n";
	echo "\t\t\t\t<uid>".		get("x","uid").	"</uid>\n";
	echo "\t\t\t\t<name>".		get("x","name").	"</name>\n";
	echo "\t\t\t\t<gid>".		get("x","gid").	"</gid>\n";
	gen_member("\t\t\t\t", $b.":".$InDeX."/member");
	echo "\t\t\t</entry>\n";
}
?>		</group>
		<session>
<?
	echo dump(3, "/device/session");
?>		</session>
	</device>
</module>
