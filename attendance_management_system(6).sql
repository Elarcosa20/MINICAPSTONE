-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 12:42 PM
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
-- Database: `attendance_management_system`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddProfessor` (IN `p_name` VARCHAR(255), IN `p_professor_id` VARCHAR(50), IN `p_birthday` DATE, IN `p_gender` VARCHAR(10), IN `p_email` VARCHAR(255), IN `p_contact` VARCHAR(50), IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO professors (name, professor_id, birthday, gender, email, contact_number, password) 
    VALUES (p_name, p_professor_id, p_birthday, p_gender, p_email, p_contact, p_password);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddStudent` (IN `p_name` VARCHAR(255), IN `p_student_id` VARCHAR(50), IN `p_birthday` DATE, IN `p_gender` VARCHAR(10), IN `p_email` VARCHAR(255), IN `p_contact` VARCHAR(15), IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO students (name, student_id, birthday, gender, email, contact_number, password) 
    VALUES (p_name, p_student_id, p_birthday, p_gender, p_email, p_contact, p_password);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProfessorSchedule` (IN `schedule_id` INT)   BEGIN
    DELETE FROM professor_schedule WHERE id = schedule_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllData` ()   BEGIN
    SELECT student_id, name FROM students;
    SELECT section_id, section_name FROM sections;
    SELECT professor_id, name FROM professors;
    SELECT subject_code, subject_name FROM subjects;
    SELECT time_id, time_range FROM times;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassSessionDetails` (IN `p_class_id` INT)   BEGIN
    -- Declare a temporary table to hold the results
    DECLARE v_present_count INT;

    -- Get the present count
    SELECT COUNT(*) INTO v_present_count
    FROM attendance a
    JOIN class_sessions cs ON a.class_id = cs.id
    WHERE cs.class_id = p_class_id AND a.status = 'present';

    -- Select the class session details along with attendance
    SELECT
        cs.id AS session_id,
        cs.start_time,
        s.subject_name,
        sec.section_name,
        t.time_range,
        std.student_id,
        std.name AS student_name,
        a.time_in,
        a.device_used,
        v_present_count AS present_count
    FROM class_sessions cs
    JOIN professor_schedule ps ON cs.class_id = ps.id
    JOIN subjects s ON ps.subject_code = s.subject_code
    JOIN sections sec ON ps.section_id = sec.section_id
    JOIN times t ON ps.time_id = t.time_id
    LEFT JOIN attendance a ON cs.id = a.class_id
    LEFT JOIN students std ON a.student_id = std.student_id
    WHERE ps.id = p_class_id
    ORDER BY a.time_in DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProfessorDetails` (IN `p_professor_id` VARCHAR(50))   BEGIN
    SELECT 
        professor_id, 
        name, 
        birthday, 
        gender, 
        email, 
        contact_number 
    FROM professors 
    WHERE professor_id = p_professor_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProfessorName` (IN `p_professor_id` INT)   BEGIN
    SELECT 
        name 
    FROM professors 
    WHERE professor_id = p_professor_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProfessorSchedule` (IN `p_professor_id` VARCHAR(50))   BEGIN
    SELECT 
        ps.id, 
        s.subject_name, 
        sec.section_name, 
        t.time_range
    FROM professor_schedule ps
    JOIN subjects s ON ps.subject_code = s.subject_code
    JOIN sections sec ON ps.section_id = sec.section_id
    JOIN times t ON ps.time_id = t.time_id
    WHERE ps.professor_id = p_professor_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProfessorSchedules` ()   BEGIN
    SELECT 
        ps.id AS schedule_id,
        p.name AS professor_name,
        s.subject_name,
        sec.section_name,
        t.time_range
    FROM professor_schedule ps
    INNER JOIN professors p ON ps.professor_id = p.professor_id
    INNER JOIN subjects s ON ps.subject_code = s.subject_code
    INNER JOIN sections sec ON ps.section_id = sec.section_id
    INNER JOIN times t ON ps.time_id = t.time_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetScheduleData` ()   BEGIN
    SELECT professor_id, name FROM professors;
    SELECT subject_code, subject_name FROM subjects;
    SELECT time_id, time_range FROM times;
    SELECT section_id, section_name FROM sections;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentClasses` (IN `studentId` INT)   BEGIN
    SELECT 
        ss.id AS schedule_id,
        s.subject_name, 
        s.subject_code,
        p.professor_id, 
        p.name AS professor_name, 
        t.time_id, 
        t.time_range,
        cs.id AS session_id,
        CASE WHEN cs.id IS NOT NULL AND cs.end_time IS NULL THEN 1 ELSE 0 END AS is_active
    FROM student_schedule ss
    JOIN subjects s ON ss.subject_code = s.subject_code
    JOIN professors p ON ss.professor_id = p.professor_id
    JOIN times t ON ss.time_id = t.time_id
    LEFT JOIN professor_schedule ps ON ss.professor_id = ps.professor_id 
        AND ss.subject_code = ps.subject_code 
        AND ss.section_id = ps.section_id
    LEFT JOIN class_sessions cs ON ps.id = cs.class_id AND cs.end_time IS NULL
    WHERE ss.student_id = studentId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentDetails` (IN `student_id` VARCHAR(50))   BEGIN
       SELECT * FROM students WHERE student_id = student_id; -- Use the correct column name
   END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentName` (IN `student_id` VARCHAR(50))   BEGIN
    SELECT name FROM students WHERE student_id = student_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentsByClass` (IN `p_class_id` INT)   BEGIN
    DECLARE v_section_id INT;

    -- Get the section_id from professor_schedule based on class_id
    SELECT section_id INTO v_section_id 
    FROM professor_schedule 
    WHERE id = p_class_id;

    -- If section_id is found, retrieve the students
    IF v_section_id IS NOT NULL THEN
        SELECT 
            students.student_id, 
            students.name
        FROM student_schedule
        JOIN students ON student_schedule.student_id = students.student_id
        WHERE student_schedule.section_id = v_section_id
        AND student_schedule.subject_code = (
            SELECT subject_code 
            FROM professor_schedule 
            WHERE id = p_class_id
        );
    ELSE
        SELECT NULL AS student_id, NULL AS name; -- Return empty result if no section found
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentsBySectionAndClass` (IN `p_section_id` INT, IN `p_class_id` INT)   BEGIN
    SELECT 
        students.student_id, 
        students.name
    FROM student_schedule
    JOIN students ON student_schedule.student_id = students.student_id
    WHERE student_schedule.section_id = p_section_id
    AND student_schedule.subject_code = (
        SELECT subject_code 
        FROM professor_schedule 
        WHERE id = p_class_id
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentSchedules` ()   BEGIN
    SELECT
        ss.id,
        s.name AS student_name,
        p.name AS professor_name,
        sub.subject_name,
        t.time_range,
        sec.section_name
    FROM student_schedule ss
    JOIN students s ON ss.student_id = s.student_id
    JOIN professors p ON ss.professor_id = p.professor_id
    JOIN subjects sub ON ss.subject_code = sub.subject_code
    JOIN times t ON ss.time_id = t.time_id
    JOIN sections sec ON ss.section_id = sec.section_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertProfessor` (IN `p_name` VARCHAR(100), IN `p_professor_id` INT, IN `p_birthday` DATE, IN `p_gender` ENUM('Male','Female','Other'), IN `p_email` VARCHAR(100), IN `p_contact_number` VARCHAR(15), IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO professors (name, professor_id, birthday, gender, email, contact_number, password) 
    VALUES (p_name, p_professor_id, p_birthday, p_gender, p_email, p_contact_number, p_password);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageSchedule` (IN `scheduleId` INT)   BEGIN
    -- Check if the 'times' table exists, if not create it
    IF NOT EXISTS (SELECT * FROM information_schema.tables WHERE table_name = 'times') THEN
        CREATE TABLE `times` (
            `time_id` INT AUTO_INCREMENT PRIMARY KEY,
            `time_range` VARCHAR(50) NOT NULL
        );

        INSERT INTO `times` (time_range) VALUES
        ('08:00 AM - 10:00 AM'),
        ('10:00 AM - 12:00 PM'),
        ('12:00 PM - 02:00 PM'),
        ('02:00 PM - 04:00 PM');
    END IF;

    -- Delete the schedule if the ID is valid
    IF scheduleId IS NOT NULL AND scheduleId > 0 THEN
        DELETE FROM schedules WHERE schedule_id = scheduleId;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `email`, `password`, `created_at`) VALUES
(0, 'renziekyleelarcosa@gmail.com', '382e0360e4eb7b70034fbaa69bec5786', '2024-12-05 06:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `class_id` int(11) NOT NULL,
  `status` enum('present','absent') NOT NULL,
  `time_in` datetime NOT NULL,
  `device_used` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_sessions`
--

CREATE TABLE `class_sessions` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `professor_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `name`, `email`, `password`, `birthday`, `gender`, `contact_number`, `created_at`) VALUES
('1', 'Danny Obidas', 'sadibodanny@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$NjdzUDl5MFBnOWJ3ZnozVQ$IIR0KWrGKQTxTMFADMqr5F1tn67QHy7YEpD//OGBCpc', '1985-05-20', 'Male', '092347327212', '2024-12-19 03:05:52'),
('2', 'Shaira jade Famulagan', 'shaira@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$RFFsSjhUbVdrenpBRlJJeA$hjdARu4vnM6l3tT8Uzn5JbB5m3k0hJlCqwlQ3wmwUeM', '1990-10-20', 'Female', '09876543212', '2024-12-19 07:15:18');

-- --------------------------------------------------------

--
-- Table structure for table `professor_schedule`
--

CREATE TABLE `professor_schedule` (
  `id` int(11) NOT NULL,
  `professor_id` varchar(50) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `section_id` int(11) NOT NULL,
  `time_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professor_schedule`
--

INSERT INTO `professor_schedule` (`id`, `professor_id`, `subject_code`, `section_id`, `time_id`) VALUES
(24, '1', 'MATH101', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `section_name`, `created_at`) VALUES
(1, '3A', '2024-12-05 06:54:08'),
(2, '3B', '2024-12-05 06:54:08'),
(3, '3C', '2024-12-05 06:54:08'),
(4, '3D', '2024-12-05 06:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `password`, `birthday`, `gender`, `contact_number`, `created_at`) VALUES
('20220128', 'Nino Ken Baguio', 'ken.baguio@gmail.com', '$2y$10$g8VlkRfbzEntxnTcT7KBkuNdCcFw7bfqRKdo/SpiOeGPfMrO5Qa2O', '2003-09-29', 'Male', '09123456789', '2024-12-19 06:21:48'),
('20220129', 'Raque Canete', 'raquecanete60@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$QkE5R0xyNllqalNFTWtQOA$cd5iMWgm1KQbnQzWxFavwP2AlLUYnG2v+e5XfXMw2tY', '2003-08-15', 'Male', '0945273927310', '2024-12-19 03:55:51'),
('20220780', 'John Rey Canete', 'johnrey@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$bElHS1NBcGlIOHNIVzdQdw$/122jYb9OBgcl1qxS+mv4BmJ+PwJzLU4Hzux4tLpYAc', '2004-05-31', 'Male', '09123456782', '2024-12-19 07:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `student_schedule`
--

CREATE TABLE `student_schedule` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professor_id` varchar(50) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `time_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_schedule`
--

INSERT INTO `student_schedule` (`id`, `student_id`, `professor_id`, `subject_code`, `time_id`, `section_id`, `created_at`) VALUES
(71, '20220128', '1', 'ENG101', 1, 1, '2024-12-19 03:19:07'),
(78, '20220780', '1', 'MATH101', 1, 1, '2024-12-19 07:41:18'),
(79, '20220128', '1', 'MATH101', 1, 1, '2024-12-19 10:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `created_at`) VALUES
(1, 'MATH101', 'Basic Mathematics', '2024-12-05 06:53:28'),
(2, 'ENG101', 'English Composition', '2024-12-05 06:53:28'),
(3, 'CS101', 'Introduction to Computer Science', '2024-12-05 06:53:28'),
(4, 'HIST101', 'World History', '2024-12-05 06:53:28');

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE `times` (
  `time_id` int(11) NOT NULL,
  `time_range` varchar(50) NOT NULL,
  `start_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `times`
--

INSERT INTO `times` (`time_id`, `time_range`, `start_time`) VALUES
(1, '08:00 AM - 10:00 AM', '00:00:00'),
(2, '10:00 AM - 12:00 PM', '00:00:00'),
(3, '12:00 PM - 02:00 PM', '00:00:00'),
(4, '02:00 PM - 04:00 PM', '00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`professor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `professor_schedule`
--
ALTER TABLE `professor_schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_schedule` (`subject_code`,`section_id`,`time_id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `time_id` (`time_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_schedule`
--
ALTER TABLE `student_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_schedule_ibfk_1` (`section_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `times`
--
ALTER TABLE `times`
  ADD PRIMARY KEY (`time_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `class_sessions`
--
ALTER TABLE `class_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `professor_schedule`
--
ALTER TABLE `professor_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `student_schedule`
--
ALTER TABLE `student_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `times`
--
ALTER TABLE `times`
  MODIFY `time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD CONSTRAINT `class_sessions_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `professor_schedule` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `professor_schedule`
--
ALTER TABLE `professor_schedule`
  ADD CONSTRAINT `professor_schedule_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`),
  ADD CONSTRAINT `professor_schedule_ibfk_2` FOREIGN KEY (`subject_code`) REFERENCES `subjects` (`subject_code`),
  ADD CONSTRAINT `professor_schedule_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `professor_schedule_ibfk_4` FOREIGN KEY (`time_id`) REFERENCES `times` (`time_id`);

--
-- Constraints for table `student_schedule`
--
ALTER TABLE `student_schedule`
  ADD CONSTRAINT `student_schedule_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
