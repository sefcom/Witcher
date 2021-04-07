<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
$minidlnacfg = "/var/etc/minidlna.conf";
$minidlnapid = "/var/run/minidlna.pid";
function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function cfgcmd($cfg)   {fwrite(a,$_GLOBALS["minidlnacfg"], $cfg."\n");}
/*
   <runtime/services>
   		<server>
			<uid>MINIDLNA.LAN-1</uid>
			<inf>LAN-1</inf>
			<ifname>br0</ifname>
			<port>8200</port>
			<name>DLNA server</name> (hostname)
		</server>
	</services/runtime>
 */
function gen_minidlna_cfg()
{
	$status = 0;
	cfgcmd("# minidlna configurations");
	cfgcmd("port=8200");
	cfgcmd("network_interface=br0");
	cfgcmd("friendly_name=".query("/device/hostname"));
	cfgcmd("album_art_names=Cover.jpg/cover.jpg/AlbumArtSmall.jpg/albumartsmall.jpg/AlbumArt.jpg/albumart.jpg/Album.jpg/album.jpg/Folder.jpg/folder.jpg/Thumb.jpg/thumb.jpg");
	cfgcmd("inotify=yes");
	cfgcmd("enable_tivo=no");
	cfgcmd("strict_dlna=yes");
	cfgcmd("notify_interval=900");
	cfgcmd("serial=12345678");
	cfgcmd("model_number=1");
	anchor("/runtime/device/storage");
	$dcnt = query("count");
	if ($dcnt<=0) {return $status;}
	foreach ("disk")
	{
		if ($InDeX > $dcnt) break;
		$pcnt = query("count");
		if ($pcnt<=0) {continue;}
		foreach ("entry")
		{
			$part=query("mntp");
			if ($part!="")
			{
				cfgcmd("media_dir=".$part);
				$status = 1;
			}
		}
	}
	return $status;
}

$cfg_is_ready = gen_minidlna_cfg();
if ($cfg_is_ready==1) {startcmd("minidlna -d -f ".$minidlnacfg." &");}
startcmd("exit 0");

stopcmd("/etc/scripts/killpid.sh ".$minidlnapid);
stopcmd("rm -f ".$minidlnacfg);
stopcmd("exit 0");
?>

