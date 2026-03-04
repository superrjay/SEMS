-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 07:43 AM
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
-- Database: `hr8_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL,
  `reference_no` varchar(30) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT 'Single',
  `position_applied_id` int(11) DEFAULT NULL,
  `application_date` date NOT NULL DEFAULT curdate(),
  `status` enum('New','Screening','Shortlisted','For Interview','Interviewed','For Exam','Examined','Ranked','Offered','Hired','Rejected','Withdrawn','Pooled') DEFAULT 'New',
  `source` varchar(100) DEFAULT NULL COMMENT 'Where the applicant learned about the position',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `reference_no`, `first_name`, `last_name`, `middle_name`, `email`, `phone`, `address`, `date_of_birth`, `gender`, `civil_status`, `position_applied_id`, `application_date`, `status`, `source`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'APP-2026-0001', 'Ana', 'Mendoza', 'Cruz', 'ana.mendoza@gmail.com', '09171234567', '123 Rizal St, Manila', '1998-05-15', 'Female', 'Single', 1, '2026-02-23', 'For Interview', 'JobStreet', NULL, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(2, 'APP-2026-0002', 'Carlos', 'Rivera', NULL, 'carlos.rivera@gmail.com', '09181234567', '456 Bonifacio Ave, Quezon City', '1995-08-20', 'Male', 'Married', 2, '2026-02-23', 'Screening', 'Walk-in', NULL, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(3, 'APP-2026-0003', 'Lisa', 'Tan', 'Marie', 'lisa.tan@gmail.com', '09191234567', '789 Mabini Rd, Makati', '2000-01-10', 'Female', 'Single', 1, '2026-02-23', 'Shortlisted', 'LinkedIn', NULL, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(4, 'APP-2026-0004', 'Mark', 'Bautista', 'James', 'mark.bautista@gmail.com', '09201234567', '321 Luna St, Pasig', '1997-11-25', 'Male', 'Single', 3, '2026-02-23', 'New', 'Referral', NULL, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(5, 'APP-2026-0005', 'Grace', 'Lim', NULL, 'grace.lim@gmail.com', '09211234567', '654 Aguinaldo Blvd, Cavite', '1996-03-08', 'Female', 'Married', 4, '2026-02-23', 'Hired', 'University Career Fair', NULL, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_documents`
--

CREATE TABLE `applicant_documents` (
  `document_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `document_type` enum('Resume','Transcript','Diploma','Certificate','NBI Clearance','Medical Certificate','Photo','Government ID','Other') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applicant_screenings`
--

CREATE TABLE `applicant_screenings` (
  `screening_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `screened_by` int(11) NOT NULL,
  `screening_date` date NOT NULL,
  `documents_complete` tinyint(1) DEFAULT 0,
  `qualifications_met` tinyint(1) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `result` enum('Passed','Failed','Pending') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `record_type` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` longtext DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext DEFAULT NULL CHECK (json_valid(`new_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `module`, `action`, `record_type`, `record_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'System', 'System Initialized', 'system', NULL, NULL, NULL, '127.0.0.1', NULL, '2026-02-23 14:36:17'),
(2, 1, 'Pre-Employment', 'Created Applicant', 'applicant', 1, NULL, NULL, '127.0.0.1', NULL, '2026-02-23 14:36:17'),
(3, 2, 'Pre-Employment', 'Screened Applicant', 'applicant', 1, NULL, NULL, '127.0.0.1', NULL, '2026-02-23 14:36:17'),
(4, 1, 'Recruitment', 'Scheduled Interview', 'interview', 1, NULL, NULL, '127.0.0.1', NULL, '2026-02-23 14:36:17'),
(5, 1, 'Employee Records', 'Created Employee', 'employee', 5, NULL, NULL, '127.0.0.1', NULL, '2026-02-23 14:36:17'),
(6, 1, 'System', 'User Login', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:47:28'),
(7, 1, 'System', 'User Logout', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:51:33'),
(8, 2, 'System', 'User Login', 'user', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:52:05'),
(9, 2, 'System', 'User Logout', 'user', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:52:49'),
(10, 3, 'System', 'User Login', 'user', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:53:59'),
(11, 3, 'System', 'User Logout', 'user', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:55:32'),
(12, 4, 'System', 'User Login', 'user', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:55:44'),
(13, 4, 'System', 'User Logout', 'user', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:56:09'),
(14, 5, 'System', 'User Login', 'user', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:56:22'),
(15, 5, 'System', 'User Logout', 'user', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 14:56:48'),
(16, 1, 'System', 'Login', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 03:57:27'),
(17, 1, 'System', 'Login', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 01:31:39'),
(18, 1, 'System', 'User Logout', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 01:31:51'),
(19, 1, 'System', 'Login', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 06:33:53'),
(20, 1, 'System', 'User Logout', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 06:35:44');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_requests`
--

CREATE TABLE `clearance_requests` (
  `clearance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `separation_type` enum('Resignation','Retirement','Termination','End of Contract','AWOL') NOT NULL,
  `effective_date` date NOT NULL,
  `last_working_day` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `exit_interview_done` tinyint(1) DEFAULT 0,
  `exit_interview_date` datetime DEFAULT NULL,
  `exit_interview_notes` text DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `service_record_generated` tinyint(1) DEFAULT 0,
  `final_pay_processed` tinyint(1) DEFAULT 0,
  `requested_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clearance_signatories`
--

CREATE TABLE `clearance_signatories` (
  `signatory_id` int(11) NOT NULL,
  `clearance_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `signatory_user_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Cleared','Not Cleared') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `head_user_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_code`, `head_user_id`, `is_active`, `created_at`) VALUES
(1, 'Human Resources', 'HR', NULL, 1, '2026-02-23 14:36:16'),
(2, 'Information Technology', 'IT', NULL, 1, '2026-02-23 14:36:16'),
(3, 'Finance & Accounting', 'FIN', NULL, 1, '2026-02-23 14:36:16'),
(4, 'Academic Affairs', 'ACAD', NULL, 1, '2026-02-23 14:36:16'),
(5, 'Student Services', 'SS', NULL, 1, '2026-02-23 14:36:16'),
(6, 'Administration', 'ADMIN', NULL, 1, '2026-02-23 14:36:16'),
(7, 'Marketing', 'MKT', NULL, 1, '2026-02-23 14:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `disciplinary_records`
--

CREATE TABLE `disciplinary_records` (
  `record_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `type` enum('Verbal Warning','Written Warning','Suspension','Commendation','Award','Memo') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `issued_by` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` enum('Active','Resolved','Appealed','Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `employee_no` varchar(30) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `nationality` varchar(50) DEFAULT 'Filipino',
  `position_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contractual','Probationary') DEFAULT 'Probationary',
  `employment_status` enum('Active','Probationary','Regular','On Leave','Suspended','Resigned','Terminated','Retired') DEFAULT 'Probationary',
  `date_hired` date DEFAULT NULL,
  `regularization_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `salary_grade` varchar(20) DEFAULT NULL,
  `basic_salary` decimal(12,2) DEFAULT NULL,
  `sss_no` varchar(20) DEFAULT NULL,
  `philhealth_no` varchar(20) DEFAULT NULL,
  `pagibig_no` varchar(20) DEFAULT NULL,
  `tin_no` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_no`, `user_id`, `applicant_id`, `first_name`, `last_name`, `middle_name`, `email`, `phone`, `address`, `date_of_birth`, `gender`, `civil_status`, `nationality`, `position_id`, `department_id`, `employment_type`, `employment_status`, `date_hired`, `regularization_date`, `contract_end_date`, `salary_grade`, `basic_salary`, `sss_no`, `philhealth_no`, `pagibig_no`, `tin_no`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relation`, `is_archived`, `created_at`, `updated_at`) VALUES
(1, 'EMP-2024-0001', 1, NULL, 'System', 'Admin', NULL, 'admin@hr8.com', '09170000001', NULL, NULL, NULL, NULL, 'Filipino', NULL, 1, 'Full-Time', 'Regular', '2020-01-15', '2020-07-15', NULL, 'SG-24', 85000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-23 14:36:17', '2026-02-23 14:36:17'),
(2, 'EMP-2024-0002', 2, NULL, 'Maria', 'Santos', NULL, 'maria.santos@hr8.com', '09170000002', NULL, NULL, NULL, NULL, 'Filipino', NULL, 1, 'Full-Time', 'Regular', '2021-03-01', '2021-09-01', NULL, 'SG-20', 65000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-23 14:36:17', '2026-02-23 14:36:17'),
(3, 'EMP-2024-0003', 3, NULL, 'Juan', 'Dela Cruz', NULL, 'juan.delacruz@hr8.com', '09170000003', NULL, NULL, NULL, NULL, 'Filipino', 2, 1, 'Full-Time', 'Regular', '2022-06-15', '2022-12-15', NULL, 'SG-10', 35000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-23 14:36:17', '2026-02-23 14:36:17'),
(4, 'EMP-2024-0004', 4, NULL, 'Rosa', 'Garcia', NULL, 'rosa.garcia@hr8.com', '09170000004', NULL, NULL, NULL, NULL, 'Filipino', 1, 2, 'Full-Time', 'Regular', '2021-08-01', '2022-02-01', NULL, 'SG-18', 55000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-23 14:36:17', '2026-02-23 14:36:17'),
(5, 'EMP-2024-0005', 5, NULL, 'Pedro', 'Reyes', NULL, 'pedro.reyes@hr8.com', '09170000005', NULL, NULL, NULL, NULL, 'Filipino', 1, 2, 'Full-Time', 'Probationary', '2025-11-01', NULL, NULL, 'SG-15', 42000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-23 14:36:17', '2026-02-23 14:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `doc_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `document_type` enum('Contract','NDA','COE','201 File','Government ID','TIN','SSS','PhilHealth','PagIBIG','Resume','Diploma','Other') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_evaluations`
--

CREATE TABLE `interview_evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `interview_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `communication_score` int(11) DEFAULT NULL CHECK (`communication_score` between 1 and 10),
  `technical_score` int(11) DEFAULT NULL CHECK (`technical_score` between 1 and 10),
  `experience_score` int(11) DEFAULT NULL CHECK (`experience_score` between 1 and 10),
  `cultural_fit_score` int(11) DEFAULT NULL CHECK (`cultural_fit_score` between 1 and 10),
  `overall_score` decimal(4,2) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `recommendation` enum('Highly Recommended','Recommended','With Reservation','Not Recommended') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_schedules`
--

CREATE TABLE `interview_schedules` (
  `interview_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `interview_type` enum('Initial','Technical','Panel','Final') DEFAULT 'Initial',
  `location` varchar(255) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled','No Show') DEFAULT 'Scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_offers`
--

CREATE TABLE `job_offers` (
  `offer_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `offered_salary` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contractual','Probationary') DEFAULT 'Probationary',
  `offer_date` date NOT NULL DEFAULT curdate(),
  `expiry_date` date DEFAULT NULL,
  `status` enum('Draft','Sent','Accepted','Declined','Expired','Withdrawn') DEFAULT 'Draft',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

CREATE TABLE `job_positions` (
  `position_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contractual','Probationary') DEFAULT 'Full-Time',
  `salary_grade` varchar(20) DEFAULT NULL,
  `slots` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_positions`
--

INSERT INTO `job_positions` (`position_id`, `title`, `department_id`, `description`, `requirements`, `employment_type`, `salary_grade`, `slots`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Software Developer', 2, 'Develop and maintain web applications', 'BS Computer Science, 2+ years experience', 'Full-Time', 'SG-15', 2, 1, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(2, 'HR Assistant', 1, 'Assist in HR operations and documentation', 'BS Psychology/HRDM, Fresh graduate welcome', 'Full-Time', 'SG-10', 1, 1, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16'),
(3, 'Accounting Clerk', 3, 'Handle financial records and reports', 'BS Accountancy, CPA preferred', 'Full-Time', 'SG-12', 1, 1, NULL, '2026-02-23 14:36:16', '2026-02-23 14:54:16'),
(4, 'Part-Time Instructor', 4, 'Teach assigned subjects', 'Masters Degree required', 'Part-Time', 'SG-18', 5, 1, NULL, '2026-02-23 14:36:16', '2026-02-23 14:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_tasks`
--

CREATE TABLE `onboarding_tasks` (
  `task_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Documentation','Training','IT Setup','Orientation','Compliance','Other') DEFAULT 'Other',
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Overdue') DEFAULT 'Pending',
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_evaluations`
--

CREATE TABLE `performance_evaluations` (
  `eval_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluation_period` varchar(50) NOT NULL COMMENT 'e.g., Q1 2026, Annual 2025',
  `evaluation_date` date NOT NULL,
  `job_knowledge` int(11) DEFAULT NULL CHECK (`job_knowledge` between 1 and 5),
  `work_quality` int(11) DEFAULT NULL CHECK (`work_quality` between 1 and 5),
  `productivity` int(11) DEFAULT NULL CHECK (`productivity` between 1 and 5),
  `communication` int(11) DEFAULT NULL CHECK (`communication` between 1 and 5),
  `teamwork` int(11) DEFAULT NULL CHECK (`teamwork` between 1 and 5),
  `attendance` int(11) DEFAULT NULL CHECK (`attendance` between 1 and 5),
  `initiative` int(11) DEFAULT NULL CHECK (`initiative` between 1 and 5),
  `overall_rating` decimal(3,2) DEFAULT NULL,
  `overall_grade` enum('Outstanding','Very Satisfactory','Satisfactory','Needs Improvement','Unsatisfactory') DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `areas_for_improvement` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `employee_comments` text DEFAULT NULL,
  `status` enum('Draft','Submitted','Acknowledged','Disputed') DEFAULT 'Draft',
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Full system access - HR Director'),
(2, 'HR Manager', 'HR management and approvals'),
(3, 'HR Staff', 'HR operations and processing'),
(4, 'Department Head', 'Department-level management'),
(5, 'Employee', 'Regular employee access');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `role_id`, `department_id`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'System', 'Admin', 'admin@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 'active', '2026-03-02 14:33:53', '2026-02-23 14:36:16', '2026-03-02 06:33:53'),
(2, 'Maria', 'Santos', 'maria.santos@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, 'active', '2026-02-23 22:52:05', '2026-02-23 14:36:16', '2026-02-23 14:52:05'),
(3, 'Juan', 'Dela Cruz', 'juan.delacruz@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 'active', '2026-02-23 22:53:59', '2026-02-23 14:36:16', '2026-02-23 14:53:59'),
(4, 'Rosa', 'Garcia', 'rosa.garcia@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 2, 'active', '2026-02-23 22:55:44', '2026-02-23 14:36:16', '2026-02-23 14:55:44'),
(5, 'Pedro', 'Reyes', 'pedro.reyes@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 2, 'active', '2026-02-23 22:56:22', '2026-02-23 14:36:16', '2026-02-23 14:56:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`),
  ADD KEY `fk_applicant_position` (`position_applied_id`);

--
-- Indexes for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `fk_doc_applicant` (`applicant_id`);

--
-- Indexes for table `applicant_screenings`
--
ALTER TABLE `applicant_screenings`
  ADD PRIMARY KEY (`screening_id`),
  ADD KEY `fk_screening_applicant` (`applicant_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_audit_user` (`user_id`),
  ADD KEY `idx_audit_module` (`module`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  ADD PRIMARY KEY (`clearance_id`),
  ADD KEY `fk_clearance_employee` (`employee_id`);

--
-- Indexes for table `clearance_signatories`
--
ALTER TABLE `clearance_signatories`
  ADD PRIMARY KEY (`signatory_id`),
  ADD KEY `fk_sign_clearance` (`clearance_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_code` (`department_code`);

--
-- Indexes for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `fk_disc_employee` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`),
  ADD KEY `fk_emp_user` (`user_id`),
  ADD KEY `fk_emp_position` (`position_id`),
  ADD KEY `fk_emp_dept` (`department_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `fk_empdoc_employee` (`employee_id`);

--
-- Indexes for table `interview_evaluations`
--
ALTER TABLE `interview_evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `fk_eval_interview` (`interview_id`);

--
-- Indexes for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD PRIMARY KEY (`interview_id`),
  ADD KEY `fk_interview_applicant` (`applicant_id`),
  ADD KEY `fk_interview_interviewer` (`interviewer_id`);

--
-- Indexes for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD PRIMARY KEY (`offer_id`),
  ADD KEY `fk_offer_applicant` (`applicant_id`),
  ADD KEY `fk_offer_position` (`position_id`);

--
-- Indexes for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `fk_position_dept` (`department_id`);

--
-- Indexes for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_onboard_employee` (`employee_id`);

--
-- Indexes for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD PRIMARY KEY (`eval_id`),
  ADD KEY `fk_perf_employee` (`employee_id`),
  ADD KEY `fk_perf_evaluator` (`evaluator_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_role` (`role_id`),
  ADD KEY `fk_user_dept` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applicant_screenings`
--
ALTER TABLE `applicant_screenings`
  MODIFY `screening_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  MODIFY `clearance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clearance_signatories`
--
ALTER TABLE `clearance_signatories`
  MODIFY `signatory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_evaluations`
--
ALTER TABLE `interview_evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_offers`
--
ALTER TABLE `job_offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_positions`
--
ALTER TABLE `job_positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `fk_applicant_position` FOREIGN KEY (`position_applied_id`) REFERENCES `job_positions` (`position_id`) ON DELETE SET NULL;

--
-- Constraints for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD CONSTRAINT `fk_doc_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_screenings`
--
ALTER TABLE `applicant_screenings`
  ADD CONSTRAINT `fk_screening_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  ADD CONSTRAINT `fk_clearance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `clearance_signatories`
--
ALTER TABLE `clearance_signatories`
  ADD CONSTRAINT `fk_sign_clearance` FOREIGN KEY (`clearance_id`) REFERENCES `clearance_requests` (`clearance_id`) ON DELETE CASCADE;

--
-- Constraints for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD CONSTRAINT `fk_disc_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_emp_position` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`position_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `fk_empdoc_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_evaluations`
--
ALTER TABLE `interview_evaluations`
  ADD CONSTRAINT `fk_eval_interview` FOREIGN KEY (`interview_id`) REFERENCES `interview_schedules` (`interview_id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD CONSTRAINT `fk_interview_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_interview_interviewer` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD CONSTRAINT `fk_offer_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_offer_position` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`position_id`);

--
-- Constraints for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD CONSTRAINT `fk_position_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  ADD CONSTRAINT `fk_onboard_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD CONSTRAINT `fk_perf_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_perf_evaluator` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
