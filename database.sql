/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: digital_health_db
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

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
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duty_schedules`
--

DROP TABLE IF EXISTS `duty_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `duty_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` varchar(150) NOT NULL,
  `staff_position` varchar(150) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `duty_date` date NOT NULL,
  `shift_type` enum('morning','afternoon','night','full_day','on_call') DEFAULT 'morning',
  `shift_name` varchar(100) DEFAULT 'เวรเช้า (08:30 - 16:30 น.)',
  `phone` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `duty_date` (`duty_date`),
  KEY `shift_type` (`shift_type`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `duty_schedules`
--

LOCK TABLES `duty_schedules` WRITE;
/*!40000 ALTER TABLE `duty_schedules` DISABLE KEYS */;
INSERT INTO `duty_schedules` VALUES
(1,'นพ. ศุภชัย วิทยานุกูล','แพทย์เวรประจำศูนย์','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-10-30','morning','เวรเช้า (08:30 - 16:30 น.)','081-456-7890','ตรวจปรึกษา Telemedicine คลินิกออนไลน์ทั่วไป','2026-10-30 15:09:34'),
(2,'พว. ศิริพร บุญเจริญ','พยาบาลวิชาชีพชำนาญการ','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-10-30','morning','เวรเช้า (08:30 - 16:30 น.)','089-123-4567','คัดกรองอาการและประสานงานส่งต่อผู้ป่วย','2026-10-30 15:09:34'),
(3,'นายธนกฤต มั่งมี','วิศวกรความปลอดภัยไซเบอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-10-30','morning','เวรเช้า (08:30 - 16:30 น.)','085-999-1122','ดูแลความเสถียรของเกตเวย์เชื่อมต่อข้อมูล รพ.','2026-10-30 15:09:34'),
(4,'นายเอกภาพ วงศ์สวรรค์','นักวิเคราะห์ระบบเครือข่าย','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-10-30','afternoon','เวรบ่าย (16:30 - 00:30 น.)','084-222-3344','เฝ้าระวังการโจมตีทางไซเบอร์และแจ้งเตือนภัย','2026-10-30 15:09:34'),
(5,'นายธีระเดช อุดมทรัพย์','เจ้าหน้าที่ปฏิบัติการระบบเซิร์ฟเวอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-10-30','night','เวรดึก (00:30 - 08:30 น.)','086-777-8899','สำรองข้อมูลแบ็กอัปและตรวจสอบ Log ระบบ','2026-10-30 15:09:34'),
(6,'พญ. มนทิรา ชินโชติ','แพทย์เวรเฉพาะทาง','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-10-30','on_call','เวรออนคอลฉุกเฉิน (24 ชม.)','081-333-5566','พร้อมรับสายเคสส่งต่อด่วนผ่านระบบฉุกเฉิน','2026-10-30 15:09:34'),
(7,'นพ. ศุภชัย วิทยานุกูล','แพทย์เวรประจำศูนย์','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-10-31','morning','เวรเช้า (08:30 - 16:30 น.)','081-456-7890','คลินิกโรคเรื้อรัง NCDs ทางไกล','2026-10-30 15:09:34'),
(8,'พว. กัลยาภรณ์ จิตต์อารี','พยาบาลวิชาชีพ','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-10-31','morning','เวรเช้า (08:30 - 16:30 น.)','083-444-5566','ระบบคิวตรวจออนไลน์','2026-10-30 15:09:34'),
(9,'นายอนุชา ทองดี','นักวิชาการคอมพิวเตอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-10-31','afternoon','เวรบ่าย (16:30 - 00:30 น.)','082-111-9988','ดูแลความต่อเนื่องของระบบ Health ID','2026-10-30 15:09:34'),
(10,'นายธีระเดช อุดมทรัพย์','เจ้าหน้าที่ปฏิบัติการระบบเซิร์ฟเวอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-10-31','night','เวรดึก (00:30 - 08:30 น.)','086-777-8899','ตรวจสอบเซิร์ฟเวอร์คลาวด์','2026-10-30 15:09:34'),
(11,'พญ. จารุวรรณ เกียรติสกุล','แพทย์เวรประจำศูนย์','ฝ่ายระบบการแพทย์ทางไกล (Telemedicine)','2026-11-01','morning','เวรเช้า (08:30 - 16:30 น.)','087-654-3210','คลินิกให้คำปรึกษาสุขภาพจิตดิจิทัล','2026-10-30 15:09:34'),
(12,'นายเอกภาพ วงศ์สวรรค์','นักวิเคราะห์ระบบเครือข่าย','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-11-01','morning','เวรเช้า (08:30 - 16:30 น.)','084-222-3344','ซ่อมบำรุงระบบโครงสร้างพื้นฐานประจำสัปดาห์','2026-10-30 15:09:34'),
(13,'นายธนกฤต มั่งมี','วิศวกรความปลอดภัยไซเบอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-11-01','afternoon','เวรบ่าย (16:30 - 00:30 น.)','085-999-1122','เฝ้าระวังระบบความปลอดภัย','2026-10-30 15:09:34'),
(14,'นายอนุชา ทองดี','นักวิชาการคอมพิวเตอร์','ศูนย์เฝ้าระวังไซเบอร์ (NOC/SOC 24/7)','2026-11-01','night','เวรดึก (00:30 - 08:30 น.)','082-111-9988','เวรดึกศูนย์ NOC','2026-10-30 15:09:34');
/*!40000 ALTER TABLE `duty_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `executives`
--

DROP TABLE IF EXISTS `executives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `executives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `position` varchar(150) NOT NULL,
  `department` varchar(150) DEFAULT NULL,
  `level` int(11) DEFAULT 1 COMMENT '1=ผู้บริหารสูงสุด, 2=รองผู้อำนวยการ, 3=หัวหน้าฝ่าย/กลุ่มงาน',
  `image_url` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `executives`
--

LOCK TABLES `executives` WRITE;
/*!40000 ALTER TABLE `executives` DISABLE KEYS */;
INSERT INTO `executives` VALUES
(1,'นพ. เกรียงศักดิ์ วิจิตรสวัสดิ์','ผู้อำนวยการศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล','สำนักบริหารยุทธศาสตร์สุขภาพดิจิทัล',1,'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80','kriangsak.w@digitalhealth.moph.go.th','02-590-3101','ผู้เชี่ยวชาญด้านเวชศาสตร์สารสนเทศ (Medical Informatics) ประสบการณ์พัฒนาระบบสาธารณสุขดิจิทัลกว่า 20 ปี',1,'2026-10-30 14:29:53'),
(2,'พญ. นภัสสร วงศ์ประเสริฐ','รองผู้อำนวยการฝ่ายพัฒนานวัตกรรมและเทคโนโลยีการแพทย์','กลุ่มงานนวัตกรรมการแพทย์และ Telemedicine',2,'https://images.unsplash.com/photo-1594824813583-b9b25a9526f8?auto=format&fit=crop&w=600&q=80','napassorn.w@digitalhealth.moph.go.th','02-590-3102','ผู้นำโครงการระบบบริการการแพทย์ทางไกลและระบบปัญญาประดิษฐ์เพื่อช่วยคัดกรองผู้ป่วยระดับปฐมภูมิ',2,'2026-10-30 14:29:53'),
(3,'ดร. ธนวัฒน์ ศิริพงษ์พาณิชย์','รองผู้อำนวยการฝ่ายโครงสร้างพื้นฐานและสารสนเทศสุขภาพ','กลุ่มงานโครงสร้างพื้นฐานคลาวด์และไซเบอร์ซีเคียวริตี้',2,'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=600&q=80','thanawat.s@digitalhealth.moph.go.th','02-590-3103','ผู้เชี่ยวชาญด้าน Big Data Architecture, Cloud Computing และ Cybersecurity มาตรฐานสากล ISO/IEC 27001',3,'2026-10-30 14:29:53'),
(4,'นายปริญญา ชัยมงคล','หัวหน้ากลุ่มงานเทคโนโลยีสารสนเทศและระบบเวชระเบียน','กลุ่มงานระบบเวชระเบียนอิเล็กทรอนิกส์ (EHR)',3,'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80','parinya.c@digitalhealth.moph.go.th','02-590-3201','รับผิดชอบระบบเชื่อมต่อฐานข้อมูลเวชระเบียนและการแลกเปลี่ยนข้อมูลตามมาตรฐาน HL7 FHIR',4,'2026-10-30 14:29:53'),
(5,'นางสาวรุจิรา พิริยะกุล','หัวหน้ากลุ่มงานความมั่นคงปลอดภัยสารสนเทศและคุ้มครองข้อมูล','กลุ่มงานไซเบอร์ซีเคียวริตี้และ PDPA สาธารณสุข',3,'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80','rujira.p@digitalhealth.moph.go.th','02-590-3202','ผู้รับผิดชอบการปฏิบัติตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA) และการตรวจประเมินความปลอดภัยระบบสุขภาพ',5,'2026-10-30 14:29:53'),
(6,'นายแพทย์อัศวิน จิตต์อำไพ','หัวหน้ากลุ่มงานส่งเสริมและวิจัยปัญญาประดิษฐ์ทางการแพทย์','กลุ่มงาน AI Healthcare Research & Analytics',3,'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80','asawin.j@digitalhealth.moph.go.th','02-590-3203','วิจัยและพัฒนาโมเดล AI ในการอ่านภาพรังสีทรวงอกและการประเมินความเสี่ยงโรคหัวใจและหลอดเลือด',6,'2026-10-30 14:29:53'),
(7,'นางสาวชนัญชิดา สิทธิโชค','หัวหน้ากลุ่มงานสื่อสารองค์กรและประชาสัมพันธ์สุขภาพดิจิทัล','กลุ่มงานสื่อสารและภาพลักษณ์องค์กร',3,'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80','chananchida.s@digitalhealth.moph.go.th','02-590-3204','วางแผนกลยุทธ์การสื่อสารสาธารณะและเผยแพร่สาระความรู้ด้านสุขภาพดิจิทัลสู่ประชาชน',7,'2026-10-30 14:29:53');
/*!40000 ALTER TABLE `executives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hero_banners`
--

DROP TABLE IF EXISTS `hero_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `badge_text` varchar(100) DEFAULT 'ข่าวประชาสัมพันธ์',
  `button_text` varchar(100) DEFAULT 'อ่านเพิ่มเติม',
  `button_link` varchar(255) DEFAULT '#',
  `image_url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_banners`
--

LOCK TABLES `hero_banners` WRITE;
/*!40000 ALTER TABLE `hero_banners` DISABLE KEYS */;
INSERT INTO `hero_banners` VALUES
(4,'','','','','#','uploads/banners/banners_1793346823_42332093.jpg',0,1,'2026-10-30 14:53:43'),
(5,'','','','','#','uploads/banners/banners_1793346849_a9e94d44.jpg',0,1,'2026-10-30 14:54:09');
/*!40000 ALTER TABLE `hero_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',
  `views` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `is_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES
(1,1,'ศูนย์สุขภาพดิจิทัลจับมือโรงพยาบาลทั่วประเทศ เปิดใช้ระบบเชื่อมโยงประวัติสุขภาพแบบไร้รอยต่อ','กระทรวงสาธารณสุขร่วมกับศูนย์สุขภาพดิจิทัล เปิดตัวโครงการเชื่อมโยงข้อมูลสุขภาพดิจิทัลทั่วประเทศ ยกระดับการส่งต่อผู้ป่วยและลดความซ้ำซ้อนในการตรวจทางห้องปฏิบัติการ','<p>กระทรวงสาธารณสุขร่วมกับศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล จัดพิธีลงนามบันทึกข้อตกลงความร่วมมือ (MOU) ยกระดับระบบสุขภาพสู่ระบบ \"สุขภาพดิจิทัลเต็มรูปแบบ\" มุ่งเน้นการเชื่อมโยงระบบเวชระเบียนอิเล็กทรอนิกส์ (Electronic Health Record - EHR) ข้ามสถานพยาบาลแบบเรียลไทม์</p>\n                <p>การพัฒนาระบบในครั้งนี้ช่วยให้แพทย์สามารถเรียกดูประวัติการแพ้ยา ประวัติการผ่าตัด และผลแล็บย้อนหลังได้ทันทีเมื่อผู้ป่วยเข้ารับการรักษาฉุกเฉินต่างพื้นที่ ภายใต้การควบคุมสิทธิความเป็นส่วนตัวตามมาตรฐาน PDPA</p>\n                <h4>เป้าหมายสำคัญของโครงการ:</h4>\n                <ul>\n                    <li>ครอบคลุมโรงพยาบาลศูนย์และโรงพยาบาลทั่วไป 100% ภายในปี 2569</li>\n                    <li>รองรับการยืนยันตัวตนด้วย Digital Health ID สะดวกรวดเร็วและปลอดภัย</li>\n                    <li>ลดระยะเวลารอคอยในโรงพยาบาลเฉลี่ยมากกว่า 45%</li>\n                </ul>','https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',1422,1,'2026-10-30 14:29:53','2026-10-30 14:29:53'),
(2,2,'ยกระดับบริการ Telemedicine ชุมชนห่างไกล เข้าถึงแพทย์เฉพาะทางไม่ต้องเดินทางข้ามจังหวัด','เปิดศูนย์แพทย์ทางไกลชุมชน นำเทคโนโลยีจอสื่อสารความละเอียดสูงและอุปกรณ์ตรวจร่างกายระบบคลาวด์ เสริมศักยภาพ รพ.สต. ทั่วประเทศ','<p>โครงการขยายจุดบริการ Telemedicine สู่โรงพยาบาลส่งเสริมสุขภาพตำบล (รพ.สต.) ช่วยให้ผู้ป่วยโรคไม่ติดต่อเรื้อรัง (NCDs) เช่น เบาหวาน ความดันโลหิตสูง และโรคหัวใจ สามารถพบแพทย์ผู้เชี่ยวชาญจากโรงพยาบาลศูนย์ได้โดยตรงผ่านระบบวิดีโอคอลความเร็วสูง</p>\n                <p>โดยมีเจ้าหน้าที่สาธารณสุขประจำพื้นที่คอยดูแลวัดสัญญาณชีพ และส่งข้อมูลขึ้นระบบคลาวด์เพื่อการวินิจฉัยที่แม่นยำ พร้อมทั้งมีระบบบริการจัดส่งยาถึงบ้านภายใน 24-48 ชั่วโมง</p>','https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',982,1,'2026-10-30 14:29:53','2026-10-30 14:29:53'),
(3,3,'ประกาศแนวปฏิบัติความมั่นคงปลอดภัยไซเบอร์ด้านสาธารณสุข (Health Cybersecurity Framework 2026)','กำหนดกรอบมาตรฐานความปลอดภัยข้อมูลสุขภาพ การป้องกันการโจมตีแบบ Ransomware และการเข้ารหัสข้อมูลเวชระเบียนขั้นสูง','<p>เพื่อสร้างความมั่นใจสูงสุดให้แก่ประชาชนและบุคลากรทางการแพทย์ ศูนย์สุขภาพดิจิทัลได้ออกประกาศข้อกำหนดและแนวทางปฏิบัติทางด้านความมั่นคงปลอดภัยทางไซเบอร์สำหรับสถานพยาบาลทุกระดับ</p>\n                <p>มาตรการสำคัญประกอบด้วยการตรวจสอบสิทธิ์การเข้าถึงแบบ Multi-Factor Authentication (MFA), การสำรองข้อมูลแบบแยกระบบ (Immutable Backup) และการจัดตั้งศูนย์เฝ้าระวังภัยคุกคามไซเบอร์ด้านสาธารณสุขตลอด 24 ชั่วโมง</p>','https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',745,1,'2026-10-30 14:29:53','2026-10-30 14:29:53'),
(4,4,'เปิดรับสมัครอบรมหลักสูตร \"ปัญญาประดิษฐ์ทางการแพทย์และเทคโนโลยีสุขภาพดิจิทัลสำหรับบุคลากร\"','ขอเชิญแพทย์ พยาบาล นักวิชาการคอมพิวเตอร์ และบุคลากรสาธารณสุข เข้าร่วมอบรมหลักสูตรเสริมทักษะ AI and Data Analytics in Healthcare ฟรีไม่มีค่าใช้จ่าย','<p>ศูนย์สุขภาพดิจิทัลขอเชิญบุคลากรทางการแพทย์และสาธารณสุขเข้าร่วมการอบรมเชิงปฏิบัติการ เพื่อเสริมสร้างความรู้ความเข้าใจเกี่ยวกับการนำ AI มาช่วยคัดกรองโรคเบื้องต้น การบริหารจัดการคลังข้อมูลสุขภาพ (Health Big Data) และการใช้งานระบบ AI Assistant ในคลินิก</p>','https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',610,0,'2026-10-30 14:29:53','2026-10-30 14:29:53'),
(5,1,'แอปพลิเคชัน \"Smart Health Pass\" อัปเดตฟังก์ชันใหม่ แจ้งเตือนฉีดวัคซีนและผลตรวจแล็บอัตโนมัติ','เพิ่มความสะดวกแก่ประชาชนในการติดตามสุขภาพของตนเองและคนในครอบครัว มีระบบสรุปผลตรวจสุขภาพรายปีในรูปแบบ Infographic เข้าใจง่าย','<p>เวอร์ชันล่าสุดของแอปพลิเคชัน Smart Health Pass ได้เพิ่มฟีเจอร์การแจ้งเตือนรอบนัดหมายแพทย์ การคำนวณดัชนีสุขภาพส่วนบุคคล และการเชื่อมต่อกับสมาร์ทวอทช์เพื่อบันทึกก้าวเดินและอัตราการเต้นของหัวใจแบบอัตโนมัติ</p>','https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',520,0,'2026-10-30 14:29:53','2026-10-30 14:29:53'),
(6,5,'ประกาศประกวดราคาซื้อระบบจัดเก็บและวิเคราะห์ภาพถ่ายทางการแพทย์ผ่านคลาวด์ (Cloud PACS) ด้วยวิธี e-Bidding','ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล มีความประสงค์จะประกวดราคาซื้อระบบจัดเก็บและรับส่งภาพทางการแพทย์ดิจิทัล เพื่อรองรับโรงพยาบาลเครือข่าย','<p>เอกสารประกวดราคาและข้อกำหนดขอบเขตงาน (TOR) สามารถดาวน์โหลดได้ทางเว็บไซต์ระบบการจัดซื้อจัดจ้างภาครัฐ หรือติดต่อสอบถามรายละเอียดได้ที่กลุ่มงานพัสดุและจัดซื้อจัดจ้าง</p>','https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80','ฝ่ายประชาสัมพันธ์สุขภาพดิจิทัล',312,0,'2026-10-30 14:29:53','2026-10-30 14:29:53');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_categories`
--

DROP TABLE IF EXISTS `news_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_categories`
--

LOCK TABLES `news_categories` WRITE;
/*!40000 ALTER TABLE `news_categories` DISABLE KEYS */;
INSERT INTO `news_categories` VALUES
(1,'ข่าวประชาสัมพันธ์ทั่วไป','general-pr'),
(2,'นวัตกรรมและเทคโนโลยีการแพทย์','health-tech'),
(3,'นโยบายและความปลอดภัยไซเบอร์','policy-security'),
(4,'กิจกรรมและการฝึกอบรมบุคลากร','events-training'),
(5,'ประกาศและจัดซื้อจัดจ้าง','procurement');
/*!40000 ALTER TABLE `news_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-solid fa-laptop-medical',
  `badge` varchar(50) DEFAULT 'บริการออนไลน์',
  `link_url` varchar(255) DEFAULT '#',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES
(1,'ระบบบัตรสุขภาพดิจิทัล (Digital Health ID)','ยืนยันตัวตนดิจิทัลเพื่อเข้าถึงประวัติการรักษา นัดหมายแพทย์ และขอรับยาได้ทั่วประเทศอย่างปลอดภัย','fa-solid fa-id-card-clip','บริการยอดนิยม','#',1,1),
(2,'บริการแพทย์ทางไกล (Telemedicine Portal)','พบแพทย์เฉพาะทางผ่านวิดีโอคอลความคมชัดสูง สะดวกรวดเร็ว ไม่ต้องเดินทางไกล พร้อมส่งยาถึงบ้าน','fa-solid fa-video','บริการออนไลน์','#',2,1),
(3,'ระบบสมุดสุขภาพครอบครัว (Personal Health Record)','เก็บบันทึกผลตรวจสุขภาพ ประวัติการรับวัคซีน และกราฟพัฒนาการสุขภาพส่วนบุคคลและคนในครอบครัว','fa-solid fa-heart-pulse','สุขภาพแม่นยำ','#',3,1),
(4,'ระบบ AI ประเมินอาการเบื้องต้น (AI Health Symptom Checker)','ระบบปัญญาประดิษฐ์อัจฉริยะช่วยประเมินอาการเบื้องต้น แนะนำแนวทางการปฏิบัติตน และส่งต่อคลินิกที่เหมาะสม','fa-solid fa-robot','เทคโนโลยี AI','#',4,1),
(5,'ระบบนัดหมายออนไลน์และคิวอัจฉริยะ (Smart Queue & Appointment)','จองคิวตรวจล่วงหน้า แจ้งเตือนเวลาเรียกลำดับคิวแบบเรียลไทม์ ลดระยะเวลารอคอยในโรงพยาบาล','fa-solid fa-calendar-check','สะดวก รวดเร็ว','#',5,1),
(6,'ระบบส่งต่อผู้ป่วยฉุกเฉินอัจฉริยะ (Smart Referral & Emergency)','ส่งต่อข้อมูลเวชระเบียนแบบเร่งด่วน ประสานงานศูนย์กู้ชีพและห้องฉุกเฉิน เพิ่มโอกาสรอดชีวิต','fa-solid fa-truck-medical','24 ชั่วโมง','#',6,1);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES
('admin_2fa_email','Khthawuth.leo@gmail.com'),
('contact_address','อาคารศูนย์สุขภาพดิจิทัลและเทคโนโลยีการแพทย์ กระทรวงสาธารณสุข ถนนติวานนท์ อำเภอเมือง จังหวัดนนทบุรี 11000'),
('contact_email','Khthawuth.leo@gmail.com'),
('contact_phone','02-590-3000 หรือสายด่วน 1422'),
('site_name','กลุ่มงานสุขภาพดิจิทัล (Hospital Software & Digital Health Division)'),
('site_short_name','กลุ่มงานสุขภาพดิจิทัล'),
('site_tagline','ทีมโปรแกรมเมอร์และพัฒนาระบบสารสนเทศสุขภาพดิจิทัลโรงพยาบาล'),
('working_hours','จันทร์ - ศุกร์: 08:30 - 16:30 น. (ยกเว้นวันหยุดราชการ)');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `two_factor_auth`
--

DROP TABLE IF EXISTS `two_factor_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `two_factor_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `otp_code` (`otp_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `two_factor_auth`
--

LOCK TABLES `two_factor_auth` WRITE;
/*!40000 ALTER TABLE `two_factor_auth` DISABLE KEYS */;
INSERT INTO `two_factor_auth` VALUES
(1,1,'Khthawuth.leo@gmail.com','628729','2026-10-30 08:52:26',1,'2026-10-30 14:42:26'),
(2,1,'Khthawuth.leo@gmail.com','452851','2026-10-30 08:54:11',1,'2026-10-30 14:44:11'),
(3,1,'Khthawuth.leo@gmail.com','203535','2026-10-30 09:00:20',1,'2026-10-30 14:50:20'),
(4,1,'Khthawuth.leo@gmail.com','897152','2026-10-30 09:31:13',1,'2026-10-30 15:21:13'),
(5,1,'Khthawuth.leo@gmail.com','127965','2026-10-30 09:32:41',1,'2026-10-30 15:22:41'),
(6,1,'Khthawuth.leo@gmail.com','450489','2026-10-30 10:27:35',1,'2026-10-30 16:17:35'),
(7,1,'Khthawuth.leo@gmail.com','576398','2026-10-30 10:30:00',1,'2026-10-30 16:20:00');
/*!40000 ALTER TABLE `two_factor_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('admin','staff','user') DEFAULT 'user',
  `two_factor_enabled` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','$2y$10$DxcG6u4yVQZkJXeqadIl4Ob40XLao0pD9XoP6RcenL2H3aoqNqI0i','ผู้ดูแลระบบสุขภาพดิจิทัล','Khthawuth.leo@gmail.com','02-590-3000','admin',1,'2026-10-30 14:29:53');
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

-- Dump completed on 2026-10-30 16:30:51
