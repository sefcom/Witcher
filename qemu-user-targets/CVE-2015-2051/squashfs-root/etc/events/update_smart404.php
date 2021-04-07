<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";


function enable_fakedns()
{
	echo "xmldbc -s /runtime/smart404/fakedns 1\n";

	//redirect all HTTP access to 404 page
	//remove this rule because the next rule will handle all http requests
	$proxyd_port="5449";
	echo "iptables -t nat -D PREROUTING -d 1.33.203.39 -p tcp --dport 80 -j REDIRECT --to-ports ".$proxyd_port."\n";

	echo "iptables -t nat -D PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80\n";
	echo "iptables -t nat -I PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80\n";

	//redirect all DNS to fakedns
    echo "iptables -t nat -D PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 63481\n";
    echo "iptables -t nat -I PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 63481\n";

	echo "iptables -t nat -D PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 63481\n";
	echo "iptables -t nat -I PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 63481\n";
}

function start_proxyd()
{
	echo "killall -9 proxyd\n";
	
	$r_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0);
	$lanif=query($r_infp."/devnam");
	if($lanif=="") $lanif="br0";
	
	$proxyd_port="5449";
	$config_file="/var/run/proxyd.conf";
	fwrite("w", $config_file, "CONTROL\n{\n");
	fwrite("a", $config_file, "\tTIMEOUT_CONNECT\t30\n");
	fwrite("a", $config_file, "\tTIMEOUT_READ\t30\n");
	fwrite("a", $config_file, "\tTIMEOUT_WRITE\t30\n");
	fwrite("a", $config_file, "\tMAX_CLIENT\t32\n");
	fwrite("a", $config_file, "}\n\n");
	fwrite("a", $config_file, "HTTP\n{\n");
	fwrite("a", $config_file, "\tINTERFACE\t".$lanif."\n");
	fwrite("a", $config_file, "\tPORT\t".$proxyd_port."\n");
	fwrite("a", $config_file, "\tALLOW_TYPE\t{ gif jpg css png }\n");
	fwrite("a", $config_file, "\tERROR_PAGE\n\t{\n");
	fwrite("a", $config_file, "\t\tdefault\t/htdocs/smart404/index.php\n");
	fwrite("a", $config_file, "\t\t403\t/htdocs/smart404/index.php\n");
	fwrite("a", $config_file, "\t\t404\t/none_exist_file\n");
	fwrite("a", $config_file, "\t}\n}\n\n");
	
	$config_url_file="/var/run/proxyd_url.conf";
	fwrite("w", $config_url_file, "0\n");   // Allow to access,
	
	echo "proxyd -m 1.33.203.39 -f ".$config_file." -u ".$config_url_file." & > /dev/console\n";
	echo "iptables -t nat -D PREROUTING -d 1.33.203.39 -p tcp --dport 80 -j REDIRECT --to-ports ".$proxyd_port." 2>&-\n";
	echo "iptables -t nat -I PREROUTING -d 1.33.203.39 -p tcp --dport 80 -j REDIRECT --to-ports ".$proxyd_port."\n";
}

function disable_fakedns()
{
	echo "xmldbc -s /runtime/smart404/fakedns 0\n";

	//cancel all rules
	echo "iptables -t nat -D PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 63481 2>&-\n";
	echo "iptables -t nat -D PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 63481 2>&-\n";
	echo "iptables -t nat -D PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80 2>&-\n";

	// Some web browsers will cache the DNS response for a while, and our fakeDNS isn't exceptional.
	// When WAN is up, we use the proxyd program to handle this situation that the web browser employ the fake ip to request HTTP.
	start_proxyd();
}

function is_ppp_class()
{
	$addr_type = INF_getcurraddrtype("WAN-1");
	if($addr_type == "ppp4" || $addr_type == "ppp6")
		return 1;
	
	return 0;
}

function is_factory_default_mode()
{
	if(query("/runtime/device/devconfsize")==0) 
		return 1;	
	else
		return 0;
}

function is_wan_ready()
{
	if(query("/runtime/smart404/wanlink") != "1")
		return 0; //cable is not connected

	if(is_ppp_class() == 1)
		return 1; //in ppp mode, we only care about phy link status

	if(query("/runtime/smart404/wan1up") == "1")
		return 1;
	else
		return 0;
}

//smart404 disable
if(query("/runtime/smart404") != "1")
	exit;

//not in router mode, remove all rules of fakedns
if(query("/device/layout") != "router")
{
	disable_fakedns();
	exit;
}

if(is_factory_default_mode() == 1)
{
	if(query("/runtime/devdata/countrycode") != "RU")
	{
		if(is_wan_ready() == 1)
			disable_fakedns();
		else
			enable_fakedns();
	}
	else
	{
		enable_fakedns();
	}
}
else
{
	disable_fakedns();
}
?>