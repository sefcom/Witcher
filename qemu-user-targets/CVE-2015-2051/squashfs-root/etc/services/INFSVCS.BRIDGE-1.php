<? /* vi: set sw=4 ts=4: */
include "/etc/services/INFSVCS/infservices.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");
fwrite("a", $STOP, "event INFSVCS.BRIDGE-1.DOWN\n");
infsvcs_bridge("1");
/* restart LLD2 */
fwrite("a",$START, "service LLD2 restart\n");
?>
