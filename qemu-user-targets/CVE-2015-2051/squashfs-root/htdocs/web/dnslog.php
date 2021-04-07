<? 
//def
	$MAX_COUNT = 20;  //max log entry number

	function set_add_entry($act, $idx, $base, $date, $time, $domain)
	{	
		if($act == "add")
		{
			add($base."entry:".$idx."/date", $date);
			add($base."entry:".$idx."/time", $time);
			add($base."entry:".$idx."/domain", $domain);
		}
		else if($act == "set")
		{
			set($base."entry:".$idx."/date", $date);
			set($base."entry:".$idx."/time", $time);
			set($base."entry:".$idx."/domain", $domain);
		}
			
	}
	
	function shift_entry($total, $base)
	{
		while($total > 0)
		{
			$f_idx = $total-1;
			$to_idx = $total;
			del($base."entry:".$to_idx."/date");
			del($base."entry:".$to_idx."/time");
			del($base."entry:".$to_idx."/domain");
			movc($base."entry:".$f_idx, $base."entry:".$to_idx);
			$total = $total -1;
		}
	}

	$dnsquery_enable = query("/device/log/mydlink/dnsquery"); //check dnsquery enable or not 
	if(isempty($dnsquery_enable) == 1 || $dnsquery_enable == 0)    //dnsquery disable
		exit;

	$date =   cut($RAW_VALUE, 0,',');
	$time =   cut($RAW_VALUE, 1,',');
	$domain = cut($RAW_VALUE, 2,',');
	$ip =     cut($RAW_VALUE, 3,',');
	$mac =    cut($RAW_VALUE, 4,',');
	
	if(isempty($mac) == 1)
		exit;
		
	//$result = $date. " ". $time. " ". $domain. " ". $ip. " ".$mac;
	//fwrite(w, "/mnt/dns_log", $result."\n");
		
	$base = "/runtime/DNSqueryhistory/entry";
	$cnt = query($base."#");
	
	if(isempty($cnt) == 1)
	{
		$cnt = 0;
		add($base.":1/macaddr", $mac);
	}
	else
	{
		$i = $cnt;
		echo "count [".$cnt."]";
		while($i > 0)
		{
			$t_mac = query($base.":".$i."/macaddr");
			//echo "mac [". $t_mac."] count[".$i."]\n";	
			if($mac == $t_mac)
			{
				echo "found entry[".$i."] \n";
				$cnt = $i;
				break;
			}
			$i = $i -1;
			if($i == 0)
			{
				$cnt = $cnt + 1;
				add($base.":".$cnt."/macaddr", $mac);
				break;
			}
		}
	}
	if($cnt > 0)
		$base = $base.":".$cnt."/";	
	else
		$base = $base.":1/";

//	echo "entry[". $cnt."] base[".$base."]\n";
//	echo "count[".$cnt."] \n";
	
	$cnt = query($base."entry#");
	
	if(isempty($cnt) == 1) {$cnt = 0;}

	if($cnt == $MAX_COUNT)
	{//shift 1~19 to 2~20 by using mov. set entry:1
		shift_entry($cnt, $base);
		set_add_entry("add", 1, $base, $date, $time, $domain);
	}
	else
	{//add entry $cnt+1, shift 1~$cnt to 2~$cnt+1, set entry:1
		//set_add_entry("add", $cnt+1, $base, $date, $time, $mac, $domain);
		add($base."entry", "");
		
		if($cnt > 0)
		{
			shift_entry($cnt+1, $base);
			set_add_entry("add", 1, $base, $date, $time, $domain); 
		}else 
			set_add_entry("add", $cnt+1, $base, $date, $time, $domain);
	}
	
?>
