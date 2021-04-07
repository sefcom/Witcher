<module>
	<service><?=$GETCFG_SVC?></service>
	<acl>
		<dos>
<?			echo dump(3, "/acl/dos");
?>		</dos>
		<spi>
<?			echo dump(3, "/acl/spi");
?>		</spi>
		<applications>
<?			echo dump(3, "/acl/applications");
?>		</applications>
	</acl>
</module>
