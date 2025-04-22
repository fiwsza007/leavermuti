-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for project_leave
CREATE DATABASE IF NOT EXISTS `project_leave` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `project_leave`;

-- Dumping structure for table project_leave.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `adminid` int NOT NULL AUTO_INCREMENT,
  `id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`adminid`),
  KEY `FK_admin_employees` (`id`),
  CONSTRAINT `FK_admin_employees` FOREIGN KEY (`id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.admin: ~0 rows (approximately)
INSERT INTO `admin` (`adminid`, `id`) VALUES
	(23, 30);

-- Dumping structure for table project_leave.employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pic` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prefix` int DEFAULT NULL,
  `fname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `department` int DEFAULT NULL,
  `staffstatus` int DEFAULT NULL,
  `startwork` date DEFAULT NULL,
  `signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Vacationday` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_employees_position` (`position`),
  KEY `FK_employees_prefix` (`prefix`),
  KEY `FK_employees_staffstatus` (`staffstatus`),
  KEY `FK_employees_subdepart` (`department`),
  CONSTRAINT `FK_employees_position` FOREIGN KEY (`position`) REFERENCES `position` (`positionid`),
  CONSTRAINT `FK_employees_prefix` FOREIGN KEY (`prefix`) REFERENCES `prefix` (`prefixid`),
  CONSTRAINT `FK_employees_staffstatus` FOREIGN KEY (`staffstatus`) REFERENCES `staffstatus` (`staffid`),
  CONSTRAINT `FK_employees_subdepart` FOREIGN KEY (`department`) REFERENCES `subdepart` (`subdepartid`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.employees: ~43 rows (approximately)
INSERT INTO `employees` (`id`, `pic`, `prefix`, `fname`, `lname`, `email`, `password`, `department`, `staffstatus`, `startwork`, `signature`, `Vacationday`, `position`) VALUES
	(24, '67e9535f81dfa.png', 6, 'จิณณพัต', 'โรจนวงศ์', 'jinnapat.ro@rmuti.ac.th', '$2y$10$2qpPaE4NGWIbICgR1V3S4.StmHmLvgKRLW5hzCUoLIeAtmg7mpXPO', 2, 5, '2007-11-16', NULL, 10, 7),
	(25, '67e9535f81dfa.png', 8, 'ภัณฑิรา', 'สุขนา', 'pattira.su@rmuti.ac.th', '$2y$10$bmD8bj1.viogi253KVDI..W1Xduwnc9ior5QhpNKU8Xx6MQL6/KNa', 2, 5, '1991-06-01', NULL, 10, 8),
	(26, '67e9535f81dfa.png', 5, 'ณัฏฐ์คณิน', 'ศุภเมธานนท์', 'natkanin.su@rmuti.ac.th', '$2y$10$17WHfzrVdXVC1tW1eGkpKe//byIu7NDnOV5kJeIHSFNWsw9rUMtyu', 4, 5, '2011-06-05', NULL, 10, 9),
	(27, '67e9535f81dfa.png', 2, 'ณัชชา', 'สุวรรณวงศ์', 'natcha.su@rmuti.ac.th', '$2y$10$HKrEnATTOSpzqftQXe1hYef9Wm0rjgXPfJH94zkao2wSOtoYKh4CS', 4, 5, '1993-07-16', NULL, 10, 10),
	(28, '67e9464aae5ee.png', 8, 'วรัญญา', 'สายสุรินทร์', 'waranya.sa@rmuti.ac.th', '$2y$10$L5xozezb03vMzILwVlfy1egO1J12qT5L9.0tmUxM8wKxEd1XxEFea', 4, 5, '2008-08-15', NULL, 10, 11),
	(29, '67e946fd3da2f.png', 8, 'ปัทมา', 'ศรีสุรมณี', 'pattama.si@rmuti.ac.th', '$2y$10$MvmUli/zl35f2civHMvqiO4kZD.JQf1eqLNskTzntd7vboUe5QBN.', 4, 5, '2011-03-14', NULL, 10, 6),
	(30, '67e9476bd0984.png', 8, 'พรรณาภรณ์', 'พับเกาะ', 'pannaporn.pa@rmuti.ac.th', '$2y$10$TZyRPbcrJTDDuZg9kQdemegn80TB16/2GlEVbd8mAZoT9ykOqvviO', 4, 5, '1995-06-01', NULL, 10, 1),
	(31, '67e949d207fe2.png', 8, 'อำภา', 'ขำคมเขตร', 'ampa.so@rmuti.ac.th', '$2y$10$YP7cbpACGpV0c3eJL4cTeOxWb.fD4YNKkMs2wVhhVYRc6oMYZ8JHu', 4, 2, '2015-02-09', NULL, NULL, 6),
	(32, '67e94af89b777.png', 2, 'สาวินี', 'ศรีวัฒนพงศ์', 'sawinee.si@rmuti.ac.th', '$2y$10$FaywZu6SWlBlOkAUcQVrZOaCDlne2iyiOcSE7aPghIWJYsqvvqA.K', 4, 2, '2019-12-02', NULL, NULL, 11),
	(33, '67e94b59c45f6.png', 1, 'ธนกร', 'ทันบุญ', 'thanakorn.ta@rmuti.ac.th', '$2y$10$oNVfJAfRDyzKs7dhpchoX.UrpbcFA/0dcWX4TlZgIH6GZaNEGidoK', 4, 2, '2022-11-01', NULL, NULL, 6),
	(34, '67e94c30afb13.png', 8, 'อังคณา', 'ขำคมเขตร', 'angkana.so@rmuti.ac.th', '$2y$10$Hy2R0bRLntX.zkwK1MU2w.iKoZ7mOI/PVI86opEE.EHl.REAtdCiu', 4, 2, '2021-04-01', NULL, NULL, 11),
	(35, '67e94e885d98f.png', 8, 'มนต์ธิณี', 'ดุลย์เภรี', 'montini.du@rmuti.ac.th', '$2y$10$MC7U.xIsDYeUc6qgrHby7O9qfvGaQUiZePd3cATU/a.Lw1ndNMWR.', 1, 5, '2008-06-17', NULL, 10, 12),
	(36, '67e94fb0ed6c2.png', 1, 'นิกร', 'ศรีนวล', 'nikorn.si@rmuti.ac.th', '$2y$10$ZsGwASQDdXMv.qqLc8RlbOeq4WIL8LQB2i9RY.8IwzSmatzhfFdU6', 1, 5, '2007-07-02', NULL, 10, 6),
	(37, '67e9506f9ed19.png', 1, 'ทวีรัตน์', 'แดงงาม', 'thaverat.da@rmuti.ac.th', '$2y$10$9atg73ckRVDg.o5i7BhE2OAb2RhOPqbTtePk2m9BU.TAoBwDUetoC', 1, 5, '2012-09-16', NULL, 10, 6),
	(38, '67e950e9bbcd6.png', 8, 'สกุณา', 'อนุเวช', 'sakuna.an@rmuti.ac.th', '$2y$10$K.1IdmQQpP4FwQzlhHhzIukl5GnF.esLjx3oblfW1oj107zwmRHZm', 1, 2, '2015-03-16', NULL, NULL, 6),
	(39, '67e9519a0144f.png', 8, 'ศิริพร', 'ปภาวดีกิตติพร', 'siriporn.pa@rmuti.ac.th', '$2y$10$NMR.aRS/2BAB8eIu04z/peP.Z6fUQI4qRUQfDY3w.3uM7phCBX/Tu', 1, 2, '2011-06-06', NULL, NULL, 6),
	(40, '67e9522156fad.png', 1, 'ทศพร', 'เวียงวิเศษ', 'todsapron.va@rmuti.ac.th', '$2y$10$R.Xi5nXGsti9fA3DfVftDuCJ.9odYpEw293Ds8UbYmkGd3vX7NKd.', 1, 2, '2024-05-16', NULL, NULL, 6),
	(41, '67e952bdca344.png', 5, 'ศศิธร', 'อินทร์นอก', 'sasitorn.in@rmuti.ac.th', '$2y$10$cFjDFLt5DIZhZxdbAcia5eyDsS8s.RaOoNEQPmqhqoSCTnU7uuiaK', 6, 5, '2006-08-07', NULL, 10, 13),
	(42, '67e9535f81dfa.png', 2, 'อาทิตยา', 'รุ้งพิมาย', 'atidtiya.ru@rmuti.ac.th', '$2y$10$AlQKDg69EXUWjvovq.8H.egPLCHfWQoBeQbVwUN8OwXFXXh1PAcn2', 6, 5, '2006-07-17', NULL, 10, 14),
	(43, '67e95400b0a83.png', 8, 'พกุลวลัย', 'พุทธมงคล', 'pagulvalai.pu@rmuti.ac.th', '$2y$10$Z3uqWM30EaVaHvEmwPGdrewiYKul0UNOAFKiNaIrL8sWFi2g5ls1u', 6, 5, '2007-03-03', NULL, 10, 6),
	(44, '67e95470b1df0.png', 1, 'เจนภพ', 'สุขขุนทด', 'janepob.su@rmuti.ac.th', '$2y$10$WKKtX/HRU1qyfCvPOSnXsOWOqcKtJ2ak5yt/8WDxRg.xF2ge/19YC', 6, 5, '2016-07-01', NULL, NULL, 6),
	(45, '67e954de817ed.png', 2, 'วาสนา', 'น้อยหมื่นไวย', 'vasana.no@rmuti.ac.th', '$2y$10$ZdZz9xz7Ssvd08Eh5IUgleo/a0nxKMomY.XDgJrV0/Hx89Gq3Alw6', 6, 5, '2012-10-01', NULL, 10, 6),
	(46, '67e955b38d486.png', 2, 'ปาริชาติ', 'ชัยวงษ์', 'parichart.ch@rmuti.ac.th', '$2y$10$kG3aD09Fwt4RgGXeIlhigOyIxTeUWL6EKX6EY5Z2eXja1iHTj88RO', 6, 2, '2010-12-20', NULL, NULL, 6),
	(47, '67e95691b78b0.png', 1, 'สิทธิชัย', 'แก้วการไร่', 'sittichai.ga@rmuti.ac.th', '$2y$10$nCrQOJjoMADGx8.fz/Uvo.jHx2H7psClOwsqVJQLH4hZZhAlzDbYy', 6, 2, '2022-10-03', NULL, NULL, 6),
	(48, '67e95737ec3d8.png', 2, 'นิตยา', 'ทองคำ', 'nitiya.to@rmuti.ac.th', '$2y$10$4Pivw97SIdTIcesc5paP6uBYiXUubTGbwqnlwl4rFr1a9WVrUlpRO', 6, 2, '2023-05-01', NULL, NULL, 6),
	(49, '67e957a72d92d.png', 8, 'สุภาวรรณ', 'พลล้า', 'supavan.po@rmuti.ac.th', '$2y$10$PLJgQLQulodzJMEXAVhKMevjWQ1J8kBV9/09ZQ9L2t58l2jV3.FhW', 6, 2, '2024-01-15', NULL, NULL, 6),
	(50, '67e9582f0ed81.png', 7, 'เกตุกาญจน์', 'โพธิจิตติกานต์', 'kedkarn.po@rmuti.ac.th', '$2y$10$cIqUe6I9cFlTisS7sInfN.SdtBQtVe3qrcCYRmofTEw7drFPyaj/.', 5, 6, '2001-04-10', NULL, 10, 15),
	(51, '67e958c49bab3.png', 2, 'ถนอมศรี', 'สุทธิจันทร์', 'thanomsri.su@rmuti.ac.th', '$2y$10$p.W5xEqdTNgLh.cZPoyHzubNOJ/D8EVoltfeU7d6gVCBxR1DHUc6q', 5, 5, '1997-08-11', NULL, 10, 16),
	(52, '67e959e0536aa.png', 8, 'ฐาณิญา', 'ทองประสาร', 'taniya.th@rmuti.ac.th', '$2y$10$Q/2tYUe/N.JcsHXnjbqOfuP7lJEbSwIlYvPJXb8bBpB/pUKuCITwK', 5, 5, '2004-09-01', NULL, 10, 6),
	(53, '67e95a4b7a786.png', 1, 'จักรพงษ์', 'คงดี', 'jakpong.ko@rmuti.ac.th', '$2y$10$s/RvCJ.cRvQzqQqrrWRiAeYyEEx6vRmdqnUsgWIwbEiDMJLaznbvG', 5, 5, '2014-12-08', NULL, 0, 6),
	(54, '67e95ac808e1d.png', 8, 'ระวิสุดา', 'นารี', 'ravisuda.na@rmuti.ac.th', '$2y$10$aU.GsPgbmGogyaSri9N/1Oco3LXxIcLjKtdhZHoBuz.EuDsoXfKvG', 5, 5, '1995-06-01', NULL, 10, 6),
	(56, '67e95c356aec8.png', 8, 'วรรณ์มณี', 'บุญฟู', 'vanmanee.bu@rmuti.ac.th', '$2y$10$.nhq3xkmVLmT01rINohWkOjRRB2pBPcpjV3RRBflc/tmPpuKjXKVG', 5, 5, '1995-06-01', NULL, 10, 6),
	(57, '67e95d562f7cc.png', 8, 'จิราพร', 'วรทองหลาง', 'jiraporn.vo@rmuti.ac.th', '$2y$10$IDIWPmF1xyMVCieQ2Wc.hetkO1x5jG9PLo5jOH/nBtj5Qn1Q0YmaG', 5, 2, '2011-11-15', NULL, NULL, 6),
	(58, '67e95db52c58e.png', 8, 'ณัฎฐนันท์', 'ไชยรัตน์', 'nattanan.ch@rmuti.ac.th', '$2y$10$ZHeuvWS2WL0sw6VqkGsAdugg3oKyy/E8bs8M8ZGci2sN9mfclFgBe', 5, 2, '2014-07-01', NULL, NULL, 6),
	(59, '67e95e15a770d.png', 8, 'ปิยธิดา', 'ชนยุทธ์', 'piyatida.ch@rmuti.ac.th', '$2y$10$BdfKtCL70zylMZc5EGQ1xeiqbJpr243QKha0BXKObr8eucH2iWCpS', 5, 2, '2022-11-01', NULL, NULL, 6),
	(60, '67e95e65e8a5c.png', 8, 'พนิดา', 'กาจกระโทก', 'panida.ga@rmuti.ac.th', '$2y$10$PI3p/LcDIH9TKbxAUslBB.8trkakPRl4YoBWl1RreXDeSwUOAoHuS', 5, 2, '2011-07-21', NULL, NULL, 6),
	(61, '67e95ee75239b.png', 8, 'อรจริน', 'สุทธิวิไล', 'ornjarin.su@rmuti.ac.th', '$2y$10$Amwc/jObL7g6yf86Cd3V/e57nmFILIZZcMosjYCDG0qNmsZatj7QK', 5, 2, '2018-02-01', NULL, NULL, 6),
	(62, '67e95f6f48b24.png', 2, 'น้ำผึ้ง', 'เมษศิลา', 'namphueng.ma@rmuti.ac.th', '$2y$10$jsSDoj0xvtCIgCjz6oZYLeUC0G6PZLiHVHXdh2wx8dlO.NkhqUd8q', 5, 2, '2011-11-15', NULL, NULL, 6),
	(63, '67e95fcd6ef92.png', 2, 'อัจฉริยา', 'พีรทัตสุวรรณ', 'achariya.pe@rmuti.ac.th', '$2y$10$nnbCrBnOJ3cfAp9n5EKxpei3kOdZ7BzdfEbarGxDAWoEGctJDf5By', 5, 2, '2010-08-09', NULL, NULL, 6),
	(64, '67e96029794e4.png', 2, 'ลดาวัลย์', 'พือสันเทียะ', 'ladavan.pe@rmuti.ac.th', '$2y$10$Ko1Abv.8PqXSMr3g5hEKPe/ExLUjix.38bzfk.7MtCYknm0n7cVj.', 3, 5, '2008-06-02', NULL, 10, 17),
	(65, '67e960a3bfc67.png', 8, 'วรวรรณ', 'สมุทรพงษ์', 'worawan.sa@rmuti.ac.th', '$2y$10$cymuf48d9sfu0EZIZ.h3TOuxn4gMgBXrZE2ocmW/w/Jody/ysstvC', 3, 2, '2019-12-02', NULL, NULL, 6),
	(67, '67e960fa37a59.png', 8, 'ธณัณณัฐ', 'ทองคำ', 'thanannat.th@rmuti.ac.th', '$2y$10$W3EB394gxC.Xfm2Iog/rDe900b3vKknteY.3./zCTHcWJ90PfxwYC', 3, 2, '2022-04-18', NULL, NULL, 6),
	(68, '67e96190b242e.png', 8, 'พรชิตา', 'พังหมื่นไวย', 'pornchita.pa@rmuti.ac.th', '$2y$10$HTEqoHRD0cRt6QawsCXRT.Ph1ud/VeF89Lku38d5EtHDC1lmk0eM2', 3, 2, '2024-05-27', NULL, NULL, 6);

-- Dumping structure for table project_leave.headepart
CREATE TABLE IF NOT EXISTS `headepart` (
  `headepartid` int NOT NULL AUTO_INCREMENT,
  `headepartname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`headepartid`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.headepart: ~0 rows (approximately)
INSERT INTO `headepart` (`headepartid`, `headepartname`) VALUES
	(1, 'สำนักส่งเสริมวิชาการและงานทะเบียน');

-- Dumping structure for table project_leave.holiday
CREATE TABLE IF NOT EXISTS `holiday` (
  `holidayid` int NOT NULL AUTO_INCREMENT,
  `holidayname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `holidayday` date DEFAULT NULL,
  PRIMARY KEY (`holidayid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.holiday: ~7 rows (approximately)
INSERT INTO `holiday` (`holidayid`, `holidayname`, `holidayday`) VALUES
	(14, 'หยุด', '2025-04-01'),
	(15, 'หยุด', '2025-04-02'),
	(18, 'หยุด', '2025-04-03'),
	(19, 'สงกรานต์', '2025-04-13'),
	(20, 'สงกรานต์', '2025-04-14'),
	(21, 'สงกรานต์', '2025-04-15'),
	(22, 'ชดเชยสงกรานต์', '2025-04-16');

-- Dumping structure for table project_leave.leaveday
CREATE TABLE IF NOT EXISTS `leaveday` (
  `leavedayid` int NOT NULL AUTO_INCREMENT,
  `leavetype` int DEFAULT NULL,
  `staffstatus` int DEFAULT NULL,
  `day` int DEFAULT NULL,
  PRIMARY KEY (`leavedayid`),
  KEY `leavetype` (`leavetype`),
  KEY `staffstatus` (`staffstatus`),
  CONSTRAINT `leaveday_ibfk_1` FOREIGN KEY (`leavetype`) REFERENCES `leavetype` (`leavetypeid`),
  CONSTRAINT `leaveday_ibfk_2` FOREIGN KEY (`staffstatus`) REFERENCES `staffstatus` (`staffid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.leaveday: ~0 rows (approximately)

-- Dumping structure for table project_leave.leaves
CREATE TABLE IF NOT EXISTS `leaves` (
  `leavesid` int NOT NULL AUTO_INCREMENT,
  `employeesid` int DEFAULT NULL,
  `leavetype` int DEFAULT NULL,
  `leavestart` date DEFAULT NULL,
  `leaveend` date DEFAULT NULL,
  `day` int DEFAULT NULL,
  `approver1` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approver2` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approver3` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `leavestatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`leavesid`),
  KEY `FK_leaves_employees` (`employeesid`),
  KEY `FK_leaves_leavetype` (`leavetype`),
  CONSTRAINT `FK_leaves_employees` FOREIGN KEY (`employeesid`) REFERENCES `employees` (`id`),
  CONSTRAINT `FK_leaves_leavetype` FOREIGN KEY (`leavetype`) REFERENCES `leavetype` (`leavetypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.leaves: ~4 rows (approximately)
INSERT INTO `leaves` (`leavesid`, `employeesid`, `leavetype`, `leavestart`, `leaveend`, `day`, `approver1`, `approver2`, `approver3`, `leavestatus`, `file`) VALUES
	(28, 36, 27, '2025-04-01', '2025-04-04', 1, '27', NULL, NULL, 'รอหัวหน้าอนุมัติ', NULL),
	(29, 53, 28, '2025-04-01', '2025-04-08', 3, '27', NULL, NULL, 'รอหัวหน้าอนุมัติ', NULL),
	(30, 28, 31, '2024-04-01', '2024-04-11', 6, '25', NULL, NULL, 'อนุมัติ', NULL),
	(31, 30, 28, '2025-04-01', '2025-04-09', NULL, NULL, NULL, NULL, 'รออนุมัติ', NULL);

-- Dumping structure for table project_leave.leavetype
CREATE TABLE IF NOT EXISTS `leavetype` (
  `leavetypeid` int NOT NULL AUTO_INCREMENT,
  `leavetypename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `staffid` int NOT NULL,
  `leaveofyear` int NOT NULL,
  `stackleaveday` int NOT NULL,
  `workage` int NOT NULL,
  `workageday` int NOT NULL,
  `nameform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `minleaveday` int DEFAULT NULL,
  `workage_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`leavetypeid`),
  KEY `staffid` (`staffid`),
  CONSTRAINT `leavetype_ibfk_1` FOREIGN KEY (`staffid`) REFERENCES `staffstatus` (`staffid`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.leavetype: ~26 rows (approximately)
INSERT INTO `leavetype` (`leavetypeid`, `leavetypename`, `staffid`, `leaveofyear`, `stackleaveday`, `workage`, `workageday`, `nameform`, `minleaveday`, `workage_type`) VALUES
	(21, 'ลาป่วย', 6, 60, 0, 3, 0, 'ใบรับรองแพทย์', 2, NULL),
	(22, 'ลากิจส่วนตัว', 6, 45, 0, 3, 0, '', 0, NULL),
	(23, 'ลาพักผ่อน', 6, 0, 0, 2, 6, '', 0, NULL),
	(24, 'ลาพักผ่อน', 6, 10, 20, 2, 120, '', 0, NULL),
	(26, 'ลาคลอดบุตร', 6, 90, 0, 3, 0, 'ใบรับรองแพทย์', 1, NULL),
	(27, 'ลาป่วย', 5, 60, 0, 3, 0, 'ใบรับรองแพทย์', 2, NULL),
	(28, 'ลากิจส่วนตัว', 5, 45, 0, 3, 0, '', 0, NULL),
	(29, 'ลาพักผ่อน', 5, 0, 0, 2, 6, '', 0, NULL),
	(30, 'ลาพักผ่อน', 5, 10, 20, 2, 120, '', 0, NULL),
	(31, 'ลาพักผ่อน', 5, 10, 30, 1, 120, '', 0, NULL),
	(32, 'ลาคลอดบุตร', 5, 90, 0, 3, 0, 'ใบรับรองแพทย์', 1, NULL),
	(33, 'ลาป่วย', 1, 30, 0, 3, 0, 'ใบรับรองแพทย์', 2, NULL),
	(34, 'ลากิจส่วนตัว', 1, 10, 0, 3, 0, '', 0, NULL),
	(35, 'ลาพักผ่อน', 1, 0, 0, 2, 6, '', 0, NULL),
	(36, 'ลาพักผ่อน', 1, 10, 15, 1, 6, '', 0, NULL),
	(37, 'ลาคลอดบุตร', 3, 60, 0, 3, 0, 'ใบรับรองแพทย์', 2, NULL),
	(38, 'ลากิจส่วนตัว', 3, 45, 0, 3, 0, '', 0, NULL),
	(39, 'ลาพักผ่อน', 3, 0, 0, 2, 6, '', 0, NULL),
	(40, 'ลาพักผ่อน', 3, 10, 20, 2, 120, '', 0, NULL),
	(41, 'ลาพักผ่อน', 3, 10, 30, 1, 120, '', 0, NULL),
	(42, 'ลาคลอดบุตร', 1, 90, 0, 3, 0, 'ใบรับรองแพทย์', 1, NULL),
	(43, 'ลาคลอดบุตร', 2, 90, 0, 3, 0, 'ใบรับรองแพทย์', 1, NULL),
	(44, 'ลาป่วย', 2, 8, 0, 2, 6, 'ใบรับรองแพทย์', 2, NULL),
	(45, 'ลาป่วย', 2, 15, 0, 1, 6, 'ใบรับรองแพทย์', 2, NULL),
	(46, 'ลาพักผ่อน', 2, 0, 0, 2, 6, '', 0, NULL),
	(47, 'ลาพักผ่อน', 2, 10, 0, 1, 6, '', 0, NULL);

-- Dumping structure for table project_leave.position
CREATE TABLE IF NOT EXISTS `position` (
  `positionid` int NOT NULL AUTO_INCREMENT,
  `positionname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `roleid` int DEFAULT NULL,
  PRIMARY KEY (`positionid`),
  KEY `FK_position_role` (`roleid`),
  CONSTRAINT `FK_position_role` FOREIGN KEY (`roleid`) REFERENCES `role` (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.position: ~13 rows (approximately)
INSERT INTO `position` (`positionid`, `positionname`, `roleid`) VALUES
	(1, 'ผู้ปฏิบัติงานบริหาร', 8),
	(6, 'นักวิชาการศึกษา ', 8),
	(7, 'ผู้อำนวยการสำนักส่งเสริมวิชาการและงานทะเบียน', 7),
	(8, 'หัวหน้าสำนักงานผู้อำนวยการ', 9),
	(9, 'รองผู้อำนวยการฝ่ายบริหารงานทั่วไป', 10),
	(10, 'หัวหน้างานบริหารงานทั่วไป', 9),
	(11, 'เจ้าหน้าที่บริหารงานทั่วไป', 8),
	(12, 'หัวหน้างานพัฒนาคุณภาพการศึกษา', 9),
	(13, 'รองผู้อำนวยการฝ่ายพัฒนาวิชาการและส่งเสริมการศึกษา', 10),
	(14, 'หัวหน้างานพัฒนาวิชาการและส่งเสริมการศึกษา', 9),
	(15, 'รองผู้อำนวยการฝ่ายทะเบียนและประมวลผล', 10),
	(16, 'หัวหน้างานทะเบียนและประมวลผล', 9),
	(17, 'หัวหน้างานฝึกประสบการณ์วิชาชีพนักศึกษา', 9);

-- Dumping structure for table project_leave.prefix
CREATE TABLE IF NOT EXISTS `prefix` (
  `prefixid` int NOT NULL AUTO_INCREMENT,
  `prefixname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`prefixid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.prefix: ~6 rows (approximately)
INSERT INTO `prefix` (`prefixid`, `prefixname`) VALUES
	(1, 'นาย'),
	(2, 'นาง'),
	(5, 'ผศ.ดร.'),
	(6, 'ดร.'),
	(7, 'อาจารย์ ดร.'),
	(8, 'นางสาว');

-- Dumping structure for table project_leave.role
CREATE TABLE IF NOT EXISTS `role` (
  `roleid` int NOT NULL AUTO_INCREMENT,
  `rolename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `level` int DEFAULT NULL,
  PRIMARY KEY (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.role: ~0 rows (approximately)
INSERT INTO `role` (`roleid`, `rolename`, `level`) VALUES
	(7, 'ผู้อำนวยการ', 4),
	(8, 'บุคลากร', 1),
	(9, 'หัวหน้างาน', 2),
	(10, 'รองผู้อำนวยการ', 3);

-- Dumping structure for table project_leave.staffstatus
CREATE TABLE IF NOT EXISTS `staffstatus` (
  `staffid` int NOT NULL AUTO_INCREMENT,
  `staffname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`staffid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.staffstatus: ~0 rows (approximately)
INSERT INTO `staffstatus` (`staffid`, `staffname`) VALUES
	(1, 'พนักงานราชการ'),
	(2, 'ลูกจ้างเงินรายได้'),
	(3, 'ลูกจ้างประจำ'),
	(5, 'พนักงานในสถาบันอุดมศึกษา'),
	(6, 'ข้าราชการ');

-- Dumping structure for table project_leave.subdepart
CREATE TABLE IF NOT EXISTS `subdepart` (
  `subdepartid` int NOT NULL AUTO_INCREMENT,
  `headepartid` int DEFAULT NULL,
  `subdepartname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`subdepartid`),
  KEY `headepartid` (`headepartid`),
  CONSTRAINT `subdepart_ibfk_1` FOREIGN KEY (`headepartid`) REFERENCES `headepart` (`headepartid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.subdepart: ~7 rows (approximately)
INSERT INTO `subdepart` (`subdepartid`, `headepartid`, `subdepartname`) VALUES
	(1, 1, 'งานพัฒนาคุณภาพการศึกษา'),
	(2, 1, 'สำนักงานผู้อำนวยการ'),
	(3, 1, 'งานฝึกประสบการณ์วิชาชีพนักศึกษา'),
	(4, 1, 'งานบริหารงานทั่วไป'),
	(5, 1, 'งานทะเบียนและประมวลผล'),
	(6, 1, 'งานพัฒนาวิชาการและส่งเสริมการศึกษา'),
	(7, 1, 'ศูนย์พัฒนาอาจารย์สู่ความเป็นเลิศ');

-- Dumping structure for table project_leave.vacationday_updates
CREATE TABLE IF NOT EXISTS `vacationday_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project_leave.vacationday_updates: ~1 rows (approximately)
INSERT INTO `vacationday_updates` (`id`, `start_date`, `end_date`, `updated_at`) VALUES
	(1, '2024-10-01', '2025-09-03', '2025-04-01 21:00:34');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
