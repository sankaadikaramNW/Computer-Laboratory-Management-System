-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: slaf_clms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `allocation_requests`
--

DROP TABLE IF EXISTS `allocation_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allocation_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `allocation_id` int(11) DEFAULT NULL,
  `requester_id` int(11) NOT NULL,
  `type` enum('reschedule','cancel','change_lab') NOT NULL,
  `new_date` date DEFAULT NULL,
  `new_start_time` time DEFAULT NULL,
  `new_end_time` time DEFAULT NULL,
  `new_lab_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewer_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `allocation_id` (`allocation_id`),
  KEY `requester_id` (`requester_id`),
  KEY `new_lab_id` (`new_lab_id`),
  CONSTRAINT `allocation_requests_ibfk_1` FOREIGN KEY (`allocation_id`) REFERENCES `allocations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `allocation_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allocation_requests_ibfk_3` FOREIGN KEY (`new_lab_id`) REFERENCES `laboratories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allocation_requests`
--

LOCK TABLES `allocation_requests` WRITE;
/*!40000 ALTER TABLE `allocation_requests` DISABLE KEYS */;
INSERT INTO `allocation_requests` VALUES (1,NULL,5,'cancel',NULL,NULL,NULL,NULL,'low student count','approved','okay','2026-06-15 03:46:36','2026-06-15 03:47:24'),(2,NULL,5,'cancel',NULL,NULL,NULL,NULL,'sutdebtm','approved','cvbvb','2026-06-15 14:54:04','2026-06-15 14:54:45'),(3,NULL,6,'cancel',NULL,NULL,NULL,NULL,'vfxgbfbh','approved','okay','2026-06-15 15:01:57','2026-06-15 15:02:59');
/*!40000 ALTER TABLE `allocation_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allocations`
--

DROP TABLE IF EXISTS `allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instructor_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `remarks` text DEFAULT NULL,
  `session_status` varchar(50) NOT NULL DEFAULT 'Scheduled',
  `instructor_remarks` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `lab_id` (`lab_id`),
  KEY `completed_by` (`completed_by`),
  CONSTRAINT `allocations_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allocations_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allocations_ibfk_3` FOREIGN KEY (`lab_id`) REFERENCES `laboratories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allocations_ibfk_4` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allocations`
--

LOCK TABLES `allocations` WRITE;
/*!40000 ALTER TABLE `allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 03:37:24'),(2,1,'CREATE_INSTRUCTOR','INSTRUCTORS','::1','Registered instructor profile & account for LAC Lakaraj (51843)','2026-06-15 03:38:48'),(3,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 03:38:52'),(4,NULL,'LOGIN','AUTH','::1','User \'51843\' logged in successfully.','2026-06-15 03:39:13'),(5,NULL,'LOGOUT','AUTH','::1','User \'51843\' logged out.','2026-06-15 03:40:20'),(6,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 03:41:55'),(7,1,'CREATE_INSTRUCTOR','INSTRUCTORS','::1','Registered instructor profile & account for LAC Rathnasooriya (51842)','2026-06-15 03:44:22'),(8,1,'CREATE_ALLOCATION','ALLOCATIONS','::1','Booked lab LAB-01 for instructor LAC Rathnasooriya on 2026-06-15 (10:00-11:00)','2026-06-15 03:45:48'),(9,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 03:45:50'),(10,5,'LOGIN','AUTH','::1','User \'51842\' logged in successfully.','2026-06-15 03:46:01'),(11,5,'SUBMIT_REQUEST','REQUESTS','::1','Submitted cancel request for allocation ID 1','2026-06-15 03:46:36'),(12,5,'LOGOUT','AUTH','::1','User \'51842\' logged out.','2026-06-15 03:46:43'),(13,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 03:46:52'),(14,1,'REVIEW_REQUEST','REQUESTS','::1','Reviewed request ID: 1 as approved. Remarks: okay','2026-06-15 03:47:24'),(15,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 03:47:27'),(16,5,'LOGIN','AUTH','::1','User \'51842\' logged in successfully.','2026-06-15 03:47:35'),(17,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 14:51:05'),(18,1,'CREATE_ALLOCATION','ALLOCATIONS','::1','Booked lab LAB-01 for instructor LAC Rathnasooriya on 2026-06-16 (08:00-10:00)','2026-06-15 14:52:57'),(19,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 14:53:17'),(20,5,'LOGIN','AUTH','::1','User \'51842\' logged in successfully.','2026-06-15 14:53:26'),(21,5,'SUBMIT_REQUEST','REQUESTS','::1','Submitted cancel request for allocation ID 2','2026-06-15 14:54:04'),(22,5,'LOGOUT','AUTH','::1','User \'51842\' logged out.','2026-06-15 14:54:18'),(23,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 14:54:28'),(24,1,'REVIEW_REQUEST','REQUESTS','::1','Reviewed request ID: 2 as approved. Remarks: cvbvb','2026-06-15 14:54:45'),(25,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 14:57:28'),(26,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 14:57:36'),(27,1,'CREATE_INSTRUCTOR','INSTRUCTORS','::1','Registered instructor profile & account for LAC Rashmika (50327)','2026-06-15 14:59:08'),(28,1,'CREATE_LESSON','LESSONS','::1','Created new syllabus lesson: LES-IT-002 - Cyber securtiy','2026-06-15 14:59:52'),(29,1,'CREATE_ALLOCATION','ALLOCATIONS','::1','Booked lab LAB-01 for instructor LAC Rashmika on 2026-06-17 (09:00-10:00)','2026-06-15 15:00:47'),(30,1,'LOGOUT','AUTH','::1','User \'admin\' logged out.','2026-06-15 15:01:05'),(31,6,'LOGIN','AUTH','::1','User \'50327\' logged in successfully.','2026-06-15 15:01:13'),(32,6,'SUBMIT_REQUEST','REQUESTS','::1','Submitted cancel request for allocation ID 3','2026-06-15 15:01:57'),(33,6,'LOGOUT','AUTH','::1','User \'50327\' logged out.','2026-06-15 15:02:05'),(34,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 15:02:33'),(35,1,'REVIEW_REQUEST','REQUESTS','::1','Reviewed request ID: 3 as approved. Remarks: okay','2026-06-15 15:02:59'),(36,1,'LOGIN','AUTH','::1','User \'admin\' logged in successfully.','2026-06-15 15:54:25');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `computers`
--

DROP TABLE IF EXISTS `computers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `computers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_no` varchar(50) NOT NULL,
  `serial_no` varchar(100) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `processor` varchar(50) NOT NULL,
  `ram` varchar(20) NOT NULL,
  `storage` varchar(50) NOT NULL,
  `os` varchar(50) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_status` varchar(100) DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `status` enum('active','faulty','maintenance','removed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_no` (`asset_no`),
  KEY `lab_id` (`lab_id`),
  CONSTRAINT `computers_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `laboratories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `computers`
--

LOCK TABLES `computers` WRITE;
/*!40000 ALTER TABLE `computers` DISABLE KEYS */;
INSERT INTO `computers` VALUES (1,'SLAF-PC-001','SN-9876543210','HP','ProDesk 600 G6','Intel Core i5-10500','16GB','512GB NVMe SSD','Windows 11 Pro','2023-01-15','3 Years Parts & Labor',1,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(2,'SLAF-PC-002','SN-9876543211','HP','ProDesk 600 G6','Intel Core i5-10500','16GB','512GB NVMe SSD','Windows 11 Pro','2023-01-15','3 Years Parts & Labor',1,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(3,'SLAF-PC-003','SN-9876543212','HP','ProDesk 600 G6','Intel Core i5-10500','8GB','512GB NVMe SSD','Windows 10 Pro','2023-01-15','Expired',1,'faulty','2026-06-15 03:31:36','2026-06-15 03:31:36'),(4,'SLAF-PC-004','SN-8876543201','Dell','OptiPlex 5080','Intel Core i7-10700','32GB','1TB SSD','Ubuntu 22.04 LTS','2022-06-20','3 Years Parts & Labor',2,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(5,'SLAF-PC-005','SN-8876543202','Dell','OptiPlex 5080','Intel Core i7-10700','16GB','512GB SSD','Ubuntu 22.04 LTS','2022-06-20','3 Years Parts & Labor',2,'maintenance','2026-06-15 03:31:36','2026-06-15 03:31:36');
/*!40000 ALTER TABLE `computers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fault_reports`
--

DROP TABLE IF EXISTS `fault_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fault_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reported_by` int(11) NOT NULL,
  `equipment_type` enum('computer','smart_board','network','other') NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('reported','in_progress','resolved','closed') DEFAULT 'reported',
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  CONSTRAINT `fault_reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fault_reports`
--

LOCK TABLES `fault_reports` WRITE;
/*!40000 ALTER TABLE `fault_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `fault_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructors`
--

DROP TABLE IF EXISTS `instructors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service_no` varchar(20) NOT NULL,
  `rank` varchar(20) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `trade` varchar(50) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `photo_uploaded_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_no` (`service_no`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `instructors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructors`
--

LOCK TABLES `instructors` WRITE;
/*!40000 ALTER TABLE `instructors` DISABLE KEYS */;
INSERT INTO `instructors` VALUES (1,2,'S-12345','SGT','Wijesinghe W.M.','IT Specialist','0771234567','sgt.wijesinghe@slaf.lk',NULL,NULL,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(2,3,'S-54321','FG OFF','Perera K.A.','Signals & IT','0719876543','fg.perera@slaf.lk',NULL,NULL,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(4,5,'51842','LAC','Rathnasooriya','comptech','0711911297','sankaadikara@gmail.com',NULL,NULL,'active','2026-06-15 03:44:22','2026-06-15 03:44:22'),(5,6,'50327','LAC','Rashmika','Comp tech','07545565','research.itw@gmail.com',NULL,NULL,'active','2026-06-15 14:59:08','2026-06-15 14:59:08');
/*!40000 ALTER TABLE `instructors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratories`
--

DROP TABLE IF EXISTS `laboratories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laboratories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_code` varchar(20) NOT NULL,
  `lab_name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_code` (`lab_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratories`
--

LOCK TABLES `laboratories` WRITE;
/*!40000 ALTER TABLE `laboratories` DISABLE KEYS */;
INSERT INTO `laboratories` VALUES (1,'LAB-01','Primary Computing Laboratory','Main Block, Ground Floor',30,'Equipped with Core i5 desktops, projector, and high-speed network.','active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(2,'LAB-02','Advanced Networking Lab','Main Block, First Floor',20,'Equipped with Cisco routers, switches, and network simulation setups.','active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(3,'LAB-03','Hardware & Electronics Lab','Technical Hangar Block',15,'Equipped with logic design boards, soldering stations, and diagnostic PCs.','active','2026-06-15 03:31:36','2026-06-15 03:31:36');
/*!40000 ALTER TABLE `laboratories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_code` varchar(20) NOT NULL,
  `lesson_name` varchar(150) NOT NULL,
  `trade` varchar(50) NOT NULL,
  `duration` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `lesson_code` (`lesson_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (1,'LES-IT01','Introduction to Hardware Maintenance','IT Specialist',180,'Basic concepts of troubleshooting, assembly, and PC upgrades.','2026-06-15 03:31:36','2026-06-15 03:31:36'),(2,'LES-NET02','Routing Protocols Configuration','Signals & IT',240,'In-depth lecture and practical labs on OSPF, EIGRP, and BGP routing.','2026-06-15 03:31:36','2026-06-15 03:31:36'),(3,'LES-SEC03','Basic Cybersecurity Practices','All Trades',120,'Military cyber security guidelines, threat classification, and password hygiene.','2026-06-15 03:31:36','2026-06-15 03:31:36'),(4,'LES-IT-002','Cyber securtiy','Comp tech',45,'','2026-06-15 14:59:52','2026-06-15 14:59:52');
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(50) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_records`
--

DROP TABLE IF EXISTS `maintenance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipment_type` enum('computer','smart_board','network','other') NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `issue_type` varchar(100) NOT NULL,
  `assigned_technician` varchar(100) NOT NULL,
  `repair_date` date NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_records`
--

LOCK TABLES `maintenance_records` WRITE;
/*!40000 ALTER TABLE `maintenance_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `published_by` int(11) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `published_by` (`published_by`),
  CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `type` varchar(50) NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,5,'Scheduled for \'LES-IT01 - Introduction to Hardware Maintenance\' in Laboratory \'LAB-01\' on 15 Jun 2026 from 10:00 to 11:00.',0,'schedule',1,'2026-06-15 03:45:48'),(2,5,'Your cancel request for \'Introduction to Hardware Maintenance\' was approved by Admin. Remarks: okay',0,'request_update',1,'2026-06-15 03:47:24'),(3,5,'Scheduled for \'LES-IT01 - Introduction to Hardware Maintenance\' in Laboratory \'LAB-01\' on 16 Jun 2026 from 08:00 to 10:00.',0,'schedule',2,'2026-06-15 14:52:57'),(4,5,'Your cancel request for \'Introduction to Hardware Maintenance\' was approved by Admin. Remarks: cvbvb',0,'request_update',2,'2026-06-15 14:54:45'),(5,6,'Scheduled for \'LES-IT-002 - Cyber securtiy\' in Laboratory \'LAB-01\' on 17 Jun 2026 from 09:00 to 10:00.',0,'schedule',3,'2026-06-15 15:00:46'),(6,6,'Your cancel request for \'Cyber securtiy\' was approved by Admin. Remarks: okay',0,'request_update',3,'2026-06-15 15:02:59');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_history`
--

LOCK TABLES `password_history` WRITE;
/*!40000 ALTER TABLE `password_history` DISABLE KEYS */;
INSERT INTO `password_history` VALUES (2,5,'$2y$10$zJAgAjNMbPGo/6/QAjZUB.Gt1YdR2nf7AX8HyfN2PRvPkXEDk.Aza','2026-06-15 03:44:22'),(3,6,'$2y$10$vxj3Xz34qXkiNjdXUGRUiOnbQTpEpzS6JARYO1A5GORP6CIxRwI.O','2026-06-15 14:59:08');
/*!40000 ALTER TABLE `password_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage_users','Ability to create, update, lock user accounts'),(2,'manage_instructors','Ability to manage instructor service records'),(3,'manage_laboratories','Ability to add, edit, or deactivate laboratory rooms'),(4,'manage_equipment','Ability to manage computers and smart boards'),(5,'manage_lessons','Ability to manage syllabus lessons'),(6,'manage_allocations','Ability to schedule and allocate labs for instructors'),(7,'view_allocations','Ability to view laboratory calendar and scheduling'),(8,'manage_requests','Ability to approve or reject change requests'),(9,'submit_requests','Ability to submit date/time change requests'),(10,'report_faults','Ability to submit fault reports for equipment'),(11,'manage_faults','Ability to handle and update status of fault tickets'),(12,'manage_maintenance','Ability to schedule technicians and repairs'),(13,'view_reports','Ability to generate lab utilization and instructor workload logs'),(14,'view_audit_logs','Ability to monitor system audit records');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(2,7),(2,9),(2,10);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator','Full system access and control','2026-06-15 03:31:36'),(2,'Instructor','Access to own lessons, allocations, notice board, and fault reporting','2026-06-15 03:31:36');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smart_boards`
--

DROP TABLE IF EXISTS `smart_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smart_boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `installation_date` date DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `status` enum('active','faulty','maintenance','removed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `lab_id` (`lab_id`),
  CONSTRAINT `smart_boards_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `laboratories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smart_boards`
--

LOCK TABLES `smart_boards` WRITE;
/*!40000 ALTER TABLE `smart_boards` DISABLE KEYS */;
INSERT INTO `smart_boards` VALUES (1,'SLAF-SB-001','Promethean','ActivPanel 9','2023-03-10',1,'active','2026-06-15 03:31:36','2026-06-15 03:31:36'),(2,'SLAF-SB-002','Smart Technologies','MX275-V3','2024-02-18',2,'active','2026-06-15 03:31:36','2026-06-15 03:31:36');
/*!40000 ALTER TABLE `smart_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES ('max_login_attempts','5','Max failed login attempts before account locking','2026-06-15 03:31:36'),('org_name','Trade Training School Ekala','Parent Organization Name','2026-06-15 03:31:36'),('session_timeout','1800','Session Timeout duration in seconds','2026-06-15 03:31:36'),('system_name','SLAF CLMS','System Name Displayed on Title and Branding','2026-06-15 03:31:36');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_password_change` datetime DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `force_password_change` tinyint(1) DEFAULT 0,
  `password_expiry_days` int(11) DEFAULT 90,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$5d7u2n2edWUnxvi2PN0dW.ZLa0F/BkHMC9zgHO7ZFoPba/6J0ykgm',1,'active','2026-06-15 21:24:25','2026-06-15 03:31:36','2026-06-15 15:54:25','2026-06-15 09:01:36',0,0,90),(2,'sgt.wijesinghe','$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.',2,'active',NULL,'2026-06-15 03:31:36','2026-06-15 03:31:36','2026-06-15 09:01:36',0,0,90),(3,'fg.perera','$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.',2,'active',NULL,'2026-06-15 03:31:36','2026-06-15 03:31:36','2026-06-15 09:01:36',0,0,90),(5,'51842','$2y$10$zJAgAjNMbPGo/6/QAjZUB.Gt1YdR2nf7AX8HyfN2PRvPkXEDk.Aza',2,'active','2026-06-15 20:23:26','2026-06-15 03:44:22','2026-06-15 14:53:26','2026-06-15 09:14:22',0,0,90),(6,'50327','$2y$10$vxj3Xz34qXkiNjdXUGRUiOnbQTpEpzS6JARYO1A5GORP6CIxRwI.O',2,'active','2026-06-15 20:31:13','2026-06-15 14:59:08','2026-06-15 15:01:13','2026-06-15 20:29:08',0,0,90);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-18  8:21:07
