-- MySQL dump 10.13  Distrib 5.6.37, for Linux (x86_64)
--
-- Host: localhost    Database: rconfig38
-- ------------------------------------------------------
-- Server version	5.6.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `active_guests`
--

DROP TABLE IF EXISTS `active_guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `active_guests` (
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `active_guests`
--

LOCK TABLES `active_guests` WRITE;
/*!40000 ALTER TABLE `active_guests` DISABLE KEYS */;
/*!40000 ALTER TABLE `active_guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `active_users`
--

DROP TABLE IF EXISTS `active_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `active_users` (
  `username` varchar(30) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `active_users`
--

LOCK TABLES `active_users` WRITE;
/*!40000 ALTER TABLE `active_users` DISABLE KEYS */;
INSERT INTO `active_users` VALUES ('admin',1507333221);
/*!40000 ALTER TABLE `active_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) DEFAULT '0',
  `status` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Switches',1),(2,'Routers',1),(4,'LoadBalancers',1),(5,'WANOptimizers',1),(8,'Firewalls',1),(9,'RouteServers',2),(10,'routeserverbelwue',2),(11,'telusRouteServer',2),(12,'GBLX',2),(13,'IsCoZa',2),(14,'opentransit',2),(15,'ColtRouteServers',2);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmdCatTbl`
--

DROP TABLE IF EXISTS `cmdCatTbl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmdCatTbl` (
  `configCmdId` int(10) DEFAULT NULL,
  `nodeCatId` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmdCatTbl`
--

LOCK TABLES `cmdCatTbl` WRITE;
/*!40000 ALTER TABLE `cmdCatTbl` DISABLE KEYS */;
INSERT INTO `cmdCatTbl` VALUES (161,1),(161,2),(161,4),(161,5),(161,8),(162,2),(163,1),(163,2),(164,1),(164,2),(165,2);
/*!40000 ALTER TABLE `cmdCatTbl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliancePolElem`
--

DROP TABLE IF EXISTS `compliancePolElem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compliancePolElem` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `elementName` varchar(255) NOT NULL,
  `elementDesc` varchar(255) NOT NULL,
  `singleParam1` int(10) DEFAULT NULL COMMENT '1, equals. 2, contains',
  `singleLine1` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliancePolElem`
--

LOCK TABLES `compliancePolElem` WRITE;
/*!40000 ALTER TABLE `compliancePolElem` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliancePolElem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliancePolElemTbl`
--

DROP TABLE IF EXISTS `compliancePolElemTbl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compliancePolElemTbl` (
  `polId` int(10) DEFAULT NULL,
  `elemId` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliancePolElemTbl`
--

LOCK TABLES `compliancePolElemTbl` WRITE;
/*!40000 ALTER TABLE `compliancePolElemTbl` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliancePolElemTbl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliancePolicies`
--

DROP TABLE IF EXISTS `compliancePolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compliancePolicies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `policyName` varchar(255) DEFAULT NULL,
  `policyDesc` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliancePolicies`
--

LOCK TABLES `compliancePolicies` WRITE;
/*!40000 ALTER TABLE `compliancePolicies` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliancePolicies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complianceReportPolTbl`
--

DROP TABLE IF EXISTS `complianceReportPolTbl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complianceReportPolTbl` (
  `reportId` int(10) DEFAULT NULL,
  `polId` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complianceReportPolTbl`
--

LOCK TABLES `complianceReportPolTbl` WRITE;
/*!40000 ALTER TABLE `complianceReportPolTbl` DISABLE KEYS */;
/*!40000 ALTER TABLE `complianceReportPolTbl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complianceReports`
--

DROP TABLE IF EXISTS `complianceReports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complianceReports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reportsName` varchar(255) DEFAULT NULL,
  `reportsDesc` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complianceReports`
--

LOCK TABLES `complianceReports` WRITE;
/*!40000 ALTER TABLE `complianceReports` DISABLE KEYS */;
/*!40000 ALTER TABLE `complianceReports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configcommands`
--

DROP TABLE IF EXISTS `configcommands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configcommands` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `command` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configcommands`
--

LOCK TABLES `configcommands` WRITE;
/*!40000 ALTER TABLE `configcommands` DISABLE KEYS */;
INSERT INTO `configcommands` VALUES (161,'show startup-config',1),(162,'show ip route',1),(163,'show cdp neigh',1),(164,'show ip access-list',1),(165,'show ip route',1),(166,'show ip bgp',2),(167,'sh ip route connected',2),(168,'show route',2),(169,'show rom-monitor ',2),(170,'show ip route',2),(171,'sh ip bgp nexthops ',2),(172,'sh ip bgp update-sources',2),(173,'sh bgp ipv4 unicast regexp 43076',2),(174,'show ip bgp summary',2),(175,'sh ip route connected ',2),(176,'show ip route static',2);
/*!40000 ALTER TABLE `configcommands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configs`
--

DROP TABLE IF EXISTS `configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `deviceId` int(10) DEFAULT NULL,
  `configLocation` varchar(255) DEFAULT NULL,
  `configFilename` varchar(255) DEFAULT NULL,
  `configDate` date DEFAULT NULL,
  `configTime` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1018 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs`
--

LOCK TABLES `configs` WRITE;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` VALUES (216,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1545.txt','2017-10-21','15:45:46'),(217,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1545.txt','2017-10-21','15:45:51'),(218,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1545.txt','2017-10-21','15:45:51'),(219,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showipbgpsummary-1546.txt','2017-10-21','15:46:29'),(220,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','shiprouteconnected-1546.txt','2017-10-21','15:46:39'),(221,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showiproutestatic-1546.txt','2017-10-21','15:46:49'),(222,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showipbgpsummary-1548.txt','2017-10-21','15:49:07'),(223,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1551.txt','2017-10-21','15:51:40'),(224,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1551.txt','2017-10-21','15:51:45'),(225,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1551.txt','2017-10-21','15:51:45'),(226,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/21','shbgpipv4unicastregexp43076-1556.txt','2017-10-21','15:56:58'),(227,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1557.txt','2017-10-21','15:57:04'),(228,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1557.txt','2017-10-21','15:57:04'),(229,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1557.txt','2017-10-21','15:57:09'),(230,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1557.txt','2017-10-21','15:57:28'),(231,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1557.txt','2017-10-21','15:57:29'),(232,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1557.txt','2017-10-21','15:57:34'),(233,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/21','shbgpipv4unicastregexp43076-1557.txt','2017-10-21','15:57:39'),(234,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showipbgpsummary-1557.txt','2017-10-21','15:57:49'),(235,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','shiprouteconnected-1557.txt','2017-10-21','15:57:50'),(236,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showiproutestatic-1557.txt','2017-10-21','15:57:52'),(237,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1558.txt','2017-10-21','15:58:43'),(238,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1558.txt','2017-10-21','15:58:43'),(239,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1558.txt','2017-10-21','15:58:48'),(240,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1600.txt','2017-10-21','16:00:32'),(241,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1600.txt','2017-10-21','16:00:33'),(242,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1600.txt','2017-10-21','16:00:39'),(243,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1601.txt','2017-10-21','16:01:16'),(244,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1602.txt','2017-10-21','16:02:58'),(245,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1602.txt','2017-10-21','16:02:58'),(246,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1602.txt','2017-10-21','16:03:03'),(247,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1603.txt','2017-10-21','16:03:18'),(248,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1603.txt','2017-10-21','16:03:23'),(249,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1603.txt','2017-10-21','16:03:24'),(250,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1604.txt','2017-10-21','16:04:00'),(251,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1604.txt','2017-10-21','16:04:01'),(252,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1604.txt','2017-10-21','16:04:15'),(253,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1604.txt','2017-10-21','16:04:20'),(254,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1604.txt','2017-10-21','16:04:21'),(255,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','showrom-monitor-1604.txt','2017-10-21','16:04:43'),(256,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpnexthops-1604.txt','2017-10-21','16:04:48'),(257,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/21','shipbgpupdate-sources-1604.txt','2017-10-21','16:04:48'),(258,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showipbgpsummary-1605.txt','2017-10-21','16:05:25'),(259,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','shiprouteconnected-1605.txt','2017-10-21','16:05:26'),(260,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/21','showiproutestatic-1605.txt','2017-10-21','16:05:28'),(261,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/22','showrom-monitor-000.txt','2017-10-22','00:00:03'),(262,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/22','shipbgpnexthops-000.txt','2017-10-22','00:00:04'),(263,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/22','shipbgpupdate-sources-000.txt','2017-10-22','00:00:09'),(264,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/22','shbgpipv4unicastregexp43076-005.txt','2017-10-22','00:05:04'),(265,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/22','showipbgpsummary-100.txt','2017-10-22','01:00:04'),(266,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/22','shiprouteconnected-100.txt','2017-10-22','01:00:08'),(267,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/22','showiproutestatic-100.txt','2017-10-22','01:00:13'),(268,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/23','showrom-monitor-000.txt','2017-10-23','00:00:03'),(269,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/23','shipbgpnexthops-000.txt','2017-10-23','00:00:03'),(270,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/23','shipbgpupdate-sources-000.txt','2017-10-23','00:00:09'),(271,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/23','shbgpipv4unicastregexp43076-005.txt','2017-10-23','00:05:04'),(272,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/23','showipbgpsummary-100.txt','2017-10-23','01:00:07'),(273,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/23','shiprouteconnected-100.txt','2017-10-23','01:00:12'),(274,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/23','showiproutestatic-100.txt','2017-10-23','01:00:17'),(275,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/24','showrom-monitor-000.txt','2017-10-24','00:00:03'),(276,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/24','shipbgpnexthops-000.txt','2017-10-24','00:00:05'),(277,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/24','shipbgpupdate-sources-000.txt','2017-10-24','00:00:10'),(278,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/24','shbgpipv4unicastregexp43076-005.txt','2017-10-24','00:05:04'),(279,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/24','showipbgpsummary-100.txt','2017-10-24','01:00:04'),(280,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/24','shiprouteconnected-100.txt','2017-10-24','01:00:06'),(281,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/24','showiproutestatic-100.txt','2017-10-24','01:00:11'),(282,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/25','showrom-monitor-000.txt','2017-10-25','00:00:03'),(283,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/25','shipbgpnexthops-000.txt','2017-10-25','00:00:03'),(284,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/25','shipbgpupdate-sources-000.txt','2017-10-25','00:00:09'),(285,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/25','shbgpipv4unicastregexp43076-005.txt','2017-10-25','00:05:04'),(286,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/25','showipbgpsummary-100.txt','2017-10-25','01:00:04'),(287,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/25','shiprouteconnected-100.txt','2017-10-25','01:00:08'),(288,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/25','showiproutestatic-100.txt','2017-10-25','01:00:13'),(289,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/26','showrom-monitor-000.txt','2017-10-26','00:00:02'),(290,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/26','shipbgpnexthops-000.txt','2017-10-26','00:00:03'),(291,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/26','shipbgpupdate-sources-000.txt','2017-10-26','00:00:08'),(292,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/26','shbgpipv4unicastregexp43076-005.txt','2017-10-26','00:05:04'),(293,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/26','showipbgpsummary-100.txt','2017-10-26','01:00:04'),(294,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/26','shiprouteconnected-100.txt','2017-10-26','01:00:08'),(295,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/26','showiproutestatic-100.txt','2017-10-26','01:00:13'),(296,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/27','showrom-monitor-000.txt','2017-10-27','00:00:03'),(297,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/27','shipbgpnexthops-000.txt','2017-10-27','00:00:04'),(298,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/27','shipbgpupdate-sources-000.txt','2017-10-27','00:00:10'),(299,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/27','shbgpipv4unicastregexp43076-005.txt','2017-10-27','00:05:04'),(300,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/27','showipbgpsummary-100.txt','2017-10-27','01:00:04'),(301,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/27','shiprouteconnected-100.txt','2017-10-27','01:00:06'),(302,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/27','showiproutestatic-100.txt','2017-10-27','01:00:07'),(303,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/28','showrom-monitor-000.txt','2017-10-28','00:00:03'),(304,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/28','shipbgpnexthops-000.txt','2017-10-28','00:00:04'),(305,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/28','shipbgpupdate-sources-000.txt','2017-10-28','00:00:10'),(306,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/28','shbgpipv4unicastregexp43076-005.txt','2017-10-28','00:05:03'),(307,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/28','showipbgpsummary-100.txt','2017-10-28','01:00:06'),(308,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/28','shiprouteconnected-100.txt','2017-10-28','01:00:07'),(309,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/28','showiproutestatic-100.txt','2017-10-28','01:00:08'),(310,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/29','showrom-monitor-000.txt','2017-10-29','00:00:02'),(311,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/29','shipbgpnexthops-000.txt','2017-10-29','00:00:03'),(312,8,'/home/rconfig/data/GBLX/route-server-gblx-net/2017/Oct/29','shipbgpupdate-sources-000.txt','2017-10-29','00:00:09'),(313,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/29','shbgpipv4unicastregexp43076-005.txt','2017-10-29','00:05:04'),(314,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/29','showipbgpsummary-100.txt','2017-10-29','01:00:05'),(315,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/29','shiprouteconnected-100.txt','2017-10-29','01:00:05'),(316,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/29','showiproutestatic-100.txt','2017-10-29','01:00:05'),(317,9,'/home/rconfig/data/IsCoZa/public-route-server-is-co-za/2017/Oct/30','shbgpipv4unicastregexp43076-005.txt','2017-10-30','00:05:03'),(318,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/30','showipbgpsummary-100.txt','2017-10-30','01:00:04'),(319,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/30','shiprouteconnected-100.txt','2017-10-30','01:00:05'),(320,10,'/home/rconfig/data/opentransit/route-server-opentransit-net/2017/Oct/30','showiproutestatic-100.txt','2017-10-30','01:00:05'),(321,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showstartup-config-1152.txt','2017-10-30','11:52:45'),(322,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showiproute-1152.txt','2017-10-30','11:52:47'),(323,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showcdpneigh-1152.txt','2017-10-30','11:52:50'),(324,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showipaccess-list-1152.txt','2017-10-30','11:52:52'),(325,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showiproute-1152.txt','2017-10-30','11:52:55'),(326,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1155.txt','2017-10-30','11:55:15'),(327,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1155.txt','2017-10-30','11:55:25'),(328,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1156.txt','2017-10-30','11:56:57'),(329,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1156.txt','2017-10-30','11:57:02'),(330,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1211.txt','2017-10-30','12:11:51'),(331,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1211.txt','2017-10-30','12:11:51'),(332,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1211.txt','2017-10-30','12:11:51'),(333,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1211.txt','2017-10-30','12:11:51'),(334,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1211.txt','2017-10-30','12:11:51'),(335,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1211.txt','2017-10-30','12:11:57'),(336,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1211.txt','2017-10-30','12:11:57'),(337,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1211.txt','2017-10-30','12:11:57'),(338,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1211.txt','2017-10-30','12:11:57'),(339,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1211.txt','2017-10-30','12:11:57'),(340,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1225.txt','2017-10-30','12:25:32'),(341,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1225.txt','2017-10-30','12:25:33'),(342,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1225.txt','2017-10-30','12:25:33'),(343,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1225.txt','2017-10-30','12:25:33'),(344,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1225.txt','2017-10-30','12:25:33'),(345,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showstartup-config-1225.txt','2017-10-30','12:25:36'),(346,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showiproute-1225.txt','2017-10-30','12:25:38'),(347,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showcdpneigh-1225.txt','2017-10-30','12:25:41'),(348,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showipaccess-list-1225.txt','2017-10-30','12:25:43'),(349,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showiproute-1225.txt','2017-10-30','12:25:46'),(350,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1225.txt','2017-10-30','12:25:59'),(351,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1225.txt','2017-10-30','12:25:59'),(352,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1225.txt','2017-10-30','12:26:00'),(353,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1226.txt','2017-10-30','12:26:00'),(354,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1226.txt','2017-10-30','12:26:00'),(355,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showstartup-config-1226.txt','2017-10-30','12:26:03'),(356,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showiproute-1226.txt','2017-10-30','12:26:05'),(357,12,'/home/rconfig/data/Routers/router2/2017/Oct/30','showcdpneigh-1226.txt','2017-10-30','12:26:08'),(358,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1230.txt','2017-10-30','12:30:07'),(359,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1230.txt','2017-10-30','12:30:07'),(360,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1230.txt','2017-10-30','12:30:07'),(361,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1230.txt','2017-10-30','12:30:07'),(362,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1230.txt','2017-10-30','12:30:07'),(363,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showstartup-config-1230.txt','2017-10-30','12:30:18'),(364,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1230.txt','2017-10-30','12:30:18'),(365,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showcdpneigh-1230.txt','2017-10-30','12:30:19'),(366,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showipaccess-list-1230.txt','2017-10-30','12:30:19'),(367,11,'/home/rconfig/data/Routers/router/2017/Oct/30','showiproute-1230.txt','2017-10-30','12:30:19'),(368,11,'/home/rconfig/data/Routers/router/2017/Oct/31','showstartup-config-000.txt','2017-10-31','00:00:06'),(369,11,'/home/rconfig/data/Routers/router/2017/Oct/31','showiproute-000.txt','2017-10-31','00:00:06'),(370,11,'/home/rconfig/data/Routers/router/2017/Oct/31','showcdpneigh-000.txt','2017-10-31','00:00:07'),(371,11,'/home/rconfig/data/Routers/router/2017/Oct/31','showipaccess-list-000.txt','2017-10-31','00:00:07'),(372,11,'/home/rconfig/data/Routers/router/2017/Oct/31','showiproute-000.txt','2017-10-31','00:00:07'),(373,12,'/home/rconfig/data/Routers/router2/2017/Oct/31','showstartup-config-000.txt','2017-10-31','00:00:10'),(374,12,'/home/rconfig/data/Routers/router2/2017/Oct/31','showiproute-000.txt','2017-10-31','00:00:12'),(375,12,'/home/rconfig/data/Routers/router2/2017/Oct/31','showcdpneigh-000.txt','2017-10-31','00:00:15'),(376,12,'/home/rconfig/data/Routers/router2/2017/Oct/31','showipaccess-list-000.txt','2017-10-31','00:00:17'),(377,12,'/home/rconfig/data/Routers/router2/2017/Oct/31','showiproute-000.txt','2017-10-31','00:00:20'),(378,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showstartup-config-000.txt','2017-09-01','00:00:06'),(379,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showiproute-000.txt','2017-09-01','00:00:06'),(380,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showcdpneigh-000.txt','2017-09-01','00:00:06'),(381,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showipaccess-list-000.txt','2017-09-01','00:00:06'),(382,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showiproute-000.txt','2017-09-01','00:00:06'),(383,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showstartup-config-000.txt','2017-09-01','00:00:09'),(384,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showiproute-000.txt','2017-09-01','00:00:12'),(385,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showcdpneigh-000.txt','2017-09-01','00:00:14'),(386,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showipaccess-list-000.txt','2017-09-01','00:00:17'),(387,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showiproute-000.txt','2017-09-01','00:00:19'),(388,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showstartup-config-002.txt','2017-09-01','00:02:02'),(389,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showiproute-002.txt','2017-09-01','00:02:02'),(390,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showcdpneigh-002.txt','2017-09-01','00:02:02'),(391,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showipaccess-list-002.txt','2017-09-01','00:02:02'),(392,11,'/home/rconfig/data/Routers/router/2017/Sep/01','showiproute-002.txt','2017-09-01','00:02:02'),(393,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showstartup-config-002.txt','2017-09-01','00:02:05'),(394,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showiproute-002.txt','2017-09-01','00:02:07'),(395,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showcdpneigh-002.txt','2017-09-01','00:02:10'),(396,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showipaccess-list-002.txt','2017-09-01','00:02:13'),(397,12,'/home/rconfig/data/Routers/router2/2017/Sep/01','showiproute-002.txt','2017-09-01','00:02:15'),(398,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showstartup-config-000.txt','2017-09-02','00:00:05'),(399,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:05'),(400,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showcdpneigh-000.txt','2017-09-02','00:00:05'),(401,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showipaccess-list-000.txt','2017-09-02','00:00:06'),(402,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:06'),(403,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showstartup-config-000.txt','2017-09-02','00:00:09'),(404,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showstartup-config-000.txt','2017-09-02','00:00:10'),(405,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:10'),(406,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showcdpneigh-000.txt','2017-09-02','00:00:10'),(407,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showipaccess-list-000.txt','2017-09-02','00:00:10'),(408,11,'/home/rconfig/data/Routers/router/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:10'),(409,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:11'),(410,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showstartup-config-000.txt','2017-09-02','00:00:13'),(411,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showcdpneigh-000.txt','2017-09-02','00:00:14'),(412,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:16'),(413,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showipaccess-list-000.txt','2017-09-02','00:00:16'),(414,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showcdpneigh-000.txt','2017-09-02','00:00:18'),(415,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showiproute-000.txt','2017-09-02','00:00:19'),(416,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showipaccess-list-000.txt','2017-09-03','00:00:01'),(417,12,'/home/rconfig/data/Routers/router2/2017/Sep/02','showiproute-000.txt','2017-09-03','00:00:04'),(418,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showstartup-config-000.txt','2017-09-03','00:00:05'),(419,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:05'),(420,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showcdpneigh-000.txt','2017-09-03','00:00:05'),(421,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showipaccess-list-000.txt','2017-09-03','00:00:05'),(422,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:05'),(423,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showstartup-config-000.txt','2017-09-03','00:00:08'),(424,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showstartup-config-000.txt','2017-09-03','00:00:09'),(425,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:09'),(426,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showcdpneigh-000.txt','2017-09-03','00:00:10'),(427,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showipaccess-list-000.txt','2017-09-03','00:00:10'),(428,11,'/home/rconfig/data/Routers/router/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:10'),(429,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:11'),(430,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showstartup-config-000.txt','2017-09-03','00:00:13'),(431,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showcdpneigh-000.txt','2017-09-03','00:00:13'),(432,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:15'),(433,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showipaccess-list-000.txt','2017-09-03','00:00:16'),(434,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showcdpneigh-000.txt','2017-09-03','00:00:18'),(435,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showiproute-000.txt','2017-09-03','00:00:19'),(436,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showipaccess-list-000.txt','2017-09-04','00:00:01'),(437,12,'/home/rconfig/data/Routers/router2/2017/Sep/03','showiproute-000.txt','2017-09-04','00:00:04'),(438,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showstartup-config-000.txt','2017-09-04','00:00:06'),(439,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:06'),(440,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showcdpneigh-000.txt','2017-09-04','00:00:06'),(441,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showipaccess-list-000.txt','2017-09-04','00:00:06'),(442,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:06'),(443,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showstartup-config-000.txt','2017-09-04','00:00:09'),(444,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showstartup-config-000.txt','2017-09-04','00:00:09'),(445,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:09'),(446,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showcdpneigh-000.txt','2017-09-04','00:00:10'),(447,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showipaccess-list-000.txt','2017-09-04','00:00:10'),(448,11,'/home/rconfig/data/Routers/router/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:10'),(449,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:12'),(450,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showstartup-config-000.txt','2017-09-04','00:00:13'),(451,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showcdpneigh-000.txt','2017-09-04','00:00:14'),(452,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:15'),(453,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showipaccess-list-000.txt','2017-09-04','00:00:17'),(454,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showcdpneigh-000.txt','2017-09-04','00:00:18'),(455,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showiproute-000.txt','2017-09-04','00:00:20'),(456,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showipaccess-list-000.txt','2017-09-05','00:00:00'),(457,12,'/home/rconfig/data/Routers/router2/2017/Sep/04','showiproute-000.txt','2017-09-05','00:00:03'),(458,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showstartup-config-000.txt','2017-09-05','00:00:06'),(459,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:06'),(460,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showcdpneigh-000.txt','2017-09-05','00:00:06'),(461,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showipaccess-list-000.txt','2017-09-05','00:00:06'),(462,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:06'),(463,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showstartup-config-000.txt','2017-09-05','00:00:08'),(464,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:08'),(465,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showcdpneigh-000.txt','2017-09-05','00:00:08'),(466,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showipaccess-list-000.txt','2017-09-05','00:00:09'),(467,11,'/home/rconfig/data/Routers/router/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:09'),(468,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showstartup-config-000.txt','2017-09-05','00:00:09'),(469,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showstartup-config-000.txt','2017-09-05','00:00:12'),(470,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:12'),(471,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:14'),(472,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showcdpneigh-000.txt','2017-09-05','00:00:15'),(473,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showcdpneigh-000.txt','2017-09-05','00:00:17'),(474,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showipaccess-list-000.txt','2017-09-05','00:00:17'),(475,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showipaccess-list-000.txt','2017-09-05','00:00:19'),(476,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showiproute-000.txt','2017-09-05','00:00:20'),(477,12,'/home/rconfig/data/Routers/router2/2017/Sep/05','showiproute-000.txt','2017-09-06','00:00:01'),(478,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showstartup-config-000.txt','2017-09-06','00:00:05'),(479,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:05'),(480,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showcdpneigh-000.txt','2017-09-06','00:00:05'),(481,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showipaccess-list-000.txt','2017-09-06','00:00:06'),(482,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:06'),(483,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showstartup-config-000.txt','2017-09-06','00:00:07'),(484,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:07'),(485,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showcdpneigh-000.txt','2017-09-06','00:00:07'),(486,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showipaccess-list-000.txt','2017-09-06','00:00:08'),(487,11,'/home/rconfig/data/Routers/router/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:08'),(488,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showstartup-config-000.txt','2017-09-06','00:00:09'),(489,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showstartup-config-000.txt','2017-09-06','00:00:11'),(490,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:11'),(491,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:13'),(492,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showcdpneigh-000.txt','2017-09-06','00:00:14'),(493,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showcdpneigh-000.txt','2017-09-06','00:00:16'),(494,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showipaccess-list-000.txt','2017-09-06','00:00:16'),(495,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showipaccess-list-000.txt','2017-09-06','00:00:18'),(496,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showiproute-000.txt','2017-09-06','00:00:19'),(497,12,'/home/rconfig/data/Routers/router2/2017/Sep/06','showiproute-000.txt','2017-09-07','00:00:01'),(498,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showstartup-config-000.txt','2017-09-07','00:00:06'),(499,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:06'),(500,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showcdpneigh-000.txt','2017-09-07','00:00:06'),(501,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showipaccess-list-000.txt','2017-09-07','00:00:06'),(502,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:06'),(503,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showstartup-config-000.txt','2017-09-07','00:00:08'),(504,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:08'),(505,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showcdpneigh-000.txt','2017-09-07','00:00:08'),(506,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showipaccess-list-000.txt','2017-09-07','00:00:08'),(507,11,'/home/rconfig/data/Routers/router/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:08'),(508,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showstartup-config-000.txt','2017-09-07','00:00:09'),(509,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showstartup-config-000.txt','2017-09-07','00:00:11'),(510,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:12'),(511,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:14'),(512,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showcdpneigh-000.txt','2017-09-07','00:00:14'),(513,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showcdpneigh-000.txt','2017-09-07','00:00:16'),(514,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showipaccess-list-000.txt','2017-09-07','00:00:17'),(515,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showipaccess-list-000.txt','2017-09-07','00:00:19'),(516,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showiproute-000.txt','2017-09-07','00:00:20'),(517,12,'/home/rconfig/data/Routers/router2/2017/Sep/07','showiproute-000.txt','2017-09-08','00:00:01'),(518,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showstartup-config-000.txt','2017-09-08','00:00:06'),(519,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:06'),(520,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showcdpneigh-000.txt','2017-09-08','00:00:06'),(521,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showipaccess-list-000.txt','2017-09-08','00:00:06'),(522,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:06'),(523,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showstartup-config-000.txt','2017-09-08','00:00:08'),(524,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:08'),(525,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showcdpneigh-000.txt','2017-09-08','00:00:08'),(526,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showipaccess-list-000.txt','2017-09-08','00:00:08'),(527,11,'/home/rconfig/data/Routers/router/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:08'),(528,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showstartup-config-000.txt','2017-09-08','00:00:09'),(529,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showstartup-config-000.txt','2017-09-08','00:00:11'),(530,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:12'),(531,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:14'),(532,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showcdpneigh-000.txt','2017-09-08','00:00:14'),(533,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showcdpneigh-000.txt','2017-09-08','00:00:16'),(534,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showipaccess-list-000.txt','2017-09-08','00:00:17'),(535,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showipaccess-list-000.txt','2017-09-08','00:00:19'),(536,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showiproute-000.txt','2017-09-08','00:00:19'),(537,12,'/home/rconfig/data/Routers/router2/2017/Sep/08','showiproute-000.txt','2017-09-09','00:00:01'),(538,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showstartup-config-000.txt','2017-09-09','00:00:06'),(539,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:06'),(540,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showcdpneigh-000.txt','2017-09-09','00:00:06'),(541,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showipaccess-list-000.txt','2017-09-09','00:00:06'),(542,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:06'),(543,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showstartup-config-000.txt','2017-09-09','00:00:08'),(544,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:08'),(545,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showcdpneigh-000.txt','2017-09-09','00:00:08'),(546,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showipaccess-list-000.txt','2017-09-09','00:00:08'),(547,11,'/home/rconfig/data/Routers/router/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:08'),(548,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showstartup-config-000.txt','2017-09-09','00:00:09'),(549,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showstartup-config-000.txt','2017-09-09','00:00:11'),(550,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:12'),(551,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:14'),(552,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showcdpneigh-000.txt','2017-09-09','00:00:14'),(553,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showcdpneigh-000.txt','2017-09-09','00:00:16'),(554,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showipaccess-list-000.txt','2017-09-09','00:00:17'),(555,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showipaccess-list-000.txt','2017-09-09','00:00:19'),(556,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showiproute-000.txt','2017-09-09','00:00:20'),(557,12,'/home/rconfig/data/Routers/router2/2017/Sep/09','showiproute-000.txt','2017-09-10','00:00:01'),(558,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showstartup-config-000.txt','2017-09-10','00:00:06'),(559,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:06'),(560,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showcdpneigh-000.txt','2017-09-10','00:00:06'),(561,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showipaccess-list-000.txt','2017-09-10','00:00:06'),(562,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:06'),(563,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showstartup-config-000.txt','2017-09-10','00:00:08'),(564,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:08'),(565,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showcdpneigh-000.txt','2017-09-10','00:00:08'),(566,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showipaccess-list-000.txt','2017-09-10','00:00:08'),(567,11,'/home/rconfig/data/Routers/router/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:08'),(568,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showstartup-config-000.txt','2017-09-10','00:00:09'),(569,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showstartup-config-000.txt','2017-09-10','00:00:11'),(570,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:12'),(571,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:14'),(572,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showcdpneigh-000.txt','2017-09-10','00:00:14'),(573,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showcdpneigh-000.txt','2017-09-10','00:00:16'),(574,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showipaccess-list-000.txt','2017-09-10','00:00:17'),(575,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showipaccess-list-000.txt','2017-09-10','00:00:19'),(576,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showiproute-000.txt','2017-09-10','00:00:19'),(577,12,'/home/rconfig/data/Routers/router2/2017/Sep/10','showiproute-000.txt','2017-09-11','00:00:01'),(578,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showstartup-config-000.txt','2017-09-11','00:00:06'),(579,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:06'),(580,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showcdpneigh-000.txt','2017-09-11','00:00:06'),(581,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showipaccess-list-000.txt','2017-09-11','00:00:06'),(582,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:06'),(583,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showstartup-config-000.txt','2017-09-11','00:00:07'),(584,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:07'),(585,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showcdpneigh-000.txt','2017-09-11','00:00:07'),(586,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showipaccess-list-000.txt','2017-09-11','00:00:07'),(587,11,'/home/rconfig/data/Routers/router/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:07'),(588,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showstartup-config-000.txt','2017-09-11','00:00:09'),(589,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showstartup-config-000.txt','2017-09-11','00:00:10'),(590,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:12'),(591,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:13'),(592,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showcdpneigh-000.txt','2017-09-11','00:00:14'),(593,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showcdpneigh-000.txt','2017-09-11','00:00:15'),(594,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showipaccess-list-000.txt','2017-09-11','00:00:17'),(595,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showipaccess-list-000.txt','2017-09-11','00:00:18'),(596,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showiproute-000.txt','2017-09-11','00:00:19'),(597,12,'/home/rconfig/data/Routers/router2/2017/Sep/11','showiproute-000.txt','2017-09-12','00:00:00'),(598,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showstartup-config-000.txt','2017-09-12','00:00:06'),(599,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showstartup-config-000.txt','2017-09-12','00:00:06'),(600,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:06'),(601,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:06'),(602,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showcdpneigh-000.txt','2017-09-12','00:00:06'),(603,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showcdpneigh-000.txt','2017-09-12','00:00:06'),(604,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showipaccess-list-000.txt','2017-09-12','00:00:07'),(605,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showipaccess-list-000.txt','2017-09-12','00:00:07'),(606,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:07'),(607,11,'/home/rconfig/data/Routers/router/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:07'),(608,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showstartup-config-000.txt','2017-09-12','00:00:10'),(609,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showstartup-config-000.txt','2017-09-12','00:00:10'),(610,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:13'),(611,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:13'),(612,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showcdpneigh-000.txt','2017-09-12','00:00:15'),(613,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showcdpneigh-000.txt','2017-09-12','00:00:15'),(614,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showipaccess-list-000.txt','2017-09-12','00:00:18'),(615,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showipaccess-list-000.txt','2017-09-12','00:00:18'),(616,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:20'),(617,12,'/home/rconfig/data/Routers/router2/2017/Sep/12','showiproute-000.txt','2017-09-12','00:00:21'),(618,11,'/home/rconfig/data/Routers/router/2017/Sep/13','showstartup-config-000.txt','2017-09-13','00:00:05'),(619,11,'/home/rconfig/data/Routers/router/2017/Sep/13','showiproute-000.txt','2017-09-13','00:00:06'),(620,11,'/home/rconfig/data/Routers/router/2017/Sep/13','showcdpneigh-000.txt','2017-09-13','00:00:06'),(621,11,'/home/rconfig/data/Routers/router/2017/Sep/13','showipaccess-list-000.txt','2017-09-13','00:00:06'),(622,11,'/home/rconfig/data/Routers/router/2017/Sep/13','showiproute-000.txt','2017-09-13','00:00:06'),(623,12,'/home/rconfig/data/Routers/router2/2017/Sep/13','showstartup-config-000.txt','2017-09-13','00:00:09'),(624,12,'/home/rconfig/data/Routers/router2/2017/Sep/13','showiproute-000.txt','2017-09-13','00:00:11'),(625,12,'/home/rconfig/data/Routers/router2/2017/Sep/13','showcdpneigh-000.txt','2017-09-13','00:00:14'),(626,12,'/home/rconfig/data/Routers/router2/2017/Sep/13','showipaccess-list-000.txt','2017-09-13','00:00:16'),(627,12,'/home/rconfig/data/Routers/router2/2017/Sep/13','showiproute-000.txt','2017-09-13','00:00:19'),(628,11,'/home/rconfig/data/Routers/router/2017/Sep/14','showstartup-config-000.txt','2017-09-14','00:00:06'),(629,11,'/home/rconfig/data/Routers/router/2017/Sep/14','showiproute-000.txt','2017-09-14','00:00:06'),(630,11,'/home/rconfig/data/Routers/router/2017/Sep/14','showcdpneigh-000.txt','2017-09-14','00:00:06'),(631,11,'/home/rconfig/data/Routers/router/2017/Sep/14','showipaccess-list-000.txt','2017-09-14','00:00:06'),(632,11,'/home/rconfig/data/Routers/router/2017/Sep/14','showiproute-000.txt','2017-09-14','00:00:06'),(633,12,'/home/rconfig/data/Routers/router2/2017/Sep/14','showstartup-config-000.txt','2017-09-14','00:00:09'),(634,12,'/home/rconfig/data/Routers/router2/2017/Sep/14','showiproute-000.txt','2017-09-14','00:00:12'),(635,12,'/home/rconfig/data/Routers/router2/2017/Sep/14','showcdpneigh-000.txt','2017-09-14','00:00:14'),(636,12,'/home/rconfig/data/Routers/router2/2017/Sep/14','showipaccess-list-000.txt','2017-09-14','00:00:17'),(637,12,'/home/rconfig/data/Routers/router2/2017/Sep/14','showiproute-000.txt','2017-09-14','00:00:20'),(638,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showstartup-config-000.txt','2017-09-15','00:00:06'),(639,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showstartup-config-000.txt','2017-09-15','00:00:06'),(640,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:06'),(641,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showcdpneigh-000.txt','2017-09-15','00:00:06'),(642,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:06'),(643,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showipaccess-list-000.txt','2017-09-15','00:00:06'),(644,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showcdpneigh-000.txt','2017-09-15','00:00:07'),(645,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:07'),(646,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showipaccess-list-000.txt','2017-09-15','00:00:07'),(647,11,'/home/rconfig/data/Routers/router/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:07'),(648,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showstartup-config-000.txt','2017-09-15','00:00:10'),(649,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showstartup-config-000.txt','2017-09-15','00:00:10'),(650,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:12'),(651,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:13'),(652,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showcdpneigh-000.txt','2017-09-15','00:00:15'),(653,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showcdpneigh-000.txt','2017-09-15','00:00:15'),(654,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showipaccess-list-000.txt','2017-09-15','00:00:17'),(655,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showipaccess-list-000.txt','2017-09-15','00:00:18'),(656,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:20'),(657,12,'/home/rconfig/data/Routers/router2/2017/Sep/15','showiproute-000.txt','2017-09-15','00:00:20'),(658,11,'/home/rconfig/data/Routers/router/2017/Sep/16','showstartup-config-000.txt','2017-09-16','00:00:06'),(659,11,'/home/rconfig/data/Routers/router/2017/Sep/16','showiproute-000.txt','2017-09-16','00:00:06'),(660,11,'/home/rconfig/data/Routers/router/2017/Sep/16','showcdpneigh-000.txt','2017-09-16','00:00:06'),(661,11,'/home/rconfig/data/Routers/router/2017/Sep/16','showipaccess-list-000.txt','2017-09-16','00:00:06'),(662,11,'/home/rconfig/data/Routers/router/2017/Sep/16','showiproute-000.txt','2017-09-16','00:00:06'),(663,12,'/home/rconfig/data/Routers/router2/2017/Sep/16','showstartup-config-000.txt','2017-09-16','00:00:09'),(664,12,'/home/rconfig/data/Routers/router2/2017/Sep/16','showiproute-000.txt','2017-09-16','00:00:12'),(665,12,'/home/rconfig/data/Routers/router2/2017/Sep/16','showcdpneigh-000.txt','2017-09-16','00:00:14'),(666,12,'/home/rconfig/data/Routers/router2/2017/Sep/16','showipaccess-list-000.txt','2017-09-16','00:00:17'),(667,12,'/home/rconfig/data/Routers/router2/2017/Sep/16','showiproute-000.txt','2017-09-16','00:00:19'),(668,11,'/home/rconfig/data/Routers/router/2017/Sep/17','showstartup-config-000.txt','2017-09-17','00:00:05'),(669,11,'/home/rconfig/data/Routers/router/2017/Sep/17','showiproute-000.txt','2017-09-17','00:00:06'),(670,11,'/home/rconfig/data/Routers/router/2017/Sep/17','showcdpneigh-000.txt','2017-09-17','00:00:06'),(671,11,'/home/rconfig/data/Routers/router/2017/Sep/17','showipaccess-list-000.txt','2017-09-17','00:00:06'),(672,11,'/home/rconfig/data/Routers/router/2017/Sep/17','showiproute-000.txt','2017-09-17','00:00:06'),(673,12,'/home/rconfig/data/Routers/router2/2017/Sep/17','showstartup-config-000.txt','2017-09-17','00:00:09'),(674,12,'/home/rconfig/data/Routers/router2/2017/Sep/17','showiproute-000.txt','2017-09-17','00:00:11'),(675,12,'/home/rconfig/data/Routers/router2/2017/Sep/17','showcdpneigh-000.txt','2017-09-17','00:00:14'),(676,12,'/home/rconfig/data/Routers/router2/2017/Sep/17','showipaccess-list-000.txt','2017-09-17','00:00:17'),(677,12,'/home/rconfig/data/Routers/router2/2017/Sep/17','showiproute-000.txt','2017-09-17','00:00:19'),(678,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showstartup-config-000.txt','2017-09-18','00:00:05'),(679,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:05'),(680,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showcdpneigh-000.txt','2017-09-18','00:00:06'),(681,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showipaccess-list-000.txt','2017-09-18','00:00:06'),(682,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showstartup-config-000.txt','2017-09-18','00:00:06'),(683,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:06'),(684,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:06'),(685,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showcdpneigh-000.txt','2017-09-18','00:00:07'),(686,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showipaccess-list-000.txt','2017-09-18','00:00:07'),(687,11,'/home/rconfig/data/Routers/router/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:07'),(688,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showstartup-config-000.txt','2017-09-18','00:00:09'),(689,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showstartup-config-000.txt','2017-09-18','00:00:10'),(690,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:12'),(691,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:12'),(692,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showcdpneigh-000.txt','2017-09-18','00:00:14'),(693,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showcdpneigh-000.txt','2017-09-18','00:00:15'),(694,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showipaccess-list-000.txt','2017-09-18','00:00:17'),(695,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showipaccess-list-000.txt','2017-09-18','00:00:18'),(696,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:19'),(697,12,'/home/rconfig/data/Routers/router2/2017/Sep/18','showiproute-000.txt','2017-09-18','00:00:20'),(698,11,'/home/rconfig/data/Routers/router/2017/Sep/19','showstartup-config-000.txt','2017-09-19','00:00:05'),(699,11,'/home/rconfig/data/Routers/router/2017/Sep/19','showiproute-000.txt','2017-09-19','00:00:05'),(700,11,'/home/rconfig/data/Routers/router/2017/Sep/19','showcdpneigh-000.txt','2017-09-19','00:00:05'),(701,11,'/home/rconfig/data/Routers/router/2017/Sep/19','showipaccess-list-000.txt','2017-09-19','00:00:05'),(702,11,'/home/rconfig/data/Routers/router/2017/Sep/19','showiproute-000.txt','2017-09-19','00:00:06'),(703,12,'/home/rconfig/data/Routers/router2/2017/Sep/19','showstartup-config-000.txt','2017-09-19','00:00:08'),(704,12,'/home/rconfig/data/Routers/router2/2017/Sep/19','showiproute-000.txt','2017-09-19','00:00:11'),(705,12,'/home/rconfig/data/Routers/router2/2017/Sep/19','showcdpneigh-000.txt','2017-09-19','00:00:14'),(706,12,'/home/rconfig/data/Routers/router2/2017/Sep/19','showipaccess-list-000.txt','2017-09-19','00:00:16'),(707,12,'/home/rconfig/data/Routers/router2/2017/Sep/19','showiproute-000.txt','2017-09-19','00:00:19'),(708,11,'/home/rconfig/data/Routers/router/2017/Sep/20','showstartup-config-000.txt','2017-09-20','00:00:06'),(709,11,'/home/rconfig/data/Routers/router/2017/Sep/20','showiproute-000.txt','2017-09-20','00:00:06'),(710,11,'/home/rconfig/data/Routers/router/2017/Sep/20','showcdpneigh-000.txt','2017-09-20','00:00:06'),(711,11,'/home/rconfig/data/Routers/router/2017/Sep/20','showipaccess-list-000.txt','2017-09-20','00:00:06'),(712,11,'/home/rconfig/data/Routers/router/2017/Sep/20','showiproute-000.txt','2017-09-20','00:00:06'),(713,12,'/home/rconfig/data/Routers/router2/2017/Sep/20','showstartup-config-000.txt','2017-09-20','00:00:09'),(714,12,'/home/rconfig/data/Routers/router2/2017/Sep/20','showiproute-000.txt','2017-09-20','00:00:12'),(715,12,'/home/rconfig/data/Routers/router2/2017/Sep/20','showcdpneigh-000.txt','2017-09-20','00:00:14'),(716,12,'/home/rconfig/data/Routers/router2/2017/Sep/20','showipaccess-list-000.txt','2017-09-20','00:00:17'),(717,12,'/home/rconfig/data/Routers/router2/2017/Sep/20','showiproute-000.txt','2017-09-20','00:00:19'),(718,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showstartup-config-000.txt','2017-09-21','00:00:05'),(719,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:05'),(720,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showcdpneigh-000.txt','2017-09-21','00:00:05'),(721,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showipaccess-list-000.txt','2017-09-21','00:00:05'),(722,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:05'),(723,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showstartup-config-000.txt','2017-09-21','00:00:07'),(724,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:07'),(725,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showcdpneigh-000.txt','2017-09-21','00:00:07'),(726,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showipaccess-list-000.txt','2017-09-21','00:00:07'),(727,11,'/home/rconfig/data/Routers/router/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:07'),(728,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showstartup-config-000.txt','2017-09-21','00:00:08'),(729,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showstartup-config-000.txt','2017-09-21','00:00:10'),(730,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:11'),(731,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:13'),(732,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showcdpneigh-000.txt','2017-09-21','00:00:14'),(733,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showcdpneigh-000.txt','2017-09-21','00:00:15'),(734,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showipaccess-list-000.txt','2017-09-21','00:00:16'),(735,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showipaccess-list-000.txt','2017-09-21','00:00:18'),(736,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showiproute-000.txt','2017-09-21','00:00:19'),(737,12,'/home/rconfig/data/Routers/router2/2017/Sep/21','showiproute-000.txt','2017-09-22','00:00:01'),(738,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showstartup-config-000.txt','2017-09-22','00:00:06'),(739,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:06'),(740,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showcdpneigh-000.txt','2017-09-22','00:00:06'),(741,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showipaccess-list-000.txt','2017-09-22','00:00:06'),(742,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:06'),(743,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showstartup-config-000.txt','2017-09-22','00:00:07'),(744,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:07'),(745,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showcdpneigh-000.txt','2017-09-22','00:00:07'),(746,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showipaccess-list-000.txt','2017-09-22','00:00:07'),(747,11,'/home/rconfig/data/Routers/router/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:08'),(748,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showstartup-config-000.txt','2017-09-22','00:00:09'),(749,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showstartup-config-000.txt','2017-09-22','00:00:11'),(750,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:12'),(751,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:13'),(752,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showcdpneigh-000.txt','2017-09-22','00:00:15'),(753,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showcdpneigh-000.txt','2017-09-22','00:00:16'),(754,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showipaccess-list-000.txt','2017-09-22','00:00:18'),(755,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showipaccess-list-000.txt','2017-09-22','00:00:19'),(756,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showiproute-000.txt','2017-09-22','00:00:20'),(757,12,'/home/rconfig/data/Routers/router2/2017/Sep/22','showiproute-000.txt','2017-09-23','00:00:01'),(758,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showstartup-config-000.txt','2017-09-23','00:00:06'),(759,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:06'),(760,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showcdpneigh-000.txt','2017-09-23','00:00:06'),(761,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showipaccess-list-000.txt','2017-09-23','00:00:06'),(762,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:06'),(763,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showstartup-config-000.txt','2017-09-23','00:00:06'),(764,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:07'),(765,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showcdpneigh-000.txt','2017-09-23','00:00:07'),(766,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showipaccess-list-000.txt','2017-09-23','00:00:07'),(767,11,'/home/rconfig/data/Routers/router/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:07'),(768,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showstartup-config-000.txt','2017-09-23','00:00:09'),(769,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showstartup-config-000.txt','2017-09-23','00:00:10'),(770,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:12'),(771,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:12'),(772,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showcdpneigh-000.txt','2017-09-23','00:00:14'),(773,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showcdpneigh-000.txt','2017-09-23','00:00:15'),(774,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showipaccess-list-000.txt','2017-09-23','00:00:17'),(775,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showipaccess-list-000.txt','2017-09-23','00:00:17'),(776,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showiproute-000.txt','2017-09-23','00:00:19'),(777,12,'/home/rconfig/data/Routers/router2/2017/Sep/23','showiproute-000.txt','2017-09-24','00:00:00'),(778,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showstartup-config-000.txt','2017-09-24','00:00:06'),(779,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showstartup-config-000.txt','2017-09-24','00:00:06'),(780,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:06'),(781,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showcdpneigh-000.txt','2017-09-24','00:00:06'),(782,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:06'),(783,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showipaccess-list-000.txt','2017-09-24','00:00:06'),(784,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showcdpneigh-000.txt','2017-09-24','00:00:06'),(785,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:06'),(786,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showipaccess-list-000.txt','2017-09-24','00:00:07'),(787,11,'/home/rconfig/data/Routers/router/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:07'),(788,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showstartup-config-000.txt','2017-09-24','00:00:09'),(789,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showstartup-config-000.txt','2017-09-24','00:00:10'),(790,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:12'),(791,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:13'),(792,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showcdpneigh-000.txt','2017-09-24','00:00:15'),(793,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showcdpneigh-000.txt','2017-09-24','00:00:15'),(794,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showipaccess-list-000.txt','2017-09-24','00:00:17'),(795,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showipaccess-list-000.txt','2017-09-24','00:00:18'),(796,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:20'),(797,12,'/home/rconfig/data/Routers/router2/2017/Sep/24','showiproute-000.txt','2017-09-24','00:00:20'),(798,11,'/home/rconfig/data/Routers/router/2017/Sep/25','showstartup-config-000.txt','2017-09-25','00:00:06'),(799,11,'/home/rconfig/data/Routers/router/2017/Sep/25','showiproute-000.txt','2017-09-25','00:00:06'),(800,11,'/home/rconfig/data/Routers/router/2017/Sep/25','showcdpneigh-000.txt','2017-09-25','00:00:06'),(801,11,'/home/rconfig/data/Routers/router/2017/Sep/25','showipaccess-list-000.txt','2017-09-25','00:00:06'),(802,11,'/home/rconfig/data/Routers/router/2017/Sep/25','showiproute-000.txt','2017-09-25','00:00:06'),(803,12,'/home/rconfig/data/Routers/router2/2017/Sep/25','showstartup-config-000.txt','2017-09-25','00:00:09'),(804,12,'/home/rconfig/data/Routers/router2/2017/Sep/25','showiproute-000.txt','2017-09-25','00:00:12'),(805,12,'/home/rconfig/data/Routers/router2/2017/Sep/25','showcdpneigh-000.txt','2017-09-25','00:00:14'),(806,12,'/home/rconfig/data/Routers/router2/2017/Sep/25','showipaccess-list-000.txt','2017-09-25','00:00:17'),(807,12,'/home/rconfig/data/Routers/router2/2017/Sep/25','showiproute-000.txt','2017-09-25','00:00:19'),(808,11,'/home/rconfig/data/Routers/router/2017/Sep/26','showstartup-config-000.txt','2017-09-26','00:00:05'),(809,11,'/home/rconfig/data/Routers/router/2017/Sep/26','showiproute-000.txt','2017-09-26','00:00:05'),(810,11,'/home/rconfig/data/Routers/router/2017/Sep/26','showcdpneigh-000.txt','2017-09-26','00:00:05'),(811,11,'/home/rconfig/data/Routers/router/2017/Sep/26','showipaccess-list-000.txt','2017-09-26','00:00:05'),(812,11,'/home/rconfig/data/Routers/router/2017/Sep/26','showiproute-000.txt','2017-09-26','00:00:05'),(813,12,'/home/rconfig/data/Routers/router2/2017/Sep/26','showstartup-config-000.txt','2017-09-26','00:00:08'),(814,12,'/home/rconfig/data/Routers/router2/2017/Sep/26','showiproute-000.txt','2017-09-26','00:00:11'),(815,12,'/home/rconfig/data/Routers/router2/2017/Sep/26','showcdpneigh-000.txt','2017-09-26','00:00:13'),(816,12,'/home/rconfig/data/Routers/router2/2017/Sep/26','showipaccess-list-000.txt','2017-09-26','00:00:16'),(817,12,'/home/rconfig/data/Routers/router2/2017/Sep/26','showiproute-000.txt','2017-09-26','00:00:18'),(818,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showstartup-config-000.txt','2017-09-27','00:00:06'),(819,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:06'),(820,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showcdpneigh-000.txt','2017-09-27','00:00:06'),(821,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showipaccess-list-000.txt','2017-09-27','00:00:06'),(822,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:06'),(823,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showstartup-config-000.txt','2017-09-27','00:00:07'),(824,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:07'),(825,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showcdpneigh-000.txt','2017-09-27','00:00:07'),(826,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showipaccess-list-000.txt','2017-09-27','00:00:07'),(827,11,'/home/rconfig/data/Routers/router/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:07'),(828,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showstartup-config-000.txt','2017-09-27','00:00:09'),(829,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showstartup-config-000.txt','2017-09-27','00:00:10'),(830,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:12'),(831,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:13'),(832,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showcdpneigh-000.txt','2017-09-27','00:00:15'),(833,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showcdpneigh-000.txt','2017-09-27','00:00:16'),(834,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showipaccess-list-000.txt','2017-09-27','00:00:18'),(835,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showipaccess-list-000.txt','2017-09-27','00:00:19'),(836,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showiproute-000.txt','2017-09-27','00:00:20'),(837,12,'/home/rconfig/data/Routers/router2/2017/Sep/27','showiproute-000.txt','2017-09-28','00:00:01'),(838,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showstartup-config-000.txt','2017-09-28','00:00:05'),(839,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:05'),(840,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showcdpneigh-000.txt','2017-09-28','00:00:05'),(841,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showipaccess-list-000.txt','2017-09-28','00:00:05'),(842,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:06'),(843,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showstartup-config-000.txt','2017-09-28','00:00:06'),(844,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:07'),(845,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showcdpneigh-000.txt','2017-09-28','00:00:07'),(846,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showipaccess-list-000.txt','2017-09-28','00:00:07'),(847,11,'/home/rconfig/data/Routers/router/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:07'),(848,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showstartup-config-000.txt','2017-09-28','00:00:08'),(849,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showstartup-config-000.txt','2017-09-28','00:00:10'),(850,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:11'),(851,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:13'),(852,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showcdpneigh-000.txt','2017-09-28','00:00:14'),(853,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showcdpneigh-000.txt','2017-09-28','00:00:16'),(854,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showipaccess-list-000.txt','2017-09-28','00:00:17'),(855,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showipaccess-list-000.txt','2017-09-28','00:00:18'),(856,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showiproute-000.txt','2017-09-28','00:00:20'),(857,12,'/home/rconfig/data/Routers/router2/2017/Sep/28','showiproute-000.txt','2017-09-29','00:00:01'),(858,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showstartup-config-000.txt','2017-09-29','00:00:06'),(859,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:06'),(860,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showcdpneigh-000.txt','2017-09-29','00:00:06'),(861,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showipaccess-list-000.txt','2017-09-29','00:00:06'),(862,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:06'),(863,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showstartup-config-000.txt','2017-09-29','00:00:06'),(864,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:07'),(865,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showcdpneigh-000.txt','2017-09-29','00:00:07'),(866,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showipaccess-list-000.txt','2017-09-29','00:00:07'),(867,11,'/home/rconfig/data/Routers/router/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:07'),(868,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showstartup-config-000.txt','2017-09-29','00:00:09'),(869,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showstartup-config-000.txt','2017-09-29','00:00:10'),(870,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:12'),(871,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:12'),(872,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showcdpneigh-000.txt','2017-09-29','00:00:14'),(873,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showcdpneigh-000.txt','2017-09-29','00:00:15'),(874,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showipaccess-list-000.txt','2017-09-29','00:00:17'),(875,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showipaccess-list-000.txt','2017-09-29','00:00:17'),(876,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showiproute-000.txt','2017-09-29','00:00:19'),(877,12,'/home/rconfig/data/Routers/router2/2017/Sep/29','showiproute-000.txt','2017-09-30','00:00:00'),(878,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showstartup-config-000.txt','2017-09-30','00:00:05'),(879,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showstartup-config-000.txt','2017-09-30','00:00:06'),(880,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:06'),(881,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showcdpneigh-000.txt','2017-09-30','00:00:06'),(882,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:06'),(883,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showipaccess-list-000.txt','2017-09-30','00:00:06'),(884,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showcdpneigh-000.txt','2017-09-30','00:00:06'),(885,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:06'),(886,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showipaccess-list-000.txt','2017-09-30','00:00:07'),(887,11,'/home/rconfig/data/Routers/router/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:07'),(888,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showstartup-config-000.txt','2017-09-30','00:00:09'),(889,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showstartup-config-000.txt','2017-09-30','00:00:10'),(890,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:12'),(891,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:12'),(892,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showcdpneigh-000.txt','2017-09-30','00:00:15'),(893,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showcdpneigh-000.txt','2017-09-30','00:00:15'),(894,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showipaccess-list-000.txt','2017-09-30','00:00:17'),(895,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showipaccess-list-000.txt','2017-09-30','00:00:18'),(896,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:20'),(897,12,'/home/rconfig/data/Routers/router2/2017/Sep/30','showiproute-000.txt','2017-09-30','00:00:20'),(898,11,'/home/rconfig/data/Routers/router/2017/Oct/01','showstartup-config-000.txt','2017-10-01','00:00:05'),(899,11,'/home/rconfig/data/Routers/router/2017/Oct/01','showiproute-000.txt','2017-10-01','00:00:05'),(900,11,'/home/rconfig/data/Routers/router/2017/Oct/01','showcdpneigh-000.txt','2017-10-01','00:00:05'),(901,11,'/home/rconfig/data/Routers/router/2017/Oct/01','showipaccess-list-000.txt','2017-10-01','00:00:05'),(902,11,'/home/rconfig/data/Routers/router/2017/Oct/01','showiproute-000.txt','2017-10-01','00:00:05'),(903,12,'/home/rconfig/data/Routers/router2/2017/Oct/01','showstartup-config-000.txt','2017-10-01','00:00:08'),(904,12,'/home/rconfig/data/Routers/router2/2017/Oct/01','showiproute-000.txt','2017-10-01','00:00:11'),(905,12,'/home/rconfig/data/Routers/router2/2017/Oct/01','showcdpneigh-000.txt','2017-10-01','00:00:13'),(906,12,'/home/rconfig/data/Routers/router2/2017/Oct/01','showipaccess-list-000.txt','2017-10-01','00:00:16'),(907,12,'/home/rconfig/data/Routers/router2/2017/Oct/01','showiproute-000.txt','2017-10-01','00:00:19'),(908,11,'/home/rconfig/data/Routers/router/2017/Oct/02','showstartup-config-000.txt','2017-10-02','00:00:06'),(909,11,'/home/rconfig/data/Routers/router/2017/Oct/02','showiproute-000.txt','2017-10-02','00:00:06'),(910,11,'/home/rconfig/data/Routers/router/2017/Oct/02','showcdpneigh-000.txt','2017-10-02','00:00:06'),(911,11,'/home/rconfig/data/Routers/router/2017/Oct/02','showipaccess-list-000.txt','2017-10-02','00:00:06'),(912,11,'/home/rconfig/data/Routers/router/2017/Oct/02','showiproute-000.txt','2017-10-02','00:00:06'),(913,12,'/home/rconfig/data/Routers/router2/2017/Oct/02','showstartup-config-000.txt','2017-10-02','00:00:09'),(914,12,'/home/rconfig/data/Routers/router2/2017/Oct/02','showiproute-000.txt','2017-10-02','00:00:11'),(915,12,'/home/rconfig/data/Routers/router2/2017/Oct/02','showcdpneigh-000.txt','2017-10-02','00:00:14'),(916,12,'/home/rconfig/data/Routers/router2/2017/Oct/02','showipaccess-list-000.txt','2017-10-02','00:00:17'),(917,12,'/home/rconfig/data/Routers/router2/2017/Oct/02','showiproute-000.txt','2017-10-02','00:00:19'),(918,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showstartup-config-000.txt','2017-10-03','00:00:06'),(919,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:06'),(920,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showcdpneigh-000.txt','2017-10-03','00:00:06'),(921,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showipaccess-list-000.txt','2017-10-03','00:00:06'),(922,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:06'),(923,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showstartup-config-000.txt','2017-10-03','00:00:07'),(924,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:07'),(925,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showcdpneigh-000.txt','2017-10-03','00:00:07'),(926,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showipaccess-list-000.txt','2017-10-03','00:00:07'),(927,11,'/home/rconfig/data/Routers/router/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:07'),(928,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showstartup-config-000.txt','2017-10-03','00:00:09'),(929,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showstartup-config-000.txt','2017-10-03','00:00:10'),(930,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:12'),(931,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:13'),(932,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showcdpneigh-000.txt','2017-10-03','00:00:15'),(933,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showcdpneigh-000.txt','2017-10-03','00:00:15'),(934,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showipaccess-list-000.txt','2017-10-03','00:00:18'),(935,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showipaccess-list-000.txt','2017-10-03','00:00:18'),(936,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showiproute-000.txt','2017-10-03','00:00:20'),(937,12,'/home/rconfig/data/Routers/router2/2017/Oct/03','showiproute-000.txt','2017-10-04','00:00:00'),(938,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showstartup-config-000.txt','2017-10-04','00:00:05'),(939,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showstartup-config-000.txt','2017-10-04','00:00:05'),(940,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:06'),(941,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showcdpneigh-000.txt','2017-10-04','00:00:06'),(942,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:06'),(943,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showipaccess-list-000.txt','2017-10-04','00:00:06'),(944,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showcdpneigh-000.txt','2017-10-04','00:00:06'),(945,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:06'),(946,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showipaccess-list-000.txt','2017-10-04','00:00:06'),(947,11,'/home/rconfig/data/Routers/router/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:07'),(948,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showstartup-config-000.txt','2017-10-04','00:00:09'),(949,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showstartup-config-000.txt','2017-10-04','00:00:10'),(950,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:12'),(951,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:12'),(952,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showcdpneigh-000.txt','2017-10-04','00:00:14'),(953,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showcdpneigh-000.txt','2017-10-04','00:00:15'),(954,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showipaccess-list-000.txt','2017-10-04','00:00:17'),(955,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showipaccess-list-000.txt','2017-10-04','00:00:17'),(956,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showiproute-000.txt','2017-10-04','00:00:19'),(957,12,'/home/rconfig/data/Routers/router2/2017/Oct/04','showiproute-000.txt','2017-10-05','00:00:00'),(958,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showstartup-config-000.txt','2017-10-05','00:00:05'),(959,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:05'),(960,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showcdpneigh-000.txt','2017-10-05','00:00:05'),(961,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showstartup-config-000.txt','2017-10-05','00:00:06'),(962,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showipaccess-list-000.txt','2017-10-05','00:00:06'),(963,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:06'),(964,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:06'),(965,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showcdpneigh-000.txt','2017-10-05','00:00:06'),(966,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showipaccess-list-000.txt','2017-10-05','00:00:07'),(967,11,'/home/rconfig/data/Routers/router/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:07'),(968,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showstartup-config-000.txt','2017-10-05','00:00:09'),(969,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showstartup-config-000.txt','2017-10-05','00:00:10'),(970,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:12'),(971,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:12'),(972,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showcdpneigh-000.txt','2017-10-05','00:00:14'),(973,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showcdpneigh-000.txt','2017-10-05','00:00:15'),(974,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showipaccess-list-000.txt','2017-10-05','00:00:17'),(975,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showipaccess-list-000.txt','2017-10-05','00:00:17'),(976,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showiproute-000.txt','2017-10-05','00:00:19'),(977,12,'/home/rconfig/data/Routers/router2/2017/Oct/05','showiproute-000.txt','2017-10-06','00:00:00'),(978,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showstartup-config-000.txt','2017-10-06','00:00:05'),(979,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showstartup-config-000.txt','2017-10-06','00:00:06'),(980,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:06'),(981,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showcdpneigh-000.txt','2017-10-06','00:00:06'),(982,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:06'),(983,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showipaccess-list-000.txt','2017-10-06','00:00:06'),(984,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showcdpneigh-000.txt','2017-10-06','00:00:06'),(985,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:06'),(986,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showipaccess-list-000.txt','2017-10-06','00:00:07'),(987,11,'/home/rconfig/data/Routers/router/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:07'),(988,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showstartup-config-000.txt','2017-10-06','00:00:09'),(989,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showstartup-config-000.txt','2017-10-06','00:00:10'),(990,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:12'),(991,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:13'),(992,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showcdpneigh-000.txt','2017-10-06','00:00:15'),(993,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showcdpneigh-000.txt','2017-10-06','00:00:15'),(994,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showipaccess-list-000.txt','2017-10-06','00:00:17'),(995,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showipaccess-list-000.txt','2017-10-06','00:00:18'),(996,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showiproute-000.txt','2017-10-06','00:00:20'),(997,12,'/home/rconfig/data/Routers/router2/2017/Oct/06','showiproute-000.txt','2017-10-07','00:00:00'),(998,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showstartup-config-000.txt','2017-10-07','00:00:05'),(999,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:05'),(1000,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showcdpneigh-000.txt','2017-10-07','00:00:05'),(1001,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showstartup-config-000.txt','2017-10-07','00:00:05'),(1002,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showipaccess-list-000.txt','2017-10-07','00:00:06'),(1003,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:06'),(1004,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:06'),(1005,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showcdpneigh-000.txt','2017-10-07','00:00:06'),(1006,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showipaccess-list-000.txt','2017-10-07','00:00:06'),(1007,11,'/home/rconfig/data/Routers/router/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:06'),(1008,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showstartup-config-000.txt','2017-10-07','00:00:09'),(1009,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showstartup-config-000.txt','2017-10-07','00:00:09'),(1010,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:11'),(1011,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:12'),(1012,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showcdpneigh-000.txt','2017-10-07','00:00:14'),(1013,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showcdpneigh-000.txt','2017-10-07','00:00:14'),(1014,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showipaccess-list-000.txt','2017-10-07','00:00:16'),(1015,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showipaccess-list-000.txt','2017-10-07','00:00:17'),(1016,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:19'),(1017,12,'/home/rconfig/data/Routers/router2/2017/Oct/07','showiproute-000.txt','2017-10-07','00:00:20');
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `customProperties`
--

DROP TABLE IF EXISTS `customProperties`;
/*!50001 DROP VIEW IF EXISTS `customProperties`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `customProperties` AS SELECT 
 1 AS `customProperty`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `devicemodelview`
--

DROP TABLE IF EXISTS `devicemodelview`;
/*!50001 DROP VIEW IF EXISTS `devicemodelview`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `devicemodelview` AS SELECT 
 1 AS `model`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `menuPages`
--

DROP TABLE IF EXISTS `menuPages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menuPages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(50) NOT NULL DEFAULT '0',
  `breadcrumbText` varchar(100) NOT NULL DEFAULT '0',
  `annoucementText` varchar(100) NOT NULL DEFAULT '0',
  `menuName` varchar(100) NOT NULL DEFAULT '0',
  `topLevel` varchar(100) NOT NULL DEFAULT '0',
  `parentId` int(11) NOT NULL DEFAULT '0',
  `menuSortId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menuPages`
--

LOCK TABLES `menuPages` WRITE;
/*!40000 ALTER TABLE `menuPages` DISABLE KEYS */;
INSERT INTO `menuPages` VALUES (1,'login.php','Login Page','Enter Username & Password to login','Login','2',1,0),(2,'compatibility.php','Compatibility Page','rConfig Compatibility Details','Compatibility','2',0,0),(3,'dashboard.php','Dashboard','View rConfig Server and Device Status on this page','Home','1',3,1),(4,'devices.php','Devices','View/Edit Devices on this page','Devices','1',4,2),(5,'devicemgmt.php','Devices > Device Management','Manage devices on this page','Device Management','0',5,0),(7,'customProperties.php','Devices > Custom Properties','Update Custom Properties on this page','Custom Properties','0',4,0),(8,'categories.php','Devices > Categories','Update Categories on this page','Categories','0',4,0),(9,'commands.php','Devices > Commands','Update Commands on this page','Commands','0',4,0),(10,'vendors.php','Devices > Vendors','Update Vendor details on this page','Vendors','0',4,0),(11,'configoverview.php','Configuration Tools > Overview','Configurations Overview','Configuration Tools','1',10,4),(12,'configcompare.php','Configuration Tools > Comparison','Configurations Comparison','Compare','0',10,0),(13,'search.php','Configuration Tools > Search','Search Configurations','Config Search','0',10,0),(14,'snippets.php','Configuration Tools > Config Snippets','Configuration Snippets','Config Snippets','0',10,0),(15,'configreports.php','Configuration Tools > Reports','Reports','Reports','0',10,0),(16,'configlogging.php','Configuration Tools > Logging Information','Logging files and archives','Logs','0',10,0),(17,'complianceoverview.php','Compliance > Overview','Configuration Compliance Management Overview','Compliance','1',16,5),(18,'compliancereports.php','Compliance > Reports','Configuration Compliance Reports','Reports','0',16,0),(19,'compliancepolicies.php','Compliance > Policies','Configuration Compliance Policies','Policies','0',16,0),(20,'compliancepolicyelements.php','Compliance > Policy Elements','Configuration Compliance Policy Elements','Policy Elements','0',16,0),(21,'settings.php','Settings > General Settings','Change general systems settings on this page','Settings','1',20,6),(22,'scheduler.php','Scheduled Tasks','Manage Scheduled Tasks on this page','Scheduled Tasks','1',21,3),(23,'useradmin.php','Settings > Users Management','Manage User details on this page','Users (Admin)','0',20,0),(24,'settingsBackup.php','Settings > Backup','Backup rConfig on this page','System Backup(Admin)','0',20,0),(25,'updater.php','Update','Update rConfig on this page','Updater','2',24,0),(6,'deviceConnTemplates.php','Devices > Device Connection Templates','Manage devices connection templates on this page','Connection Templates','0',4,0);
/*!40000 ALTER TABLE `menuPages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `taskId767033` varchar(20) NOT NULL DEFAULT '2',
  `deviceName` varchar(255) DEFAULT NULL,
  `deviceUsername` varchar(255) DEFAULT NULL,
  `devicePassword` varchar(255) DEFAULT NULL,
  `deviceEnablePassword` varchar(255) DEFAULT NULL,
  `deviceIpAddr` varchar(255) DEFAULT NULL,
  `devicePrompt` varchar(255) DEFAULT NULL,
  `deviceEnablePrompt` varchar(255) DEFAULT NULL,
  `nodeCatId` int(10) DEFAULT NULL,
  `templateId` int(10) DEFAULT NULL,
  `vendorId` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `nodeVersion` varchar(255) DEFAULT NULL,
  `nodeAddedBy` varchar(255) DEFAULT '-',
  `defaultCreds` int(1) DEFAULT NULL,
  `defaultUsername` varchar(255) DEFAULT NULL,
  `defaultPassword` varchar(255) DEFAULT NULL,
  `defaultEnablePassword` varchar(255) DEFAULT NULL,
  `deviceDateAdded` date DEFAULT NULL,
  `deviceLastUpdated` date DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  `custom_Location` varchar(255) DEFAULT NULL COMMENT 'Custom Property - Location',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodes`
--

LOCK TABLES `nodes` WRITE;
/*!40000 ALTER TABLE `nodes` DISABLE KEYS */;
INSERT INTO `nodes` VALUES (1,'2','route-server.ip.tiscali.net','public','public','','213.200.64.94','public@route-server.as3257.net-re0>','public@route-server.as3257.net-re0>',2,5,'36','15',NULL,'admin',0,NULL,NULL,NULL,'2017-09-29',NULL,2,NULL),(2,'2','route-server.eu.gblx.net','','','','67.17.81.187','route-server.ams2>','',9,1,'1','7200',NULL,'admin',0,NULL,NULL,NULL,'2017-10-01',NULL,2,''),(3,'2','route-server-eu-gblx-net','','','','67.17.81.187','route-server.ams2>','',9,1,'1','7200',NULL,'admin',0,NULL,NULL,NULL,'2017-10-01',NULL,2,NULL),(4,'2','Cisco','cisco','cisco','','192.168.1.1','router#','',1,3,'1','7200',NULL,'admin',0,NULL,NULL,NULL,'2017-10-13',NULL,2,NULL),(5,'2','test','admin','admin','','192.168.1.1','router#','',2,NULL,'36','12',NULL,'admin',1,NULL,NULL,NULL,'2017-10-20',NULL,2,NULL),(6,'2','route-server-belwue-de-','','','','129.143.4.244','bird>','',10,1,'38','bird',NULL,'admin',0,NULL,NULL,NULL,'2017-10-21',NULL,2,NULL),(7,'2','route-views-ab-bb-telus-com','','','','154.11.98.18','route-views.on>','',11,1,'39','telus',NULL,'admin',0,NULL,NULL,NULL,'2017-10-21',NULL,2,NULL),(8,'2','route-server-gblx-net','','','','67.17.81.28','route-server.phx1>','',12,1,'40','gblx',NULL,'admin',0,NULL,NULL,NULL,'2017-10-21',NULL,2,NULL),(9,'2','public-route-server-is-co-za','rviews','rviews','','196.4.160.227','local-route-server>','',13,1,'1','isCoZa',NULL,'admin',0,NULL,NULL,NULL,'2017-10-21',NULL,2,NULL),(10,'2','route-server-opentransit-net','rviews','Rviews','','204.59.3.38','saxum.opentransit.net#','',14,1,'41','open1',NULL,'admin',0,NULL,NULL,NULL,'2017-10-21',NULL,2,NULL),(11,'1','router','cisco','cisco','cisco','192.168.1.170','router#','router>',2,2,'1','2811',NULL,'admin',0,NULL,NULL,NULL,'2017-10-30',NULL,1,''),(12,'1','router2','cisco','cisco','','192.168.1.172','router2#','',2,3,'1','2811',NULL,'admin',0,NULL,NULL,NULL,'2017-10-30',NULL,1,NULL);
/*!40000 ALTER TABLE `nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reportData`
--

DROP TABLE IF EXISTS `reportData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportData` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `device` varchar(255) DEFAULT NULL,
  `error` longtext,
  `script` varchar(50) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportData`
--

LOCK TABLES `reportData` WRITE;
/*!40000 ALTER TABLE `reportData` DISABLE KEYS */;
/*!40000 ALTER TABLE `reportData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `fileSaveChk` int(10) DEFAULT NULL,
  `fileLocation` varchar(255) DEFAULT NULL,
  `defaultNodeUsername` varchar(255) DEFAULT NULL,
  `defaultNodePassword` varchar(255) DEFAULT NULL,
  `defaultNodeEnable` varchar(255) DEFAULT NULL,
  `useDefaultCredsManualSet` int(1) DEFAULT NULL,
  `commandDebug` int(10) DEFAULT '0' COMMENT '0 is default where 1 is debug on',
  `commandDebugLocation` varchar(255) DEFAULT NULL,
  `phpErrorLogging` int(2) DEFAULT '0',
  `phpErrorLoggingLocation` varchar(255) DEFAULT '/home/rconfig/logs/phpLog/',
  `deviceConnectionTimout` int(3) DEFAULT '10',
  `smtpServerAddr` varchar(255) DEFAULT NULL,
  `smtpFromAddr` varchar(255) DEFAULT NULL,
  `smtpRecipientAddr` longtext,
  `smtpAuth` tinyint(2) DEFAULT NULL,
  `smtpAuthUser` varchar(100) DEFAULT NULL,
  `smtpAuthPass` varchar(100) DEFAULT NULL,
  `smtpLastTest` varchar(20) DEFAULT NULL,
  `smtpLastTestTime` datetime DEFAULT NULL,
  `timeZone` varchar(100) DEFAULT NULL,
  `ldapServer` int(1) NOT NULL DEFAULT '0',
  `pageTimeout` int(1) NOT NULL DEFAULT '600' COMMENT 'Page Timeout Value',
  `passwordEncryption` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,1,'/home/rconfig/data/','admin','admin','admin',0,0,'/home/rconfig/logs/debugging/',0,'/home/rconfig/logs/phpLog/',15,'','','',0,'','','','1980-01-01 00:00:00','Europe/Dublin',0,600,0);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snippets`
--

DROP TABLE IF EXISTS `snippets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snippets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snippetName` varchar(255) NOT NULL,
  `snippetDesc` varchar(255) NOT NULL,
  `snippet` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snippets`
--

LOCK TABLES `snippets` WRITE;
/*!40000 ALTER TABLE `snippets` DISABLE KEYS */;
/*!40000 ALTER TABLE `snippets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(6) NOT NULL,
  `taskType` int(3) NOT NULL,
  `taskname` varchar(255) NOT NULL,
  `taskDescription` varchar(255) NOT NULL,
  `snipId` int(10) DEFAULT NULL,
  `crontime` varchar(255) NOT NULL COMMENT 'e.g. 5 * * 6 *',
  `croncmd` varchar(255) NOT NULL COMMENT 'e.g. "php /script/script.php"',
  `addedBy` varchar(255) NOT NULL COMMENT 'for later use',
  `dateAdded` date NOT NULL,
  `catId` varchar(255) DEFAULT NULL COMMENT 'Used for Compare Reports Only',
  `catCommand` varchar(255) DEFAULT NULL COMMENT 'Used for Compare Reports Only',
  `status` int(2) NOT NULL COMMENT 'if 2 = deleted and not in crontab',
  `mailConnectionReport` int(10) DEFAULT '0',
  `mailErrorsOnly` int(10) DEFAULT '0',
  `complianceId` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (193686,1,'Route Servers Download','Route Servers Download',NULL,'0 0 * * * ','php /home/rconfig/lib/showCmdScript.php 193686','admin','2017-09-29','a:1:{i:0;s:1:\"2\";}',NULL,2,0,0,NULL),(422504,1,'rS Test','rS Test',NULL,'0 0 1 1 * ','php /home/rconfig/lib/showCmdScript.php 422504','admin','2017-10-01','',NULL,2,0,0,NULL),(511996,1,'isCoZa Download','isCoZa Download',NULL,'5 0 * * * ','php /home/rconfig/lib/showCmdScript.php 511996','admin','2017-10-21','a:1:{i:0;s:2:\"13\";}',NULL,2,0,0,NULL),(529102,1,'Route Servers Download2','Route Servers Download2',NULL,'0 0 1 1 * ','php /home/rconfig/lib/showCmdScript.php 529102','admin','2017-10-01','',NULL,2,0,0,NULL),(750836,1,'GBLX','GBLX',NULL,'0 0 * * * ','php /home/rconfig/lib/showCmdScript.php 750836','admin','2017-10-21','a:1:{i:0;s:2:\"12\";}',NULL,2,0,0,NULL),(752095,1,'openTransit Download','openTransit Download',NULL,'0 1 * * * ','php /home/rconfig/lib/showCmdScript.php 752095','admin','2017-10-21','a:1:{i:0;s:2:\"14\";}',NULL,2,0,0,NULL),(767033,1,'download routers test','download routers test',NULL,'0 0 * * * ','php /home/rconfig/lib/showCmdScript.php 767033','admin','2017-10-30','a:1:{i:0;s:1:\"2\";}',NULL,1,0,0,NULL),(924450,1,'belwue download','belwue download',NULL,'0 0 * * * ','php /home/rconfig/lib/showCmdScript.php 924450','admin','2017-10-21','a:1:{i:0;s:2:\"10\";}',NULL,2,0,0,NULL);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `dateAdded` date DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `dateLastEdit` date DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,'/home/rconfig/templates/ios-telnet-noenable.yml','Cisco IOS - TELNET - No Enable','Cisco IOS TELNET based connection without enable mode','2017-08-18','admin','2017-10-21',1),(2,'/home/rconfig/templates/ios-telnet-enable.yml','Cisco IOS - TELNET - Enable','Cisco IOS TELNET based connection with enable mode','2017-08-18','admin','2017-10-30',1),(3,'/home/rconfig/templates/ios-ssh-noenable.yml','Cisco IOS - SSH - No Enable','Cisco IOS SSH based connection without enable mode','2017-08-18','admin',NULL,1),(4,'/home/rconfig/templates/ios-ssh-enable.yml','Cisco IOS - SSH - Enable','Cisco IOS SSH based connection with enable mode','2017-08-18','admin',NULL,1),(5,'/home/rconfig/templates/junos15-telnet-noenable.yml','Junos 15 No Enable','Junos 15 No Enable','2017-09-29','admin','2017-09-29',1);
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userlevels`
--

DROP TABLE IF EXISTS `userlevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userlevels` (
  `id` int(10) NOT NULL,
  `userlevel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userlevels`
--

LOCK TABLES `userlevels` WRITE;
/*!40000 ALTER TABLE `userlevels` DISABLE KEYS */;
INSERT INTO `userlevels` VALUES (1,'User'),(9,'Administrator');
/*!40000 ALTER TABLE `userlevels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `userid` varchar(32) DEFAULT NULL,
  `userlevel` tinyint(1) unsigned NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `status` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','bee68036b4ccb7679339c6bea7e6ca93',9,'admin@domain.com',1507333221,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendors`
--

DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vendorName` varchar(50) DEFAULT '0',
  `vendorLogo` varchar(255) NOT NULL DEFAULT 'images/logos/Coding16.png',
  `status` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendors`
--

LOCK TABLES `vendors` WRITE;
/*!40000 ALTER TABLE `vendors` DISABLE KEYS */;
INSERT INTO `vendors` VALUES (1,'Cisco','images/vendor/cisco.jpg',1),(36,'Juniper','images/logos/rconfig16.png',1),(37,'sgn','images/logos/rconfig16.png',2),(38,'routeServerBelwue','images/logos/rconfig16.png',2),(39,'telusRouteServer','images/logos/rconfig16.png',2),(40,'GBLX','images/logos/rconfig16.png',2),(41,'opentransit','images/logos/rconfig16.png',2),(42,'Colt','images/logos/rconfig16.png',2);
/*!40000 ALTER TABLE `vendors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `customProperties`
--

/*!50001 DROP VIEW IF EXISTS `customProperties`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `customProperties` AS select `information_schema`.`COLUMNS`.`COLUMN_NAME` AS `customProperty` from `INFORMATION_SCHEMA`.`COLUMNS` where ((`information_schema`.`COLUMNS`.`TABLE_SCHEMA` = 'rconfig38') and (`information_schema`.`COLUMNS`.`TABLE_NAME` = 'nodes') and (`information_schema`.`COLUMNS`.`COLUMN_NAME` like '%custom%')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `devicemodelview`
--

/*!50001 DROP VIEW IF EXISTS `devicemodelview`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `devicemodelview` AS select distinct `nodes`.`model` AS `model` from `nodes` where ((`nodes`.`model` <> 'NULL') and (`nodes`.`model` <> '')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-07  0:45:05
