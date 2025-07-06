-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 07:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edu cu connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `course_code` varchar(10) DEFAULT NULL,
  `credits` int(11) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `total_seats` int(100) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `DAY` varchar(10) NOT NULL,
  `TIME` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `credits`, `semester`, `status`, `total_seats`, `available_seats`, `DAY`, `TIME`, `description`, `course_id`) VALUES
(1, 'FUND DATA SYS VIS', '2766233', 3, '2/2567', 1, 30, 22, 'TH', '09:00 - 11:00', 'พื้นฐานหลักการระบบฐานข้อมูล ข้อมูลและสารสนเทศ การจัดการสารสนเทศ แนวคิดเกี่ยวกับฐานข้อมูลและระบบการจัดการฐานข้อมูล การออกแบบและพัฒนาฐานข้อมูลเบื้องต้นด้วยโปรแกรมการประยุกต์และความรู้พื้นฐานในการโปรแกรมภาษาสำหรับเรียกใช้ฐานข้อมูล(เอส.คิว.แอล) กับการประมวลผลข้อมูล และการทำข้อมูลให้เป็นภาพ', 2766233),
(2, 'FOUND PSY ED', '2759151', 2, '2/2567', 1, 30, 3, 'MON', '08:00 - 10:00', 'ธรรมชาติของผู้เรียนและความแตกต่างระหว่างบุคคล หลักการ แนวคิด และทฤษฎีทางจิตวิทยาที่เกี่ยวข้องกับ การรับรู้ การเรียนรู้ การประมวลข้อมูลและการถ่ายโยงการเรียนรู้ เชาวน์ปัญญาและความถนัด พัฒนาการของผู้เรียนทุกช่วงวัย ลักษณะของผู้เรียนที่มีความต้องการพิเศษ การบูรณาการทฤษฎีทางจิตวิทยาเพื่อการวิเคราะห์และเข้าใจลักษณะของผู้เรียนเป็นรายบุคคล ทั้งผู้เรียนทั่วไปและผู้เรียนที่มีความต้องการพิเศษ ความเข้าใจในอารมณ์ความรู้สึกของผู้อื่น การมีความคิดเชิงบวก การมีวุฒิภาวะทางอารมณ์และทางสังคม', 2759151),
(3, 'FOUND ELEC EDU', '2765135', 3, '2/2567', 1, 30, 20, 'MON', '16:00 - 18:00', 'ทฤษฎีหลักการทางไฟฟ้า-อิเล็กทรอนิกส์ ความรู้เบื้องต้นเกี่ยวกับวัสดุ อุปกรณ์ เครื่องมือพื้นฐานด้านไฟฟ้า-อิเล็กทรอนิกส์ กลไก-ไฟฟ้า อิเล็กทรอนิกส์ ทฤษฎีแนวโน้มการศึกษาด้านไฟฟ้า-อิเล็กทรอนิกส์ ระบบเทคโนโลยีที่ทันสมัย กระบวนการออกแบบเชิงวิศวกรรมเพื่อนำไปใช้ในการปฏิบัติงานด้านเทคโนโลยีการศึกษา การศึกษาร่วมสมัย วิธีการเรียนทางอิเล็กทรอนิกส์ในรูปแบบต่าง ๆ เน้นปฏิบัติการ การทำโครงงาน การนำความรู้ด้านไฟฟ้า-อิเล็กทรอนิกส์ไปประยุกต์ใช้ในการกำหนดคุณลักษณะเฉพาะของสื่อการศึกษา และแนวคิดพื้นฐานและการประยุกต์ใช้ Internet of Things (IOT) ในการสร้างโครงงานและนวัตกรรมทางการศึกษา', 2765135),
(5, 'COMP PROG', '2301170', 3, '2/2567', 1, 200, 38, 'MON', '10:00 - 11:00', 'แนวคิดเบื้องต้นของระบบคอมพิวเตอร์ แนวคิดเบื้องต้นของโปรแกรม จรรยาบรรณและความปลอดภัยทางด้านคอมพิวเตอร์ การประยุกต์', 2301170);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_code` varchar(255) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `academic_year` varchar(10) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_code`, `course_code`, `semester`, `academic_year`, `enrollment_date`) VALUES
(33, '6740002727', '2301170', '2', '2567', '2025-05-05 20:59:56'),
(38, '6742422627', '2301170', '2', '2567', '2025-05-15 09:44:57');

--
-- Triggers `enrollments`
--
DELIMITER $$
CREATE TRIGGER `insert_grade_from_enrollment` AFTER INSERT ON `enrollments` FOR EACH ROW BEGIN
  INSERT INTO grades (enrollment_id)
  VALUES (NEW.enrollment_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` varchar(100) NOT NULL,
  `faculty_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_name`) VALUES
('1', 'สถาบันภาษาไทยสิรินธร'),
('2', 'ศูนย์การศึกษาทั่วไป'),
('20', 'บัณฑิตวิทยาลัย'),
('21', 'คณะวิศวกรรมศาสตร์'),
('22', 'คณะอักษรศาสตร์'),
('23', 'คณะวิทยาศาสตร์'),
('24', 'คณะรัฐศาสตร์'),
('25', 'คณะสถาปัตยกรรมศาสตร์'),
('26', 'คณะพาณิชยศาสตร์และการบัญชี'),
('27', 'คณะครุศาสตร์'),
('28', 'คณะนิเทศศาสตร์'),
('29', 'คณะเศรษฐศาสตร์'),
('30', 'คณะแพทยศาสตร์'),
('31', 'คณะสัตวแพทยศาสตร์'),
('32', 'คณะทันตแพทยศาสตร์'),
('33', 'คณะเภสัชศาสตร์'),
('34', 'คณะนิติศาสตร์'),
('35', 'คณะศิลปกรรมศาสตร์'),
('36', 'คณะพยาบาลศาสตร์'),
('37', 'คณะสหเวชศาสตร์'),
('38', 'คณะจิตวิทยา'),
('39', 'คณะวิทยาศาสตร์การกีฬา'),
('40', 'สำนักวิชาทรัพยากรการเกษตร'),
('51', 'วิทยาลัยประชากรศาสตร์'),
('53', 'วิทยาลัยวิทยาศาสตร์สาธารณสุข'),
('55', 'สถาบันภาษา'),
('56', 'สถาบันนวัตกรรมบูรณาการ'),
('58', 'สถาบันบัณฑิตบริหารธุรกิจ ศศินทร์ฯ'),
('99', 'มหาวิทยาลัยอื่น');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `grade_value` varchar(10) DEFAULT NULL,
  `recorded_by_teacher_id` varchar(50) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `grade_value`, `recorded_by_teacher_id`, `recorded_at`) VALUES
(14, 33, 'A', '123456', '2025-05-15 02:53:22'),
(19, 38, 'A', '123456', '2025-05-15 02:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `major_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `major_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `major_name_eng` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`major_id`, `major_name`, `major_name_eng`) VALUES
('0100', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('0101', 'สำนักบริหารการเงินการบัญชีและพัสดุ (สบง.)', ''),
('0102', 'สำนักบริหารวิชาการ', ''),
('0103', 'สำนักบริหารทรัพยากรมนุษย์และระบบงาน', ''),
('0104', 'สำนักบริหารระบบกายภาพ', ''),
('0123', 'สถาบันภาษาไทยสิรินธร', 'THE SIRINDHORN THAI LANGUAGE INSTITUTE'),
('0201', 'ศูนย์การศึกษาทั่วไป', 'OFFICE OF THE GENERAL EDUCATION'),
('0202', 'โครงการขยายโอกาสอุดมศึกษาแห่งจุฬาลงกรณ์มหาวิทยาลัย', 'OFFICE OF HIGHER EDUCATION OF OPPORTUNITY ENHANCEMENT OF CHULALONGKORN'),
('0294', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดสังคมศาสตร์', '21ST C.GENED IN SOCIAL SCIENCE'),
('0295', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดสหศาสตร์', '21ST C.GENED IN MULTI-DISCIPLINES'),
('0296', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดมนุษย์ศาสตร์และศิลปะ', '21ST C.GENED IN ART & HUMANITIES'),
('0298', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดวิทยาศาสตร์-คณิตศาสตร์', '21ST C.GENED IN SCI-MATH'),
('0299', 'รายวิชาศึกษาทั่วไปที่มีรูปแบบการเรียนที่ยืดหยุ่น', 'CHULA MOOC FLEXI'),
('2000', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2001', 'สหสาขาวิชาปิโตรเคมี-โพลิเมอร์', 'INTER-DEPARTMENT OF PETROCHEMISTRY-POLYMER'),
('2002', 'ศูนย์ระดับภูมิภาคทางวิศวกรรมระบบการผลิต', 'REGIONAL CENTRE FOR MANUFACTURING SYSTEMS ENGINEERING'),
('2003', 'สหสาขาวิชายุโรปศึกษา', 'INTER-DEPARTMENT OF EUROPEAN STUDIES'),
('2004', 'สหสาขาวิชาวิทยาศาสตร์สิ่งแวดล้อม', 'INTER-DEPARTMENT OF ENVIRONMENTAL SCIENCE'),
('2005', 'สหสาขาวิชาจุลชีววิทยาทางการแพทย์', 'MEDICAL MICROBIOLOGY,INTERDISCIPLINARY PROGRAM'),
('2006', 'สหสาขาวิชาเภสัชวิทยา', 'INTER-DEPARTMENT OF PHARMACOLOGY'),
('2007', 'สหสาขาวิชาสรีรวิทยา', 'INTER-DEPARTMENT OF PHYSIOLOGY'),
('2008', 'สหสาขาวิชาการจัดการทางวัฒนธรรม', 'INTER-DEPARTMENT OF CULTURAL MANAGEMENT'),
('2009', 'สหสาขาวิชาการจัดการสิ่งแวดล้อม', 'INTER-DEPARTMENT OF ENVIRONMENTAL MANAGEMENT'),
('2010', 'สหสาขาวิชาภาษาอังกฤษเป็นภาษานานาชาติ', 'INTER-DEPARTMENT OF ENGLISH AS AN INTERNATIONAL LANGUAGE'),
('2011', 'สหสาขาวิชาชีวเวชศาสตร์', 'INTER-DEPARTMENT OF BIOMEDICAL SCIENCES'),
('2012', 'สหสาขาวิชาเพศศาสตร์', 'INTER-DEPARTMENT OF HUMAN SEXUALITY'),
('2013', 'สหสาขาวิชาการจัดการด้านโลจิสติกส์', 'INTER-DEPARTMENT OF LOGISTICS MANAGEMENT'),
('2014', 'สหสาขาวิชาการเพาะเลี้ยงทางน้ำ', 'INTER-DEPARTMENT OF AQUACULTURE'),
('2015', 'สหสาขาวิชาเอเชียตะวันออกเฉียงใต้ศึกษา', 'INTER-DEPARTMENT OF SOUTHEAST ASIAN STUDIES'),
('2016', 'สหสาขาวิชาพัฒนามนุษย์และสังคม', 'INTER-DEPARTMENT OF HUMAN AND SOCIAL DEVELOPMENT'),
('2017', 'สหสาขาวิชาทันตชีววัสดุศาสตร์', 'INTER-DEPARTMENT OF DENTAL BIOMATERIALS SCIENCE'),
('2018', 'สหสาขาวิชาธุรกิจแฟชั่น', 'INTER-DEPARTMENT OF FASHION BUSINESS'),
('2019', 'สหสาขาวิชาวิจัยเพื่อการพัฒนาสุขภาพ', 'INTER-DEPARTMENT RESEARCH FOR HEALTH DEVELOPMENT'),
('2020', 'สหสาขาวิชาวิศวกรรมชีวเวช', 'INTER-DEPARTMENT OF BIOMEDICAL ENGINEERING'),
('2021', 'สหสาขาวิชาวิทยาศาสตร์นาโนและเทคโนโลยี', 'INTER-DEPARTMENT OF NANOSCIENCE AND TECHNOLOGY'),
('2022', 'สหสาขาวิชาธุรกิจเทคโนโลยีและการจัดการนวัตกรรม', 'INTER-DEPARTMENT OF TECHNOPRENEURSHIP AND INNOVATION MANAGEMENT'),
('2023', 'สหสาขาวิชาสิ่งแวดล้อม การพัฒนาและความยั่งยืน', 'INTER-DEPARTMENT OF ENVIRONMENT,DEVELOPMENT AND SUSTAINABILITY'),
('2024', 'สหสาขาวิชาเกาหลีศึกษา', 'INTER-DEPARTMENT OF KOREAN STUDIES'),
('2025', 'สหสาขาวิชาการบริหารทางทะเล', 'INTER-DEPARTMENT OF MARITIME ADMINISTRATION'),
('2026', 'สหสาขาวิชาเทคโนโลยีและการจัดการพลังงาน', 'INTER-DEPARTMENT OF ENERGY TECHNOLOGY AND MANAGEMENT'),
('2027', 'สหสาขาวิชารัสเซียศึกษา', 'INTER-DEPARTMENT OF RUSSIAN STUDIES'),
('2028', 'สหสาขาวิชาชีวสารสนเทศศาสตร์และชีววิทยาทางคอมพิวเตอร์', 'INTER-DEPARTMENT OF BIOINFORMATICS AND COMPUTATIONAL BIOLOGY'),
('2029', 'สหสาขาวิชาการจัดการด้านภัยพิบัติ', 'INTER-DEPARTMENT OF DISASTER MANAGEMENT'),
('2030', 'สหสาขาวิชาเทคโนโลยีและการจัดการการเกษตร', 'INTER-DEPARTMENT OF AGRICULTURAL TECHNOLOGY AND MANAGEMENT'),
('2031', 'สหสาขาวิชาดนตรีบำบัด', 'INTER-DEPARTMENT OF ARTS PROGRAM IN MUSIC THERAPY'),
('2032', 'สหสาขาวิชาการเกษตรนวัตกรรมและการเป็นผู้ประกอบการเพื่อความยั่งยืน', 'INTER-DEPARTMENT OF INNOVATIVE AGRICULTURE AND ENTREPRENEURSHIP FOR SU'),
('2033', 'สหสาขาวิชาการบริหารจัดเก็บภาษี', 'INTER-DEPARTMENT OF TAXATION ADMINISTRATION'),
('2090', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2100', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2101', 'ภาควิชาวิศวกรรมโยธา', 'DEPARTMENT OF CIVIL ENGINEERING'),
('2102', 'ภาควิชาวิศวกรรมไฟฟ้า', 'DEPARTMENT OF ELECTRICAL ENGINEERING'),
('2103', 'ภาควิชาวิศวกรรมเครื่องกล', 'DEPARTMENT OF MECHANICAL ENGINEERING'),
('2104', 'ภาควิชาวิศวกรรมอุตสาหการ', 'DEPARTMENT OF INDUSTRIAL ENGINEERING'),
('2105', 'ภาควิชาวิศวกรรมเคมี', 'DEPARTMENT OF CHEMICAL ENGINEERING'),
('2106', 'ภาควิชาวิศวกรรมเหมืองแร่และปิโตรเลียม', 'DEPARTMENT OF MINING AND PETROLEUM ENGINEERING'),
('2107', 'ภาควิชาวิศวกรรมสิ่งแวดล้อมและความยั่งยืน', 'DEPARTMENT OF ENVIRONMENTAL AND SUSTAINABLE ENGINEERING'),
('2108', 'ภาควิชาวิศวกรรมสำรวจ', 'DEPARTMENT OF SURVEY ENGINEERING'),
('2109', 'ภาควิชาวิศวกรรมโลหการ', 'DEPARTMENT OF METALLURGICAL ENGINEERING'),
('2110', 'ภาควิชาวิศวกรรมคอมพิวเตอร์', 'DEPARTMENT OF COMPUTER ENGINEERING'),
('2111', 'ภาควิชาวิศวกรรมนิวเคลียร์', 'DEPARTMENT OF NUCLEAR ENGINEERING'),
('2112', 'ภาควิชาวิศวกรรมแหล่งน้ำ', 'DEPARTMENT OF WATER RESOURCES ENGINEERING'),
('2113', 'ภาควิชานิวเคลียร์เทคโนโลยี', 'DEPARTMENT OF NUCLEAR TECHNOLOGY'),
('2120', 'ไม่สังกัดภาควิชา/เทียบเท่า (ภาษาไทย)', 'COMMON COURSE'),
('2121', 'สาขาวิชาวิศวกรรมโยธา (ภาษาไทย)', 'FIELD OF STUDY OF CIVIL ENGINEERING'),
('2122', 'สาขาวิชาวิศวกรรมไฟฟ้า (ภาษาไทย)', 'FIELD OF STUDY OF ELECTRICAL ENGINEERING'),
('2123', 'สาขาวิชาวิศวกรรมเครื่องกล (ภาษาไทย)', 'FIELD OF STUDY OF MECHANICAL ENGINEERING'),
('2124', 'สาขาวิชาวิศวกรรมอุตสาหการ (ภาษาไทย)', 'FIELD OF STUDY OF INDUSTRIAL ENGINEERING'),
('2125', 'สาขาวิชาวิศวกรรมเคมี (ภาษาไทย)', 'FIELD OF STUDY OF CHEMICAL ENGINEERING'),
('2126', 'สาขาวิชาวิศวกรรมเหมืองแร่และปิโตรเลียม (ภาษาไทย)', 'FIELD OF STUDY OF MINING AND PETROLEUM ENGINEERING'),
('2127', 'สาขาวิชาสิ่งแวดล้อม (ภาษาไทย)', 'FIELD OF STUDY OF OF ENVIRONMENTAL ENGINEERING'),
('2128', 'สาขาวิศวกรรมสำรวจ (ภาษาไทย)', 'FIELD OF STUDY OF SURVEY ENGINEERING'),
('2129', 'สาขาวิชาวิศวกรรมโลหการ (ภาษาไทย)', 'FIELD OF STUDY OF METALLURGICAL ENGINEERING'),
('2130', 'สาขาวิชาวิศวกรรมคอมพิวเตอร์ (ภาษาไทย)', 'FIELD OF STUDY OF COMPUTER ENGINEERING'),
('2131', 'สาขาวิชานิวเคลียร์เทคโนโลยี (ภาษาไทย)', 'FIELD OF STUDY OF NUCLEAR ENGINEERING'),
('2132', 'สาขาวิชาวิศวกรรมแหล่งน้ำ (ภาษาไทย)', 'FIELD OF STUDY OF WATER RESOURCES ENGINEERING'),
('2133', 'สาขาวิชาวิศวกรรมระบบเมืองอัจฉริยะ', 'FIELD OF STUDY OF SMART URBAN SYSTEMS ENGINEERING'),
('2140', 'กลุ่มวิชาวิศวกรรมทั่วไป', 'GENERAL ENGINEERING'),
('2141', 'สาขาวิชาวิศวกรรมนาโน', 'FIELD OF STUDY OF NANO ENGINEERING'),
('2142', 'สาขาวิชาวิศวกรรมการออกแบบและการผลิตยานยนต์', 'FIELD OF STUDY OF AUTOMOTIVE DESIGN AND MANUFACTURING ENGINEERING'),
('2143', 'สาขาวิชาวิศวกรรมสารสนเทศและการสื่อสาร', 'FIELD OF STUDY OF INFORMATION AND COMMUNICATION ENGINEERING'),
('2145', 'สาขาวิชาวิศวกรรมอากาศยาน', 'FIELD OF STUDY OF AERO SPACE ENGINEERING'),
('2146', 'ศูนย์ระดับภูมิภาคทางวิศวกรรมระบบการผลิต', 'REGIONAL CENTRE FOR MANUFACTURING SYSTEMS ENGINEERING'),
('2147', 'สาขาวิชาวิศวกรรมหุ่นยนต์และปัญญาประดิษฐ์(นานาชาติ)', 'FIELD OF STUDY OF ROBOTICS AND ARTIFICIAL INTELLIGENCE (INTERNATIONAL)'),
('2148', 'สาขาวิชาวิศวกรรมยานพาหนะและโครงสร้างพื้นฐานระบบราง', 'FIELD OF STUDY OF RAILWAY VEHICLES AND INFRASTRUCTURE ENGINEERING'),
('2163', 'สาขาวิชาการจัดการทางวิศวกรรม', 'FIELD OF STUDY OF ENGINEERING MANAGEMENT'),
('2180', 'ไม่สังกัดภาควิชา/เทียบเท่า (นานาชาติ)', 'COMMON COURSE (INTERNATIONAL)'),
('2181', 'สาขาวิชาวิศวกรรมโยธา (นานาชาติ)', 'FIELD OF STUDY OF CIVIL ENGINEERING (INTERNATIONAL)'),
('2182', 'สาขาวิชาวิศวกรรมไฟฟ้า (นานาชาติ)', 'FIELD OF STUDY OF ELECTRICAL ENGINEERING (INTERNATIONAL)'),
('2183', 'สาขาวิชาวิศวกรรมเครื่องกล (นานาชาติ)', 'FIELD OF STUDY OF MECHANICAL ENGINEERING (INTERNATIONAL)'),
('2184', 'สาขาวิชาวิศวกรรมอุตสาหการ (นานาชาติ)', 'FIELD OF STUDY OF INDUSTRIAL ENGINEERING (INTERNATIONAL)'),
('2185', 'สาขาวิชาวิศวกรรมเคมี (นานาชาติ)', 'FIELD OF STUDY OF CHEMICAL ENGINEERING (INTERNATIONAL)'),
('2186', 'สาขาวิชาวิศวกรรมเหมืองแร่และปิโตรเลียม (นานาชาติ)', 'FIELD OF STUDY OF MINING AND PETROLEUM ENGINEERING (INTERNATIONAL)'),
('2187', 'สาขาวิชาวิศวกรรมสิ่งแวดล้อม (นานาชาติ)', 'FIELD OF STUDY OF ENVIRONMENTAL ENGINEERING (INTERNATIONAL)'),
('2188', 'สาขาวิชาวิศวกรรมสำรวจ (นานาชาติ)', 'FIELD OF STUDY OF SURVEY ENGINEERING (INTERNATIONAL)'),
('2189', 'สาขาวิชาวิศวกรรมโลหการ (นานาชาติ)', 'FIELD OF STUDY OF METALLURGICAL ENGINEERING (INTERNATIONAL)'),
('2190', 'สาขาวิชาวิศวกรรมคอมพิวเตอร์ (นานาชาติ)', 'FIELD OF STUDY OF COMPUTER ENGINEERING (INTERNATIONAL)'),
('2191', 'สาขาวิชานิวเคลียร์เทคโนโลยี (นานาชาติ)', 'FIELD OF STUDY OF NUCLEAR ENGINEERING (INTERNATIONAL)'),
('2192', 'สาขาวิชาวิศวกรรมแหล่งน้ำ (นานาชาติ)', 'FIELD OF STUDY OF WATER RESOURCES ENGINEERING (INTERNATIONAL)'),
('21A7', 'ภาควิชาวิศวกรรมสิ่งแวดล้อม', 'DEPARTMENT OF ENVIRONMENTAL ENGINEERING'),
('2200', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2201', 'ภาควิชาภาษาไทย', 'DEPARTMENT OF THAI'),
('2202', 'ภาควิชาภาษาอังกฤษ', 'DEPARTMENT OF ENGLISH'),
('2204', 'ภาควิชาประวัติศาสตร์', 'DEPARTMENT OF HISTORY'),
('2205', 'ภาควิชาภูมิศาสตร์', 'DEPARTMENT OF GEOGRAPHY'),
('2206', 'ภาควิชาบรรณารักษศาสตร์', 'DEPARTMENT OF LIBRARY SCIENCE'),
('2207', 'ภาควิชาปรัชญา', 'DEPARTMENT OF PHILOSOPHY'),
('2208', 'ภาควิชาศิลปการละคร', 'DEPARTMENT OF DRAMATIC ARTS'),
('2209', 'ภาควิชาภาษาศาสตร์', 'DEPARTMENT OF LINGUISTICS'),
('2210', 'ภาควิชาวรรณคดีเปรียบเทียบ', 'DEPARTMENT OF COMPARATIVE LITERATURE'),
('2220', 'ภาควิชาภาษาตะวันออก', 'DEPARTMENT OF EASTERN LANGUAGES'),
('2221', 'สาขาวิชาภาษาเอเชียใต้', 'SOUTH ASIAN LANGUAGES SECTION'),
('2222', 'สาขาวิชาภาษาจีน', 'CHINESE SECTION'),
('2223', 'สาขาวิชาภาษาญี่ปุ่น', 'JAPANESE SECTION'),
('2224', 'สาขาวิชาภาษามาเลย์', 'MALAY SECTION'),
('2225', 'สาขาวิชาภาษาเกาหลี', 'KOREAN SECTION'),
('2226', 'สาขาวิชาภาษาเวียตนาม', 'VIETNAMESE SECTION'),
('2227', 'สาขาวิชาภาษาพม่า', 'BURMESE SECTION'),
('2228', 'สาขาวิชาภาษาอาหรับ', 'ARABIC SECTION'),
('2230', 'ภาควิชาภาษาตะวันตก', 'DEPARTMENT OF WESTERN LANGUAGES'),
('2231', 'สาขาวิชาภาษาฝรั่งเศส', 'FRENCH SECTION'),
('2232', 'สาขาวิชาภาษาเยอรมัน', 'GERMAN SECTION'),
('2233', 'สาขาวิชาภาษาสเปน', 'SPANISH SECTION'),
('2234', 'สาขาวิชาภาษาอิตาเลียน', 'ITALIAN SECTION'),
('2235', 'สาขาวิชาภาษาโปรตุเกส', 'PORTUGUESE SECTION'),
('2236', 'สาขาวิชาภาษารัสเซีย', 'RUSSIAN SECTION'),
('2240', 'หน่วยวิชาวรรณคดีเปรียบเทียบ', 'COMPARATIVE LITERATURE DIVISION'),
('2241', 'สาขาวิชาการแปลและการล่าม', 'FIELD OF STUDY OF TRANSLATION AND INTERPRETATION'),
('2242', 'สาขาวิชาพุทธศาสน์ศึกษา', 'FIELD OF STUDY OF BUDDHIST RELIGION STUDIES'),
('2243', 'สาขาวิชาวรรณคดีและวรรณคดีเปรียบเทียบ', 'FIELD OF STUDY OF LITERATURE AND COMPARATIVE LITERATURE'),
('2244', 'สาขาวิชาภาษาเขมร', 'CAMBODIAN SECTION'),
('2245', 'สาขาวิชาภาษาลาว', 'LAO SECTION'),
('2246', 'สาขาวิชาภาษามอญ', 'MON SECTION'),
('2247', 'สาขาวิชาไทยศึกษา', 'FIELD OF STUDY OF THAI STUDIES'),
('2248', 'สาขาวิชาภาษาไทยในฐานะภาษาต่างประเทศ', 'THAI AS A FOREIGN LANGUAGE'),
('2296', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดมนุษย์ศาสตร์และศิลปะ', '21ST C.GENED IN ART & HUMANITIES'),
('2300', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2301', 'ภาควิชาคณิตศาสตร์และวิทยาการคอมพิวเตอร์', 'DEPARTMENT OF MATHEMATICS AND COMPUTER SCIENCE'),
('2302', 'ภาควิชาเคมี', 'DEPARTMENT OF CHEMISTRY'),
('2303', 'ภาควิชาชีววิทยา', 'DEPARTMENT OF BIOLOGY'),
('2304', 'ภาควิชาฟิสิกส์', 'DEPARTMENT OF PHYSICS'),
('2305', 'ภาควิชาพฤกษศาสตร์', 'DEPARTMENT OF BOTANY'),
('2306', 'ภาควิชาเคมีเทคนิค', 'DEPARTMENT OF CHEMICAL TECHNOLOGY'),
('2307', 'ภาควิชาธรณีวิทยา', 'DEPARTMENT OF GEOLOGY'),
('2308', 'ภาควิชาวิทยาศาสตร์สิ่งแวดล้อม', 'DEPARTMENT OF ENVIRONMENTAL SCIENCE'),
('2309', 'ภาควิชาวิทยาศาสตร์ทางทะเล', 'DEPARTMENT OF MARINE SCIENCE'),
('2310', 'ภาควิชาชีวเคมี', 'DEPARTMENT OF BIOCHEMISTRY'),
('2311', 'ภาควิชาวัสดุศาสตร์', 'DEPARTMENT OF MATERIALS SCIENCE'),
('2312', 'ภาควิชาจุลชีววิทยา', 'DEPARTMENT OF MICROBIOLOGY'),
('2313', 'ภาควิชาเทคโนโลยีทางภาพและการพิมพ์', 'DEPARTMENT OF IMAGING AND PRINTING TECHNOLOGY'),
('2314', 'ภาควิชาเทคโนโลยีทางอาหาร', 'DEPARTMENT OF FOOD TECHNOLOGY'),
('2315', 'ภาควิชาคณิตศาสตร์', 'DEPARTMENT OF MATHEMATICS'),
('2316', 'ภาควิชาวิทยาศาสตร์ทั่วไป', 'DEPARTMENT OF GENERAL SCIECNE'),
('2339', 'ภาควิชาวิทยาศาสตร์ทางภาพถ่ายและเทคโนโลยีทางการพิมพ์', 'DEPARTMENT OF IMAGING AND PRINTING TECHNOLOGY'),
('2340', 'สหสาขาวิชาวิทยาศาสตร์สภาวะแวดล้อม', 'INTER-DEPARTMENT OF ENVIRONMENTAL SCIENCE'),
('2341', 'สาขาวิชาเทคโนโลยีชีวภาพ', 'FIELD OF STUDY OF BIOTECHNOLOGY'),
('2342', 'สาขาวิชาปิโตรเคมีและวิทยาศาสตร์พอลิเมอร์', 'FIELD OF STUDY OF PETROCHEMISTRY AND POLYMER SCIENCE'),
('2343', 'สาขาวิชาเทคโนโลยีการเกษตร', 'FIELD OF STUDY OF AGRICULTURAL TECHNOLOGY'),
('2344', 'สาขาวิชาวิทยาศาสตร์ชีวภาพ', 'FIELD OF STUDY OF BIOLOGICAL SCIENCES'),
('2345', 'สาขาวิชาวิทยาศาสตร์มหโมเลกุล', 'FIELD OF STUDY OF MACROMOLECULAR SCIENCE'),
('2346', 'สาขาวิชามาตรวิทยา', 'FIELD OF STUDY OF METROLOGICAL SCIENCE'),
('2347', 'สาขาวิชาเทคโนโลยีเยื่อและกระดาษ', 'FIELD OF STUDY OF PULP AND PAPER TECHNOLOGY'),
('2348', 'สาขาวิชานิติวิทยาศาสตร์', 'FIELD OF STUDY OF FORENSIC SCIENCE'),
('2349', 'สาขาวิชาวิทยาศาสตร์ชีวภาพและเทคโนโลยีชีวภาพ', 'FIELD OF STUDY OF LIFE SCIENCE AND BIOTECHNOLOGY'),
('2350', 'สาขาวิชาวิทยาการคอมพิวเตอร์และเทคโนโลยีสารสนเทศ', 'FIELD OF STUDY OF COMPUTER SCIENCE AND INFORMATION TECHNOLOGY'),
('2351', 'สาขาวิชาธรณีศาสตร์ปิโตรเลียม', 'FIELD OF STUDY OF PETROLEUM GEOSCIENCE'),
('2352', 'สาขาวิชาวิทยาศาสตร์ระบบโลก', 'FIELD OF STUDY OF EARTH SYSTEM SCIENCE'),
('2353', 'สาขาวิชาวิทยาศาสตร์สิ่งแวดล้อม', 'FIELD OF STUDY OF ENVIRONMENTAL SCIENCE'),
('2354', 'สาขาวิชาวัสดุและเทคโนโลยียั่งยืนเพื่ออุตสาหกรรม', 'FIELD OF STUDY OF SUSTAINABLE MATERIALS AND TECHNOLOGY FOR INDUSTRIES'),
('2355', 'สาขาวิชาวิทยาศาสตร์และเทคโนโลยีอุตสาหการ', 'FIELD OF STUDY OF INDUSTRIAL SCIENCE AND TECHNOLOGY'),
('2398', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดวิทยาศาสตร์-คณิตศาสตร์', '21ST C.GENED IN SCI-MATH'),
('2400', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2401', 'ภาควิชาการปกครอง', 'DEPARTMENT OF GOVERNMENT'),
('2402', 'ภาควิชาความสัมพันธ์ระหว่างประเทศ', 'DEPARTMENT OF INTERNATIONAL RELATIONS'),
('2403', 'ภาควิชาสังคมวิทยาและมานุษยวิทยา', 'DEPARTMENT OF SOCIOLOGY AND ANTHROPOLOGY'),
('2404', 'ภาควิชารัฐประศาสนศาสตร์', 'DEPARTMENT OF PUBLIC ADMINISTRATION'),
('2410', 'สาขาวิชาการเมืองและโลกสัมพันธ์ศึกษา (นานาชาติ)', 'FIELD OF STUDY OF POLITICS AND GLOBAL STUDIES (INTERNATIONAL)'),
('2440', 'สาขาวิชาการพัฒนาระหว่างประเทศ', 'FIELD OF STUDY OF INTERNATIONAL DEVELOPMENT STUDIES'),
('2441', 'สาขาวิชาธรรมาภิบาล', 'FIELD OF STUDY OF GOVERNANCE'),
('2450', 'ความร่วมมือสถาบันการศึกษาต่างประเทศ University of Essex', 'COLLABORATION WITH THE UNIVERSITY OF ESSEX, UNITED KINGDOM'),
('2451', 'ความร่วมมือสถาบันการศึกษาต่างประเทศ University of Queensland', 'COLLABORATION WITH THE UNIVERSITY OF QUEENSLAND'),
('2500', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2501', 'ภาควิชาสถาปัตยกรรมศาสตร์', 'DEPARTMENT OF ARCHITECTURE'),
('2502', 'ภาควิชาการออกแบบอุตสาหกรรม', 'DEPARTMENT OF INDUSTRIAL DESIGN'),
('2503', 'ภาควิชาการวางแผนภาคและเมือง', 'DEPARTMENT OF URBAN AND REGIONAL PLANNING'),
('2504', 'ภาควิชาภูมิสถาปัตยกรรม', 'DEPARTMENT OF LANDSCAPE ARCHITECTURE'),
('2505', 'ภาควิชาสถาปัตยกรรมภายใน', 'DEPARTMENT OF INTERIOR ARCHITECTURE'),
('2506', 'ภาควิชาเคหการ', 'DEPARTMENT OF HOUSING'),
('2540', 'สาขาวิชาการออกแบบชุมชนเมือง', 'FIELD OF STUDY OF URBAN DESIGN'),
('2541', 'สาขาวิชาการออกแบบสถาปัตยกรรม', 'FIELD OF STUDY OF ARCHITECTURAL DESIGN'),
('2542', 'สาขาวิชาการออกแบบนิเทศศิลป์', 'FIELD OF STUDY OF COMMUNICATION DESIGN'),
('2600', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2601', 'ภาควิชาการบัญชี', 'DEPARTMENT OF ACCOUNTANCY'),
('2602', 'ภาควิชาพาณิชยศาสตร์', 'DEPARTMENT OF COMMERCE'),
('2603', 'ภาควิชาสถิติ', 'DEPARTMENT OF STATISTICS'),
('2604', 'ภาควิชาการธนาคารและการเงิน', 'DEPARTMENT OF BANKING AND FINANCE'),
('2605', 'ภาควิชาการตลาด', 'DEPARTMENT OF MARKETING'),
('2700', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2701', 'ภาควิชาสารัตถศึกษา', 'DEPARTMENT OF FOUNDATIONS OF EDUCATION'),
('2702', 'ภาควิชาวิจัยการศึกษา', 'DEPARTMENT OF EDUCATIONAL RESEARCH'),
('2703', 'ภาควิชาประถมศึกษา', 'DEPARTMENT OF ELEMENTARY EDUCATION'),
('2704', 'ภาควิชามัธยมศึกษา', 'DEPARTMENT OF SECONDARY EDUCATION'),
('2705', 'ภาควิชาพลศึกษา', 'DEPARTMENT OF PHYSICAL EDUCATION'),
('2706', 'ภาควิชาบริหารการศึกษา', 'DEPARTMENT OF EDUCATIONAL ADMINISTRATION'),
('2707', 'ภาควิชาจิตวิทยา', 'DEPARTMENT OF PSYCHOLOGY'),
('2708', 'ภาควิชาโสตทัศนศึกษา', 'DEPARTMENT OF AUDIO-VISUAL EDUCATION'),
('2709', 'ภาควิชาศิลปศึกษา', 'DEPARTMENT OF ART EDUCATION'),
('2710', 'ภาควิชาอุดมศึกษา', 'DEPARTMENT OF HIGHER EDUCATION'),
('2711', 'ภาควิชาการศึกษานอกโรงเรียน', 'DEPARTMENT OF NON-FORMAL EDUCATION'),
('2712', 'ภาควิชาดนตรีศึกษา', 'DEPARTMENT OF MUSIC EDUCATION'),
('2714', 'ภาควิชาหลักสูตร การสอนและเทคโนโลยีการศึกษา', 'DEPARTMENT OF CURRICULUM,INSTRUCTION, AND EDUCATIONAL TECHNOLOGY'),
('2715', 'ภาควิชาหลักสูตรและการสอน', 'DEPARTMENT OF CURRICULUM AND INSTRUCTION'),
('2716', 'สาขาวิชาหลักสูตรและการสอน', 'FIELD OF STUDY OF CURRICULUM AND INSTRUCTION'),
('2717', 'สาขาวิชาการศึกษาปฐมวัย', 'FIELD OF STUDY OF EARLY CHILDHOOD EDUCATION'),
('2718', 'สาขาวิชาประถมศึกษา', 'FIELD OF STUDY OF ELEMENTARY EDUCATION'),
('2719', 'สาขาวิชาการสอนภาษาไทย', 'FIELD OF STUDY OF TEACHING THAI LANGUAGE'),
('2720', 'สาขาวิชาการศึกษาคณิตศาสตร์', 'FIELD OF STUDY OF MATHEMATICS EDUCATION'),
('2721', 'สาขาวิชาการศึกษาวิทยาศาสตร์', 'FIELD OF STUDY OF SCIENCE EDUCATION'),
('2722', 'สาขาวิชาการสอนสังคมศึกษา', 'FIELD OF STUDY OF TEACHING SOCIAL STUDIES'),
('2723', 'สาขาวิชาสุขศึกษาและพลศึกษา', 'FIELD OF STUDY OF HEALTH AND PHYSICAL EDUCATION'),
('2724', 'สาขาวิชาการสอนการงานอาชีพและเทคโนโลยี', 'FIELD OF STUDY OF TEACHING CAREER AND TECHNOLOGY'),
('2725', 'สาขาวิชาการสอนภาษาต่างประเทศ', 'FIELD OF STUDY OF TEACHING FOREIGN LANGUAGES'),
('2726', 'สาขาวิชาเทคโนโลยีการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL TECHNOLOGY'),
('2735', 'ภาควิชาศิลปะ ดนตรีและนาฏศิลป์ศึกษา', 'DEPARTMENT OF ART,MUSIC, AND DANCE EDUCATION'),
('2736', 'สาขาวิชาศิลปศึกษา', 'FIELD OF STUDY OF ART EDUCATION'),
('2737', 'สาขาวิชาดนตรีศึกษา', 'FIELD OF STUDY OF MUSIC EDUCATION'),
('2738', 'สาขาวิชานาฏศิลป์ศึกษา', 'FIELD OF STUDY OF DANCE EDUCATION'),
('2740', 'สาขาวิชาหลักสูตรและการสอน', 'FIELD OF STUDY OF CURRICULUM AND INSTRUCTION'),
('2741', 'สาขาวิชาจิตวิทยาการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL PSYCHOLOGY'),
('2742', 'สาขาวิชาการอบรมและพัฒนาทรัพยากรมนุษย์', 'FIELD OF STUDY OF HUMAN RESOURCE TRAINING AND DEVELOPMENT'),
('2745', 'ภาควิชานโยบาย การจัดการและความเป็นผู้นำทางการศึกษา', 'DEPARTMENT OF EDUCATIONAL POLICY,MANAGEMENT, AND LEADERSHIP'),
('2746', 'สาขาวิชาพัฒนศึกษา', 'FIELD OF STUDY OF DEVELOPMENT EDUCATION'),
('2747', 'สาขาวิชาบริหารการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL ADMINISTRATION'),
('2748', 'สาขาวิชานิเทศการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL SUPERVISION'),
('2749', 'สาขาวิชาอุดมศึกษา', 'FIELD OF STUDY OF HIGHER EDUCATION'),
('2750', 'สาขาวิชาการศึกษานอกระบบโรงเรียน', 'FIELD OF STUDY OF NON-FORMAL EDUCATION'),
('2751', 'สาขาวิชาธุรกิจและอาชีวศึกษา', 'FIELD OF STUDY OF BUSINESS AND VOCATIONAL EDUCATION'),
('2752', 'สาขาวิชาภาวะผู้นำการบริหารระบบการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL SYSTEM MANAGEMENT LEADERSHIP'),
('2755', 'ภาควิชาวิจัยและจิตวิทยาการศึกษา', 'DEPARTMENT OF EDUCATIONAL RESEARCH AND PSYCHOLOGY'),
('2756', 'สาขาวิชาวิจัยการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL RESEARCH'),
('2757', 'สาขาวิชาการวัดและประเมินผลการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL MEASUREMENT AND EVALUATION'),
('2758', 'สาขาวิชาสถิติการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL STATISTICS'),
('2759', 'สาขาวิชาจิตวิทยาการศึกษา', 'FIELD OF STUDY OF EDUCATIONAL PSYCHOLOGY'),
('2760', 'สาขาวิชาวิธีวิทยาการพัฒนานวัตกรรมทางการศึกษา', 'FIELD OF STUDY OF METHODOLOGY FOR INNOVATION DEVELOPMENT IN EDUCATION'),
('2765', 'ภาควิชาเทคโนโลยีและสื่อสารการศึกษา', 'DEPARTMENT OF EDUCATIONAL TECHNOLOGY AND COMMUNICATIONS'),
('2766', 'สาขาวิชาเทคโนโลยีการศึกษา', ''),
('2767', 'สาขาวิชาคอมพิวเตอร์ศึกษา', ''),
('2775', 'ภาควิชาการศึกษาตลอดชีวิต', 'DEPARTMENT OF LIFELONG EDUCATION'),
('2776', 'สาขาวิชาการศึกษาตลอดชีวิตเพื่อการพัฒนาทรัพยากรมนุษย์และสังคม', 'FIELD OF STUDY OF LIFELONG EDUCATION FOR HUMAN RESOURCE AND SOCIETY DEVELOPMENT'),
('2777', 'สาขาวิชาพฤฒิศึกษา', 'FIELD OF STUDY OF ACTIVE AGING STUDIES'),
('2778', 'สาขาวิชาการศึกษาชุมชน', 'FIELD OF STUDY OF COMMUNITY EDUCATION'),
('2779', 'สาขาวิชานอกระบบโรงเรียน', 'FIELD OF STUDY OF NON-FORMAL EDUCATION'),
('2796', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดมนุษย์ศาสตร์และศิลปะ', '21ST C.GENED IN ART & HUMANITIES'),
('2800', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2801', 'ภาควิชาวารสารสนเทศ', 'DEPARTMENT OF JOURNALISM'),
('2802', 'ภาควิชาการสื่อสารมวลชน', 'DEPARTMENT OF MASS COMMUNICATION'),
('2803', 'ภาควิชาการประชาสัมพันธ์', 'DEPARTMENT OF PUBLIC RELATIONS'),
('2804', 'ภาควิชาวาทวิทยาและสื่อสารการแสดง', 'DEPARTMENT OF SPEECH COMMUNICATION AND PERFORMING ARTS'),
('2805', 'ภาควิชาการภาพยนตร์และภาพนิ่ง', 'DEPARTMENT OF MOTION PICTURES AND STILL PHOTOGRAPHY'),
('2840', 'หน่วยวิชาภาษาและวัฒนธรรมไทย', 'SECTION OF THAILANGUAGE AND CULTURE'),
('2894', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดสังคมศาสตร์', '21ST C.GENED IN SOCIAL SCIENCE'),
('2900', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('2940', 'รายวิชาหลักและทฤษฏีเศรษฐศาสตร์', 'PRINCIPLES AND THEORIES OF ECONOMICS'),
('2941', 'สาขาวิชาเศรษฐศาสตร์พัฒนาการ', 'FIELD OF STUDY OF DEVELOPMENT ECONOMICS'),
('2942', 'สาขาวิชาเศรษฐศาสตร์ระหว่างประเทศ', 'FIELD OF STUDY OF INTERNATIONAL ECONOMICS'),
('2943', 'สาขาวิชาเศรษฐศาสตร์การคลังสาธารณะ', 'FIELD OF STUDY OF ECONOMICS OF PUBLIC FINANCE'),
('2944', 'สาขาวิชาเศรษฐศาสตร์การเงิน', 'FIELD OF STUDY OF MONETARY ECONOMICS'),
('2945', 'สาขาวิชาเศรษฐศาสตร์ปริมาณวิเคราะห์', 'FIELD OF STUDY OF QUANTITATIVE ECONOMICS'),
('2946', 'สาขาวิชาเศรษฐศาสตร์แรงงานและทรัพยากรมนุษย์', 'FIELD OF STUDY OF LABOUR AND HUMAN RESOURCE ECONOMICS'),
('2947', 'สาขาวิชาเศรษฐศาสตร์การเกษตรและธุรกิจการเกษตร', 'FIELD OF STUDY OF ECONOMICS OF AGRICULTURE AND AGRI-BUSINESS'),
('2948', 'สาขาวิชาเศรษฐศาสตร์สิ่งแวดล้อมและทรัพยากรธรรมชาติ', 'FIELD OF STUDY OF ENVIRONMENTAL AND NATURAL RESOURCE ECONOMICS'),
('2949', 'สาขาวิชาเศรษฐศาสตร์ธุรกิจและการจัดการ', 'FIELD OF STUDY OF BUSINESS AND MANAGERIAL ECONOMICS'),
('2950', 'หมวดวิชาเศรษฐศาสตร์สาธารณสุข', 'AREA OF CONCENTRATION OF HEALTH ECONOMICS'),
('2951', 'หมวดวิชาเศรษฐศาสตร์การเมือง', 'AREA OF CONCENTRATION OF POLITICAL ECONOMY'),
('2952', 'สาขาวิชาเศรษฐศาสตร์', 'FIELD OF STUDY OF ECONOMICS'),
('2953', 'สาขาวิชาเศรษฐศาสตร์แรงงานและการจัดการทรัพยากรมนุษย์', 'FIELD OF STUDY OF LABOUR ECONOMICS AND HUMAN RESOURCE MANAGEMENT'),
('2954', 'สาขาวิชาเศรษฐศาสตร์การเมืองและนโยบายสาธารณะ', 'FIELD OF STUDY OF POLITICAL ECONOMY AND PUBLIC POLICIES'),
('2955', 'สาขาวิชาเศรษฐศาสตร์ประยุกต์', 'FIELD OF STUDY OF APPLIED ECONOMICS'),
('3000', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3001', 'ภาควิชากายวิภาคศาสตร์', 'DEPARTMENT OF ANATOMY'),
('3002', 'ภาควิชากุมารเวชศาสตร์', 'DEPARTMENT OF PEDIATRICS'),
('3003', 'ภาควิชาจักษุวิทยา', 'DEPARTMENT OF OPHTHALMOLOGY'),
('3004', 'ภาควิชาจุลชีววิทยา', 'DEPARTMENT OF MICROBIOLOGY'),
('3005', 'ภาควิชาชีวเคมี', 'DEPARTMENT OF BIOCHEMISTRY'),
('3006', 'ภาควิชานิติเวชศาสตร์', 'DEPARTMENT OF FORENSIC MEDICINE'),
('3007', 'ภาควิชาจิตเวชศาสตร์', 'DEPARTMENT OF PSYCHIATRY'),
('3008', 'ภาควิชาปรสิตวิทยา', 'DEPARTMENT OF PARASITOLOGY'),
('3009', 'ภาควิชาพยาธิวิทยา', 'DEPARTMENT OF PATHOLOGY'),
('3010', 'ภาควิชาเภสัชวิทยา', 'DEPARTMENT OF PHARMACOLOGY'),
('3011', 'ภาควิชารังสีวิทยา', 'DEPARTMENT OF RADIOLOGY'),
('3012', 'ภาควิชาวิสัญญีวิทยา', 'DEPARTMENT OF ANESTHESIOLOGY'),
('3013', 'ภาควิชาเวชศาสตร์ชันสูตร', 'DEPARTMENT OF LABORATORY MEDICINE'),
('3014', 'ภาควิชาเวชศาสตร์ป้องกันและสังคม', 'DEPARTMENT OF PREVENTIVE AND SOCIAL MEDICINE'),
('3015', 'ภาควิชาศัลยศาสตร์', 'DEPARTMENT OF SURGERY'),
('3016', 'ภาควิชาออร์โธปิดิกส์', 'DEPARTMENT OF ORTHOPEDICS'),
('3017', 'ภาควิชาสรีรวิทยา', 'DEPARTMENT OF PHYSIOLOGY'),
('3018', 'ภาควิชาสูติศาสตร์-นรีเวชวิทยา', 'DEPARTMENT OF OBSTETRICS AND GYNECOLOGY'),
('3019', 'ภาควิชาโสต ศอ นาสิกวิทยา', 'DEPARTMENT OF OTOLARYNGOLOGY'),
('3020', 'ภาควิชาอายุรศาสตร์', 'DEPARTMENT OF MEDICINE'),
('3021', 'ภาควิชาเวชศาสตร์ฟื้นฟู', 'DEPARTMENT OF REHABILITATION MEDICINE'),
('3022', 'ภาควิชาอายุรศาสตร์', 'DEPARTMENT OF MEDICINE'),
('3023', 'ภาควิชาเวชศาสตร์ฉุกเฉิน', 'DEPARTMENT OF EMERGENCY MEDICINE'),
('3024', 'ภาควิชาเวชศาสตร์ครอบครัว', 'DEPARTMENT OF FAMILY MEDICINE'),
('3030', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3031', 'กลุ่มวิชาโรงพยาบาลพระปกเกล้า', 'PRAPOKKLAO HOSPITAL'),
('3032', 'กลุ่มวิชาโรงพยาบาลชลบุรี', 'CHONBURI HOSPITAL'),
('3033', 'กลุ่มวิชาโรงพยาบาลภูมิพลอดุลยเดช', 'BHUMIBOL ADULYADEJ HOSPITAL'),
('3040', 'สาขาวิชาการพัฒนาสุขภาพ', 'FIELD OF STUDY OF HEALTH DEVELOPMENT'),
('3041', 'สหสาขาวิชาจุลชีววิทยาทางการแพทย์', 'INTER-DEPARTMENT OF MEDICAL MICROBIOLOGY'),
('3042', 'สหสาขาวิชาเภสัชวิทยา', 'INTER-DEPARTMENT OF PHARMACOLOGY'),
('3043', 'สหสาขาวิชาสรีรวิทยา', 'INTER-DEPARTMENT OF PHYSIOLOGY'),
('3044', 'สหสาขาวิชาการศึกษาวิชาชีพสุขภาพ', 'INTER-DEPARTMENT OF HEALTH PROFESSIONS EDUCATION'),
('3045', 'สาขาวิชาเวชศาสตร์ครอบครัว', 'FIELD OF STUDY OF FAMILY MEDICINE'),
('3050', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3098', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดวิทยาศาสตร์-คณิตศาสตร์', '21ST C.GENED IN SCI-MATH'),
('3100', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3101', 'ภาควิชากายวิภาคศาสตร์', 'DEPARTMENT OF VETERINARY ANATOMY'),
('3102', 'ภาควิชาสรีรวิทยา', 'DEPARTMENT OF VETERINARY PHYSIOLOGY'),
('3103', 'ภาควิชาสัตวบาล', 'DEPARTMENT OF ANIMAL HUSBANDRY'),
('3104', 'ภาควิชาเภสัชวิทยา', 'DEPARTMENT OF VETERINARY PHARMACOLOGY'),
('3105', 'ภาควิชาพยาธิวิทยา', 'DEPARTMENT OF VETERINARY PATHOLOGY'),
('3106', 'ภาควิชาศัลยศาสตร์', 'DEPARTMENT OF VETERINARY SURGERY'),
('3107', 'ภาควิชาอายุรศาสตร์', 'DEPARTMENT OF VETERINARY MEDICINE'),
('3108', 'ภาควิชาสูติศาสตร์-เธนุเวชวิทยาและวิทยาการสืบพันธุ์', 'DEPARTMENT OF OBSTETRICS GYNAECOLOGY AND REPRODUCTION'),
('3109', 'ภาควิชาสัตวแพทยสาธารณสุข', 'DEPARTMENT OF VETERINARY PUBLIC HEALTH'),
('3110', 'ภาควิชาจุลชีววิทยา', 'DEPARTMENT OF MICROBIOLOGY'),
('3140', 'ไม่สังกัดกลุ่มวิชา/เทียบเท่า', 'COMMON COURSE'),
('3141', 'กลุ่มวิชาชีวเวชศาสตร์', 'VETERINARY BIOMEDICAL SCIENCES'),
('3142', 'กลุ่มวิชาพยาธิชีววิทยา', 'VETERINARY PATHOBIOLOGY'),
('3143', 'กลุ่มวิชาเวชศาสตร์คลินิก', 'VETERINARY CLINICAL SCIENCES'),
('3144', 'กลุ่มวิชาเวชศาสตร์ประชากรและการป้องกันโรคสัตว์', ''),
('3200', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3201', 'ภาควิชากายวิภาคศาสตร์', 'DEPARTMENT OF ANATOMY'),
('3202', 'ภาควิชาจุลชีววิทยา', 'DEPARTMENT OF MICROBIOLOGY'),
('3203', 'ภาควิชาทันตพยาธิวิทยา', 'DEPARTMENT OF ORAL PATHOLOGY'),
('3204', 'ภาควิชาเภสัชวิทยา', 'DEPARTMENT OF PHARMACOLOGY'),
('3205', 'ภาควิชาสรีรวิทยา', 'DEPARTMENT OF PHYSIOLOGY'),
('3206', 'ภาควิชาทันตกรรมจัดฟัน', 'DEPARTMENT OF ORTHODONTICS'),
('3207', 'ภาควิชาทันตกรรมประดิษฐ์', 'DEPARTMENT OF PROSTHODONTICS'),
('3208', 'ภาควิชาทันตกรรมหัตถการ', 'DEPARTMENT OF OPERATIVE DENTISTRY'),
('3209', 'ภาควิชารังสีวิทยา', 'DEPARTMENT OF RADIOLOGY'),
('3210', 'ภาควิชาศัลยศาสตร์', 'DEPARTMENT OF ORAL AND MAXILLOFACIAL SURGERY'),
('3211', 'ภาควิชาเวชศาสตร์ช่องปาก', 'DEPARTMENT OF ORAL MEDICINE'),
('3212', 'ภาควิชาทันตกรรมสำหรับเด็ก', 'DEPARTMENT OF PEDIATRIC DENTISTRY'),
('3213', 'ภาควิชาชีวเคมี', 'DEPARTMENT OF BIOCHEMISTRY'),
('3214', 'ภาควิชาทันตกรรมชุมชน', 'DEPARTMENT OF COMMUNITY DENTISTRY'),
('3215', 'ภาควิชาปริทันตวิทยา', 'DEPARTMENT OF PERIODONTOLOGY'),
('3216', 'ภาควิชาทันตกรรมบดเคี้ยว', 'DEPARTMENT OF OCCLUSION'),
('3300', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3301', 'ภาควิชาเภสัชกรรม', 'DEPARTMENT OF PHARMACY'),
('3302', 'ภาควิชาเภสัชอุตสาหกรรม', 'DEPARTMENT OF MANUFACTURING PHARMACY'),
('3303', 'ภาควิชาเภสัชเคมี', 'DEPARTMENT OF PHARMACEUTICAL CHEMISTRY'),
('3304', 'ภาควิชาชีวเคมี', 'DEPARTMENT OF BIOCHEMISTRY'),
('3305', 'ภาควิชาอาหารเคมี', 'DEPARTMENT OF FOOD CHEMISTRY'),
('3306', 'ภาควิชาเภสัชพฤกษศาสตร์', 'DEPARTMENT OF PHARMACEUTICAL BOTANY'),
('3307', 'ภาควิชาเภสัชเวท', 'DEPARTMENT OF PHARMACOGNOSY'),
('3308', 'ภาควิชาจุลชีววิทยา', 'DEPARTMENT OF MICROBIOLOGY'),
('3309', 'ภาควิชาสรีรวิทยา', 'DEPARTMENT OF PHYSIOLOGY'),
('3310', 'ภาควิชาเภสัชวิทยา', 'DEPARTMENT OF PHARMACOLOGY'),
('3311', 'ภาควิชาวิทยาการเภสัชกรรมและเภสัชอุตสาหกรรม', 'DEPARTMENT OF PHARMACEUTICS AND INDUSTRIAL PHARMACY'),
('3312', 'ภาควิชาเภสัชกรรมปฏิบัติ', 'DEPARTMENT OF PHARMACY PRACTICE'),
('3313', 'ภาควิชาเภสัชศาสตร์สังคมและบริหาร', 'DEPARTMENT OF SOCIAL AND ADMINISTRATIVE PHARMACY'),
('3314', 'ภาควิชาอาหารและเภสัชเคมี', 'DEPARTMENT OF FOOD AND PHARMACEUTICAL CHEMISTRY'),
('3315', 'ภาควิชาเภสัชเวทและเภสัชพฤกษศาสตร์', 'DEPARTMENT OF PHARMACOGNOSY AND PHARMACEUTICAL BOTANY'),
('3316', 'ภาควิชาชีวเคมีและจุลชีววิทยา', 'DEPARTMENT OF BIOCHEMISTRY AND MICROBIOLOGY'),
('3317', 'ภาควิชาเภสัชวิทยาและสรีรวิทยา', 'DEPARTMENT OF PHARMACOLOGY AND PHYSIOLOGY'),
('3340', 'สาขาวิชาเภสัชกรรม', 'FIELD OF STUDY OF PHARMACEUTICS'),
('3341', 'สาขาวิชาเภสัชเคมีและผลิตภัณฑ์ธรรมชาติ', 'FIELD OF STUDY OF PHARMACEUTICAL CHEMISTRY AND NATURAL PRODUCTS'),
('3342', 'สาขาวิชาเภสัชศาสตร์ชีวภาพ', 'FIELD OF STUDY OF BIOPHARMACEUTICAL SCIENCE'),
('3343', 'สาขาวิชาเภสัชศาสตร์สังคมและบริหาร', 'FIELD OF STUDY OF SOCIAL AND ADMINISTRATIVE PHARMACY'),
('3344', 'สาขาวิชาเทคโนโลยีเภสัชกรรม', 'FIELD OF STUDY OF PHARMACEUTICAL TECHNOLOGY'),
('3345', 'สาขาวิชาเภสัชบริบาลศาสตร์', 'FIELD OF STUDY OF DOCTOR OF PHARMACY'),
('3346', 'สาขาวิชาเภสัชกรรมผลิตภัณฑ์', 'FIELD OF STUDY OF PHARMACEUTICAL PRODUCTS'),
('3347', 'สาขาวิชาการบริบาลทางเภสัชกรรม', 'FIELD OF STUDY OF PHARMACEUTICAL CARE'),
('3348', 'สาขาวิชาเภสัชเวท', 'FIELD OF STUDY OF PHARMACOGNOSY'),
('3400', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3401', 'ภาควิชากฎหมายแพ่งและพาณิชย์', 'DEPARTMENT OF CIVIL AND COMMERCIAL LAWS'),
('3402', 'ภาควิชากฎหมายอาญาและอาชญาวิทยา', 'DEPARTMENT OF CRIMINAL LAW AND CRIMINOLOGY'),
('3403', 'ภาควิชากฎหมายวิธีพิจารณาและธรรมนูญศาล', 'DEPARTMENT OF PROCEDURAL AND COURT ORGANIZATION LAWS'),
('3404', 'ภาควิชากฎหมายการปกครองและกฎหมายทั่วไป', 'DEPARTMENT OF ADMINISTRATIVE LAW AND JURISPRUDENCE'),
('3405', 'ภาควิชากฎหมายระหว่างประเทศ', 'DEPARTMENT OF INTERNATIONAL LAW'),
('3480', 'สาขาวิชากฎหมายธุรกิจและเทคโนโลยี (นานาชาติ)', 'FIELD OF STUDY OF BUSINESS AND TECHNOLOGY LAW (INTERNATIONAL)'),
('3494', 'การศึกษาทั่วไปในศตวรรษที่ 21 หมวดสังคมศาสตร์', '21ST C.GENED IN SOCIAL SCIENCE'),
('3500', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3501', 'ภาควิชาทัศนศิลป์', 'DEPARTMENT OF VISUAL ARTS'),
('3502', 'ภาควิชานฤมิตศิลป์', 'DEPARTMENT OF CREATIVE ARTS'),
('3503', 'ภาควิชาดุริยางคศิลป์', 'DEPARTMENT OF MUSIC'),
('3504', 'ภาควิชานาฏยศิลป์', 'DEPARTMENT OF DANCE'),
('3600', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3640', 'สาขาวิชาการพยาบาลครอบครัว', 'FIELD OF STUDY OF FAMILY NURSING'),
('3641', 'สาขาวิชาการพยาบาลผู้ใหญ่', 'FIELD OF STUDY OF ADULT NURSING'),
('3642', 'สาขาวิชาการพยาบาลสุขภาพจิตและจิตเวช', 'FIELD OF STUDY OF MENTAL HEALTH AND PSYCHIATRIC NURSING'),
('3643', 'สาขาวิชาการอนามัยชุมชน', 'FIELD OF STUDY OF COMMUNITY HEALTH NURSING'),
('3644', 'สาขาวิชาการพยาบาลสูติศาสตร์และการผดุงครรภ์', 'FIELD OF STUDY OF OBSTETRIC NURSING AND MIDWIFERY'),
('3645', 'สาขาวิชาการพยาบาลศึกษา', 'FIELD OF STUDY OF NURSING EDUCATION'),
('3646', 'สาขาวิชาการบริหารการพยาบาล', 'FIELD OF STUDY OF NURSING ADMINISTRATION'),
('3647', 'สาขาวิชาพยาบาลศาสตร์', 'FIELD OF STUDY OF NURSING SCIENCE'),
('3648', 'สาขาวิชาการพยาบาลเด็ก', 'FIELD OF STUDY OF PEDIATRIC NURSING'),
('3649', 'สาขาวิชาการพยาบาลผู้สูงอายุ', 'FIELD OF STUDY OF GERONTOLOGICAL NURSING'),
('3700', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3701', 'ภาควิชาเคมีคลินิก', 'DEPARTMENT OF CLINICAL CHEMISTRY'),
('3702', 'ภาควิชาจุลทรรศน์ศาสตร์คลินิก', 'DEPARTMENT OF CLINICAL MICROSCOPY'),
('3703', 'ภาควิชาเวชศาสตร์การธนาคารเลือดและจุลชีววิทยาคลินิก', 'DEPARTMENT OF TRANSFUSION MEDICINE AND CLINICAL MICROBIOLOGY'),
('3704', 'ภาควิชากายภาพบำบัด', 'DEPARTMENT OF PHYSICAL THERAPY'),
('3705', 'ภาควิชาโภชนาการและการกำหนดอาหาร', 'DEPARTMENT OF NUTRITION AND DIETETICS'),
('3706', 'ภาควิชารังสีเทคนิคและฟิสิกส์ทางการแพทย์', 'DEPARTMENT OF RADIOLOGICAL AND MEDICAL PHYSICS'),
('3741', 'สาขาวิชาเทคนิคการแพทย์', 'FIELD OF STUDY OF MEDICAL TECHNOLOGY'),
('3742', 'สาขาวิชากายภาพบำบัด', 'FIELD OF STUDY OF PHYSICAL THERAPY'),
('3743', 'สาขาวิชาโภชนาการและการกำหนดอาหาร', 'FIELD OF STUDY OF NUTRITION AND DIETETICS'),
('3744', 'สาขาวิชาชีวเคมีคลินิคและอณูทางการแพทย์', 'FIELD OF STUDY OF CLINICAL BIOCHEMISTRY AND MOLECULAR MEDICINE'),
('3745', 'สาขาวิชาวิทยาศาสตร์โลหิตวิทยาคลินิก', 'FIELD OF STUDY OF CLINICAL HEMATOLOGY SCIENCES'),
('3746', 'สาขาวิชาวิทยาศาสตร์ระดับโมเลกุลทางจุลชีววิทยาทางการแพทย์และวิทยาภูมิคุ้มกัน', 'FIELD OF STUDY OF MOLECULAR SCIENCE OF MEDICAL MICROBIOLOGY AND IMMUNOLOGY'),
('3747', 'สาขาวิชาอาหารและโภชนาการ', 'FIELD OF STUDY OF FOOD AND NUTRITION'),
('3748', 'สาขาวิชาผู้นำห้องปฏิบัติการระดับสากล', 'FIELD OF STUDY OF GLOBAL LABORATORY LEADERSHIP PROGRAM'),
('3800', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3801', 'สาขาวิชาจิตวิทยาการทดลอง', 'FIELD OF STUDY OF EXPERIMENTAL PSYCHOLOGY'),
('3802', 'สาขาวิชาจิตวิทยาการปรึกษา', 'FIELD OF STUDY OF COUNSELING PSYCHOLOGY'),
('3803', 'สาขาวิชาจิตวิทยาสังคม', 'FIELD OF STUDY OF SOCIAL PSYCHOLOGY'),
('3804', 'สาขาวิชาจิตวิทยาพัฒนาการ', 'FIELD OF STUDY OF DEVELOPMENTAL PSYCHOLOGY'),
('3805', 'สาขาวิชาจิตวิทยาอุตสาหกรรมและองค์การ', 'FIELD OF STUDY OF INDUSTRIAL AND ORGANIZATIONAL PSYCHOLOGY'),
('3806', 'สาขาวิชาจิตวิทยาชุมชน', 'FIELD OF STUDY OF COMMUNITY PSYCHOLOGY'),
('3807', 'สาขาวิชาวิทยาศาสตร์จิตวิทยา', 'FIELD OF STUDY OF PSYCHOLOGICAL SCIENCE'),
('3808', 'สาขาวิชาจิตวิทยาคลินิก', 'FIELD OF STUDY OF CLINICAL PSYCHOLOGY'),
('3809', 'สาขาวิชาจิตวิทยาทรัพยากรมนุษย์และการทำงาน', 'FIELD OF STUDY OF HUMAN RESOURCE AND WORK PSYCHOLOGY'),
('3810', 'สาขาวิชาการวิจัยจิตวิทยาประยุกต์', 'FIELD OF STUDY OF APPLIED PSYCHOLOGY RESEARCH'),
('3811', 'สาขาวิชาจิตวิทยาธุรกิจ', 'FIELD OF STUDY OF BUSINESS PSYCHOLOGY'),
('3812', 'สาขาวิชาจิตวิทยาปริชาน', 'FIELD OF STUDY OF COGNITIVE PSYCHOLOGY'),
('3900', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('3901', 'กลุ่มวิชาวิทยาศาสตร์และเทคโนโลยีการกีฬา', 'AREA OF CONCENTRATION OF SPORTS SCIENCE AND TECHNOLOGY'),
('3902', 'กลุ่มวิชาวิทยาศาสตร์และเทคโนโลยีการกีฬา', 'AREA OF CONCENTRATION OF SPORTS SCIENCE AND TECHNOLOGY'),
('3903', 'กลุ่มวิชาการโค้ชและจิตวิทยาการกีฬา', 'AREA OF CONCENTRATION OF SPORTS COACHING AND SPORTS PSYCHOLOGY'),
('3904', 'กลุ่มวิชาวิทยาศาสตร์สุขภาพประยุกต์', 'AREA OF CONCENTRATION OF APPLIED HEALTH SCIENCE'),
('3905', 'กลุ่มวิชาการโค้ชและจิตวิทยาการกีฬา', 'AREA OF CONCENTRATION OF SPORTS COACHING AND SPORTS PSYCHOLOGY'),
('3906', 'กลุ่มวิชานันทนาการศาสตร์และการจัดการกีฬา', 'AREA OF CONCENTRATION OF RECREATION SCIENCE AND SPORTS MANAGEMENT'),
('3907', 'กลุ่มวิชานันทนาการศาสตร์และการจัดการกีฬา', 'AREA OF CONCENTRATION OF RECREATION SCIENCE AND SPORTS MANAGEMENT'),
('3908', 'กลุ่มวิชาวิทยาศาสตร์และเทคโนโลยีทางการกีฬา', 'AREA OF CONCENTRATION OF SPORTS SCIENCE AND TECHNOLOGY'),
('3910', 'ไม่สังกัดกลุ่มวิชา/เทียบเท่า', 'COMMON COURSE'),
('3911', 'กลุ่มวิชาวิทยาศาสตร์การกีฬา', 'SPORTS SCIENCE'),
('3912', 'กลุ่มวิชาการจัดการการกีฬา', 'SPORTS MANAGEMENT'),
('3913', 'กลุ่มวิชาวิทยาการส่งเสริมสุขภาพ', 'HEALTH PROMOTION SCIENCE'),
('3914', 'กลุ่มวิชาการจัดการนันทนาการและการท่องเที่ยว', 'MANAGEMENT OF RECREATION AND TOURISM'),
('3915', 'กลุ่มวิชาการเสริมสร้างสมรรถนะทางการกีฬา', 'SPORTS PERFORMANCE ENHANCEMENT'),
('3916', 'กลุ่มวิชาสรีรวิทยาการออกกำลังกาย', 'EXERCISE PHYSIOLOGY'),
('3917', 'กลุ่มวิชาการส่งเสริมสุขภาพ', 'HEALTH PROMOTION'),
('3918', 'กลุ่มวิชาการจัดการการกีฬา', 'SPORTS MANAGEMENT'),
('3919', 'กลุ่มวิชาการจัดการนันทนาการและการท่องเที่ยวเชิงกีฬา', 'RECREATION AND SPORTS TOURISM MANAGEMENT'),
('4000', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('5100', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('5140', 'สาขาวิชานโยบายประชากรกับการพัฒนามนุษย์ (นานาชาติ)', 'FIELD OF STUDY OF POPULATION POLICY AND HUMAN DEVELOPMENT'),
('5300', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('5500', 'ไม่สังกัดภาควิชา/เทียบเท่า', 'COMMON COURSE'),
('5501', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะวิศวกรรมศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN ENGINEERING'),
('5503', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะวิทยาศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN SCIENCE'),
('5504', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะรัฐศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN POLITICAL SCIENCE'),
('5505', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะสถาปัตยกรรมศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN ARCHITECTURE'),
('5506', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะพาณิชยศาสตร์และการบัญชี', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN COMMERCE AND ACCOUNTAN'),
('5507', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะครุศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN EDUCATION'),
('5508', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะนิเทศศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN COMMUNICATION ARTS'),
('5509', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะเศรษฐศาสตร์', 'LANGUAGE INSTITURE FOR INTERNATIONAL PROGRAM IN ECONOMICS'),
('5510', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะแพทยศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN MEDICINE'),
('5511', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะสัตวแพทยศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN VETERINARY SCIENCE'),
('5512', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะทันตแพทยศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN DENTISTRY'),
('5513', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะเภสัขศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN PHARMACEUTICAL SCIENCE'),
('5514', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะนิติศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN LAW'),
('5515', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะศิลปกรรมศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN FINE AND APPLIED ARTS'),
('5517', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะสหเวชศาสตร์', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN ALLIED HEALTH SCIENCES'),
('5518', 'สถาบันภาษาสำหรับโครงการนานาชาติ คณะจิตวิทยา', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN PSYCHOLOGY'),
('5519', 'สถาบันภาษาสำหรับโครงการนานาชาติ สำนักวิชาวิทยาศาสตร์การกีฬา', 'LANGUAGE INSTITUTE FOR INTERNATIONAL PROGRAM IN SPORTS SCIENCE'),
('5520', 'สถาบันภาษาสำหรับสาขาวิชาภาษาอังกฤษเป็นภาษานานาชาติ', 'LANGUAGE INSTITUTE FOR PROGRAM IN ENGLISH AS AN INTERNATIONAL LANGUAGE'),
('5536', 'สถาบันภาษาสำหรับ สถาบันนวัตกรรมบูรณาการ', 'LANGUAGE INSTITUTE FOR SCHOOL OF INTEGRATED INNOVATION'),
('5801', 'สาขาวิชาวิศวกรรมอุตสาหการและการจัดการ', 'FIELD OF STUDY OF INDUSTRIAL ENGINEERING AND MANAGEMENT'),
('9901', 'มหาวิทยาลัยธรรมศาสตร์', 'THAMMASAT UNIVERSITY'),
('9902', 'มหาวิทยาลัยเกษตรศาสตร์', 'KASETSART UNIVERSITY'),
('9903', 'มหาวิทยาลัยศิลปากร', 'SILPAKORN UNIVERSITY'),
('9904', 'มหาวิทยาลัยมหิดล', 'MAHIDOL UNIVERSITY'),
('9905', 'มหาวิทยาลัยขอนแก่น', 'KHON KAEN UNIVERSITY'),
('9906', 'มหาวิทยาลัยเชียงใหม่', 'CHIANG MAI UNIVERSITY'),
('9907', 'มหาวิทยาลัยสงขลานครินทร์', 'PRINCE OF SONGKLA UNIVERSITY'),
('9908', 'มหาวิทยาลัยเทคโนโลยีพระจอมเกล้าธนบุรี', 'KING MONGKUT\'S UNIVERSITY OF TECHNOLOGY THONBURI'),
('9909', 'มหาวิทยาลัยต่างประเทศ', 'INTERNATIONAL UNIVERSITY'),
('9910', 'มหาวิทยาลัยจี เอส โอ,เยอรมันนี', 'GSO UNIVERSITY OF APPLIED SCIENCES NURNBERG,GERMANY'),
('9911', 'มหาวิทยาลัยดาร์ทเมาท์คอลเลจ สหรัฐอเมริกา', 'DARTMOUTH COLLEGE,U.S.A.'),
('9912', 'มหาวิทยาลัยเคสเวสเทอร์นรีเซอฟ', 'CASE WESTERN RESERVE UNIVERSITY'),
('9913', 'มหาวิทยาลัยนานาชาติซุนยัทเซน', 'NATIONAL SUN YAT-SEN UNIVERSITY'),
('9914', 'มหาวิทยาลัยแมสทริชท์ เนเธอร์แลนด์', 'MAASTRICHT UNIVERSITY,NETHERLANDS'),
('9915', 'มหาวิทยาลัยลาโทร๊บ', 'LA TROBE UNIVERSITY'),
('9916', 'มหาวิทยาลัยอาร์เอ็มไอที', 'RMIT UNIVERSITY'),
('9917', 'มหาวิทยาลัยมาเก๊า', 'UNIVERSITY MACAU'),
('9918', 'มหาวิทยาลัยคาร์โล แคทแทนโก,ประเทศอิตาลี', 'UNIVERSITY CARLO CATTANCO,ITALY'),
('9919', 'มหาวิทยาลัยเซนท์กอลเลน,ประเทศสวิสเซอร์แลนด์', 'ST.GALLEN UNIVERSITY,SWITZERLAND'),
('9920', 'มหาวิทยาลัยเพอร์ดู', 'PURDUE UNIVERSITY,USA'),
('9921', '', 'UTRECHT SCHOOL OF ECONOMICS'),
('9922', '', 'VRIJE UNIVERSITEIT AMSTERDAM'),
('9923', '', 'WIRTSCHAFTSUNIVERSITAT WIEN,AUSTRIA'),
('9924', 'บีไอ นอร์วีเจียนสคูลออฟแมนเนจเม้นท์ นอร์เวย์', 'BI.NORWEGIAN.SCHOOL.OF.MANAGEMENT,NORWAY'),
('9925', 'มหาวิทยาลัยทิวเบิร์ก', 'TILBURG UNIVERSITY'),
('9926', 'มหาวิทยาลัยเซนต์กอลเลน', 'UNIVERSITY OF ST.GALLEN'),
('9927', 'มหาวิทยาลัยออสโล', 'UNIVERSITY OF OSLO'),
('9928', 'โคเปนเฮเก้นบิซิเนสสคูล ประเทศเดนมาร์ค', 'COPENHAGEN BUSINESS SCHOOL,DENMARK'),
('9929', '', 'RWTH AACHEN UNIVERSITY'),
('9930', 'มหาวิทยาลัยแมคควอเรีย', 'MACQUARIE UNIVERSITY'),
('9931', 'มหาวิทยาลัยไซตามา', 'SAITAMA UNIVERSITY'),
('9932', 'มหาวิทยาลัยทิวเบอร์ก', 'TILBURG UNIVERSITY'),
('9933', 'มหาวิทยาลัยบ๊อคโคนี่', 'BOCCONI UNIVERSITY'),
('9934', 'บี ไอ นอร์วีเจียนสคูลออฟแมนเนจเมนท์', 'BI NORWEGIAN SCHOOL OF MANAGEMENT'),
('9935', 'มหาวิทยาลัยเคลมสัน', 'CLEMSON UNIVERSITY'),
('9936', 'มหาวิทยาลัยเวสเซด้า', 'WESEDA UNIVERSITY'),
('9937', 'มหาวิทยาลัยปอมเป เฟบรา ประเทศสเปน', 'POMPEU FABRA UNIVERSITY'),
('9938', 'มหาวิทยาลัยวิสคอนซิน-เมดิสัน', 'UNIVERSITY OF WISCONSIN-MADISON'),
('9939', 'มหาวิทยาลัยฟลอริด้า', 'UNIVERSITY OF FLORIDA'),
('9940', 'โฮกสคูล แวน อัมเตอร์ดัม,ประเทศเนเธอร์แลนด์', 'HOGESCHOOL VAN AMSTERDAM,NETHERLANDS'),
('9941', 'มหาวิทยาลัยควีนส์ ประเทศแคนาดา', 'QUEEN\'S UNIVERSITY,CANADA'),
('9942', 'มหาวิทยาลัยอาร์ดับบลิวทีเอช อาร์เชน ประเทศเยอรมนี', 'RWTH AACHEN UNIVERSITY,GERMAN'),
('9943', 'มหาวิทยาลัยอินเดียน่า ประเทศสหรัฐอเมริกา', 'INDIANA UNIVERSITY'),
('9944', 'มหาวิทยาลัยแอพซาร่า ประเทศสวีเดน', 'APPSALA UNIVERSITY OF SWEDEN'),
('9945', 'มหาวิทยาลัยอัมสเตอร์ดัม', 'UNIVERSITY OF AMSTERDAM'),
('9946', 'อีเอสซี-ลิว ประเทศฝรั่งเศส', 'ESC-LILLE OF FRANCE'),
('9947', 'อีเอสซี-ฮวง ประเทศฝรั่งเศส', 'ESC-ROUEN OF FRANCE'),
('9948', 'มหาวิทยาลัยแกรนด์วัลเลย์สเตท', 'GRAND VALLEY STATE UNIVERSITY'),
('9949', 'มหาวิทยาลัยซันชายน์โคสท์', 'UNIVERSITY OF THE SUNSHINE COAST'),
('9950', 'มหาวิทยาลัยโอเรกอน', 'OREGON STATE UNIVERSITY'),
('9951', 'มหาวิทยาลัยวอร์ชิงตัน', 'UNIVERSITY OF WASHINGTON'),
('9952', 'มหาวิทยาลัยวิคตอเรีย', 'UNIVERSITY OF VICTORIA'),
('9953', 'มหาวิทยาลัยออตตาวาร์', 'UNIVERSITY OF OTTAWA'),
('9954', 'มหาวิทยาลัยวาเซดะ', 'WASEDA UNIVERSITY'),
('9955', 'มหาวิทยาลัยแคนเบอร่า', 'UNIVERSITY OF CANBERRA'),
('9956', 'มหาวิทยาลัยมิชิแกน', 'MICHIGAN STATE UNIVERSITY'),
('9957', 'มหาวิทยาลัยซิดนีย์', 'UNIVERSITY OF SYDNEY'),
('9958', 'มหาวิทยาลัยนิวเซาท์เวล', 'UNIVERSITY OF NEW SOUTH WALES'),
('9959', 'มหาวิทยาลัยฮาร์วาร์ด', 'HARVARD UNIVERSITY'),
('9960', 'มหาวิทยาลัยฮักเซ่โฮกสคูล', 'HAAGSE HOGESCHOOL UNIVERSITY'),
('9961', 'มหาวิทยาลัยออสเตรเลียใต้', 'UNIVERSITY OF SOUTH AUSTRALIA'),
('9962', 'มหาวิทยาลัยกริฟฟิธ', 'GRFFITH UNIVERSITY'),
('9963', 'มหาวิทยาลัยฮาวาย', 'UNIVERSITY OF HAWAII'),
('9964', 'มหาวิทยาลัยโมแนช', 'MONASH UNIVERSITY'),
('9965', 'วิทยาลัยบอสตัน', 'BOSTON COLLEGE'),
('9966', 'มหาวิทยาลัยเทคโนโลยีเคอร์ติน', 'CURTIN UNIVERSITY OF TECHNOLOGY'),
('9967', 'มหาวิทยาลัยไซมอน เฟรเซอร์', 'SIMON FRASER UNIVERSITY'),
('9968', 'มหาวิทยาลัยนิวยอร์ค', 'NEW YORK UNIVERSITY'),
('9969', 'มหาวิทยาลัยเทคโนโลยีนันยาง', 'NANYANG TECHNOLOGICAL UNIVERSITY'),
('9970', 'มหาวิทยาลัยฮ่องกง', 'HONG KONG UNIVERSITY OF SCIENCE & TECHNOLOGY'),
('9971', 'มหาวิทยาลัยสิงค์โปร', 'SINGAPORE MANAGEMENT UNIVERSITY'),
('9972', 'มหาวิทยาลัยนานาชาติแห่งสิงค์โปร์', 'NATIONAL UNIVERSITY OF SINGAPORE'),
('9973', 'มหาวิทยาลัยเอยูที(โอ๊คแลนด์)', 'AUT UNIVERSITY(AUCKLAND UNIVERSITY OF TECHNOLOGY)'),
('9974', 'มหาวิทยาลัยจองคอบ', 'JONKOPING UNIVERSITY'),
('9975', 'มหาวิทยาลัยคาลแกรี่', 'CALGARY UNIVERSITY'),
('9976', 'มหาวิทยาลัยออกซ์ฟอร์ด บรู๊ค', 'OXFORD BROOKES UNIVERSITY'),
('9977', 'มหาวิทยาลัยแคลิฟอร์ เนีย', 'CALIFORNIA STATE UNIVERSITY'),
('9978', 'สถาบันเทคโนโลยีโตเกียว', 'TOKYO INSTITUTE OF TECHNOLOGY'),
('9979', 'ยูโรเปียนบิซิเนสสคูล', 'EUROPEAN BUSINESS SCHOOL'),
('9980', 'มหาวิทยาลัยโกเบ', 'KOBE UNIVERSITY'),
('9981', 'มหาวิทยาลัยโอกลาโฮมา', 'UNIVERSITY OF OKLAHOMA'),
('9982', 'มหาวิทยาลัยยอร์ค', 'YORK UNIVERSITY'),
('9983', 'มหาวิทยาลัยนอร์ธเทิรนโคโลราโด', 'UNIVERSITY OF NORTHERN COLORADO'),
('9984', 'มหาวิทยาลัยยอนไซ', 'YONSEI UNIVERSITY'),
('9985', 'มหาวิทยาลัยเคอร์ติน', 'CURTIN UNIVERSITY'),
('9986', 'สถาบัน AIT', 'ASIAN INSTITUTE OF TECHNOLOGY'),
('9987', 'มหาวิทยาลัยนอร์ธคาโรไลนา', 'UNIVERSITY OF NORTH CAROLINA'),
('9988', 'มหาวิทยาลัยแซนโจสสเตท', 'SAN JOSE STATE UNIVERSITY'),
('9989', 'มหาวิทยาลัยยูเทร', 'UNIVERSITY OF UTRECHT'),
('9990', 'มหาวิทยาลัยทิวเบอร์ก', 'TILBURG UNIVERSITY'),
('9991', 'มหาวิทยาลัยวีน ประเทศออสเตรเลีย', 'WIRTSCHAUNIVERSITATE WIEN(WU),AUSTRIA'),
('9992', 'มหาวิทยาลัยมันน์ไฮม์', 'UNIVERSITY OF MANNHEIM'),
('9993', 'ลอนดอนสคูลออฟอีโคโนมิคส์แอนโพลิติคอลซาย', 'LONDON SCHOOL OF ECONOMICS AND POLITICAL SCIENCE(LSE)'),
('9994', 'ฮวงแกรดดูเอดสคูลออฟแมนเนจเม้นท์', 'ROUEN GRADUATE SCHOOL OF MANAGEMENT'),
('9995', 'มหาวิทยาลัยเมลเบิร์น', 'UNIVERSITY OF MELBOURNE'),
('9996', 'มหาวิทยาลัยเกาหลี', 'KOREA UNIVERSITY'),
('9997', 'มหาวิทยาลัยควีนส์แลนด์', 'UNIVERSITY OF QUEENSLAND'),
('9998', 'มหาวิทยาลัยแดเจียน', 'DAEJEON UNIVERSITY OF KOREA'),
('9999', 'มหาวิทยาลัยบ๊อคโคนี่', 'BOCCONI UNIVERSITY');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `author_id` varchar(50) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL,
  `media` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `author_id`, `created_at`, `date`, `details`, `media`, `title`) VALUES
(6, '123456', 'สำนักงานทะเบียน', '2025-05-04 22:14:20', 'นิสิตใหม่ภาคการศึกษาต้น ปีการศึกษา 2567 สอบถามเลขประจำตัวนิสิต และรับรหัสผ่าน CU-NET ได้ตั้งแต่วันที่ 5 กรกฎาคม 2567 เป็นต้นไป\r\nที่ https://www.reg.chula.ac.th/th/student/newstudents/\r\n* หากพบปัญหาเกี่ยวกับรหัสผ่าน CU-NET โปรดติดต่อ สำนักบริหารเทคโนโลยีสารสนเทศ อาคารจามจุรี 3 ชั้น 4 โทร. 02-218-3314 อีเมล์ help@chula.ac.th', '..\\images\\news\\NewNisit2568_Th_0.jpg', 'กำหนดการเบื้องต้น “สำหรับนิสิตใหม่ ปีการศึกษา 2567”'),
(7, '1234', 'สำนักงานทะเบียน', '2025-05-13 17:00:00', 'ให้กำหนดวันเปิด–ปิดภาคการศึกษาของจุฬาลงกรณ์มหาวิทยาลัย ระบบทวิภาค                    \r\nปีการศึกษำ 2568 ซึ่งที่ประชุมคณบดีในการประชุม ครั้งที่ 6/ 2568 เมื่อวันที่ 2 เมษายน 2568 ได้มีมติ\r\n เห็นชอบแล้ว ดังต่อไปนี้ \r\nภาคการศึกษาต้น \r\n วันเปิดเรียนภาคการศึกษาต้น                 วันจันทร์ที่ 4 สิงหาคม 2568 \r\n วันสอบกลางภาคการศึกษาต้น (ถ้ามี)         วันจันทร์ที่ 22 – วันศุกร์ที่ 26 กันยายน 2568 \r\n วันปิดกิจกรรมภาคการศึกษาต้น     วันอาทิตย์ที่  26 ตุลาคม 2568 \r\n วันสุดท้ำยของการเรียนภาคการศึกษาต้น         วันศุกร์ที่ 21 พฤศจิกายน 2568 \r\n วันสอบปลายภาคการศึกษาต้น          วันจันทร์ที่ 24 พฤศจิกายน – วันจันทร์ที่ 8 ธันวาคม 2568 \r\n วันปิดเรียนภาคการศึกษาต้น                   วันอังคารที่ 9 ธันวำคม 2568', 'https://www.reg.chula.ac.th/th/wp-content/uploads/sites/4/2025/04/Start_EndDates_T-Sem2568.pdf', 'กำหนดวันเปิด-ปิดภาคการศึกษาของจุฬาลงกรณ์มหาวิทยาลัย ปีการศึกษา 2568');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(10) NOT NULL,
  `semester_name` varchar(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `semester` int(11) GENERATED ALWAYS AS (substring_index(`semester_name`,'/',1)) STORED,
  `academic_year` int(11) GENERATED ALWAYS AS (substring_index(`semester_name`,'/',-1)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `semester_name`, `start_date`, `end_date`) VALUES
(1, '2/2567', '2025-01-10', '2025-05-15'),
(2, '1/2568', '2025-08-04', '2025-12-24');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_code` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_thai_520_w2 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_thai_520_w2 DEFAULT NULL,
  `thaiid` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_code`, `first_name`, `last_name`, `thaiid`, `password`) VALUES
(1, '6742422627', 'สุทธิพงษ์', 'เปิ้นปัญญา', '1539900922259', '$2y$10$7.Qrmsm0TplzNLHHzciPBuHv9ZVrpHOi1.7F49/YIupgYvnFSob/C'),
(21, '6740002127', 'สุทธิพงษ์', 'เปิ้นปัญญา', '1234567890123', '$2y$10$oQ66C572SJjCt3miGOAv7uNz1soyv3Sh6r57WMrNH1rXnr0JplmZC'),
(26, '6740002627', 'ทดสอบ', 'ทดสอบ', '123', '$2y$10$DWg3x5MqHkRIanFhHz3vbeOI54vQ4TJPUoHjuyJmZ5.DLFwxPAIyq'),
(27, '6740002727', 'นายนิสิต', 'เรียนดี', '12345678', '$2y$10$orlpmS8MRwCd.L1nnu3Mx.z3ZkSji/6ApqLgcYl6jcXZwQeKTteKS'),
(114, '6740011427', 'ทดสอบ', 'ฐานข้อมูล', '123456789012', '$2y$10$EPWWNiRTqAkaIOBw8GzTpeoqmenJ3pUDOQ/n0lkj8AvWP2fxKrCby'),
(116, '6740011627', 'test', 'test', '1234', '$2y$10$Z/OXC2nQJz62bwXBAiM8y.jOruOYyUmo1Kr6iR.ZEQG2rAyCJvHgq');

--
-- Triggers `students`
--
DELIMITER $$
CREATE TRIGGER `after_student_insert` AFTER INSERT ON `students` FOR EACH ROW BEGIN
    IF EXISTS (SELECT 1 FROM student_info WHERE student_code = NEW.student_code) THEN
        UPDATE student_info
        SET student_code = NEW.student_code
        WHERE student_code = NEW.student_code;
    ELSE
        INSERT INTO student_info (student_code)
        VALUES (NEW.student_code);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_students_insert` AFTER INSERT ON `students` FOR EACH ROW BEGIN
    INSERT INTO student_info (student_code)
    VALUES (NEW.student_code);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `students_documents`
--

CREATE TABLE `students_documents` (
  `document_id` int(11) NOT NULL,
  `student_code` varchar(10) DEFAULT NULL,
  `id_card_path` varchar(255) DEFAULT NULL,
  `student_image_path` varchar(255) DEFAULT NULL,
  `change_name_path` varchar(255) DEFAULT NULL,
  `parent_guarantee_path` varchar(255) DEFAULT NULL,
  `consent_agreement_path` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_documents`
--

INSERT INTO `students_documents` (`document_id`, `student_code`, `id_card_path`, `student_image_path`, `change_name_path`, `parent_guarantee_path`, `consent_agreement_path`, `upload_date`) VALUES
(1, '6742422627', '../images/id_card/6742422627.jpeg', '../images/student_image/6742422627.png', '', '../images/parent_guarantee/6742422627.pdf', '../images/consent_agreement/6742422627.jpeg', '2025-05-01 18:08:37'),
(3, '6740002727', '../images/id_card/6740002727.png', '../images/student_image/6740002727.png', '', '../images/parent_guarantee/6740002727.jpg', '../images/consent_agreement/6740002727.jpg', '2025-05-05 13:56:39'),
(15, '6740011627', 'images/id_card/6740011627.png', '..images/student_image/6740011627.jpg', '', '..images/parent_guarantee/6740011627.png', '..images/consent_agreement/6740011627.png', '2025-05-17 17:00:45'),
(16, '6740011627', '../images/id_card/6740011627.png', '../images/student_image/6740011627.jpg', '', '../images/parent_guarantee/6740011627.png', '../images/consent_agreement/6740011627.png', '2025-05-17 17:04:58');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `student_code` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`student_code`, `email`, `phone_number`, `faculty_id`, `major_id`, `year`, `date_of_birth`, `address`, `profile_picture`) VALUES
('6740002127', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6740002627', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6740002727', '6740002727@student.chula.ac.th', '0123456789', 21, 2130, 1, '2548-05-05', '254 ถ.พญาไท แขวงวังใหม่ เขตปทุมวัน กรงเทพฯ', NULL),
('6742422627', 'suthipong7754@gmail.com', '0623525681', 27, 2765, 1, '2005-10-15', '254 ถนนพญาไท แขวงวังใหม่ เขตปทุมวัน กรุงเทพมหานคร 10330', '../images/6742422627.png'),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6740011527', '6740011527@student.chula.ac.th', '1234567890', 23, 2301, 1, '2545-10-15', '254 ถ.พญาไท', '..images/student_image/6740011527.jpg'),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6740011627', '1234@student.chula.ac.th', '0123456789', 23, 2301, 1, '2005-04-17', '4\\r\\n242\\\\2\\\\', '..images/student_image/6740011627.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_course_assignments`
--

CREATE TABLE `teacher_course_assignments` (
  `course_id` int(10) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `teacher_id` varchar(100) NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `semester` int(11) NOT NULL,
  `academic_year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_course_assignments`
--

INSERT INTO `teacher_course_assignments` (`course_id`, `assignment_id`, `teacher_id`, `course_code`, `semester`, `academic_year`, `created_at`, `updated_at`) VALUES
(2301170, 9, '9355845652', '2301170', 1, 2568, '2025-05-04 18:19:54', '2025-05-04 18:19:54'),
(2301170, 10, '9355845652', '2301170', 2, 2567, '2025-05-04 19:29:43', '2025-05-04 19:29:43'),
(2301170, 11, '123456', '2301170', 1, 2567, '2025-05-14 17:58:40', '2025-05-14 17:58:40'),
(2301170, 12, '123456', '2301170', 2, 2567, '2025-05-14 17:59:02', '2025-05-14 17:59:02'),
(2759151, 13, '123456', '2759151', 2, 2567, '2025-05-15 02:52:36', '2025-05-15 02:52:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `staff_id` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `referral_code` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `staff_id`, `name`, `email`, `referral_code`, `role`, `password`) VALUES
(1, '123', 'สุทธิพงษ์ เปิ้นปัญญา', 'suthipong7754@gmail.com', '10024', 'Admin', '$2y$10$dfmJ9M6oKa7u30RXQNxBFu5rZAFgCOIzO/qfyc4w0.T3ohMfP/SWO'),
(2, '9355845652', 'teacher', 'teacher@chula.ac.th', '10023', 'Teachers', '$2y$10$sBw1.MUMSmvjh0NAUQ8qeengaNdV.qlatQcyz2TlfVvsmJel39Udu'),
(3, '123456', 'อาจารย์ ใจดี', '1234@chula.ac.th', '10024', 'Teachers', '$2y$10$vm8iY0Yy1UHEGs42nUTYlu8O5jnnIFgiqGGVpMib1FttsH8eG6TJK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`course_code`) USING BTREE;

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `idx_student_id` (`student_code`),
  ADD KEY `idx_course_code` (`course_code`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`) USING BTREE;

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`major_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`thaiid`);

--
-- Indexes for table `students_documents`
--
ALTER TABLE `students_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `student_code` (`student_code`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD UNIQUE KEY `student_code` (`student_code`);

--
-- Indexes for table `teacher_course_assignments`
--
ALTER TABLE `teacher_course_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`,`course_code`,`semester`,`academic_year`),
  ADD KEY `course_code` (`course_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`staff_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `students_documents`
--
ALTER TABLE `students_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `teacher_course_assignments`
--
ALTER TABLE `teacher_course_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_grades_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`recorded_by_teacher_id`) REFERENCES `users` (`staff_id`) ON UPDATE CASCADE;

--
-- Constraints for table `teacher_course_assignments`
--
ALTER TABLE `teacher_course_assignments`
  ADD CONSTRAINT `teacher_course_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teacher_course_assignments_ibfk_2` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
