<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/upnp.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/gena.php";

$udn = UPNP_getudnbytype($INF_UID, $G_IGD); if ($udn=="") exit;
GENA_notify_req_event_hdr($HDR_URL, $HDR_HOST, "", $HDR_SID, $HDR_SEQ, "");
echo "<?xml version=\"1.0\"?>";
?>
<e:propertyset xmlns:e="urn:schemas-upnp-org:event-1-0">
	<e:property />
</e:propertyset>
