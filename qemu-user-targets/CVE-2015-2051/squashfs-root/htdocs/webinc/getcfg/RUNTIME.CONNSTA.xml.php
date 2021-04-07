<module>
	<service>RUNTIME.CONNSTA</service>
	<runtime>
		<media>
			<connstatus>
<?
include "/htdocs/phplib/xnode.php";
$stsp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "WLAN-1", 0);
if ($stsp != "") echo dump(3, $stsp."/media/connstatus");
?>			</connstatus>
		<media>
	</runtime>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
