<module>
	<service><?=$GETCFG_SVC?></service>
		<device>
			<qos>
				<?echo dump(3, "/device/qos");?>
			</qos>
		</device>
		<inf>
<?
		include "/htdocs/phplib/xnode.php";
		$wan = query("/runtime/device/activewan");
		if($wan == "") $wan = "WAN-1";
		$infp = XNODE_getpathbytarget("", "inf", "uid", $wan, 0);
		if($infp!="") echo dump(2, $infp);
?>
		</inf>
		<runtime>
			<device>
				<layout><? echo dump(0, "/runtime/device/layout");?></layout>
				<curwan><?echo $wan;?></curwan>
				<activewan><?echo $wan;?></activewan>
				<qos>
					<bwup><? echo dump(0, "/runtime/device/qos/bwup");?></bwup>
				</qos>
			</device>
		</runtime>
</module>
