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
-- Database `webhemi`
--

CREATE DATABASE  IF NOT EXISTS `webhemi` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `webhemi`;

--
-- Table structure for table `webhemi_user`
--

DROP TABLE IF EXISTS `webhemi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user` (
    `user_id`       INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL,
    `display_name`  VARCHAR(100) DEFAULT NULL,
    `password`      VARCHAR(128) NOT NULL,
	`role`          ENUM('member','moderator','editor','publisher','admin') DEFAULT 'member',
    `last_ip`       VARCHAR(15) DEFAULT NULL,
    `register_ip`   VARCHAR(15) NOT NULL,
    `is_active`     TINYINT(1) NOT NULL DEFAULT '0',
    `is_enabled`    TINYINT(1) NOT NULL DEFAULT '0',
    `time_login`    DATETIME DEFAULT NULL,
    `time_register` DATETIME NOT NULL,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `uqk_wh_user_1` (`username`),
	UNIQUE KEY `uqk_wh_user_2` (`email`),
	KEY `idx_wh_user_1` (`password`),
	KEY `idx_wh_user_2` (`is_active`),
	KEY `idx_wh_user_3` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_user_meta`
--

DROP TABLE IF EXISTS `webhemi_user_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_meta` (
    `user_id`  INT(10) unsigned NOT NULL,
    `meta_key` VARCHAR(255) NOT NULL,
    `meta`     LONGTEXT NOT NULL,
    PRIMARY KEY (`user_id`, `meta_key`),
    CONSTRAINT `fk_wh_user_meta_1` FOREIGN KEY (`user_id`) REFERENCES `webhemi_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_lock`
--

DROP TABLE IF EXISTS `webhemi_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_lock` (
	`lock_id`   INT(10) unsigned NOT NULL AUTO_INCREMENT,
	`client_ip` VARCHAR(15) NOT NULL,
	`tryings`   INT(10) unsigned NOT NULL DEFAULT '0',
	`time_lock` DATETIME DEFAULT NULL,
	PRIMARY KEY (`lock_id`),
	KEY `idx_wh_lock_1` (`client_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
