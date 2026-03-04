-- ============================================================
-- HR8 - Human Resource Management Sub-system Database
-- Version: 1.0.0
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- Drop tables if exist (in reverse dependency order)
-- ============================================================
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `clearance_signatories`;
DROP TABLE IF EXISTS `clearance_requests`;
DROP TABLE IF EXISTS `disciplinary_records`;
DROP TABLE IF EXISTS `performance_evaluations`;
DROP TABLE IF EXISTS `onboarding_tasks`;
DROP TABLE IF EXISTS `employee_documents`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `job_offers`;
DROP TABLE IF EXISTS `interview_evaluations`;
DROP TABLE IF EXISTS `interview_schedules`;
DROP TABLE IF EXISTS `applicant_screenings`;
DROP TABLE IF EXISTS `applicant_documents`;
DROP TABLE IF EXISTS `applicants`;
DROP TABLE IF EXISTS `job_positions`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `users`;

-- ============================================================
-- Core Tables
-- ============================================================

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Full system access - HR Director'),
(2, 'HR Manager', 'HR management and approvals'),
(3, 'HR Staff', 'HR operations and processing'),
(4, 'Department Head', 'Department-level management'),
(5, 'Employee', 'Regular employee access');

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `head_user_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_code` (`department_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `departments` (`department_id`, `department_name`, `department_code`, `head_user_id`) VALUES
(1, 'Human Resources', 'HR', NULL),
(2, 'Information Technology', 'IT', NULL),
(3, 'Finance & Accounting', 'FIN', NULL),
(4, 'Academic Affairs', 'ACAD', NULL),
(5, 'Student Services', 'SS', NULL),
(6, 'Administration', 'ADMIN', NULL),
(7, 'Marketing', 'MKT', NULL);

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  KEY `fk_user_role` (`role_id`),
  KEY `fk_user_dept` (`department_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `fk_user_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password: admin123
INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `role_id`, `department_id`) VALUES
(1, 'System', 'Admin', 'admin@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
(2, 'Maria', 'Santos', 'maria.santos@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1),
(3, 'Juan', 'Dela Cruz', 'juan.delacruz@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1),
(4, 'Rosa', 'Garcia', 'rosa.garcia@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 2),
(5, 'Pedro', 'Reyes', 'pedro.reyes@hr8.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 2);

-- ============================================================
-- Module 1: Pre-Employment Management
-- ============================================================

CREATE TABLE `job_positions` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`position_id`),
  KEY `fk_position_dept` (`department_id`),
  CONSTRAINT `fk_position_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `job_positions` (`position_id`, `title`, `department_id`, `description`, `requirements`, `employment_type`, `salary_grade`, `slots`) VALUES
(1, 'Software Developer', 2, 'Develop and maintain web applications', 'BS Computer Science, 2+ years experience', 'Full-Time', 'SG-15', 2),
(2, 'HR Assistant', 1, 'Assist in HR operations and documentation', 'BS Psychology/HRDM, Fresh graduate welcome', 'Full-Time', 'SG-10', 1),
(3, 'Accounting Clerk', 3, 'Handle financial records and reports', 'BS Accountancy, CPA preferred', 'Full-Time', 'SG-12', 1),
(4, 'Part-Time Instructor', 4, 'Teach assigned subjects', 'Masters Degree required', 'Part-Time', 'SG-18', 5);

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(30) NOT NULL UNIQUE,
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
  `application_date` date NOT NULL DEFAULT (CURRENT_DATE),
  `status` enum('New','Screening','Shortlisted','For Interview','Interviewed','For Exam','Examined','Ranked','Offered','Hired','Rejected','Withdrawn','Pooled') DEFAULT 'New',
  `source` varchar(100) DEFAULT NULL COMMENT 'Where the applicant learned about the position',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`applicant_id`),
  KEY `fk_applicant_position` (`position_applied_id`),
  CONSTRAINT `fk_applicant_position` FOREIGN KEY (`position_applied_id`) REFERENCES `job_positions` (`position_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `applicants` (`applicant_id`, `reference_no`, `first_name`, `last_name`, `middle_name`, `email`, `phone`, `address`, `date_of_birth`, `gender`, `civil_status`, `position_applied_id`, `status`, `source`) VALUES
(1, 'APP-2026-0001', 'Ana', 'Mendoza', 'Cruz', 'ana.mendoza@gmail.com', '09171234567', '123 Rizal St, Manila', '1998-05-15', 'Female', 'Single', 1, 'For Interview', 'JobStreet'),
(2, 'APP-2026-0002', 'Carlos', 'Rivera', NULL, 'carlos.rivera@gmail.com', '09181234567', '456 Bonifacio Ave, Quezon City', '1995-08-20', 'Male', 'Married', 2, 'Screening', 'Walk-in'),
(3, 'APP-2026-0003', 'Lisa', 'Tan', 'Marie', 'lisa.tan@gmail.com', '09191234567', '789 Mabini Rd, Makati', '2000-01-10', 'Female', 'Single', 1, 'Shortlisted', 'LinkedIn'),
(4, 'APP-2026-0004', 'Mark', 'Bautista', 'James', 'mark.bautista@gmail.com', '09201234567', '321 Luna St, Pasig', '1997-11-25', 'Male', 'Single', 3, 'New', 'Referral'),
(5, 'APP-2026-0005', 'Grace', 'Lim', NULL, 'grace.lim@gmail.com', '09211234567', '654 Aguinaldo Blvd, Cavite', '1996-03-08', 'Female', 'Married', 4, 'Hired', 'University Career Fair');

CREATE TABLE `applicant_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `document_type` enum('Resume','Transcript','Diploma','Certificate','NBI Clearance','Medical Certificate','Photo','Government ID','Other') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `fk_doc_applicant` (`applicant_id`),
  CONSTRAINT `fk_doc_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `applicant_screenings` (
  `screening_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `screened_by` int(11) NOT NULL,
  `screening_date` date NOT NULL,
  `documents_complete` tinyint(1) DEFAULT 0,
  `qualifications_met` tinyint(1) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `result` enum('Passed','Failed','Pending') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`screening_id`),
  KEY `fk_screening_applicant` (`applicant_id`),
  CONSTRAINT `fk_screening_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Module 2: Recruitment & Selection Workflow
-- ============================================================

CREATE TABLE `interview_schedules` (
  `interview_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `interview_type` enum('Initial','Technical','Panel','Final') DEFAULT 'Initial',
  `location` varchar(255) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled','No Show') DEFAULT 'Scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`interview_id`),
  KEY `fk_interview_applicant` (`applicant_id`),
  KEY `fk_interview_interviewer` (`interviewer_id`),
  CONSTRAINT `fk_interview_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_interview_interviewer` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `interview_evaluations` (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
  `interview_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `communication_score` int(11) DEFAULT NULL CHECK (`communication_score` BETWEEN 1 AND 10),
  `technical_score` int(11) DEFAULT NULL CHECK (`technical_score` BETWEEN 1 AND 10),
  `experience_score` int(11) DEFAULT NULL CHECK (`experience_score` BETWEEN 1 AND 10),
  `cultural_fit_score` int(11) DEFAULT NULL CHECK (`cultural_fit_score` BETWEEN 1 AND 10),
  `overall_score` decimal(4,2) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `recommendation` enum('Highly Recommended','Recommended','With Reservation','Not Recommended') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`evaluation_id`),
  KEY `fk_eval_interview` (`interview_id`),
  CONSTRAINT `fk_eval_interview` FOREIGN KEY (`interview_id`) REFERENCES `interview_schedules` (`interview_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_offers` (
  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `offered_salary` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contractual','Probationary') DEFAULT 'Probationary',
  `offer_date` date NOT NULL DEFAULT (CURRENT_DATE),
  `expiry_date` date DEFAULT NULL,
  `status` enum('Draft','Sent','Accepted','Declined','Expired','Withdrawn') DEFAULT 'Draft',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`offer_id`),
  KEY `fk_offer_applicant` (`applicant_id`),
  KEY `fk_offer_position` (`position_id`),
  CONSTRAINT `fk_offer_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_offer_position` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Module 3: Employment Records & Onboarding
-- ============================================================

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_no` varchar(30) NOT NULL UNIQUE,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`employee_id`),
  KEY `fk_emp_user` (`user_id`),
  KEY `fk_emp_position` (`position_id`),
  KEY `fk_emp_dept` (`department_id`),
  CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_emp_position` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`position_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `employees` (`employee_id`, `employee_no`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `position_id`, `department_id`, `employment_type`, `employment_status`, `date_hired`, `regularization_date`, `salary_grade`, `basic_salary`) VALUES
(1, 'EMP-2024-0001', 1, 'System', 'Admin', 'admin@hr8.com', '09170000001', NULL, 1, 'Full-Time', 'Regular', '2020-01-15', '2020-07-15', 'SG-24', 85000.00),
(2, 'EMP-2024-0002', 2, 'Maria', 'Santos', 'maria.santos@hr8.com', '09170000002', NULL, 1, 'Full-Time', 'Regular', '2021-03-01', '2021-09-01', 'SG-20', 65000.00),
(3, 'EMP-2024-0003', 3, 'Juan', 'Dela Cruz', 'juan.delacruz@hr8.com', '09170000003', 2, 1, 'Full-Time', 'Regular', '2022-06-15', '2022-12-15', 'SG-10', 35000.00),
(4, 'EMP-2024-0004', 4, 'Rosa', 'Garcia', 'rosa.garcia@hr8.com', '09170000004', 1, 2, 'Full-Time', 'Regular', '2021-08-01', '2022-02-01', 'SG-18', 55000.00),
(5, 'EMP-2024-0005', 5, 'Pedro', 'Reyes', 'pedro.reyes@hr8.com', '09170000005', 1, 2, 'Full-Time', 'Probationary', '2025-11-01', NULL, 'SG-15', 42000.00);

CREATE TABLE `employee_documents` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `document_type` enum('Contract','NDA','COE','201 File','Government ID','TIN','SSS','PhilHealth','PagIBIG','Resume','Diploma','Other') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`doc_id`),
  KEY `fk_empdoc_employee` (`employee_id`),
  CONSTRAINT `fk_empdoc_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `onboarding_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Documentation','Training','IT Setup','Orientation','Compliance','Other') DEFAULT 'Other',
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Overdue') DEFAULT 'Pending',
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`task_id`),
  KEY `fk_onboard_employee` (`employee_id`),
  CONSTRAINT `fk_onboard_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Module 4: Employee Performance & Service Management
-- ============================================================

CREATE TABLE `performance_evaluations` (
  `eval_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluation_period` varchar(50) NOT NULL COMMENT 'e.g., Q1 2026, Annual 2025',
  `evaluation_date` date NOT NULL,
  `job_knowledge` int(11) DEFAULT NULL CHECK (`job_knowledge` BETWEEN 1 AND 5),
  `work_quality` int(11) DEFAULT NULL CHECK (`work_quality` BETWEEN 1 AND 5),
  `productivity` int(11) DEFAULT NULL CHECK (`productivity` BETWEEN 1 AND 5),
  `communication` int(11) DEFAULT NULL CHECK (`communication` BETWEEN 1 AND 5),
  `teamwork` int(11) DEFAULT NULL CHECK (`teamwork` BETWEEN 1 AND 5),
  `attendance` int(11) DEFAULT NULL CHECK (`attendance` BETWEEN 1 AND 5),
  `initiative` int(11) DEFAULT NULL CHECK (`initiative` BETWEEN 1 AND 5),
  `overall_rating` decimal(3,2) DEFAULT NULL,
  `overall_grade` enum('Outstanding','Very Satisfactory','Satisfactory','Needs Improvement','Unsatisfactory') DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `areas_for_improvement` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `employee_comments` text DEFAULT NULL,
  `status` enum('Draft','Submitted','Acknowledged','Disputed') DEFAULT 'Draft',
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`eval_id`),
  KEY `fk_perf_employee` (`employee_id`),
  KEY `fk_perf_evaluator` (`evaluator_id`),
  CONSTRAINT `fk_perf_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_perf_evaluator` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `disciplinary_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `type` enum('Verbal Warning','Written Warning','Suspension','Commendation','Award','Memo') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `issued_by` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` enum('Active','Resolved','Appealed','Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`record_id`),
  KEY `fk_disc_employee` (`employee_id`),
  CONSTRAINT `fk_disc_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Module 5: Post-Employment & Clearance Processing
-- ============================================================

CREATE TABLE `clearance_requests` (
  `clearance_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`clearance_id`),
  KEY `fk_clearance_employee` (`employee_id`),
  CONSTRAINT `fk_clearance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `clearance_signatories` (
  `signatory_id` int(11) NOT NULL AUTO_INCREMENT,
  `clearance_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `signatory_user_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Cleared','Not Cleared') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`signatory_id`),
  KEY `fk_sign_clearance` (`clearance_id`),
  CONSTRAINT `fk_sign_clearance` FOREIGN KEY (`clearance_id`) REFERENCES `clearance_requests` (`clearance_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Module 6: Audit Trail
-- ============================================================

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `record_type` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` longtext DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext DEFAULT NULL CHECK (json_valid(`new_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `fk_audit_user` (`user_id`),
  KEY `idx_audit_module` (`module`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_logs` (`user_id`, `module`, `action`, `record_type`, `record_id`, `ip_address`) VALUES
(1, 'System', 'System Initialized', 'system', NULL, '127.0.0.1'),
(1, 'Pre-Employment', 'Created Applicant', 'applicant', 1, '127.0.0.1'),
(2, 'Pre-Employment', 'Screened Applicant', 'applicant', 1, '127.0.0.1'),
(1, 'Recruitment', 'Scheduled Interview', 'interview', 1, '127.0.0.1'),
(1, 'Employee Records', 'Created Employee', 'employee', 5, '127.0.0.1');

COMMIT;
