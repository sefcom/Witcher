<module>
	<service><?=$GETCFG_SVC?></service>
	<device>
		<diagnostic>
			<chkconn>
				<host>
<?					echo dump(3, "device/diagnostic/chkconn/host");
?>				</host>
			</chkconn>
		</diagnostic>
	</device>
</module>
