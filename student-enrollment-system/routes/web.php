# Routes Configuration

## Web Routes (routes/web.php)

```php
<?php
// ============================================================================
// STUDENT INFORMATION MANAGEMENT ROUTES
// ============================================================================

$router->group('student', function($router) {
    
    // Student Profile Routes
    $router->get('profile', 'StudentProfileController@index');
    $router->get('profile/create', 'StudentProfileController@create');
    $router->post('profile/create', 'StudentProfileController@store');
    $router->get('profile/view/{id}', 'StudentProfileController@view');
    $router->get('profile/edit/{id}', 'StudentProfileController@edit');
    $router->post('profile/update/{id}', 'StudentProfileController@update');
    $router->post('profile/delete/{id}', 'StudentProfileController@delete');
    
    // Personal Information Routes
    $router->get('personal-info/{id}', 'StudentUpdateController@index');
    $router->post('personal-info/update/{id}', 'StudentUpdateController@update');
    
    // Academic Records Routes
    $router->get('academic-records/{id}', 'AcademicRecordsController@index');
    $router->get('academic-records/view/{id}/{year}', 'AcademicRecordsController@view');
    $router->get('academic-records/print/{id}', 'AcademicRecordsController@print');
    
    // Student ID Routes
    $router->get('student-id/generate/{id}', 'StudentIDController@generate');
    $router->get('student-id/view/{id}', 'StudentIDController@view');
    $router->get('student-id/print/{id}', 'StudentIDController@print');
    
    // Status Routes
    $router->get('status', 'StudentStatusController@index');
    $router->post('status/update/{id}', 'StudentStatusController@update');
    
    // Audit Logs
    $router->get('audit/logs', 'StudentAuditController@index');
    $router->get('audit/logs/{id}', 'StudentAuditController@view');
    
})->middleware(['AuthMiddleware', 'RoleMiddleware']);

// ============================================================================
// ENROLLMENT & REGISTRATION ROUTES
// ============================================================================

$router->group('enrollment', function($router) {
    
    // Enrollment Application Routes
    $router->get('application', 'EnrollmentApplicationController@index');
    $router->get('application/create', 'EnrollmentApplicationController@create');
    $router->post('application/submit', 'EnrollmentApplicationController@submit');
    $router->get('application/view/{id}', 'EnrollmentApplicationController@view');
    
    // Pre-Enrollment Routes
    $router->get('pre-enrollment/start', 'PreEnrollmentController@start');
    $router->get('pre-enrollment/subjects', 'PreEnrollmentController@selectSubjects');
    $router->post('pre-enrollment/save', 'PreEnrollmentController@save');
    $router->get('pre-enrollment/schedule/{id}', 'PreEnrollmentController@viewSchedule');
    
    // Validation Routes
    $router->get('validation/pending', 'EnrollmentValidationController@pending');
    $router->post('validation/approve/{id}', 'EnrollmentValidationController@approve');
    $router->post('validation/reject/{id}', 'EnrollmentValidationController@reject');
    
    // Status Monitoring Routes
    $router->get('status/tracking', 'EnrollmentStatusController@tracking');
    $router->get('status/details/{id}', 'EnrollmentStatusController@details');
    
    // Reports
    $router->get('reports/summary', 'EnrollmentReportController@summary');
    $router->get('reports/statistics', 'EnrollmentReportController@statistics');
    $router->get('reports/export', 'EnrollmentReportController@export');
    
})->middleware(['AuthMiddleware']);

// ============================================================================
// CURRICULUM & COURSE MANAGEMENT ROUTES
// ============================================================================

$router->group('curriculum', function($router) {
    
    // Curriculum Setup Routes
    $router->get('setup', 'CurriculumSetupController@index');
    $router->get('setup/create', 'CurriculumSetupController@create');
    $router->post('setup/store', 'CurriculumSetupController@store');
    $router->get('setup/edit/{id}', 'CurriculumSetupController@edit');
    $router->post('setup/update/{id}', 'CurriculumSetupController@update');
    
    // Course Catalog Routes
    $router->get('catalog', 'CourseCatalogController@index');
    $router->get('catalog/create', 'CourseCatalogController@create');
    $router->post('catalog/store', 'CourseCatalogController@store');
    $router->get('catalog/edit/{id}', 'CourseCatalogController@edit');
    $router->post('catalog/update/{id}', 'CourseCatalogController@update');
    
    // Prerequisite Configuration Routes
    $router->get('prerequisites/configure', 'PrerequisiteController@configure');
    $router->post('prerequisites/save', 'PrerequisiteController@save');
    $router->get('prerequisites/view/{id}', 'PrerequisiteController@view');
    
    // Course Scheduling Routes
    $router->get('scheduling/plan', 'CourseSchedulingController@plan');
    $router->post('scheduling/save', 'CourseSchedulingController@save');
    
    // Curriculum Revision Routes
    $router->get('revision/create', 'CurriculumRevisionController@create');
    $router->get('revision/compare/{id}', 'CurriculumRevisionController@compare');
    $router->get('revision/history', 'CurriculumRevisionController@history');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// ============================================================================
// CLASS SCHEDULING & SECTION MANAGEMENT ROUTES
// ============================================================================

$router->group('scheduling', function($router) {
    
    // Section Management Routes
    $router->get('sections', 'SectionController@index');
    $router->get('sections/create', 'SectionController@create');
    $router->post('sections/store', 'SectionController@store');
    $router->get('sections/edit/{id}', 'SectionController@edit');
    $router->post('sections/update/{id}', 'SectionController@update');
    
    // Timetable Routes
    $router->get('timetable/generate', 'TimetableController@generate');
    $router->post('timetable/create', 'TimetableController@create');
    $router->get('timetable/view', 'TimetableController@view');
    $router->get('timetable/print', 'TimetableController@print');
    
    // Room Assignment Routes
    $router->get('rooms/assignment', 'RoomAssignmentController@index');
    $router->post('rooms/assign', 'RoomAssignmentController@assign');
    $router->get('rooms/availability', 'RoomAssignmentController@availability');
    $router->get('rooms/manage', 'RoomAssignmentController@manage');
    
    // Teacher Loading Routes
    $router->get('teacher-loading', 'TeacherLoadingController@index');
    $router->post('teacher-loading/assign', 'TeacherLoadingController@assign');
    $router->get('teacher-loading/view/{id}', 'TeacherLoadingController@view');
    $router->get('teacher-loading/report', 'TeacherLoadingController@report');
    
    // Conflict Detection Routes
    $router->get('conflicts/detect', 'ConflictDetectionController@detect');
    $router->get('conflicts/resolve/{id}', 'ConflictDetectionController@resolve');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// ============================================================================
// GRADES & ASSESSMENT MANAGEMENT ROUTES
// ============================================================================

$router->group('grades', function($router) {
    
    // Grade Encoding Routes
    $router->get('encoding', 'GradeEncodingController@index');
    $router->get('encoding/input/{section_id}', 'GradeEncodingController@input');
    $router->post('encoding/save', 'GradeEncodingController@save');
    $router->get('encoding/batch', 'GradeEncodingController@batchInput');
    
    // Grade Verification Routes
    $router->get('verification/pending', 'GradeVerificationController@pending');
    $router->post('verification/approve/{id}', 'GradeVerificationController@approve');
    $router->post('verification/reject/{id}', 'GradeVerificationController@reject');
    
    // Student Grade View Routes
    $router->get('view/current', 'StudentGradeViewController@current');
    $router->get('view/history', 'StudentGradeViewController@history');
    $router->get('view/print/{id}', 'StudentGradeViewController@print');
    
    // Grade Correction Routes
    $router->get('correction/request', 'GradeCorrectionController@request');
    $router->post('correction/submit', 'GradeCorrectionController@submit');
    $router->get('correction/approve/{id}', 'GradeCorrectionController@approve');
    $router->get('correction/history', 'GradeCorrectionController@history');
    
    // Grade Reports Routes
    $router->get('reports/class-sheet/{section_id}', 'GradeReportController@classSheet');
    $router->get('reports/summary', 'GradeReportController@summary');
    $router->get('reports/analytics', 'GradeReportController@analytics');
    
})->middleware(['AuthMiddleware']);

// ============================================================================
// PAYMENT & ACCOUNTING ROUTES
// ============================================================================

$router->group('payment', function($router) {
    
    // Fee Assessment Routes
    $router->get('assessment', 'FeeAssessmentController@index');
    $router->post('assessment/calculate', 'FeeAssessmentController@calculate');
    $router->get('assessment/view/{student_id}', 'FeeAssessmentController@view');
    $router->post('assessment/adjust', 'FeeAssessmentController@adjust');
    
    // Payment Posting Routes
    $router->get('posting', 'PaymentPostingController@index');
    $router->post('posting/input', 'PaymentPostingController@input');
    $router->post('posting/validate', 'PaymentPostingController@validate');
    $router->get('posting/receipt/{id}', 'PaymentPostingController@receipt');
    
    // Billing Routes
    $router->get('billing/statement/{student_id}', 'BillingController@statement');
    $router->get('billing/history/{student_id}', 'BillingController@history');
    $router->get('billing/print/{id}', 'BillingController@print');
    
    // Scholarship Routes
    $router->get('scholarship/apply', 'ScholarshipController@apply');
    $router->post('scholarship/process', 'ScholarshipController@process');
    $router->get('scholarship/manage', 'ScholarshipController@manage');
    
    // Transaction Logs
    $router->get('transactions/log', 'TransactionLogController@index');
    $router->get('transactions/report', 'TransactionLogController@report');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// ============================================================================
// DOCUMENT & CREDENTIALS ROUTES
// ============================================================================

$router->group('document', function($router) {
    
    // Document Request Routes
    $router->get('request/create', 'DocumentRequestController@create');
    $router->post('request/submit', 'DocumentRequestController@submit');
    $router->get('request/list', 'DocumentRequestController@list');
    $router->get('request/track/{id}', 'DocumentRequestController@track');
    
    // Document Processing Routes
    $router->get('processing/workflow', 'DocumentProcessingController@workflow');
    $router->post('processing/approve/{id}', 'DocumentProcessingController@approve');
    $router->get('processing/queue', 'DocumentProcessingController@queue');
    
    // Document Generation Routes
    $router->post('generation/generate/{id}', 'DocumentGenerationController@generate');
    $router->get('generation/preview/{id}', 'DocumentGenerationController@preview');
    $router->get('generation/print/{id}', 'DocumentGenerationController@print');
    
    // Document Release Routes
    $router->get('release/ready', 'DocumentReleaseController@ready');
    $router->post('release/claim/{id}', 'DocumentReleaseController@claim');
    $router->get('release/tracking', 'DocumentReleaseController@tracking');
    
    // Archive Routes
    $router->get('archive', 'ArchiveController@index');
    $router->get('archive/search', 'ArchiveController@search');
    $router->get('archive/view/{id}', 'ArchiveController@view');
    
})->middleware(['AuthMiddleware']);

// ============================================================================
// HUMAN RESOURCE MANAGEMENT ROUTES
// ============================================================================

$router->group('hr', function($router) {
    
    // Pre-Employment Routes
    $router->get('pre-employment/applicants', 'PreEmploymentController@applicants');
    $router->get('pre-employment/profile/{id}', 'PreEmploymentController@profile');
    $router->post('pre-employment/requirements/{id}', 'PreEmploymentController@requirements');
    $router->post('pre-employment/screening/{id}', 'PreEmploymentController@screening');
    
    // Recruitment Routes
    $router->get('recruitment/interviews', 'RecruitmentController@interviews');
    $router->post('recruitment/schedule-interview', 'RecruitmentController@scheduleInterview');
    $router->post('recruitment/evaluate/{id}', 'RecruitmentController@evaluate');
    $router->post('recruitment/job-offer/{id}', 'RecruitmentController@jobOffer');
    
    // Employment Records Routes
    $router->get('employment/records', 'EmploymentRecordsController@index');
    $router->get('employment/view/{id}', 'EmploymentRecordsController@view');
    $router->post('employment/onboarding/{id}', 'EmploymentRecordsController@onboarding');
    $router->post('employment/update-status/{id}', 'EmploymentRecordsController@updateStatus');
    
    // Performance Routes
    $router->get('performance/evaluations', 'PerformanceController@index');
    $router->post('performance/evaluate/{id}', 'PerformanceController@evaluate');
    $router->get('performance/workload/{id}', 'PerformanceController@workload');
    $router->post('performance/disciplinary/{id}', 'PerformanceController@disciplinary');
    
    // Post-Employment Routes
    $router->post('post-employment/resignation/{id}', 'PostEmploymentController@resignation');
    $router->get('post-employment/clearance/{id}', 'PostEmploymentController@clearance');
    $router->get('post-employment/service-record/{id}', 'PostEmploymentController@serviceRecord');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// ============================================================================
// CLINIC & MEDICAL SERVICES ROUTES
// ============================================================================

$router->group('clinic', function($router) {
    
    // Medical Records Routes
    $router->get('medical-records', 'MedicalRecordsController@index');
    $router->get('medical-records/create', 'MedicalRecordsController@create');
    $router->post('medical-records/store', 'MedicalRecordsController@store');
    $router->get('medical-records/view/{id}', 'MedicalRecordsController@view');
    $router->post('medical-records/update/{id}', 'MedicalRecordsController@update');
    
    // Consultation Routes
    $router->get('consultation/register', 'ConsultationController@register');
    $router->post('consultation/save', 'ConsultationController@save');
    $router->get('consultation/treatment/{id}', 'ConsultationController@treatment');
    $router->get('consultation/history/{student_id}', 'ConsultationController@history');
    
    // Medicine Inventory Routes
    $router->get('inventory/medicines', 'MedicineInventoryController@index');
    $router->post('inventory/dispense', 'MedicineInventoryController@dispense');
    $router->get('inventory/stock-management', 'MedicineInventoryController@stockManagement');
    
    // Medical Clearance Routes
    $router->post('clearance/issue/{student_id}', 'MedicalClearanceController@issue');
    $router->get('clearance/verify/{id}', 'MedicalClearanceController@verify');
    
    // Incident Report Routes
    $router->get('incidents/report', 'IncidentReportController@report');
    $router->post('incidents/submit', 'IncidentReportController@submit');
    $router->get('incidents/list', 'IncidentReportController@list');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// ============================================================================
// USER MANAGEMENT ROUTES
// ============================================================================

$router->group('user', function($router) {
    
    // User Account Routes
    $router->get('accounts', 'UserAccountController@index');
    $router->get('accounts/create', 'UserAccountController@create');
    $router->post('accounts/store', 'UserAccountController@store');
    $router->get('accounts/edit/{id}', 'UserAccountController@edit');
    $router->post('accounts/update/{id}', 'UserAccountController@update');
    $router->post('accounts/delete/{id}', 'UserAccountController@delete');
    
    // Role & Permission Routes
    $router->get('roles/manage', 'RolePermissionController@manageRoles');
    $router->post('roles/create', 'RolePermissionController@createRole');
    $router->get('roles/permissions/{id}', 'RolePermissionController@permissions');
    $router->post('roles/assign-permission', 'RolePermissionController@assignPermission');
    $router->post('users/assign-role/{user_id}', 'RolePermissionController@assignRole');
    
    // Audit Trail Routes
    $router->get('audit/activity-logs', 'AuditTrailController@activityLogs');
    $router->get('audit/search', 'AuditTrailController@search');
    $router->get('audit/reports', 'AuditTrailController@reports');
    
})->middleware(['AuthMiddleware', 'PermissionMiddleware']);

// Authentication Routes (No middleware)
$router->get('login', 'AuthenticationController@showLogin');
$router->post('login', 'AuthenticationController@login');
$router->get('logout', 'AuthenticationController@logout');
$router->get('forgot-password', 'PasswordRecoveryController@showForgotPassword');
$router->post('forgot-password', 'PasswordRecoveryController@sendResetLink');
$router->get('reset-password/{token}', 'PasswordRecoveryController@showResetPassword');
$router->post('reset-password', 'PasswordRecoveryController@resetPassword');

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================

$router->get('dashboard', 'DashboardController@index')->middleware(['AuthMiddleware']);
$router->get('dashboard/admin', 'DashboardController@admin')->middleware(['AuthMiddleware', 'RoleMiddleware']);
$router->get('dashboard/student', 'DashboardController@student')->middleware(['AuthMiddleware']);
$router->get('dashboard/faculty', 'DashboardController@faculty')->middleware(['AuthMiddleware']);
$router->get('dashboard/registrar', 'DashboardController@registrar')->middleware(['AuthMiddleware', 'RoleMiddleware']);

// ============================================================================
// HOME & ERROR ROUTES
// ============================================================================

$router->get('/', 'HomeController@index');
$router->get('home', 'HomeController@index');