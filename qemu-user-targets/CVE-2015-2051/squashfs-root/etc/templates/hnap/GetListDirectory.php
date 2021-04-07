<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

function pvt_shell_injection($parameter)
{
	return "\"".escape("s",$parameter)."\"";
}

$result = "OK";
$request_path = get("","/runtime/hnap/GetListDirectory/ListDirectoryPath");

//Error Check
if(strstr($request_path, "/..") != "")
{$result = "ERROR_BAD_LISTDIRECTORYPATH";}

if($result == "OK")
{
	$full_request_path = "/var/tmp/storage".$request_path;
	TRACE_debug("full_request_path=".$full_request_path);

	$full_request_path = pvt_shell_injection($full_request_path); //sammy
	setattr("/runtime/hnap/GetListDirectory/get_ls","get","cd ".$full_request_path." && ls -1F | grep /");
}

function print_DirectoryList()
{
	echo "<DirectoryList>";

		$request_path = get("","/runtime/hnap/GetListDirectory/ListDirectoryPath");
		$get_ls = get("","/runtime/hnap/GetListDirectory/get_ls");
		
		if($get_ls == "") //No such file or directory
		{
			$result = "ERROR_BAD_LISTDIRECTORYPATH";
			
			echo "<ListDirectoryInfo>";
			echo "<DirectoryName></DirectoryName>";
			echo "<DirectoryPath></DirectoryPath>";
			echo "<SubDirectory></SubDirectory>";		
			echo "</ListDirectoryInfo>";
		}
		else
		{
			$i=0;
			$cut_count = cut_count($get_ls,"/")-1;
			
			/* print each directorys */
			while($i < $cut_count)
			{
				$DirectoryName = cut($get_ls,$i,"/");
				
				if($i >= 1) //remove " " from the begine
					{$DirectoryName = substr($DirectoryName,1,strlen($DirectoryName)-1);}
				
				$DirectoryPath = $request_path."/".$DirectoryName;
				
				TRACE_debug("DirectoryName=".$DirectoryName);
				TRACE_debug("DirectoryPath=".$DirectoryPath);
				
				/* modify DirectoryName: mntp to label
					 mntp = JetFlash_TS1GJFV20_72VY1
					 label = JetFlash
				*/			
				foreach("/runtime/webaccess/device/entry")
				{
					foreach("entry")
					{
						$mntp = get("","mntp");
						
						if($mntp == "/tmp/storage/".$DirectoryName)
							{$DirectoryName = get("","label").":";}
					}
				}
		
				/* check has SubDirectory? */
				$ls_dir = "/var/tmp/storage".$DirectoryPath;
				$ls_dir = pvt_shell_injection($ls_dir); //sammy
				setattr("/runtime/hnap/GetListDirectory/check_subdir","get","cd ".$ls_dir." && ls -1F | grep /");
				$check_subdir = get("","/runtime/hnap/GetListDirectory/check_subdir");
				if($check_subdir == "") $SubDirectory = false;
				else 										$SubDirectory = true;
				
				TRACE_debug("SubDirectory=".$SubDirectory."\n");
				
				echo "<ListDirectoryInfo>";
				echo "<DirectoryName>".escape("x",$DirectoryName)."</DirectoryName>";
				echo "<DirectoryPath>".escape("x",$DirectoryPath)."</DirectoryPath>";
				echo "<SubDirectory>".escape("x",$SubDirectory)."</SubDirectory>";		
				echo "</ListDirectoryInfo>";
				
				$i++;
			}
		}
		
	echo "</DirectoryList>";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
<GetListDirectoryResponse xmlns="http://purenetworks.com/HNAP1/">
	<GetListDirectoryResult><?=$result?></GetListDirectoryResult>
	<? if($result == "OK")	{print_DirectoryList();}?>
</GetListDirectoryResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

