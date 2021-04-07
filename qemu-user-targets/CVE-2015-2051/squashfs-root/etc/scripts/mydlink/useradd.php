<?
include "/htdocs/phplib/xnode.php";
$USERLISTP	= "/runtime/mydlink/userlist";
$found=0;
foreach($USERLISTP."/entry"){
	$ipaddr = query($USERLISTP."/entry:".$InDeX."/ipaddr");
	if($ipaddr==$IPADDR){
		$found = 1;
		break;
	}
}
if($found==0){
	$cnt = query($USERLISTP."/entry#");
	$cnt = $cnt+1;
	anchor($USERLISTP."/entry:".$cnt);
	set("ipaddr",	$IPADDR);
	if($HOSTNAME!=""){set("hostname",	$HOSTNAME);}
	if($MACADDR!="") {set("macaddr", $MACADDR);}
}else if($found==1){
	anchor($USERLISTP."/entry:".$cnt);
	if($HOSTNAME!=""){
		if(query("hostname")!=$HOSTNAME){set("hostname", $HOSTNAME);}
	}
	if($MACADDR!=""){
		if(query("macaddr")!=$MACADDR)  {set("macaddr", $MACADDR);}
	}
}
?>
