-- Dumping structure for table DATABASE_NAME.nodes
SET @dbname = DATABASE();
SET @tablename = "settings";
SET @columnname = "passwordEncryption";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " INT(11) DEFAULT '0';")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
ALTER TABLE settings
ADD COLUMN `ldap_host` varchar(255) NOT NULL,
ADD COLUMN `ldap_dn` varchar(255) NOT NULL,
ADD COLUMN `ldap_user_group` varchar(255) NOT NULL,
ADD COLUMN `ldap_admin_group` varchar(255) NOT NULL,
ADD COLUMN `ldap_usr_dom` varchar(255) NOT NULL;
INSERT INTO `settings` (`commandDebug`, `pageTimeout`, `passwordEncryption`) VALUES
	(0, 600, 0);
-- Dumping structure for table DATABASE_NAME.nodes
ALTER TABLE nodes
ADD COLUMN `deviceEnablePrompt` varchar(255) NOT NULL;
ALTER TABLE nodes
Add COLUMN `templateId` INT NULL DEFAULT '1' AFTER `nodeCatId`;

-- Dumping structure for table DATABASE_NAME.menuPages
DROP TABLE IF EXISTS `menuPages`;
CREATE TABLE IF NOT EXISTS `menuPages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(50) NOT NULL DEFAULT '0',
  `breadcrumbText` varchar(100) NOT NULL DEFAULT '0',
  `annoucementText` varchar(100) NOT NULL DEFAULT '0',
  `menuName` varchar(100) NOT NULL DEFAULT '0',
  `topLevel` varchar(100) NOT NULL DEFAULT '0',
  `parentId` int(11) NOT NULL DEFAULT '0',
  `menuSortId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.menuPages: 24 rows
DELETE FROM `menuPages`;
/*!40000 ALTER TABLE `menuPages` DISABLE KEYS */;
INSERT INTO `menuPages` (`id`, `pageName`, `breadcrumbText`, `annoucementText`, `menuName`, `topLevel`, `parentId`, `menuSortId`) VALUES
	(1, 'login.php', 'Login Page', 'Enter Username & Password to login', 'Login', '2', 1, 0),
	(2, 'compatibility.php', 'Compatibility Page', 'rConfig Compatibility Details', 'Compatibility', '2', 0, 0),
	(3, 'dashboard.php', 'Dashboard', 'View rConfig Server and Device Status on this page', 'Home', '1', 3, 1),
	(4, 'devices.php', 'Devices', 'View/Edit Devices on this page', 'Devices', '1', 4, 2),
	(5, 'devicemgmt.php', 'Devices > Device Management', 'Manage devices on this page', 'Device Management', '0', 5, 0),
	(7, 'customProperties.php', 'Devices > Custom Properties', 'Update Custom Properties on this page', 'Custom Properties', '0', 4, 0),
	(8, 'categories.php', 'Devices > Categories', 'Update Categories on this page', 'Categories', '0', 4, 0),
	(9, 'commands.php', 'Devices > Commands', 'Update Commands on this page', 'Commands', '0', 4, 0),
	(10, 'vendors.php', 'Devices > Vendors', 'Update Vendor details on this page', 'Vendors', '0', 4, 0),
	(11, 'configoverview.php', 'Configuration Tools > Overview', 'Configurations Overview', 'Configuration Tools', '1', 10, 4),
	(12, 'configcompare.php', 'Configuration Tools > Comparison', 'Configurations Comparison', 'Compare', '0', 10, 0),
	(13, 'search.php', 'Configuration Tools > Search', 'Search Configurations', 'Config Search', '0', 10, 0),
	(14, 'snippets.php', 'Configuration Tools > Config Snippets', 'Configuration Snippets', 'Config Snippets', '0', 10, 0),
	(15, 'configreports.php', 'Configuration Tools > Reports', 'Reports', 'Reports', '0', 10, 0),
	(16, 'configlogging.php', 'Configuration Tools > Logging Information', 'Logging files and archives', 'Logs', '0', 10, 0),
	(17, 'complianceoverview.php', 'Compliance > Overview', 'Configuration Compliance Management Overview', 'Compliance', '1', 16, 5),
	(18, 'compliancereports.php', 'Compliance > Reports', 'Configuration Compliance Reports', 'Reports', '0', 16, 0),
	(19, 'compliancepolicies.php', 'Compliance > Policies', 'Configuration Compliance Policies', 'Policies', '0', 16, 0),
	(20, 'compliancepolicyelements.php', 'Compliance > Policy Elements', 'Configuration Compliance Policy Elements', 'Policy Elements', '0', 16, 0),
	(21, 'settings.php', 'Settings > General Settings', 'Change general systems settings on this page', 'Settings', '1', 20, 6),
	(22, 'scheduler.php', 'Scheduled Tasks', 'Manage Scheduled Tasks on this page', 'Scheduled Tasks', '1', 21, 3),
	(23, 'useradmin.php', 'Settings > Users Management', 'Manage User details on this page', 'Users (Admin)', '0', 20, 0),
	(24, 'settingsBackup.php', 'Settings > Backup', 'Backup rConfig on this page', 'System Backup(Admin)', '0', 20, 0),
	(25, 'updater.php', 'Update', 'Update rConfig on this page', 'Updater', '2', 24, 0),
	(6, 'deviceConnTemplates.php', 'Devices > Device Connection Templates', 'Manage devices connection templates on this page', 'Connection Templates', '0', 4, 0);
/*!40000 ALTER TABLE `menuPages` ENABLE KEYS */;


-- Dumping structure for table DATABASE_NAME.templates
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `dateAdded` date DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `dateLastEdit` date DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` (`id`, `fileName`, `name`, `desc`, `dateAdded`, `addedby`, `dateLastEdit`, `status`) VALUES
	(1, '/home/rconfig/templates/ios-telnet-noenable.yml', 'Cisco IOS - TELNET - No Enable', 'Cisco IOS TELNET based connection without enable mode', '2017-08-18', 'admin', NULL, 1),
	(2, '/home/rconfig/templates/ios-telnet-enable.yml', 'Cisco IOS - TELNET - Enable', 'Cisco IOS TELNET based connection with enable mode', '2017-08-18', 'admin', NULL, 1),
	(3, '/home/rconfig/templates/ios-ssh-noenable.yml', 'Cisco IOS - SSH - No Enable', 'Cisco IOS SSH based connection without enable mode', '2017-08-18', 'admin', NULL, 1),
	(4, '/home/rconfig/templates/ios-ssh-enable.yml', 'Cisco IOS - SSH - Enable', 'Cisco IOS SSH based connection with enable mode', '2017-08-18', 'admin', NULL, 1);
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;