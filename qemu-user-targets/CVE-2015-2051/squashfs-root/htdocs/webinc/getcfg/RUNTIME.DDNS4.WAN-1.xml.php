<module>
	<service>RUNTIME.DDNS4.WAN-1</service>
	<runtime>
		<inf>
			<ddns4>
<?
include "/htdocs/phplib/xnode.php";
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
if ($stsp != "") echo dump(4, $stsp."/ddns4");

?>			</ddns4>
		</inf>
	</runtime>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>
