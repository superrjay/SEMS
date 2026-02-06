# Database Schema - Student and Enrollment Management System

## Complete SQL Schema

-- ============================================================================
-- DATABASE CREATION
-- ============================================================================

CREATE DATABASE IF NOT EXISTS student_enrollment_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE student_enrollment_db;

-- ============================================================================
-- USER MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles table
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions table
CREATE TABLE permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    module VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Roles junction table
CREATE TABLE user_roles (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role (user_id, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role Permissions junction table
CREATE TABLE role_permissions (
    role_permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Sessions table
CREATE TABLE user_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_token (session_token),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Logs table
CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_module (module),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password Reset Tokens table
CREATE TABLE password_reset_tokens (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STUDENT INFORMATION MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Programs table
CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    program_code VARCHAR(20) UNIQUE NOT NULL,
    program_name VARCHAR(200) NOT NULL,
    department VARCHAR(100),
    degree_level ENUM('undergraduate', 'graduate', 'doctoral') NOT NULL,
    duration_years INT NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students table
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    suffix VARCHAR(10),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    civil_status ENUM('single', 'married', 'widowed', 'separated') DEFAULT 'single',
    nationality VARCHAR(50) DEFAULT 'Filipino',
    religion VARCHAR(50),
    program_id INT NOT NULL,
    year_level INT NOT NULL,
    student_type ENUM('regular', 'irregular', 'transferee', 'shiftee') DEFAULT 'regular',
    status ENUM('active', 'inactive', 'graduated', 'dropped', 'on_leave') DEFAULT 'active',
    admission_date DATE NOT NULL,
    graduation_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    INDEX idx_student_number (student_number),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_program (program_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Personal Information table
CREATE TABLE student_personal_info (
    info_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNIQUE NOT NULL,
    birth_place VARCHAR(200),
    present_address TEXT,
    permanent_address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relation VARCHAR(50),
    father_name VARCHAR(100),
    father_occupation VARCHAR(100),
    father_phone VARCHAR(20),
    mother_name VARCHAR(100),
    mother_occupation VARCHAR(100),
    mother_phone VARCHAR(20),
    guardian_name VARCHAR(100),
    guardian_relation VARCHAR(50),
    guardian_phone VARCHAR(20),
    guardian_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student ID Cards table
CREATE TABLE student_id_cards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    card_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('active', 'expired', 'lost', 'replaced') DEFAULT 'active',
    photo_path VARCHAR(255),
    barcode VARCHAR(100),
    qr_code TEXT,
    issued_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_card_number (card_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Status History table
CREATE TABLE student_status_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    previous_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    reason TEXT,
    changed_by INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CURRICULUM & COURSE MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Curricula table
CREATE TABLE curricula (
    curriculum_id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    curriculum_code VARCHAR(50) UNIQUE NOT NULL,
    curriculum_name VARCHAR(200) NOT NULL,
    effective_year VARCHAR(10) NOT NULL,
    revision_number INT DEFAULT 1,
    total_units INT NOT NULL,
    status ENUM('active', 'archived', 'draft') DEFAULT 'active',
    approved_by INT,
    approved_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_program (program_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subjects table
CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(200) NOT NULL,
    description TEXT,
    units DECIMAL(3,1) NOT NULL,
    lec_hours INT DEFAULT 0,
    lab_hours INT DEFAULT 0,
    subject_type ENUM('major', 'minor', 'elective', 'general_education', 'nstp', 'pe') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (subject_code),
    INDEX idx_type (subject_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Curriculum Subjects junction table
CREATE TABLE curriculum_subjects (
    curriculum_subject_id INT AUTO_INCREMENT PRIMARY KEY,
    curriculum_id INT NOT NULL,
    subject_id INT NOT NULL,
    year_level INT NOT NULL,
    semester INT NOT NULL,
    is_required BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (curriculum_id) REFERENCES curricula(curriculum_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    UNIQUE KEY unique_curriculum_subject (curriculum_id, subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prerequisites table
CREATE TABLE prerequisites (
    prerequisite_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    prerequisite_subject_id INT NOT NULL,
    prerequisite_type ENUM('prerequisite', 'corequisite') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    UNIQUE KEY unique_prerequisite (subject_id, prerequisite_subject_id, prerequisite_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Curriculum Revisions table
CREATE TABLE curriculum_revisions (
    revision_id INT AUTO_INCREMENT PRIMARY KEY,
    old_curriculum_id INT NOT NULL,
    new_curriculum_id INT NOT NULL,
    revision_reason TEXT,
    changes_summary TEXT,
    revised_by INT,
    revised_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (old_curriculum_id) REFERENCES curricula(curriculum_id),
    FOREIGN KEY (new_curriculum_id) REFERENCES curricula(curriculum_id),
    FOREIGN KEY (revised_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CLASS SCHEDULING & SECTION MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Academic Years table
CREATE TABLE academic_years (
    academic_year_id INT AUTO_INCREMENT PRIMARY KEY,
    year_code VARCHAR(10) UNIQUE NOT NULL,
    year_name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'completed', 'upcoming') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Semesters table
CREATE TABLE semesters (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    semester_number INT NOT NULL,
    semester_name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'completed', 'upcoming') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(academic_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rooms table
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_code VARCHAR(20) UNIQUE NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    building VARCHAR(100),
    floor INT,
    capacity INT NOT NULL,
    room_type ENUM('lecture', 'laboratory', 'computer_lab', 'auditorium', 'gym') NOT NULL,
    facilities TEXT,
    status ENUM('available', 'under_maintenance', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Faculty/Teachers table
CREATE TABLE faculty (
    faculty_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    employee_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    suffix VARCHAR(10),
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    position VARCHAR(100),
    specialization TEXT,
    employment_status ENUM('full_time', 'part_time', 'contractual') NOT NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    hire_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_employee_number (employee_number),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sections table
CREATE TABLE sections (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,
    subject_id INT NOT NULL,
    section_code VARCHAR(20) NOT NULL,
    section_name VARCHAR(100),
    max_students INT DEFAULT 40,
    enrolled_count INT DEFAULT 0,
    year_level INT,
    status ENUM('open', 'closed', 'cancelled') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    UNIQUE KEY unique_section (semester_id, subject_id, section_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Class Schedules table
CREATE TABLE class_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    faculty_id INT,
    room_id INT,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    schedule_type ENUM('lecture', 'laboratory') DEFAULT 'lecture',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(section_id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE SET NULL,
    INDEX idx_section (section_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_room (room_id),
    INDEX idx_day (day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teacher Loading table
CREATE TABLE teacher_loads (
    load_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    section_id INT NOT NULL,
    semester_id INT NOT NULL,
    units DECIMAL(3,1) NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(section_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (assigned_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_teacher_section (faculty_id, section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedule Conflicts table
CREATE TABLE schedule_conflicts (
    conflict_id INT AUTO_INCREMENT PRIMARY KEY,
    conflict_type ENUM('room_conflict', 'faculty_conflict', 'student_conflict') NOT NULL,
    schedule_id_1 INT NOT NULL,
    schedule_id_2 INT NOT NULL,
    conflict_date DATE,
    description TEXT,
    status ENUM('detected', 'resolved', 'ignored') DEFAULT 'detected',
    resolved_by INT,
    resolved_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id_1) REFERENCES class_schedules(schedule_id),
    FOREIGN KEY (schedule_id_2) REFERENCES class_schedules(schedule_id),
    FOREIGN KEY (resolved_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ENROLLMENT & REGISTRATION SUBSYSTEM TABLES
-- ============================================================================

-- Enrollment Applications table
CREATE TABLE enrollment_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    application_type ENUM('new', 'continuing', 'returnee', 'cross_enrollee') NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'for_validation', 'validated', 'rejected', 'enrolled') DEFAULT 'pending',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    INDEX idx_student (student_id),
    INDEX idx_semester (semester_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pre-Enrollment table
CREATE TABLE pre_enrollments (
    pre_enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    student_id INT NOT NULL,
    section_id INT NOT NULL,
    priority_level INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES enrollment_applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(section_id),
    UNIQUE KEY unique_pre_enrollment (application_id, section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enrollment Validations table
CREATE TABLE enrollment_validations (
    validation_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    validated_by INT,
    validation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('approved', 'rejected', 'pending_payment') NOT NULL,
    prerequisite_check BOOLEAN DEFAULT FALSE,
    grade_check BOOLEAN DEFAULT FALSE,
    clearance_check BOOLEAN DEFAULT FALSE,
    payment_check BOOLEAN DEFAULT FALSE,
    remarks TEXT,
    FOREIGN KEY (application_id) REFERENCES enrollment_applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (validated_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enrollments table
CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    section_id INT NOT NULL,
    semester_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    enrollment_status ENUM('enrolled', 'dropped', 'withdrawn', 'completed') DEFAULT 'enrolled',
    grade DECIMAL(3,2),
    completion_status ENUM('passed', 'failed', 'incomplete', 'ongoing') DEFAULT 'ongoing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(section_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    UNIQUE KEY unique_enrollment (student_id, section_id, semester_id),
    INDEX idx_student (student_id),
    INDEX idx_semester (semester_id),
    INDEX idx_status (enrollment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enrollment Status Tracking table
CREATE TABLE enrollment_status_tracking (
    tracking_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES enrollment_applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_application (application_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- GRADES & ASSESSMENT MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Grades table
CREATE TABLE grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester_id INT NOT NULL,
    midterm_grade DECIMAL(3,2),
    final_grade DECIMAL(3,2),
    final_rating DECIMAL(3,2),
    remarks ENUM('passed', 'failed', 'incomplete', 'dropped', 'withdrawn'),
    encoded_by INT,
    encoded_at DATETIME NULL DEFAULT NULL,
    verified_by INT,
    verified_at DATETIME NULL DEFAULT NULL,
    status ENUM('draft', 'submitted', 'verified', 'posted') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (encoded_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_grade (enrollment_id),
    INDEX idx_student (student_id),
    INDEX idx_semester (semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Grade Components table
CREATE TABLE grade_components (
    component_id INT AUTO_INCREMENT PRIMARY KEY,
    grade_id INT NOT NULL,
    component_name VARCHAR(100) NOT NULL,
    component_type ENUM('quiz', 'exam', 'project', 'assignment', 'attendance', 'participation') NOT NULL,
    score DECIMAL(5,2),
    max_score DECIMAL(5,2) NOT NULL,
    weight DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grade_id) REFERENCES grades(grade_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Grade Corrections table
CREATE TABLE grade_corrections (
    correction_id INT AUTO_INCREMENT PRIMARY KEY,
    grade_id INT NOT NULL,
    old_grade DECIMAL(3,2),
    new_grade DECIMAL(3,2) NOT NULL,
    reason TEXT NOT NULL,
    requested_by INT NOT NULL,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_by INT,
    approved_at DATETIME NULL DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    remarks TEXT,
    FOREIGN KEY (grade_id) REFERENCES grades(grade_id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES users(user_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Academic Records table (for transcript)
CREATE TABLE academic_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    gpa DECIMAL(3,2),
    total_units_enrolled DECIMAL(4,1),
    total_units_earned DECIMAL(4,1),
    academic_status ENUM('good_standing', 'probation', 'warning', 'dean_list') DEFAULT 'good_standing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    UNIQUE KEY unique_record (student_id, semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PAYMENT & ACCOUNTING SUBSYSTEM TABLES
-- ============================================================================

-- Fee Types table
CREATE TABLE fee_types (
    fee_type_id INT AUTO_INCREMENT PRIMARY KEY,
    fee_code VARCHAR(20) UNIQUE NOT NULL,
    fee_name VARCHAR(100) NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,
    fee_category ENUM('tuition', 'miscellaneous', 'laboratory', 'library', 'registration', 'other') NOT NULL,
    is_per_unit BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fee Assessments table
CREATE TABLE fee_assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    total_units DECIMAL(4,1) NOT NULL,
    tuition_fee DECIMAL(10,2) NOT NULL,
    miscellaneous_fees DECIMAL(10,2) DEFAULT 0,
    laboratory_fees DECIMAL(10,2) DEFAULT 0,
    other_fees DECIMAL(10,2) DEFAULT 0,
    total_assessment DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    net_amount DECIMAL(10,2) NOT NULL,
    assessed_by INT,
    assessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'finalized', 'adjusted') DEFAULT 'draft',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (assessed_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_assessment (student_id, semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment Details table
CREATE TABLE assessment_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    fee_type_id INT NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1,
    unit_amount DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (assessment_id) REFERENCES fee_assessments(assessment_id) ON DELETE CASCADE,
    FOREIGN KEY (fee_type_id) REFERENCES fee_types(fee_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assessment_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'check', 'credit_card', 'debit_card', 'bank_transfer', 'gcash', 'paymaya', 'online') NOT NULL,
    reference_number VARCHAR(100),
    receipt_number VARCHAR(50) UNIQUE NOT NULL,
    payment_status ENUM('pending', 'verified', 'cancelled') DEFAULT 'pending',
    remarks TEXT,
    received_by INT,
    verified_by INT,
    verified_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES fee_assessments(assessment_id),
    FOREIGN KEY (received_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Scholarships table
CREATE TABLE scholarships (
    scholarship_id INT AUTO_INCREMENT PRIMARY KEY,
    scholarship_code VARCHAR(20) UNIQUE NOT NULL,
    scholarship_name VARCHAR(200) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed_amount') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    requirements TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Scholarships table
CREATE TABLE student_scholarships (
    student_scholarship_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    scholarship_id INT NOT NULL,
    semester_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    grant_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    granted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(scholarship_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (granted_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_student_scholarship (student_id, scholarship_id, semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DOCUMENT & CREDENTIALS SUBSYSTEM TABLES
-- ============================================================================

-- Document Types table
CREATE TABLE document_types (
    document_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_code VARCHAR(20) UNIQUE NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    description TEXT,
    processing_fee DECIMAL(10,2) DEFAULT 0,
    processing_days INT DEFAULT 3,
    requirements TEXT,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document Requests table
CREATE TABLE document_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    document_type_id INT NOT NULL,
    quantity INT DEFAULT 1,
    purpose TEXT,
    request_date DATE NOT NULL,
    expected_release_date DATE,
    actual_release_date DATE,
    tracking_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'ready', 'released', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    claimed_by VARCHAR(100),
    claimed_date DATE,
    valid_id_type VARCHAR(50),
    valid_id_number VARCHAR(50),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (document_type_id) REFERENCES document_types(document_type_id),
    INDEX idx_student (student_id),
    INDEX idx_tracking (tracking_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document Workflow table
CREATE TABLE document_workflow (
    workflow_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    step_name VARCHAR(100) NOT NULL,
    step_order INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'skipped') DEFAULT 'pending',
    assigned_to INT,
    started_at TIMESTAMP,
    completed_at DATETIME NULL DEFAULT NULL,
    remarks TEXT,
    FOREIGN KEY (request_id) REFERENCES document_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Generated Documents table
CREATE TABLE generated_documents (
    generated_doc_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    document_path VARCHAR(255),
    generated_by INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_hash VARCHAR(64),
    FOREIGN KEY (request_id) REFERENCES document_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Archived Documents table
CREATE TABLE archived_documents (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    document_path VARCHAR(255),
    academic_year VARCHAR(10),
    archived_by INT,
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (archived_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- HUMAN RESOURCE MANAGEMENT SUBSYSTEM TABLES
-- ============================================================================

-- Applicants table
CREATE TABLE applicants (
    applicant_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    position_applied VARCHAR(100) NOT NULL,
    application_date DATE NOT NULL,
    resume_path VARCHAR(255),
    status ENUM('new', 'screening', 'interview', 'evaluation', 'hired', 'rejected') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pre-Employment Requirements table
CREATE TABLE pre_employment_requirements (
    requirement_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    requirement_name VARCHAR(100) NOT NULL,
    file_path VARCHAR(255),
    submission_date DATE,
    verified_by INT,
    verified_at DATETIME NULL DEFAULT NULL,
    status ENUM('pending', 'submitted', 'verified', 'rejected') DEFAULT 'pending',
    remarks TEXT,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Interviews table
CREATE TABLE interviews (
    interview_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    interview_date DATE NOT NULL,
    interview_time TIME NOT NULL,
    interview_type ENUM('initial', 'technical', 'final', 'panel') NOT NULL,
    interviewer_id INT,
    location VARCHAR(200),
    status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Evaluations table
CREATE TABLE evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    interview_id INT,
    evaluator_id INT NOT NULL,
    evaluation_date DATE NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    strengths TEXT,
    weaknesses TEXT,
    recommendation ENUM('highly_recommended', 'recommended', 'conditional', 'not_recommended') NOT NULL,
    remarks TEXT,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (interview_id) REFERENCES interviews(interview_id) ON DELETE SET NULL,
    FOREIGN KEY (evaluator_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job Offers table
CREATE TABLE job_offers (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    position VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    salary DECIMAL(10,2),
    employment_type ENUM('full_time', 'part_time', 'contractual') NOT NULL,
    start_date DATE,
    offer_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('pending', 'accepted', 'declined', 'expired') DEFAULT 'pending',
    offered_by INT,
    remarks TEXT,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (offered_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employee Performance table
CREATE TABLE employee_performance (
    performance_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    evaluation_period VARCHAR(50) NOT NULL,
    evaluator_id INT,
    evaluation_date DATE NOT NULL,
    overall_rating DECIMAL(3,2),
    teaching_effectiveness INT CHECK (teaching_effectiveness BETWEEN 1 AND 5),
    professionalism INT CHECK (professionalism BETWEEN 1 AND 5),
    innovation INT CHECK (innovation BETWEEN 1 AND 5),
    student_interaction INT CHECK (student_interaction BETWEEN 1 AND 5),
    strengths TEXT,
    areas_for_improvement TEXT,
    action_plan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
    FOREIGN KEY (evaluator_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clearances table
CREATE TABLE clearances (
    clearance_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    clearance_type ENUM('resignation', 'retirement', 'end_of_contract') NOT NULL,
    request_date DATE NOT NULL,
    last_working_day DATE,
    status ENUM('pending', 'in_progress', 'cleared', 'with_accountabilities') DEFAULT 'pending',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clearance Items table
CREATE TABLE clearance_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    clearance_id INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('pending', 'cleared', 'with_issues') DEFAULT 'pending',
    cleared_by INT,
    cleared_at TIMESTAMP,
    remarks TEXT,
    FOREIGN KEY (clearance_id) REFERENCES clearances(clearance_id) ON DELETE CASCADE,
    FOREIGN KEY (cleared_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CLINIC & MEDICAL SERVICES SUBSYSTEM TABLES
-- ============================================================================

-- Medical Records table
CREATE TABLE medical_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    blood_type VARCHAR(5),
    height DECIMAL(5,2),
    weight DECIMAL(5,2),
    allergies TEXT,
    medical_conditions TEXT,
    current_medications TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relation VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    UNIQUE KEY unique_medical_record (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Consultations table
CREATE TABLE consultations (
    consultation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    consultation_date DATETIME NOT NULL,
    chief_complaint TEXT NOT NULL,
    vital_signs TEXT,
    diagnosis TEXT,
    treatment TEXT,
    prescriptions TEXT,
    follow_up_date DATE,
    attended_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (attended_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id),
    INDEX idx_date (consultation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medicines table
CREATE TABLE medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_code VARCHAR(20) UNIQUE NOT NULL,
    medicine_name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200),
    dosage VARCHAR(50),
    form VARCHAR(50),
    description TEXT,
    unit_of_measure VARCHAR(20),
    reorder_level INT DEFAULT 10,
    status ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medicine Inventory table
CREATE TABLE medicine_inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    batch_number VARCHAR(50),
    quantity INT NOT NULL,
    expiry_date DATE,
    received_date DATE NOT NULL,
    supplier VARCHAR(200),
    unit_cost DECIMAL(10,2),
    received_by INT,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_medicine (medicine_id),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medicine Dispensing table
CREATE TABLE medicine_dispensing (
    dispensing_id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,
    dosage_instruction TEXT,
    dispensed_by INT,
    dispensed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(consultation_id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id),
    FOREIGN KEY (dispensed_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medical Clearances table
CREATE TABLE medical_clearances (
    clearance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT,
    clearance_type ENUM('enrollment', 'athletic', 'internship', 'graduation', 'general') NOT NULL,
    issue_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('fit', 'unfit', 'with_restrictions') NOT NULL,
    restrictions TEXT,
    issued_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id) ON DELETE SET NULL,
    FOREIGN KEY (issued_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Health Incidents table
CREATE TABLE health_incidents (
    incident_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    incident_date DATETIME NOT NULL,
    incident_type ENUM('injury', 'illness', 'accident', 'emergency', 'other') NOT NULL,
    location VARCHAR(200),
    description TEXT NOT NULL,
    severity ENUM('minor', 'moderate', 'serious', 'critical') NOT NULL,
    first_aid_given TEXT,
    referred_to VARCHAR(200),
    witnesses TEXT,
    reported_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL,
    FOREIGN KEY (reported_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id),
    INDEX idx_date (incident_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INDEXES FOR OPTIMIZATION
-- ============================================================================

-- Additional composite indexes for common queries
CREATE INDEX idx_enrollment_student_semester ON enrollments(student_id, semester_id);
CREATE INDEX idx_grade_student_semester ON grades(student_id, semester_id);
CREATE INDEX idx_payment_student_date ON payments(student_id, payment_date);
CREATE INDEX idx_consultation_student_date ON consultations(student_id, consultation_date);

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger to update enrolled count in sections
DELIMITER //
CREATE TRIGGER after_enrollment_insert
AFTER INSERT ON enrollments
FOR EACH ROW
BEGIN
    UPDATE sections 
    SET enrolled_count = enrolled_count + 1
    WHERE section_id = NEW.section_id;
END//

CREATE TRIGGER after_enrollment_delete
AFTER DELETE ON enrollments
FOR EACH ROW
BEGIN
    UPDATE sections 
    SET enrolled_count = enrolled_count - 1
    WHERE section_id = OLD.section_id;
END//
DELIMITER ;

-- Trigger to log activity
DELIMITER //
CREATE TRIGGER after_student_update
AFTER UPDATE ON students
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, module, record_id, description)
    VALUES (
        @current_user_id,
        'UPDATE',
        'student',
        NEW.student_id,
        CONCAT('Updated student: ', NEW.first_name, ' ', NEW.last_name)
    );
END//
DELIMITER ;

-- ============================================================================
-- VIEWS FOR COMMON QUERIES
-- ============================================================================

-- View for student summary
CREATE VIEW vw_student_summary AS
SELECT 
    s.student_id,
    s.student_number,
    CONCAT(s.first_name, ' ', IFNULL(s.middle_name, ''), ' ', s.last_name) AS full_name,
    s.email,
    p.program_name,
    s.year_level,
    s.status,
    COUNT(DISTINCT e.enrollment_id) AS total_enrollments,
    AVG(ar.gpa) AS overall_gpa
FROM students s
LEFT JOIN programs p ON s.program_id = p.program_id
LEFT JOIN enrollments e ON s.student_id = e.student_id
LEFT JOIN academic_records ar ON s.student_id = ar.student_id
GROUP BY s.student_id;

-- View for current enrollment
CREATE VIEW vw_current_enrollments AS
SELECT 
    e.enrollment_id,
    s.student_number,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    subj.subject_code,
    subj.subject_name,
    sec.section_code,
    sem.semester_name,
    e.enrollment_status
FROM enrollments e
JOIN students s ON e.student_id = s.student_id
JOIN sections sec ON e.section_id = sec.section_id
JOIN subjects subj ON sec.subject_id = subj.subject_id
JOIN semesters sem ON e.semester_id = sem.semester_id
WHERE e.enrollment_status = 'enrolled';

-- View for payment summary
CREATE VIEW vw_payment_summary AS
SELECT 
    s.student_id,
    s.student_number,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    fa.total_assessment,
    IFNULL(SUM(p.amount), 0) AS total_paid,
    (fa.total_assessment - IFNULL(SUM(p.amount), 0)) AS balance
FROM students s
LEFT JOIN fee_assessments fa ON s.student_id = fa.student_id
LEFT JOIN payments p ON fa.assessment_id = p.assessment_id AND p.payment_status = 'verified'
GROUP BY s.student_id, fa.assessment_id;

## Initial Data Seeds

-- ============================================================================
-- SEED DATA FOR ROLES
-- ============================================================================

INSERT INTO roles (role_name, description) VALUES
('super_admin', 'Full system access and control'),
('admin', 'Administrative access to most features'),
('registrar', 'Access to student records and enrollment'),
('faculty', 'Access to grades and class management'),
('accounting', 'Access to payment and billing'),
('clinic_staff', 'Access to medical records'),
('hr_staff', 'Access to HR management'),
('document_staff', 'Access to document processing'),
('student', 'Student portal access');

-- ============================================================================
-- SEED DATA FOR PERMISSIONS
-- ============================================================================

INSERT INTO permissions (permission_name, description, module) VALUES
-- Student Module
('student.view', 'View student information', 'student'),
('student.create', 'Create new student records', 'student'),
('student.update', 'Update student information', 'student'),
('student.delete', 'Delete student records', 'student'),

-- Enrollment Module
('enrollment.view', 'View enrollment records', 'enrollment'),
('enrollment.create', 'Create enrollment applications', 'enrollment'),
('enrollment.validate', 'Validate enrollments', 'enrollment'),
('enrollment.approve', 'Approve enrollments', 'enrollment'),

-- Curriculum Module
('curriculum.view', 'View curriculum', 'curriculum'),
('curriculum.manage', 'Manage curriculum', 'curriculum'),

-- Scheduling Module
('scheduling.view', 'View schedules', 'scheduling'),
('scheduling.manage', 'Manage class schedules', 'scheduling'),

-- Grades Module
('grades.view', 'View grades', 'grades'),
('grades.encode', 'Encode grades', 'grades'),
('grades.verify', 'Verify grades', 'grades'),

-- Payment Module
('payment.view', 'View payment records', 'payment'),
('payment.post', 'Post payments', 'payment'),
('payment.verify', 'Verify payments', 'payment'),

-- Document Module
('document.request', 'Request documents', 'document'),
('document.process', 'Process document requests', 'document'),
('document.release', 'Release documents', 'document'),

-- HR Module
('hr.view', 'View HR records', 'hr'),
('hr.manage', 'Manage HR records', 'hr'),

-- Clinic Module
('clinic.view', 'View medical records', 'clinic'),
('clinic.manage', 'Manage medical services', 'clinic'),

-- User Module
('user.view', 'View users', 'user'),
('user.create', 'Create users', 'user'),
('user.manage', 'Manage users and roles', 'user');
