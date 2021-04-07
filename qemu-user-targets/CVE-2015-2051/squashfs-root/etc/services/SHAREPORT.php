<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";

$with_samba = "1";
$hostname        = query("/device/hostname");
$product         = query("/device/gw_name");
$inf             = "br0";
$dev_allow       = "/etc/silex/sxuptp_devices.allow";
$dev_deny        = "/etc/silex/sxuptp_devices.deny";
$proc_allow      = "/proc/sxuptp/devices.allow";
$proc_deny       = "/proc/sxuptp/devices.deny";
$jcpd            = "/usr/sbin/jcpd";
$hotplugd        = "/usr/sbin/hotplugd";
$sxuptp_inf      = "/sys/module/sxuptp/parameters/netif";
$jcpcmd_hostname = "/sys/module/jcp_cmd/parameters/hostname";
$jcpcmd_product  = "/sys/module/jcp_cmd/parameters/product";
$insmod_libpath  = "insmod /lib/modules/silex";
$samba_sharepath = "/tmp/storage";
$prefix_mountname = "Drive";

$NASCONF         = "/var/etc/silex/nas.conf";
$SAMBA_RUN       = "/var/etc/silex/hotplug_misc.sh";
$HOTPLUG_KILL    = "/var/etc/silex/hotplug_kill.sh";

/*-------------------------------------------------------------------------*/
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("a",$START, "echo -n 10240 > /proc/sys/vm/min_free_kbytes\n");

//remove sleep for improving bootup speed (tom, 20121116)
//fwrite("a",$START, "sleep 5\n");
//let modules order silex > usb-storage (tom, 20130805)
fwrite("a",$START, "rmmod usb_storage\n");

fwrite("a",$START, $insmod_libpath."/sxuptp.ko\n");
fwrite("a",$START, $insmod_libpath."/sxuptp_devfilter.ko\n");
fwrite("a",$START, "cat ".$dev_allow." > ".$proc_allow."\n");
fwrite("a",$START, "cat ".$dev_deny." > ".$proc_deny."\n");
fwrite("a",$START, $insmod_libpath."/sxuptp_driver.ko\n");
//joel add jcp.ko need netif interface .default is br0 in code.
fwrite("a",$START, "echo -n ".$inf." > ".$sxuptp_inf."\n");
fwrite("a",$START, $insmod_libpath."/jcp.ko\n");
fwrite("a",$START, $insmod_libpath."/jcp_cmd.ko\n");
//let modules order silex > usb-storage (tom, 20130805)
fwrite("a",$START, "insmod /lib/modules/usb-storage.ko\n");
fwrite("a",$START, "echo -n ".$product." > ".$jcpcmd_hostname."\n");
fwrite("a",$START, "echo -n ".$product." > ".$jcpcmd_product."\n");
//+++ hendry, add event for setting gw_name in runtime without restarting SHAREPORT service 
fwrite("a",$START, "event SHAREPORT.SETGWNAME add \"/etc/scripts/shareport_setgw.sh\"\n");
//--- hendry

if($with_samba == "1")
{
	fwrite("a",$START, $jcpd." -i ".$inf."\n");
	fwrite("a",$START, "mkdir -p ".$samba_sharepath."\n");
	fwrite("a",$START, "chmod 755 ".$SAMBA_RUN."\n");
	fwrite("a",$START, "chmod 755 ".$HOTPLUG_KILL."\n");
	/*
	fix bug:
		hotplugd not work when run in background in realtek solution.
	Root cause: 
		Still not know, but we know it is cause by running fork then pthread_create(),
		The created thread will run, but the pthread_create() will not return.	*/	
	//fwrite("a",$START, $hotplugd." -D\n");
	fwrite("a",$START, $hotplugd." &\n");

	//remove sleep for improving bootup speed (tom, 20121116)
	//fwrite("a",$START, "sleep 1; udevtrigger\n");
	fwrite("a",$START, "udevtrigger\n");

	fwrite("a",$STOP, "sxmount umount\n");
	fwrite("a",$STOP, "killall hotplugd 2>/dev/null\n");
	fwrite("a",$STOP, "killall jcpd\n");
}

fwrite("a",$STOP, "rmmod jcp_cmd\n");
fwrite("a",$STOP, "rmmod jcp\n");
fwrite("a",$STOP, "rmmod sxuptp_driver\n");
fwrite("a",$STOP, "rmmod sxuptp_devfilter\n");
fwrite("a",$STOP, "rmmod sxuptp\n");
//+++ hendry, add event for setting gw_name in runtime without restarting SHAREPORT service 
fwrite("a",$STOP, "event SHAREPORT.SETGWNAME add null\n");
//--- hendry

/*---------------------------------------------------------------------------*/
if($with_samba == "1")
{
	fwrite("w", $NASCONF, "SHAREDIR=".$samba_sharepath."\n");
	fwrite("a", $NASCONF, "TMPDIR=/tmp\n");
	fwrite("a", $NASCONF, "DRVNAME=%V_%D\n");
	fwrite("a", $NASCONF, "VLABEL=".$prefix_mountname."_%D\n");
	fwrite("a", $NASCONF, "DMOUNT=1\n");
	fwrite("a", $NASCONF, "MAXPART=15\n");
	fwrite("a", $NASCONF, "MNTGID=0\n");
	fwrite("a", $NASCONF, "SMB_ADMIN=administrator\n");
	fwrite("a", $NASCONF, "SMB_MAXCON=0\n");
	fwrite("a", $NASCONF, "PROG=".$SAMBA_RUN."\n");
	fwrite("a", $NASCONF, "RC_PATH=/etc/rc.d\n");

	/*
	    note: you should include smb.dir.conf to your smb.conf,
	    so you must rewrite your SAMBA service at prog.brand/$brand/service;
    */
	fwrite("w", $SAMBA_RUN, "#!/bin/sh\n");
	fwrite("a", $SAMBA_RUN, "SMBCONF=/usr/sbin/sxsambaconf\n");
	fwrite("a", $SAMBA_RUN, "case \"\$ACTION\" in\n");
	fwrite("a", $SAMBA_RUN, "\"BEFOREMNT\")\n");
	fwrite("a", $SAMBA_RUN, ";;\n");
	fwrite("a", $SAMBA_RUN, "\"AFTERMNT\")\n");
	fwrite("a", $SAMBA_RUN, "	\$SMBCONF -c \"/var/etc/silex/smb.dir.conf\" -d \"/var/etc/silex/smb.def.conf\"\n");
	fwrite("a", $SAMBA_RUN, "	service SAMBA restart\n");
	fwrite("a", $SAMBA_RUN, ";;\n");
	fwrite("a", $SAMBA_RUN, "esac\n");
	
	fwrite("w", $HOTPLUG_KILL, "#!/bin/sh\n");
	fwrite("a", $HOTPLUG_KILL, "ACTION=\$1\n");
	fwrite("a", $HOTPLUG_KILL, "DIRTY=\$2\n");
	fwrite("a", $HOTPLUG_KILL, "case \$ACTION in\n");
	fwrite("a", $HOTPLUG_KILL, "\"start\" )\n");	
	fwrite("a", $HOTPLUG_KILL, ";;\n");
	fwrite("a", $HOTPLUG_KILL, "\"stop\" )\n");
	fwrite("a", $HOTPLUG_KILL, "	service SAMBA stop\n");	
	fwrite("a", $HOTPLUG_KILL, ";;\n");
	fwrite("a", $HOTPLUG_KILL, "esac\n");
}

?>
