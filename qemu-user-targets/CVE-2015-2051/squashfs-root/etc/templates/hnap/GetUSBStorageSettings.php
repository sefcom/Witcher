<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 
include "/htdocs/phplib/encrypt.php";

$result = "OK";
$enable = get("","/webaccess/enable");
$RemoteHttp = get("","/webaccess/httpenable");
$RemoteHttpPort = get("","/webaccess/httpport");
$RemoteHttps = get("","/webaccess/httpsenable");
$RemoteHttpsPort = get("","/webaccess/httpsport");

if($enable==1) $enable = "true"; else $enable = "false";
if($RemoteHttp==1) $RemoteHttp = "true"; else $RemoteHttp = "false";
if($RemoteHttps==1) $RemoteHttps = "true"; else $RemoteHttps = "false";

function print_StorageUserInfo()
{
	echo "<StorageUserInfoLists>";
	foreach("/webaccess/account/entry")
	{
		$username = get("","username");
		$passwd = get("","passwd");
		$path = get("","entry/path");
    if (substr($path,0,1) != "/")
    {
    	$path = "/".$path;
    }
		$permission = get("","entry/permission");
		
		if($username != "Admin" && $username != "admin")
		{
			if($permission == "rw") $permission = "true";
			else $permission = "false";
			
			echo "<StorageUser>";
			echo "<UserName>".escape("x",$username)."</UserName>";
			echo "<Password>".AES_Encrypt128($passwd)."</Password>";
			echo "<AccessPath>".escape("x",$path)."</AccessPath>";
			echo "<Promission>".escape("x",$permission)."</Promission>";
			echo "</StorageUser>";
		}
	}
	echo "</StorageUserInfoLists>";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetUSBStorageSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetUSBStorageSettingsResult><?=$result?></GetUSBStorageSettingsResult>
	<Enabled><?=$enable?></Enabled>
	<RemoteHttp><?=$RemoteHttp?></RemoteHttp>
	<RemoteHttpPort><?=$RemoteHttpPort?></RemoteHttpPort>
	<RemoteHttps><?=$RemoteHttps?></RemoteHttps>
	<RemoteHttpsPort><?=$RemoteHttpsPort?></RemoteHttpsPort>
	<?print_StorageUserInfo();?>
</GetUSBStorageSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>