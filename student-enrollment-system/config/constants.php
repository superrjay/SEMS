<?php
/**
 * Constants Configuration
 * 
 * System-wide constants and enumerations
 */

// User roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_REGISTRAR', 'registrar');
define('ROLE_FACULTY', 'faculty');
define('ROLE_ACCOUNTING', 'accounting');
define('ROLE_CLINIC_STAFF', 'clinic_staff');
define('ROLE_HR_STAFF', 'hr_staff');
define('ROLE_DOCUMENT_STAFF', 'document_staff');
define('ROLE_STUDENT', 'student');

// User statuses
define('USER_STATUS_ACTIVE', 'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_SUSPENDED', 'suspended');

// Student statuses
define('STUDENT_STATUS_ACTIVE', 'active');
define('STUDENT_STATUS_INACTIVE', 'inactive');
define('STUDENT_STATUS_GRADUATED', 'graduated');
define('STUDENT_STATUS_DROPPED', 'dropped');
define('STUDENT_STATUS_ON_LEAVE', 'on_leave');

// Student types
define('STUDENT_TYPE_REGULAR', 'regular');
define('STUDENT_TYPE_IRREGULAR', 'irregular');
define('STUDENT_TYPE_TRANSFEREE', 'transferee');
define('STUDENT_TYPE_SHIFTEE', 'shiftee');

// Enrollment statuses
define('ENROLLMENT_STATUS_PENDING', 'pending');
define('ENROLLMENT_STATUS_FOR_VALIDATION', 'for_validation');
define('ENROLLMENT_STATUS_VALIDATED', 'validated');
define('ENROLLMENT_STATUS_REJECTED', 'rejected');
define('ENROLLMENT_STATUS_ENROLLED', 'enrolled');
define('ENROLLMENT_STATUS_DROPPED', 'dropped');
define('ENROLLMENT_STATUS_WITHDRAWN', 'withdrawn');
define('ENROLLMENT_STATUS_COMPLETED', 'completed');

// Application types
define('APPLICATION_TYPE_NEW', 'new');
define('APPLICATION_TYPE_CONTINUING', 'continuing');
define('APPLICATION_TYPE_RETURNEE', 'returnee');
define('APPLICATION_TYPE_CROSS_ENROLLEE', 'cross_enrollee');

// Academic periods
define('SEMESTER_FIRST', 1);
define('SEMESTER_SECOND', 2);
define('SEMESTER_SUMMER', 3);

// Academic year statuses
define('ACADEMIC_YEAR_ACTIVE', 'active');
define('ACADEMIC_YEAR_COMPLETED', 'completed');
define('ACADEMIC_YEAR_UPCOMING', 'upcoming');

// Subject types
define('SUBJECT_TYPE_MAJOR', 'major');
define('SUBJECT_TYPE_MINOR', 'minor');
define('SUBJECT_TYPE_ELECTIVE', 'elective');
define('SUBJECT_TYPE_GEN_ED', 'general_education');
define('SUBJECT_TYPE_NSTP', 'nstp');
define('SUBJECT_TYPE_PE', 'pe');

// Prerequisite types
define('PREREQUISITE_TYPE_PREREQUISITE', 'prerequisite');
define('PREREQUISITE_TYPE_COREQUISITE', 'corequisite');

// Section statuses
define('SECTION_STATUS_OPEN', 'open');
define('SECTION_STATUS_CLOSED', 'closed');
define('SECTION_STATUS_CANCELLED', 'cancelled');

// Days of the week
define('DAY_MONDAY', 'monday');
define('DAY_TUESDAY', 'tuesday');
define('DAY_WEDNESDAY', 'wednesday');
define('DAY_THURSDAY', 'thursday');
define('DAY_FRIDAY', 'friday');
define('DAY_SATURDAY', 'saturday');
define('DAY_SUNDAY', 'sunday');

// Schedule types
define('SCHEDULE_TYPE_LECTURE', 'lecture');
define('SCHEDULE_TYPE_LABORATORY', 'laboratory');

// Room types
define('ROOM_TYPE_LECTURE', 'lecture');
define('ROOM_TYPE_LABORATORY', 'laboratory');
define('ROOM_TYPE_COMPUTER_LAB', 'computer_lab');
define('ROOM_TYPE_AUDITORIUM', 'auditorium');
define('ROOM_TYPE_GYM', 'gym');

// Room statuses
define('ROOM_STATUS_AVAILABLE', 'available');
define('ROOM_STATUS_MAINTENANCE', 'under_maintenance');
define('ROOM_STATUS_UNAVAILABLE', 'unavailable');

// Employment statuses
define('EMPLOYMENT_STATUS_FULL_TIME', 'full_time');
define('EMPLOYMENT_STATUS_PART_TIME', 'part_time');
define('EMPLOYMENT_STATUS_CONTRACTUAL', 'contractual');

// Faculty statuses
define('FACULTY_STATUS_ACTIVE', 'active');
define('FACULTY_STATUS_INACTIVE', 'inactive');
define('FACULTY_STATUS_ON_LEAVE', 'on_leave');

// Grade statuses
define('GRADE_STATUS_DRAFT', 'draft');
define('GRADE_STATUS_SUBMITTED', 'submitted');
define('GRADE_STATUS_VERIFIED', 'verified');
define('GRADE_STATUS_POSTED', 'posted');

// Grade remarks
define('GRADE_REMARK_PASSED', 'passed');
define('GRADE_REMARK_FAILED', 'failed');
define('GRADE_REMARK_INCOMPLETE', 'incomplete');
define('GRADE_REMARK_DROPPED', 'dropped');
define('GRADE_REMARK_WITHDRAWN', 'withdrawn');

// Completion statuses
define('COMPLETION_STATUS_PASSED', 'passed');
define('COMPLETION_STATUS_FAILED', 'failed');
define('COMPLETION_STATUS_INCOMPLETE', 'incomplete');
define('COMPLETION_STATUS_ONGOING', 'ongoing');

// Academic standing
define('ACADEMIC_STATUS_GOOD_STANDING', 'good_standing');
define('ACADEMIC_STATUS_PROBATION', 'probation');
define('ACADEMIC_STATUS_WARNING', 'warning');
define('ACADEMIC_STATUS_DEAN_LIST', 'dean_list');

// Payment methods
define('PAYMENT_METHOD_CASH', 'cash');
define('PAYMENT_METHOD_CHECK', 'check');
define('PAYMENT_METHOD_CREDIT_CARD', 'credit_card');
define('PAYMENT_METHOD_DEBIT_CARD', 'debit_card');
define('PAYMENT_METHOD_BANK_TRANSFER', 'bank_transfer');
define('PAYMENT_METHOD_GCASH', 'gcash');
define('PAYMENT_METHOD_PAYMAYA', 'paymaya');
define('PAYMENT_METHOD_ONLINE', 'online');

// Payment statuses
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_VERIFIED', 'verified');
define('PAYMENT_STATUS_CANCELLED', 'cancelled');

// Fee categories
define('FEE_CATEGORY_TUITION', 'tuition');
define('FEE_CATEGORY_MISCELLANEOUS', 'miscellaneous');
define('FEE_CATEGORY_LABORATORY', 'laboratory');
define('FEE_CATEGORY_LIBRARY', 'library');
define('FEE_CATEGORY_REGISTRATION', 'registration');
define('FEE_CATEGORY_OTHER', 'other');

// Discount types
define('DISCOUNT_TYPE_PERCENTAGE', 'percentage');
define('DISCOUNT_TYPE_FIXED_AMOUNT', 'fixed_amount');

// Scholarship statuses
define('SCHOLARSHIP_STATUS_ACTIVE', 'active');
define('SCHOLARSHIP_STATUS_EXPIRED', 'expired');
define('SCHOLARSHIP_STATUS_REVOKED', 'revoked');

// Document request statuses
define('DOCUMENT_STATUS_PENDING', 'pending');
define('DOCUMENT_STATUS_PROCESSING', 'processing');
define('DOCUMENT_STATUS_READY', 'ready');
define('DOCUMENT_STATUS_RELEASED', 'released');
define('DOCUMENT_STATUS_CANCELLED', 'cancelled');

// Document types
define('DOCUMENT_TYPE_TOR', 'transcript_of_records');
define('DOCUMENT_TYPE_DIPLOMA', 'diploma');
define('DOCUMENT_TYPE_CERTIFICATE', 'certificate');
define('DOCUMENT_TYPE_GOOD_MORAL', 'good_moral');
define('DOCUMENT_TYPE_REGISTRATION_FORM', 'registration_form');
define('DOCUMENT_TYPE_GRADES', 'grades');
define('DOCUMENT_TYPE_CLEARANCE', 'clearance');

// Workflow statuses
define('WORKFLOW_STATUS_PENDING', 'pending');
define('WORKFLOW_STATUS_IN_PROGRESS', 'in_progress');
define('WORKFLOW_STATUS_COMPLETED', 'completed');
define('WORKFLOW_STATUS_SKIPPED', 'skipped');

// Applicant statuses
define('APPLICANT_STATUS_NEW', 'new');
define('APPLICANT_STATUS_SCREENING', 'screening');
define('APPLICANT_STATUS_INTERVIEW', 'interview');
define('APPLICANT_STATUS_EVALUATION', 'evaluation');
define('APPLICANT_STATUS_HIRED', 'hired');
define('APPLICANT_STATUS_REJECTED', 'rejected');

// Requirement statuses
define('REQUIREMENT_STATUS_PENDING', 'pending');
define('REQUIREMENT_STATUS_SUBMITTED', 'submitted');
define('REQUIREMENT_STATUS_VERIFIED', 'verified');
define('REQUIREMENT_STATUS_REJECTED', 'rejected');

// Interview types
define('INTERVIEW_TYPE_INITIAL', 'initial');
define('INTERVIEW_TYPE_TECHNICAL', 'technical');
define('INTERVIEW_TYPE_FINAL', 'final');
define('INTERVIEW_TYPE_PANEL', 'panel');

// Interview statuses
define('INTERVIEW_STATUS_SCHEDULED', 'scheduled');
define('INTERVIEW_STATUS_COMPLETED', 'completed');
define('INTERVIEW_STATUS_CANCELLED', 'cancelled');
define('INTERVIEW_STATUS_NO_SHOW', 'no_show');

// Evaluation recommendations
define('EVALUATION_HIGHLY_RECOMMENDED', 'highly_recommended');
define('EVALUATION_RECOMMENDED', 'recommended');
define('EVALUATION_CONDITIONAL', 'conditional');
define('EVALUATION_NOT_RECOMMENDED', 'not_recommended');

// Job offer statuses
define('JOB_OFFER_PENDING', 'pending');
define('JOB_OFFER_ACCEPTED', 'accepted');
define('JOB_OFFER_DECLINED', 'declined');
define('JOB_OFFER_EXPIRED', 'expired');

// Clearance types
define('CLEARANCE_TYPE_RESIGNATION', 'resignation');
define('CLEARANCE_TYPE_RETIREMENT', 'retirement');
define('CLEARANCE_TYPE_END_OF_CONTRACT', 'end_of_contract');

// Clearance statuses
define('CLEARANCE_STATUS_PENDING', 'pending');
define('CLEARANCE_STATUS_IN_PROGRESS', 'in_progress');
define('CLEARANCE_STATUS_CLEARED', 'cleared');
define('CLEARANCE_STATUS_WITH_ACCOUNTABILITIES', 'with_accountabilities');

// Medical clearance types
define('MEDICAL_CLEARANCE_ENROLLMENT', 'enrollment');
define('MEDICAL_CLEARANCE_ATHLETIC', 'athletic');
define('MEDICAL_CLEARANCE_INTERNSHIP', 'internship');
define('MEDICAL_CLEARANCE_GRADUATION', 'graduation');
define('MEDICAL_CLEARANCE_GENERAL', 'general');

// Medical fitness statuses
define('MEDICAL_STATUS_FIT', 'fit');
define('MEDICAL_STATUS_UNFIT', 'unfit');
define('MEDICAL_STATUS_WITH_RESTRICTIONS', 'with_restrictions');

// Health incident types
define('INCIDENT_TYPE_INJURY', 'injury');
define('INCIDENT_TYPE_ILLNESS', 'illness');
define('INCIDENT_TYPE_ACCIDENT', 'accident');
define('INCIDENT_TYPE_EMERGENCY', 'emergency');
define('INCIDENT_TYPE_OTHER', 'other');

// Incident severity levels
define('INCIDENT_SEVERITY_MINOR', 'minor');
define('INCIDENT_SEVERITY_MODERATE', 'moderate');
define('INCIDENT_SEVERITY_SERIOUS', 'serious');
define('INCIDENT_SEVERITY_CRITICAL', 'critical');

// Medicine statuses
define('MEDICINE_STATUS_AVAILABLE', 'available');
define('MEDICINE_STATUS_OUT_OF_STOCK', 'out_of_stock');
define('MEDICINE_STATUS_DISCONTINUED', 'discontinued');

// Gender options
define('GENDER_MALE', 'male');
define('GENDER_FEMALE', 'female');
define('GENDER_OTHER', 'other');

// Civil status options
define('CIVIL_STATUS_SINGLE', 'single');
define('CIVIL_STATUS_MARRIED', 'married');
define('CIVIL_STATUS_WIDOWED', 'widowed');
define('CIVIL_STATUS_SEPARATED', 'separated');

// Degree levels
define('DEGREE_UNDERGRADUATE', 'undergraduate');
define('DEGREE_GRADUATE', 'graduate');
define('DEGREE_DOCTORAL', 'doctoral');

// Curriculum statuses
define('CURRICULUM_STATUS_ACTIVE', 'active');
define('CURRICULUM_STATUS_ARCHIVED', 'archived');
define('CURRICULUM_STATUS_DRAFT', 'draft');

// Conflict types
define('CONFLICT_TYPE_ROOM', 'room_conflict');
define('CONFLICT_TYPE_FACULTY', 'faculty_conflict');
define('CONFLICT_TYPE_STUDENT', 'student_conflict');

// Conflict resolution statuses
define('CONFLICT_DETECTED', 'detected');
define('CONFLICT_RESOLVED', 'resolved');
define('CONFLICT_IGNORED', 'ignored');

// Assessment statuses
define('ASSESSMENT_STATUS_DRAFT', 'draft');
define('ASSESSMENT_STATUS_FINALIZED', 'finalized');
define('ASSESSMENT_STATUS_ADJUSTED', 'adjusted');

// Correction statuses
define('CORRECTION_STATUS_PENDING', 'pending');
define('CORRECTION_STATUS_APPROVED', 'approved');
define('CORRECTION_STATUS_REJECTED', 'rejected');

// Component types for grades
define('COMPONENT_TYPE_QUIZ', 'quiz');
define('COMPONENT_TYPE_EXAM', 'exam');
define('COMPONENT_TYPE_PROJECT', 'project');
define('COMPONENT_TYPE_ASSIGNMENT', 'assignment');
define('COMPONENT_TYPE_ATTENDANCE', 'attendance');
define('COMPONENT_TYPE_PARTICIPATION', 'participation');

// HTTP Status Codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_ACCEPTED', 202);
define('HTTP_NO_CONTENT', 204);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_CONFLICT', 409);
define('HTTP_UNPROCESSABLE_ENTITY', 422);
define('HTTP_TOO_MANY_REQUESTS', 429);
define('HTTP_INTERNAL_SERVER_ERROR', 500);
define('HTTP_SERVICE_UNAVAILABLE', 503);

// File types
define('FILE_TYPE_PDF', 'pdf');
define('FILE_TYPE_DOC', 'doc');
define('FILE_TYPE_DOCX', 'docx');
define('FILE_TYPE_XLS', 'xls');
define('FILE_TYPE_XLSX', 'xlsx');
define('FILE_TYPE_CSV', 'csv');
define('FILE_TYPE_JPG', 'jpg');
define('FILE_TYPE_JPEG', 'jpeg');
define('FILE_TYPE_PNG', 'png');
define('FILE_TYPE_GIF', 'gif');

// Notification types
define('NOTIFICATION_SUCCESS', 'success');
define('NOTIFICATION_ERROR', 'error');
define('NOTIFICATION_WARNING', 'warning');
define('NOTIFICATION_INFO', 'info');

// Log levels
define('LOG_LEVEL_DEBUG', 'debug');
define('LOG_LEVEL_INFO', 'info');
define('LOG_LEVEL_WARNING', 'warning');
define('LOG_LEVEL_ERROR', 'error');
define('LOG_LEVEL_CRITICAL', 'critical');

// Year levels
define('YEAR_LEVEL_1', 1);
define('YEAR_LEVEL_2', 2);
define('YEAR_LEVEL_3', 3);
define('YEAR_LEVEL_4', 4);
define('YEAR_LEVEL_5', 5);

// Permissions (module-based)
$permissions_list = [
    // Student permissions
    'student.view',
    'student.create',
    'student.update',
    'student.delete',
    
    // Enrollment permissions
    'enrollment.view',
    'enrollment.create',
    'enrollment.validate',
    'enrollment.approve',
    
    // Curriculum permissions
    'curriculum.view',
    'curriculum.manage',
    
    // Scheduling permissions
    'scheduling.view',
    'scheduling.manage',
    
    // Grades permissions
    'grades.view',
    'grades.encode',
    'grades.verify',
    
    // Payment permissions
    'payment.view',
    'payment.post',
    'payment.verify',
    
    // Document permissions
    'document.request',
    'document.process',
    'document.release',
    
    // HR permissions
    'hr.view',
    'hr.manage',
    
    // Clinic permissions
    'clinic.view',
    'clinic.manage',
    
    // User permissions
    'user.view',
    'user.create',
    'user.manage',
];

return [
    'roles' => [
        ROLE_SUPER_ADMIN,
        ROLE_ADMIN,
        ROLE_REGISTRAR,
        ROLE_FACULTY,
        ROLE_ACCOUNTING,
        ROLE_CLINIC_STAFF,
        ROLE_HR_STAFF,
        ROLE_DOCUMENT_STAFF,
        ROLE_STUDENT,
    ],
    'permissions' => $permissions_list,
    'student_statuses' => [
        STUDENT_STATUS_ACTIVE,
        STUDENT_STATUS_INACTIVE,
        STUDENT_STATUS_GRADUATED,
        STUDENT_STATUS_DROPPED,
        STUDENT_STATUS_ON_LEAVE,
    ],
    'enrollment_statuses' => [
        ENROLLMENT_STATUS_PENDING,
        ENROLLMENT_STATUS_FOR_VALIDATION,
        ENROLLMENT_STATUS_VALIDATED,
        ENROLLMENT_STATUS_REJECTED,
        ENROLLMENT_STATUS_ENROLLED,
    ],
    'payment_methods' => [
        PAYMENT_METHOD_CASH,
        PAYMENT_METHOD_CHECK,
        PAYMENT_METHOD_CREDIT_CARD,
        PAYMENT_METHOD_DEBIT_CARD,
        PAYMENT_METHOD_BANK_TRANSFER,
        PAYMENT_METHOD_GCASH,
        PAYMENT_METHOD_PAYMAYA,
        PAYMENT_METHOD_ONLINE,
    ],
];