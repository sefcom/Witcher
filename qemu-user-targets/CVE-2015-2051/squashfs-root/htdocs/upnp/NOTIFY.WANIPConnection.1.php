<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/upnp.php";
include "/htdocs/upnpinc/gvar.php";
include "/htdocs/upnpinc/gena.php";

$udn = UPNP_getudnbytype($INF_UID, $G_IGD);
if ($udn=="") exit;
GENA_notify_req_event_hdr($HDR_URL, $HDR_HOST, "", $HDR_SID, $HDR_SEQ, "");

if ($WID=="") $WID=1;
if (query("/runtime/device/layout")=="router")	$RouterOn = 1;
else											$RouterOn = 0;

function get_curr_inet_path_by_inf_uid($uid)
{
	/* get value /inf:x/inet */
	$inet = query(XNODE_getpathbytarget("", "inf", "uid", $uid)."/inet");
	/* get path  /runtime/inf:x/inet:y */
	$runp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid);
	$runp = XNODE_getpathbytarget($runp, "inet", "uid", $inet);
	/* get value /runtime/inf:x/inet:y/addrtype */
	$type = query($runp."/addrtype");
	/* get path  /runtime/inf:x/inet:y/[ipv4|ipv6] */
	return $runp."/".$type;
}

echo "<?xml version=\"1.0\"?>";
?>
<e:propertyset xmlns:e="urn:schemas-upnp-org:event-1-0">
	<e:property>
		<PossibleConnectionTypes><?
			if ($RouterOn==1)	echo "IP_Routed";
			else				echo "IP_Bridge";
		?></PossibleConnectionTypes>
	</e:property>
	<e:property>
		<ConnectionStatus><?
			if ($RouterOn==1)
			{
				$actWan = query("/runtime/device/activewan");
				if($actWan == "WAN-3")	$runp = get_curr_inet_path_by_inf_uid($actWan);
				else	$runp = get_curr_inet_path_by_inf_uid("WAN-".$WID);
				echo map($runp."/valid", "1", "Connected", *, "Disconnected");
			}
			else
			{
				echo "Connected";
			}
		?></ConnectionStatus>
	</e:property>
	<e:property>
		<ExternalIPAddress><?
			if ($RouterOn==1)
			{
				$runp = get_curr_inet_path_by_inf_uid("WAN-".$WID);
				echo query($runp."/ipaddr");
			}
			else
			{
				$runp = get_curr_inet_path_by_inf_uid("LAN-1");
				echo query($runp."/ipaddr");
			}
		?></ExternalIPAddress>
	</e:property>
	<e:property>
		<PortMappingNumberOfEntries><?
			if ($RouterOn==1)
			{
				$n=query("/runtime/upnpigd/portmapping/entry#");
				if ($n=="")	echo "0";
				else		echo $n;
			}
			else
			{
				echo "0";
			}
		?></PortMappingNumberOfEntries>
	</e:property>
	<e:property>
		<X_Name><?
			if ($RouterOn==1)	echo "WAN Connection";
			else				echo "Bridge Mode";
		?></X_Name>
	</e:property>
</e:propertyset>
