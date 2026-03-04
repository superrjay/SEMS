SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Database: `clinic_db`

-- --------------------------------------------------------
-- ROLES
-- --------------------------------------------------------
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Full system access — Clinic Administrator'),
(2, 'Doctor', 'Physician — consultations, clearances, records'),
(3, 'Nurse', 'Clinic nurse — vitals, dispensing, incidents'),
(4, 'Staff', 'Clinic staff — basic record viewing');

-- --------------------------------------------------------
-- USERS
-- --------------------------------------------------------
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_role` (`role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `role_id`, `status`) VALUES
(1, 'System', 'Admin', 'admin@clinic.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
(2, 'Dr. Maria', 'Santos', 'doctor@clinic.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'active'),
(3, 'Nurse', 'Reyes', 'nurse@clinic.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'active'),
(4, 'Ana', 'Staff', 'staff@clinic.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'active');

-- --------------------------------------------------------
-- PATIENTS (Students)
-- --------------------------------------------------------
CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_number` varchar(30) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` int(11) DEFAULT 1,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown') DEFAULT 'Unknown',
  `allergies` text DEFAULT NULL,
  `existing_conditions` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive','Graduated') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`patient_id`),
  UNIQUE KEY `student_number` (`student_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- MEDICAL RECORDS
-- --------------------------------------------------------
CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `record_type` enum('Physical Exam','Lab Result','Vaccination','Dental','Vision','Xray','Medical History','Other') NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `record_date` date NOT NULL,
  `attending_physician` varchar(150) DEFAULT NULL,
  `attachments` varchar(255) DEFAULT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`record_id`),
  KEY `patient_id` (`patient_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `fk_mr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_user` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- CONSULTATIONS & TREATMENT LOGS
-- --------------------------------------------------------
CREATE TABLE `consultations` (
  `consultation_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `consultation_date` datetime NOT NULL,
  `chief_complaint` text NOT NULL,
  `symptoms` text DEFAULT NULL,
  `vital_signs_bp` varchar(20) DEFAULT NULL,
  `vital_signs_temp` varchar(10) DEFAULT NULL,
  `vital_signs_hr` varchar(10) DEFAULT NULL,
  `vital_signs_rr` varchar(10) DEFAULT NULL,
  `vital_signs_weight` varchar(10) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `status` enum('Ongoing','Completed','Follow-up','Referred') DEFAULT 'Ongoing',
  `attending_doctor` int(11) DEFAULT NULL,
  `nurse_on_duty` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`consultation_id`),
  KEY `patient_id` (`patient_id`),
  KEY `attending_doctor` (`attending_doctor`),
  CONSTRAINT `fk_cons_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cons_doctor` FOREIGN KEY (`attending_doctor`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- MEDICINES
-- --------------------------------------------------------
CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `generic_name` varchar(200) DEFAULT NULL,
  `category` enum('Analgesic','Antibiotic','Antiviral','Antiseptic','Vitamins','First Aid','Antacid','Antihistamine','Antifungal','Other') DEFAULT 'Other',
  `dosage_form` enum('Tablet','Capsule','Syrup','Cream','Ointment','Injection','Drops','Inhaler','Other') DEFAULT 'Tablet',
  `unit` varchar(50) DEFAULT 'pcs',
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `reorder_level` int(11) DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(150) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT 0.00,
  `status` enum('Available','Low Stock','Out of Stock','Expired') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`medicine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `medicines` (`medicine_id`, `name`, `generic_name`, `category`, `dosage_form`, `stock_quantity`, `reorder_level`, `expiry_date`, `unit_cost`) VALUES
(1, 'Biogesic', 'Paracetamol 500mg', 'Analgesic', 'Tablet', 200, 50, '2027-06-30', 2.50),
(2, 'Neozep', 'Phenylephrine+Chlorphenamine', 'Antihistamine', 'Tablet', 150, 30, '2027-03-15', 5.00),
(3, 'Amoxicillin 500mg', 'Amoxicillin', 'Antibiotic', 'Capsule', 80, 20, '2026-12-31', 8.00),
(4, 'Betadine Solution', 'Povidone-Iodine', 'Antiseptic', 'Other', 30, 10, '2027-08-01', 45.00),
(5, 'Cetirizine 10mg', 'Cetirizine', 'Antihistamine', 'Tablet', 120, 25, '2027-05-20', 3.50),
(6, 'Mefenamic Acid 500mg', 'Mefenamic Acid', 'Analgesic', 'Capsule', 100, 25, '2027-04-10', 4.00),
(7, 'Kremil-S', 'Aluminum+Magnesium Hydroxide', 'Antacid', 'Tablet', 90, 20, '2027-07-15', 5.50),
(8, 'Band-Aid Strips', 'Adhesive Bandage', 'First Aid', 'Other', 300, 50, '2028-01-01', 2.00);

-- --------------------------------------------------------
-- MEDICINE DISPENSING
-- --------------------------------------------------------
CREATE TABLE `medicine_dispensing` (
  `dispensing_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `dosage_instructions` text DEFAULT NULL,
  `dispensed_date` datetime NOT NULL DEFAULT current_timestamp(),
  `dispensed_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`dispensing_id`),
  KEY `patient_id` (`patient_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `consultation_id` (`consultation_id`),
  KEY `dispensed_by` (`dispensed_by`),
  CONSTRAINT `fk_disp_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_disp_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  CONSTRAINT `fk_disp_consult` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_disp_user` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- MEDICAL CLEARANCES
-- --------------------------------------------------------
CREATE TABLE `medical_clearances` (
  `clearance_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `purpose` enum('Enrollment','OJT','Sports','Graduation','Employment','Field Trip','Other') NOT NULL,
  `purpose_details` varchar(255) DEFAULT NULL,
  `exam_date` date NOT NULL,
  `bp` varchar(20) DEFAULT NULL,
  `temp` varchar(10) DEFAULT NULL,
  `hr` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `status` enum('Pending','Cleared','Not Cleared','Conditional') DEFAULT 'Pending',
  `issued_by` int(11) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clearance_id`),
  KEY `patient_id` (`patient_id`),
  KEY `issued_by` (`issued_by`),
  CONSTRAINT `fk_clr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_clr_user` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- HEALTH INCIDENTS
-- --------------------------------------------------------
CREATE TABLE `health_incidents` (
  `incident_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `incident_date` datetime NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `incident_type` enum('Injury','Illness','Allergic Reaction','Fainting','Seizure','Mental Health','Accident','Other') NOT NULL,
  `severity` enum('Minor','Moderate','Severe','Critical') DEFAULT 'Minor',
  `description` text NOT NULL,
  `immediate_action` text DEFAULT NULL,
  `outcome` text DEFAULT NULL,
  `referred_to` varchar(200) DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `status` enum('Open','Under Review','Resolved','Closed') DEFAULT 'Open',
  `reported_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`incident_id`),
  KEY `patient_id` (`patient_id`),
  KEY `reported_by` (`reported_by`),
  CONSTRAINT `fk_hi_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_hi_user` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- AUDIT LOGS
-- --------------------------------------------------------
CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(200) NOT NULL,
  `record_type` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `module` (`module`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
