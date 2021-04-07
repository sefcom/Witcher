<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("a", $START, "echo \"Start IPSEC service ..\"  > /dev/console\n");
fwrite("w", $STOP,  "#!/bin/sh\n");
fwrite("a", $STOP, "echo \"Stop IPSEC service ..\"  > /dev/console\n");

$ipsec_secrets = "/var/etc/ipsec.secrets";
$ipsec_conf = "/var/etc/ipsec.conf";
$ipsec_dir = "/var/etc/ipsec.d";
$xl2tpd_conf = "/var/etc/xl2tpd.conf";
$options_xl2tpd = "/var/etc/ppp/options.xl2tpd";
$chap_secrets = "/var/etc/ppp/chap-secrets";
$pap_secrets = "/var/etc/ppp/pap-secrets";
$l2tp_auth = get("", "/vpn/ipsec/auth");
$l2tp_mppe = get("", "/vpn/ipsec/mppe");

if(get("", "/vpn/ipsec/enable") == "1") {
	if ($l2tp_auth == "PAP") {
		//Create /var/etc/ppp/pap-secrets
		fwrite("w", $pap_secrets, get("", "/vpn/ipsec/username")." xl2tpd ".get("", "/vpn/ipsec/password")." *\n");
	} else {
		//Create /var/etc/ppp/chap-secrets
		fwrite("w", $chap_secrets, get("", "/vpn/ipsec/username")." xl2tpd ".get("", "/vpn/ipsec/password")." *\n");
	}

	//Create /var/etc/ipsec.secrets
	fwrite("w", $ipsec_secrets, "%any %any : PSK \"".get("", "/vpn/ipsec/psk")."\"\n");

	//Disable some kernel options
	fwrite("a", $START, "for each in /proc/sys/net/ipv4/conf/*; do\n");
	fwrite("a", $START, "\techo 0 > \$each/accept_redirects\n");
	fwrite("a", $START, "\techo 0 > \$each/send_redirects\n");
	fwrite("a", $START, "\techo 0 > \$each/rp_filter\n");
	fwrite("a", $START, "done\n");
	fwrite("a", $START, "mkdir -p /var/run/xl2tpd/\n");
	fwrite("a", $START, "mkdir -p /var/etc/ipsec.d/cacerts/\n");
	fwrite("a", $START, "mkdir -p /var/etc/ipsec.d/aacerts/\n");
	fwrite("a", $START, "mkdir -p /var/etc/ipsec.d/ocspcerts/\n");
	fwrite("a", $START, "mkdir -p /var/etc/ipsec.d/crls/\n");

	//Create /var/etc/ppp/options.xl2tpd
	fwrite("w", $options_xl2tpd, "noccp\n");
	fwrite("a", $options_xl2tpd, "nopcomp\n");
	fwrite("a", $options_xl2tpd, "noaccomp\n");
	fwrite("a", $options_xl2tpd, "nobsdcomp\n");
	fwrite("a", $options_xl2tpd, "novj\n");
	fwrite("a", $options_xl2tpd, "novjccomp\n");
	//fwrite("a", $options_xl2tpd, "noipv6\n");
	fwrite("a", $options_xl2tpd, "ipcp-accept-local\n");
	fwrite("a", $options_xl2tpd, "ipcp-accept-remote\n");
	fwrite("a", $options_xl2tpd, "auth\n");
	fwrite("a", $options_xl2tpd, "idle 1800\n");
	fwrite("a", $options_xl2tpd, "crtscts\n");
	fwrite("a", $options_xl2tpd, "mtu 1300\n");
	fwrite("a", $options_xl2tpd, "mru 1300\n");
	fwrite("a", $options_xl2tpd, "nodefaultroute\n");
	fwrite("a", $options_xl2tpd, "proxyarp\n");
	fwrite("a", $options_xl2tpd, "asyncmap 0\n");
	fwrite("a", $options_xl2tpd, "modem\n");
	fwrite("a", $options_xl2tpd, "lcp-echo-interval 120\n");
	fwrite("a", $options_xl2tpd, "lcp-echo-failure 5\n");
	if (get("", "/vpn/ipsec/debug") == 1)
		fwrite("a", $options_xl2tpd, "debug\n");
	fwrite("a", $options_xl2tpd, "connect-delay 3000\n");
	if ($l2tp_auth == "PAP") {
		fwrite("a", $options_xl2tpd, "require-pap\n");
	} else if ($l2tp_auth == "CHAP") {
		fwrite("a", $options_xl2tpd, "require-chap\n");
	} else {
		fwrite("a", $options_xl2tpd, "require-mschap-v2\n");
	}
	if ($l2tp_mppe == "RC4-128") {
		fwrite("a", $options_xl2tpd, "require-mppe-128\n");
	} else if ($l2tp_mppe == "RC4-40") {
		fwrite("a", $options_xl2tpd, "require-mppe-40\n");
	} else {
		fwrite("a", $options_xl2tpd, "nomppe\n");
	}
	$lan_ip = INF_getcurripaddr("LAN-1");
	if ($lan_ip != "")
		fwrite("a", $options_xl2tpd, "ms-dns ".$lan_ip."\n");
	$wan_ip = INF_getcurripaddr("WAN-1");
	$wan_stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
	$wan_addrtype = query($wan_stsp."/inet/addrtype");
	if ($wan_addrtype == "ipv4" || $wan_addrtype == "ppp4") {
		foreach ($wan_stsp."/inet/".$wan_addrtype."/dns") {
			if ($VaLuE != "") {
				fwrite("a", $options_xl2tpd, "ms-dns ".$VaLuE."\n");
			}
		}
	}

	//Create /var/etc/xl2tpd.conf
	fwrite("w", $xl2tpd_conf, "[global]\n");
	fwrite("a", $xl2tpd_conf, "port = 1701\n");
	fwrite("a", $xl2tpd_conf, "access control = no\n");
	fwrite("a", $xl2tpd_conf, "force userspace = yes\n");
	fwrite("a", $xl2tpd_conf, "ipsec saref = yes\n");
	fwrite("a", $xl2tpd_conf, "saref refinfo = 30\n");
	if ($wan_ip != "")
	    fwrite("a", $xl2tpd_conf, "listen-addr = ".$wan_ip."\n");
	if (get("", "/vpn/ipsec/debug") == 1) {
		fwrite("a", $xl2tpd_conf, "debug tunnel = yes\n");
		fwrite("a", $xl2tpd_conf, "debug avp = yes\n");
		fwrite("a", $xl2tpd_conf, "debug packet = yes\n");
		fwrite("a", $xl2tpd_conf, "debug network = yes\n");
		fwrite("a", $xl2tpd_conf, "debug state = yes\n");
	}
	fwrite("a", $xl2tpd_conf, "\n");
	fwrite("a", $xl2tpd_conf, "[lns default]\n");
	fwrite("a", $xl2tpd_conf, "ip range = 192.168.100.2-192.168.100.254\n");
	fwrite("a", $xl2tpd_conf, "local ip = 192.168.100.1\n");
	fwrite("a", $xl2tpd_conf, "refuse chap = yes\n");
	fwrite("a", $xl2tpd_conf, "refuse pap = yes\n");
	fwrite("a", $xl2tpd_conf, "require authentication = yes\n");
	fwrite("a", $xl2tpd_conf, "name = xl2tpd\n");
	fwrite("a", $xl2tpd_conf, "length bit = yes\n");
	fwrite("a", $xl2tpd_conf, "pppoptfile = /etc/ppp/options.xl2tpd\n");
	if (get("", "/vpn/ipsec/debug") == 1)
		fwrite("a", $xl2tpd_conf, "ppp debug = yes\n");

	//Create /var/etc/ipsec.conf
	fwrite("w", $ipsec_conf, "version 2.0\n");
	fwrite("a", $ipsec_conf, "config setup\n");
	if ($wan_ip != "")
	    fwrite("a", $ipsec_conf, "\tlisten=".$wan_ip."\n");
	fwrite("a", $ipsec_conf, "\tnat_traversal=yes\n");
	fwrite("a", $ipsec_conf, "\tvirtual_private=%v4:10.0.0.0/8,%v4:192.168.0.0/16,%v4:172.16.0.0/12,%v4:25.0.0.0/8,%v6:fd00::/8,%v6:fe80::/10\n");
	fwrite("a", $ipsec_conf, "\toe=off\n");
	fwrite("a", $ipsec_conf, "\tprotostack=mast\n");
	fwrite("a", $ipsec_conf, "\tplutostderrlog=/dev/console\n");
	if (get("", "/vpn/ipsec/debug") == 1) {
		fwrite("a", $ipsec_conf, "\tplutodebug=all\n");
		fwrite("a", $ipsec_conf, "\tklipsdebug=all\n");
	}
	fwrite("a", $ipsec_conf, "\n");
	fwrite("a", $ipsec_conf, "conn l2tp-psk-nat\n");
	fwrite("a", $ipsec_conf, "\trightsubnet=vhost:%priv\n");
	fwrite("a", $ipsec_conf, "\talso=l2tp-psk-nonat\n");
	fwrite("a", $ipsec_conf, "\n");
	fwrite("a", $ipsec_conf, "conn l2tp-psk-nonat\n");
	fwrite("a", $ipsec_conf, "\tauthby=secret\n");
	if(get("x", "/runtime/devdata/countrycode") == "RU") {
		fwrite("a", $ipsec_conf, "\tike=des!\n");
		fwrite("a", $ipsec_conf, "\tphase2alg=des!\n");
	}
	fwrite("a", $ipsec_conf, "\tpfs=no\n");
	fwrite("a", $ipsec_conf, "\tauto=add\n");
	fwrite("a", $ipsec_conf, "\trekey=no\n");
	fwrite("a", $ipsec_conf, "\ttype=transport\n");
//	fwrite("a", $ipsec_conf, "\tauth=esp\n");
	fwrite("a", $ipsec_conf, "\taggrmode=no\n");
	fwrite("a", $ipsec_conf, "\tkeyingtries=5\n");
	fwrite("a", $ipsec_conf, "\tcompress=yes\n");
	fwrite("a", $ipsec_conf, "\tleft=%defaultroute\n");
	fwrite("a", $ipsec_conf, "\tleftprotoport=17/%any\n");
	fwrite("a", $ipsec_conf, "\tright=%any\n");
	fwrite("a", $ipsec_conf, "\tikelifetime=8h\n");
	fwrite("a", $ipsec_conf, "\tkeylife=1h\n");
	fwrite("a", $ipsec_conf, "\trightprotoport=17/%any\n");
	fwrite("a", $ipsec_conf, "\t#forceencaps=yes\n");
	fwrite("a", $ipsec_conf, "\tdpddelay=40\n");
	fwrite("a", $ipsec_conf, "\tdpdtimeout=130\n");
	fwrite("a", $ipsec_conf, "\tdpdaction=clear\n");
	fwrite("a", $ipsec_conf, "\toverlapip=yes\n");
	fwrite("a", $ipsec_conf, "\tsareftrack=yes\n");

	fwrite("a", $START, "xl2tpd -D -c ".$xl2tpd_conf." &\n");
	fwrite("a", $STOP, "killall xl2tpd\n");

	// disable BCM_NAT
	fwrite("a", $START, "echo 0 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat\n");
	fwrite("a", $START, "echo 0 > /proc/alpha_fast_route\n");

	// iptables rules
	//fwrite("a", $START, "iptables -t nat -A PRE.VPN -p udp -m policy --dir in --pol ipsec -m udp --dport 1701 -j ACCEPT\n");
	fwrite("a", $START, "iptables -t nat -A PRE.VPN -p udp --dport 1701 -j DROP\n");
	fwrite("a", $START, "iptables -t nat -A PRE.VPN -p udp --dport 500 -j ACCEPT \n");
	fwrite("a", $START, "iptables -t nat -A PRE.VPN -p udp --dport 4500 -j ACCEPT \n");
	//fwrite("a",$START, "iptables -t nat -A PRE.VPN -p AH -j ACCEPT\n");
	fwrite("a", $START, "iptables -t nat -A PRE.VPN -p ESP -j ACCEPT\n");

	//fwrite("a", $STOP, "iptables -t nat -D PRE.VPN -p udp -m policy --dir in --pol ipsec -m udp --dport 1701 -j ACCEPT\n");
	fwrite("a", $STOP, "iptables -t nat -D PRE.VPN -p udp --dport 1701 -j DROP\n");
	fwrite("a", $STOP, "iptables -t nat -D PRE.VPN -p udp --dport 500 -j ACCEPT \n");
	fwrite("a", $STOP, "iptables -t nat -D PRE.VPN -p udp --dport 4500 -j ACCEPT \n");
	//fwrite("a",$STOP, "iptables -t nat -D PRE.VPN -p AH -j ACCEPT\n");
	fwrite("a", $STOP, "iptables -t nat -D PRE.VPN -p ESP -j ACCEPT\n");

	// ipsec daemon
	fwrite("a", $START, "ipsec setup start\n");
	fwrite("a", $STOP, "ipsec setup stop\n");
}

fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP, "exit 0\n");
?>
