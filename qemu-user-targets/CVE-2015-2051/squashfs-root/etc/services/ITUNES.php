<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/mdnsresponder.php";

$mnt_root        = "/tmp/storage";
$product         = query("/runtime/device/modelname");
$partition_count = query("/runtime/device/storage/disk/count");
$active          = query("/itunes/server/active");
$sharepath       = query("/itunes/server/sharepath");
$mntpath         = query("/runtime/itunes/server/mntpath");
    	
if($sharepath == "" || $sharepath == "/")
{ 
	$sharepath = $mnt_root."/";
	if($partition_count > 0)
	{  //force to use first partition if there are disk connected.
	   //use /tmp/storage/ as db_dir may cause OOM
		$mntpath = query("/runtime/device/storage/disk/entry:1/mntp")."/"; 
	}
	else
	{
	$mntpath   = $mnt_root."/";	
}
}
else	
{			
	$index = strstr($sharepath, "/");
	if($index != "")
	{	
		$diskname = substr($sharepath, 0, $index);
		$mntpath = $mnt_root."/".$diskname."/";
	}
	else
	{
		$mntpath = $mnt_root."/".$sharepath."/";
	}		
	$sharepath = $mnt_root."/".$sharepath."/";			
}

if($partition_count!="" && $partition_count!="0")
{
    $sd_status = "active";
}
else
{
    $sd_status = "inactive";
}

$ITUNES_CONF = "/var/mt-daapd.conf";
$INF = "br0";

/* info for mdnsresponder */
$port	= "3689";
$product = query("/runtime/device/modelname");
$vendor = query("/runtime/device/vendor");
$srvname = $vendor." ".$product;
$srvcfg = "_daap._tcp.";
$mdirty = 0;
/*---------------------------------------------------------------------*/

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

/* if path not exist, use root path */
if (isdir($sharepath)==0)
{
	$sharepath = $mnt_root."/";
	//$mntpath   = $mnt_root."/";
	if($partition_count > 0)
	{  //force to use first partition if there are disk connected.
	   //use /tmp/storage/ as db_dir may cause OOM
		$mntpath = query("/runtime/device/storage/disk/entry:1/mntp")."/"; 
	}
	else
	{
	$mntpath   = $mnt_root."/";	
	}	
    fwrite("a", $START, "xmldbc -s /itunes/server/sharepath \"/\"\n");
}

if($sd_status == "inactive")
{
    fwrite("a", $START, "echo \"No HD found\"  > /dev/console\n");
    $mdirty = setup_mdns("MDNSRESPONDER.ITUNES","0",null,null);
}
else
{
	if ($active!="1")
	{
	   	fwrite("a", $START, "echo \"itunes server is disabled !\" > /dev/console\n");
	   	$mdirty = setup_mdns("MDNSRESPONDER.ITUNES","0",null,null);
	}
	else
	{	
		$mdirty = setup_mdns("MDNSRESPONDER.ITUNES",$port,$srvname,$srvcfg);	
		fwrite("w", $ITUNES_CONF, "web_root        /etc/admin-root\n");
		fwrite("a", $ITUNES_CONF, "port            3689\n");
		fwrite("a", $ITUNES_CONF, "admin_pw        vykvkhvkhvilhbn1561\n");
		fwrite("a", $ITUNES_CONF, "mp3_dir         ".$sharepath."\n");		
        fwrite("a", $ITUNES_CONF, "servername      ".$srvname."\n");
        fwrite("a", $ITUNES_CONF, "runas           root\n");
        fwrite("a", $ITUNES_CONF, "playlist        /etc/mt-daapd.playlist\n");
        fwrite("a", $ITUNES_CONF, "extensions      .mp3,.m4a,.m4p\n");
        fwrite("a", $ITUNES_CONF, "db_dir          ".$mntpath.".systemfile/\n");
        fwrite("a", $ITUNES_CONF, "rescan_interval 0\n");
        fwrite("a", $ITUNES_CONF, "scan_type       0\n");
        fwrite("a", $ITUNES_CONF, "always_scan     1\n");
        //fwrite("a", $ITUNES_CONF, "logfile         /var/log/mt-daapd.log\n");
        fwrite("a", $ITUNES_CONF, "process_m3u     0\n");
        fwrite("a", $ITUNES_CONF, "compress        0\n");
	fwrite("a", $START, "mt-daapd -m -i ".$INF." -c ".$ITUNES_CONF." -I -L 0 -f &\n");
	}	
}

if ($active!="1")
{
    fwrite("a", $STOP, "echo \"itunes server is disabled !\" > /dev/console\n");
}
else
{
	fwrite("a",$STOP, "killall -9 mt-daapd\n");
}
if ($mdirty>0)
{
	fwrite("a", $START, "service MDNSRESPONDER restart\n");
	fwrite("a", $STOP, "sh /etc/scripts/delpathbytarget.sh /runtime/services/mdnsresponder server uid MDNSRESPONDER.ITUNES\n");
	fwrite("a", $STOP, "service MDNSRESPONDER restart\n");
}
?>
