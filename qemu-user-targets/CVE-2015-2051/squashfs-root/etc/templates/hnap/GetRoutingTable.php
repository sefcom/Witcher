<?
include "/htdocs/phplib/trace.php";

function getRoutingTable($ipVer)
{
	if($ipVer=="IPv4")
	{$get_routing_cmd = "ip route; ip route show table default; ip route list table STATIC;";}
	else if($ipVer=="IPv6")
	{$get_routing_cmd = "ip -6 route; ip -6 route show table STATIC;";}
	
	if ($get_routing_cmd == "")
	{
		echo "Invalid parameter!";
	}
	else
	{
		$count=cut_count($get_routing_cmd, ";");
		function execute($cmd)
		{
			setattr("/runtime/command", "get", $cmd ." >> /var/cmd.result"); 		
			get("x", "/runtime/command");	
		}
		$i=0;
		unlink("/var/cmd.result");
		while ($i < $count)
		{
			$str = cut($get_routing_cmd, $i, ";"); 
			execute($str);
			$i++;
		}
		
		$result = fread("","/var/cmd.result");
		unlink("/var/cmd.result");

		return $result;
	}
}
?>