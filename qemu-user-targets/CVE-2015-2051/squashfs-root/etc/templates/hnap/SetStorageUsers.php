HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 
include "/htdocs/phplib/encrypt.php";

function XNODE_add_entry_for_WEBACCESS($base)
{
	$count = query($base."/count");
	if ($count == "")
		{$count = 0;}
	$count++;
	set($base."/count", $count);
	return $base."/entry:".$count;
}

$result = "OK";

/* check parameters 
foreach("/runtime/hnap/SetStorageUsers/StorageUserInfoLists/StorageUser")
{
	$req_UserName = get("","UserName");
	$req_Password = get("","Password");
	$req_AccessPath = get("","AccessPath");
	$req_Promission = get("","Permission");
	
	if($req_UserName == "" || $req_AccessPath == "")
	{
		$result = "ERROR";
		TRACE_error("SetStorageUsers is not OK: check parameters fail"); 
	}
	
	if($req_Promission != "true" || $req_Promission != "false")
	{
		$result = "ERROR_BAD_PROMISION";
		TRACE_error("SetStorageUsers is not OK ret=".$result); 
	}
}	
*/

/* start to set */
if($result == "OK")
{
	/* clean old entry */
	$i = 0;
	$old_count = get("", "/webaccess/account/count");
	while($i < $old_count)
	{
		del("/webaccess/account/entry");
		$i++;
	}
	set("/webaccess/account/count",0);
	
	/* add admin account */
	foreach("/device/account/entry")
	{
		$admin = get("","name");
		if( $admin == "Admin" || $admin == "admin") 
		{
			$admin_name = $admin;
			$admin_passwd = get("","password");
		}
	}
	if($admin_name == "")
	{
		TRACE_error("SetStorageUsers is not OK: add admin account fail"); 
		$result = "ERROR";
	}
	else
	{
		$newentry = XNODE_add_entry_for_WEBACCESS("/webaccess/account");
		set($newentry."/username",$admin_name);
		set($newentry."/passwd",$admin_passwd);
		set($newentry."/entry/path","root");
		set($newentry."/entry/permission","rw");
	}
	
	/* start to set storage users */
	foreach("/runtime/hnap/SetStorageUsers/StorageUserInfoLists/StorageUser")
	{
		$req_UserName = get("","UserName");
		$req_Password = get("","Password");
		$req_Password = AES_Decrypt128($req_Password);
		$req_AccessPath = get("","AccessPath");
		$req_Promission = get("","Permission");
		
		if($req_Promission == "true") 				{$req_Promission = "rw";}
		else if($req_Promission == "false")		{$req_Promission = "ro";}
		else 																	{$req_Promission = "ro";}
		
		TRACE_debug("req_UserName=".$req_UserName);
		TRACE_debug("req_Password=".$req_Password);
		TRACE_debug("req_AccessPath=".$req_AccessPath);
		TRACE_debug("req_Promission=".$req_Promission);
		
		/* modify AccessPath: mntp to label
			 mntp = /JetFlash_TS1GJFV20_72VY1/miiiCasa_Photos, (/$mntp_name + $share_path)
			 label = JetFlash:/miiiCasa_Photos
		*/
/*
		if(strstr($req_AccessPath,":") == "" && //already use label name
			 $req_AccessPath != "root" && //The path is root.
			 $req_UserName != "Admin" &&
			 $req_UserName != "admin" &&
			 $req_UserName != "Guest" &&
			 $req_UserName != "guest" )
		{
			$mntp_name = cut($req_AccessPath,1,"/");
			$share_path = substr($req_AccessPath,1,strlen($req_AccessPath)-1); //eat first "/"
			$share_path = substr($share_path,strlen($mntp_name),strlen($share_path)-strlen($mntp_name));
	
			foreach("/webaccess/device/entry")
			{
				foreach("entry")
				{
					$mntp = get("","mntp");
					
					if($mntp == "/tmp/storage/".$mntp_name)
						{$label = get("","label");}
				}
			}
			
			$req_AccessPath = $label.":".$share_path;
			
			TRACE_debug("Transform AccessPath to=".$req_AccessPath);
		}
*/
    if (substr($req_AccessPath,0,1) == "/") //remove leading
    {
    	$req_AccessPath = substr($req_AccessPath,1,strlen($req_AccessPath) -1 );
    }
    TRACE_error("req_AccessPath=".$req_AccessPath);
    
		$newentry = XNODE_add_entry_for_WEBACCESS("/webaccess/account");
		set($newentry."/username",$req_UserName);
		set($newentry."/passwd",$req_Password);
		set($newentry."/entry/path",$req_AccessPath);
		set($newentry."/entry/permission",$req_Promission);
	}
	
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service WEBACCESS restart > /dev/console\n");
	fwrite("a",$ShellPath, "service INET.LAN-1 restart > /dev/console\n");
	fwrite("a",$ShellPath, "service UPNPC restart > /dev/console\n");
	if($FEATURE_NOSAMBA != "1")
		{	fwrite("a",$ShellPath, "service SAMBA restart > /dev/console\n");	}
}
else
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" soap:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<soap:Body>
<SetStorageUsersResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetStorageUsersResult><?=$result?></SetStorageUsersResult>
</SetStorageUsersResponse>
</soap:Body>
</soap:Envelope>
