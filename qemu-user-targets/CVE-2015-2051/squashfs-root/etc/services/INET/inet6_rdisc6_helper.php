#!/bin/sh
<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function cmd($cmd) {echo $cmd."\n";}
function msg($msg) {cmd("echo [RDISC6]: ".$msg." > /dev/console");}

/*****************************************/
msg("[IFNAME]: ".$_GLOBALS["IFNAME"].", [MFLAG]: ".$_GLOBALS["MFLAG"]." ,[OFLAG]: ".$_GLOBALS["OFLAG"]);
$ifname = $_GLOBALS["IFNAME"];
$mflag  = $_GLOBALS["MFLAG"];
$oflag  = $_GLOBALS["OFLAG"];
$prefix = $_GLOBALS["PREFIX"];
$pfxlen = $_GLOBALS["PFXLEN"];
$router = $_GLOBALS["LLADDR"];
$rdnss  = $_GLOBALS["RDNSS"];
$dnssl  = $_GLOBALS["DNSSL"];
$mtu  = $_GLOBALS["MTU"];
$routerlft  = $_GLOBALS["ROUTERLFT"];

if($ifname=="") {msg("Doesn't have IFNAME"); return;}

$conf = "/var/run/".$ifname;

if($mflag!="")	cmd("echo ".$mflag." > ".$conf.".ra_mflag");
if($oflag!="")	cmd("echo ".$oflag." > ".$conf.".ra_oflag");
if($prefix!="")	cmd("echo ".$prefix." > ".$conf.".ra_prefix");
if($pfxlen!="")	cmd("echo ".$pfxlen." > ".$conf.".ra_prefix_len");
if($router!="")	cmd("echo ".$router." > ".$conf.".ra_saddr");
if($rdnss!="")	cmd("echo ".$rdnss." > ".$conf.".ra_rdnss");
if($dnssl!="")	cmd("echo ".$dnssl." > ".$conf.".ra_dnssl");
if($mtu!="")	cmd("echo ".$mtu." > ".$conf.".ra_mtu");
if($routerlft!="")	cmd("echo ".$routerlft." > ".$conf.".ra_routerlft");

/* save to runtime wandetect node */
if($mflag=="0") set("/runtime/services/wandetect6/autoconf/CHECK", "STATELESS");
if($mflag=="1") set("/runtime/services/wandetect6/autoconf/CHECK", "STATEFUL");
?>
