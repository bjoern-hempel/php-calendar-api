-- MariaDB dump 10.19  Distrib 10.7.7-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: php-calendar-api
-- ------------------------------------------------------
-- Server version	10.7.3-MariaDB-1:10.7.3+maria~focal-log

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
-- Table structure for table `place_u`
--

DROP TABLE IF EXISTS `place_u`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `place_u` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `geoname_id` int(11) NOT NULL,
  `name` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ascii_name` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_names` varchar(4096) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinate` point NOT NULL COMMENT '(DC2Type:point)',
  `feature_class` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc2` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `population` bigint(20) DEFAULT NULL,
  `elevation` int(11) DEFAULT NULL,
  `dem` int(11) DEFAULT NULL,
  `timezone` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modification_date` date NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `admin1_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin2_code` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin3_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin4_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `coordinate_place_u` (`coordinate`)
) ENGINE=InnoDB AUTO_INCREMENT=678989 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place_u`
--

LOCK TABLES `place_u` WRITE;
/*!40000 ALTER TABLE `place_u` DISABLE KEYS */;
INSERT INTO `place_u` VALUES
(200417,7283696,'HVDC Baltic Cable - Cathode','HVDC Baltic Cable - Cathode','','\0\0\0\0\0\0\0œ†°K@πàÔƒ¨G&@','U','RDGU','DE','',0,0,-9999,'Europe/Berlin','2010-03-24','2022-05-09 17:14:56','2022-05-09 17:14:56','12',NULL,NULL,NULL),
(334406,8081323,'Isla del Descubridor','Isla del Descubridor','','\0\0\0\0\0\0\0ÒcÃ]K@C@ÊÿGßÆ¿ø','U','SMU','ES','',0,0,0,'Europe/Madrid','2011-12-12','2022-05-10 01:13:10','2022-05-10 01:13:10','60','A','03031',NULL),
(497149,8739859,'Cafe Grappa','Cafe Grappa','','\0\0\0\0\0\0\0µ7¯¬dJ@ôd‰,Ù4@','U','CRSU','PL','',0,0,109,'Europe/Warsaw','2014-03-16','2022-05-10 18:46:35','2022-05-10 18:46:35','78','1465','146501',NULL),
(637478,10345399,'Gisse 100 m ude','Gisse 100 m ude','','\0\0\0\0\0\0\0ÔÊ©πΩK@\\¨®¡§,@','U','BNKU','SE','',0,0,-9999,'Europe/Stockholm','2015-11-02','2022-05-11 20:06:41','2022-05-11 20:06:41','27','1291','129101','1167'),
(645516,2614545,'R√∏nnen','Ronnen','','\0\0\0\0\0\0\0>\"¶D¬K@º\"¯ﬂJ&&@','U','SHLU','DK','',0,0,-9999,'Europe/Copenhagen','2017-09-12','2022-05-12 15:07:12','2022-05-12 15:07:12','20','326',NULL,NULL),
(649585,2619259,'Jordsand','Jordsand','Jordsand','\0\0\0\0\0\0\0nã2dÇK@}ë–ñs!@','U','BNKU','DK','',0,0,-9999,'Europe/Copenhagen','2018-05-27','2022-05-12 15:40:10','2022-05-12 15:40:10','21','550',NULL,NULL),
(656177,10345377,'Timannevej 100 m ude','Timannevej 100 m ude','','\0\0\0\0\0\0\0#Û»L@\'à∫@z(@','U','BKSU','DK','',0,0,-9999,'Europe/Copenhagen','2015-06-22','2022-05-12 16:31:27','2022-05-12 16:31:27','17',NULL,NULL,NULL),
(656178,10345378,'R√•geleje 100 m ude','Rageleje 100 m ude','','\0\0\0\0\0\0\0\"\Z›AÏL@rßt∞˛O(@','U','BKSU','DK','',0,0,-9999,'Europe/Copenhagen','2015-06-22','2022-05-12 16:31:27','2022-05-12 16:31:27','17','270',NULL,NULL),
(656179,10345379,'Tisvilleleje 100 m ude','Tisvilleleje 100 m ude','','\0\0\0\0\0\0\0∂J∞8L@i∆¢È(@','U','BNKU','DK','',0,0,-9999,'Europe/Copenhagen','2015-06-22','2022-05-12 16:31:28','2022-05-12 16:31:28','17',NULL,NULL,NULL),
(656180,10345400,'R√∏dvig 100 m ude','Rodvig 100 m ude','','\0\0\0\0\0\0\0j\'˜;†K@ñ	ø‘œÀ(@','U','BNKU','DK','',0,0,-9999,'Europe/Copenhagen','2015-06-22','2022-05-12 16:31:28','2022-05-12 16:31:28','20','336',NULL,NULL),
(656181,10345413,'Busende have 100 m ude','Busende have 100 m ude','','\0\0\0\0\0\0\0ï∑#úxK@ÕÈ≤òÿ)@','U','BNKU','DK','',0,0,-9999,'Europe/Copenhagen','2017-06-22','2022-05-12 16:31:29','2022-05-12 16:31:29','20','390',NULL,NULL),
(678986,2522617,'Graham Island','Graham Island','Banco Graham,Banco Grahm,Ferdinandea Bank,Ferdinandea Island,Giulia-Ferdinandeo Bank,Graham Bank,Graham Island,Graham Shoal,Graham\'s Reef,Graham‚Äôs Reef,Isola Ferdinandea,Isola Giulia,Isola Gi√∫lia,Isola Gullia,Julia Bank,Julia Island','\0\0\0\0\0\0\0C9—ÆBíB@¡ãæÇ4√)@','U','SHLU','IT','',0,0,-9999,'','2021-04-27','2023-05-24 23:13:37','2023-05-24 23:13:37','15',NULL,NULL,NULL),
(678987,6621072,'Secca Missipezza','Secca Missipezza','','\0\0\0\0\0\0\0àÖZ”ºD@G ^◊/x2@','U','SHLU','IT','',0,0,-9999,'Europe/Rome','2008-01-05','2023-05-25 01:39:13','2023-05-25 01:39:13','13','LE',NULL,NULL),
(678988,7534683,'Faraglioni','Faraglioni','','\0\0\0\0\0\0\0ËŸ¨˙\\ED@˝ˆu‡úÅ,@','U','PKSU','IT','',0,0,-9999,'Europe/Rome','2010-09-11','2023-05-25 01:54:48','2023-05-25 01:54:48','04',NULL,NULL,NULL);
/*!40000 ALTER TABLE `place_u` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
