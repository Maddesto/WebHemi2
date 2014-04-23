-- CREATE DATABASE  IF NOT EXISTS `webhemi` /*!40100 DEFAULT CHARACTER SET utf8 */;
-- USE `webhemi`;
-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: webhemi
-- ------------------------------------------------------
-- Server version	5.5.35-1ubuntu1

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
-- Table structure for table `webhemi_lock`
--

DROP TABLE IF EXISTS `webhemi_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_lock` (
  `lock_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(15) NOT NULL,
  `tryings` int(10) unsigned NOT NULL DEFAULT '0',
  `time_lock` datetime DEFAULT NULL,
  PRIMARY KEY (`lock_id`),
  KEY `idx_wh_lock_1` (`client_ip`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_user`
--

DROP TABLE IF EXISTS `webhemi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `hash` varchar(32) DEFAULT '',
  `role` enum('member','moderator','editor','publisher','admin') DEFAULT 'member',
  `last_ip` varchar(15) DEFAULT NULL,
  `register_ip` varchar(15) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `time_login` datetime DEFAULT NULL,
  `time_register` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uqk_wh_user_1` (`username`),
  UNIQUE KEY `uqk_wh_user_2` (`email`),
  KEY `idx_wh_user_1` (`password`),
  KEY `idx_wh_user_2` (`is_active`),
  KEY `idx_wh_user_3` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user`
--

LOCK TABLES `webhemi_user` WRITE;
/*!40000 ALTER TABLE `webhemi_user` DISABLE KEYS */;
INSERT INTO `webhemi_user` VALUES (1,'admin','admin@armyspy.com','$2y$14$vhLxtyar2rNcBjWk6p8PcebtpkxvHpnUnr1T0Of9kP0eNBXYfzvWS','6e24a1858c949ee67b4eaf1a23d5ab31','admin','127.0.0.1','127.0.0.1',1,1,'2014-04-23 10:00:00','2014-04-23 10:00:00');
/*!40000 ALTER TABLE `webhemi_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_meta`
--

DROP TABLE IF EXISTS `webhemi_user_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_meta` (
  `user_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta` longtext NOT NULL,
  PRIMARY KEY (`user_id`,`meta_key`),
  CONSTRAINT `fk_wh_user_meta_1` FOREIGN KEY (`user_id`) REFERENCES `webhemi_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_meta`
--

LOCK TABLES `webhemi_user_meta` WRITE;
/*!40000 ALTER TABLE `webhemi_user_meta` DISABLE KEYS */;
INSERT INTO `webhemi_user_meta` VALUES (1,'avatar',''),(1,'details',''),(1,'displayEmail','0'),(1,'displayName',''),(1,'headLine',''),(1,'instantMessengers',''),(1,'location',''),(1,'phoneNumber',''),(1,'socialNetworks',''),(1,'websites','');
/*!40000 ALTER TABLE `webhemi_user_meta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-23 12:41:23
