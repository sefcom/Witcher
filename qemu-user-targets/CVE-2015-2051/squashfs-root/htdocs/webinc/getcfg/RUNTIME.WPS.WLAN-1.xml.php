<?
/* Notice:
 *   This service, RUNTIME.WPS.WLAN-1, is created before the php built-in function,
 *   'event' and 'service', are impletemented.
 *
 *   So DON'T use this service in the new impletementation any more.
 *   Please use the php built-in function, 'event', instead.
 *
 *	 You can use this getcfg file to get config only.
 *	 Don't use its fatlay, setcfg, and service file to make WPS PIN and PBC
 *	 take effect.
 *
 *                       Joan Wang <joan_wang@alphanetworks.com>
 */
?><module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<phyinf>
<?
			include "/htdocs/phplib/xnode.php";
			$inf = XNODE_getpathbytarget("/runtime", "phyinf", "uid", cut($GETCFG_SVC,2,"."), 0);
			echo dump(3, $inf);
?>		</phyinf>
	</runtime>
</module>
