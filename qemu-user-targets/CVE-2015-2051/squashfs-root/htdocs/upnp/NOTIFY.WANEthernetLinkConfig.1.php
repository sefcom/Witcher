<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/upnp.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/gena.php";

$udn = UPNP_getudnbytype($INF_UID, $G_IGD);
if ($udn=="")   exit;

GENA_notify_req_event_hdr($HDR_URL, $HDR_HOST, "", $HDR_SID, $HDR_SEQ, "");

/* to get the ethernet link status*/
$phy_uid = query(XNODE_getpathbytarget("", "inf", "uid", "WAN-1")."/phyinf");
$runp	 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy_uid);
if (query($runp."/linkstatus")!="")	$status="Up";
else								$status="Down";

echo "<?xml version=\"1.0\"?>";
?>
<e:propertyset xmlns:e="urn:schemas-upnp-org:event-1-0">
	<e:property>
		<EthernetLinkStatus><?=$status?></EthernetLinkStatus>
	</e:property>
</e:propertyset>
