# Student and Enrollment Management System - MVC Project Structure

## Project Root Directory Structure

```
student-enrollment-system/
│
├── config/
│   ├── database.php              # Database configuration
│   ├── app.php                   # Application configuration
│   ├── security.php              # Security settings
│   ├── mail.php                  # Email configuration
│   └── constants.php             # System-wide constants
│
├── core/
│   ├── Application.php           # Main application bootstrap
│   ├── Router.php                # URL routing handler
│   ├── Controller.php            # Base controller class
│   ├── Model.php                 # Base model class
│   ├── View.php                  # View rendering engine
│   ├── Database.php              # Database connection handler
│   ├── Session.php               # Session management
│   ├── Request.php               # HTTP request handler
│   ├── Response.php              # HTTP response handler
│   ├── Validator.php             # Input validation
│   ├── Middleware.php            # Base middleware class
│   └── Helper.php                # Helper functions
│
├── app/
│   ├── controllers/
│   │   │
│   │   ├── student/
│   │   │   ├── StudentProfileController.php
│   │   │   ├── StudentUpdateController.php
│   │   │   ├── AcademicRecordsController.php
│   │   │   ├── StudentIDController.php
│   │   │   ├── StudentStatusController.php
│   │   │   └── StudentAuditController.php
│   │   │
│   │   ├── enrollment/
│   │   │   ├── EnrollmentApplicationController.php
│   │   │   ├── PreEnrollmentController.php
│   │   │   ├── EnrollmentValidationController.php
│   │   │   ├── EnrollmentStatusController.php
│   │   │   ├── EnrollmentReportController.php
│   │   │   └── EnrollmentAuditController.php
│   │   │
│   │   ├── curriculum/
│   │   │   ├── CurriculumSetupController.php
│   │   │   ├── CourseCatalogController.php
│   │   │   ├── PrerequisiteController.php
│   │   │   ├── CourseSchedulingController.php
│   │   │   ├── CurriculumRevisionController.php
│   │   │   └── CurriculumAuditController.php
│   │   │
│   │   ├── scheduling/
│   │   │   ├── SectionController.php
│   │   │   ├── TimetableController.php
│   │   │   ├── RoomAssignmentController.php
│   │   │   ├── TeacherLoadingController.php
│   │   │   ├── ConflictDetectionController.php
│   │   │   └── SchedulingAuditController.php
│   │   │
│   │   ├── grades/
│   │   │   ├── GradeEncodingController.php
│   │   │   ├── GradeVerificationController.php
│   │   │   ├── StudentGradeViewController.php
│   │   │   ├── GradeCorrectionController.php
│   │   │   ├── GradeReportController.php
│   │   │   └── GradesAuditController.php
│   │   │
│   │   ├── payment/
│   │   │   ├── FeeAssessmentController.php
│   │   │   ├── PaymentPostingController.php
│   │   │   ├── BillingController.php
│   │   │   ├── ScholarshipController.php
│   │   │   ├── TransactionLogController.php
│   │   │   └── PaymentAuditController.php
│   │   │
│   │   ├── document/
│   │   │   ├── DocumentRequestController.php
│   │   │   ├── DocumentProcessingController.php
│   │   │   ├── DocumentGenerationController.php
│   │   │   ├── DocumentReleaseController.php
│   │   │   ├── ArchiveController.php
│   │   │   └── DocumentAuditController.php
│   │   │
│   │   ├── hr/
│   │   │   ├── PreEmploymentController.php
│   │   │   ├── RecruitmentController.php
│   │   │   ├── EmploymentRecordsController.php
│   │   │   ├── PerformanceController.php
│   │   │   ├── PostEmploymentController.php
│   │   │   └── HRAuditController.php
│   │   │
│   │   ├── clinic/
│   │   │   ├── MedicalRecordsController.php
│   │   │   ├── ConsultationController.php
│   │   │   ├── MedicineInventoryController.php
│   │   │   ├── MedicalClearanceController.php
│   │   │   └── IncidentReportController.php
│   │   │
│   │   ├── user/
│   │   │   ├── UserAccountController.php
│   │   │   ├── RolePermissionController.php
│   │   │   ├── AuthenticationController.php
│   │   │   ├── AuditTrailController.php
│   │   │   └── PasswordRecoveryController.php
│   │   │
│   │   └── DashboardController.php
│   │
│   ├── models/
│   │   │
│   │   ├── student/
│   │   │   ├── Student.php
│   │   │   ├── StudentProfile.php
│   │   │   ├── StudentPersonalInfo.php
│   │   │   ├── AcademicRecord.php
│   │   │   ├── StudentStatus.php
│   │   │   └── StudentActivityLog.php
│   │   │
│   │   ├── enrollment/
│   │   │   ├── Enrollment.php
│   │   │   ├── EnrollmentApplication.php
│   │   │   ├── PreEnrollment.php
│   │   │   ├── EnrollmentValidation.php
│   │   │   ├── EnrollmentStatus.php
│   │   │   └── EnrollmentLog.php
│   │   │
│   │   ├── curriculum/
│   │   │   ├── Curriculum.php
│   │   │   ├── Course.php
│   │   │   ├── Subject.php
│   │   │   ├── Prerequisite.php
│   │   │   ├── Corequisite.php
│   │   │   ├── CurriculumRevision.php
│   │   │   └── CurriculumLog.php
│   │   │
│   │   ├── scheduling/
│   │   │   ├── Section.php
│   │   │   ├── ClassSchedule.php
│   │   │   ├── Timetable.php
│   │   │   ├── Room.php
│   │   │   ├── RoomAssignment.php
│   │   │   ├── TeacherLoad.php
│   │   │   ├── ScheduleConflict.php
│   │   │   └── SchedulingLog.php
│   │   │
│   │   ├── grades/
│   │   │   ├── Grade.php
│   │   │   ├── GradeEntry.php
│   │   │   ├── GradeVerification.php
│   │   │   ├── GradeCorrection.php
│   │   │   ├── GradeReport.php
│   │   │   └── GradeLog.php
│   │   │
│   │   ├── payment/
│   │   │   ├── Payment.php
│   │   │   ├── FeeAssessment.php
│   │   │   ├── PaymentTransaction.php
│   │   │   ├── Billing.php
│   │   │   ├── StatementOfAccount.php
│   │   │   ├── Scholarship.php
│   │   │   ├── Discount.php
│   │   │   └── PaymentLog.php
│   │   │
│   │   ├── document/
│   │   │   ├── Document.php
│   │   │   ├── DocumentRequest.php
│   │   │   ├── DocumentType.php
│   │   │   ├── DocumentWorkflow.php
│   │   │   ├── DocumentRelease.php
│   │   │   ├── ArchivedDocument.php
│   │   │   └── DocumentLog.php
│   │   │
│   │   ├── hr/
│   │   │   ├── Employee.php
│   │   │   ├── Applicant.php
│   │   │   ├── PreEmployment.php
│   │   │   ├── Recruitment.php
│   │   │   ├── EmploymentRecord.php
│   │   │   ├── Performance.php
│   │   │   ├── ServiceRecord.php
│   │   │   ├── Clearance.php
│   │   │   └── HRLog.php
│   │   │
│   │   ├── clinic/
│   │   │   ├── MedicalRecord.php
│   │   │   ├── Consultation.php
│   │   │   ├── Treatment.php
│   │   │   ├── Medicine.php
│   │   │   ├── MedicineDispensing.php
│   │   │   ├── MedicalClearance.php
│   │   │   └── HealthIncident.php
│   │   │
│   │   └── user/
│   │       ├── User.php
│   │       ├── Role.php
│   │       ├── Permission.php
│   │       ├── UserRole.php
│   │       ├── RolePermission.php
│   │       ├── UserSession.php
│   │       ├── ActivityLog.php
│   │       └── PasswordReset.php
│   │
│   ├── views/
│   │   │
│   │   ├── layouts/
│   │   │   ├── main.php              # Main layout template
│   │   │   ├── admin.php             # Admin layout
│   │   │   ├── student.php           # Student portal layout
│   │   │   ├── faculty.php           # Faculty portal layout
│   │   │   └── guest.php             # Public/guest layout
│   │   │
│   │   ├── partials/
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navigation.php
│   │   │   ├── sidebar.php
│   │   │   └── alerts.php
│   │   │
│   │   ├── student/
│   │   │   ├── profile/
│   │   │   │   ├── register.php
│   │   │   │   ├── view.php
│   │   │   │   └── edit.php
│   │   │   ├── personal-info/
│   │   │   │   ├── update.php
│   │   │   │   └── view.php
│   │   │   ├── academic-records/
│   │   │   │   ├── index.php
│   │   │   │   ├── view.php
│   │   │   │   └── print.php
│   │   │   ├── student-id/
│   │   │   │   ├── generate.php
│   │   │   │   └── view.php
│   │   │   ├── status/
│   │   │   │   ├── index.php
│   │   │   │   └── update.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── enrollment/
│   │   │   ├── application/
│   │   │   │   ├── create.php
│   │   │   │   ├── view.php
│   │   │   │   └── list.php
│   │   │   ├── pre-enrollment/
│   │   │   │   ├── subject-selection.php
│   │   │   │   └── schedule-view.php
│   │   │   ├── validation/
│   │   │   │   ├── pending.php
│   │   │   │   ├── approve.php
│   │   │   │   └── reject.php
│   │   │   ├── status/
│   │   │   │   ├── tracking.php
│   │   │   │   └── details.php
│   │   │   ├── reports/
│   │   │   │   ├── summary.php
│   │   │   │   └── statistics.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── curriculum/
│   │   │   ├── setup/
│   │   │   │   ├── create.php
│   │   │   │   ├── edit.php
│   │   │   │   └── list.php
│   │   │   ├── course-catalog/
│   │   │   │   ├── index.php
│   │   │   │   ├── create.php
│   │   │   │   └── edit.php
│   │   │   ├── prerequisites/
│   │   │   │   ├── configure.php
│   │   │   │   └── view.php
│   │   │   ├── scheduling/
│   │   │   │   ├── term-schedule.php
│   │   │   │   └── plan.php
│   │   │   ├── revision/
│   │   │   │   ├── create.php
│   │   │   │   ├── compare.php
│   │   │   │   └── history.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── scheduling/
│   │   │   ├── sections/
│   │   │   │   ├── create.php
│   │   │   │   ├── edit.php
│   │   │   │   └── list.php
│   │   │   ├── timetable/
│   │   │   │   ├── generate.php
│   │   │   │   ├── view.php
│   │   │   │   └── print.php
│   │   │   ├── rooms/
│   │   │   │   ├── assignment.php
│   │   │   │   ├── availability.php
│   │   │   │   └── manage.php
│   │   │   ├── teacher-loading/
│   │   │   │   ├── assign.php
│   │   │   │   ├── view.php
│   │   │   │   └── report.php
│   │   │   ├── conflicts/
│   │   │   │   ├── detection.php
│   │   │   │   └── resolution.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── grades/
│   │   │   ├── encoding/
│   │   │   │   ├── input.php
│   │   │   │   ├── batch-input.php
│   │   │   │   └── preview.php
│   │   │   ├── verification/
│   │   │   │   ├── pending.php
│   │   │   │   ├── approve.php
│   │   │   │   └── reject.php
│   │   │   ├── student-view/
│   │   │   │   ├── current.php
│   │   │   │   ├── history.php
│   │   │   │   └── print.php
│   │   │   ├── correction/
│   │   │   │   ├── request.php
│   │   │   │   ├── approve.php
│   │   │   │   └── history.php
│   │   │   ├── reports/
│   │   │   │   ├── class-sheet.php
│   │   │   │   ├── summary.php
│   │   │   │   └── analytics.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── payment/
│   │   │   ├── assessment/
│   │   │   │   ├── calculate.php
│   │   │   │   ├── view.php
│   │   │   │   └── adjust.php
│   │   │   ├── posting/
│   │   │   │   ├── input.php
│   │   │   │   ├── validate.php
│   │   │   │   └── receipt.php
│   │   │   ├── billing/
│   │   │   │   ├── statement.php
│   │   │   │   ├── history.php
│   │   │   │   └── print.php
│   │   │   ├── scholarship/
│   │   │   │   ├── apply.php
│   │   │   │   ├── process.php
│   │   │   │   └── manage.php
│   │   │   ├── transactions/
│   │   │   │   ├── log.php
│   │   │   │   └── report.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── document/
│   │   │   ├── request/
│   │   │   │   ├── create.php
│   │   │   │   ├── list.php
│   │   │   │   └── track.php
│   │   │   ├── processing/
│   │   │   │   ├── workflow.php
│   │   │   │   ├── approve.php
│   │   │   │   └── queue.php
│   │   │   ├── generation/
│   │   │   │   ├── generate.php
│   │   │   │   ├── preview.php
│   │   │   │   └── print.php
│   │   │   ├── release/
│   │   │   │   ├── ready.php
│   │   │   │   ├── claim.php
│   │   │   │   └── tracking.php
│   │   │   ├── archive/
│   │   │   │   ├── index.php
│   │   │   │   ├── search.php
│   │   │   │   └── view.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── hr/
│   │   │   ├── pre-employment/
│   │   │   │   ├── applicant-profile.php
│   │   │   │   ├── requirements.php
│   │   │   │   └── screening.php
│   │   │   ├── recruitment/
│   │   │   │   ├── interview.php
│   │   │   │   ├── evaluation.php
│   │   │   │   └── job-offer.php
│   │   │   ├── employment/
│   │   │   │   ├── records.php
│   │   │   │   ├── onboarding.php
│   │   │   │   └── status.php
│   │   │   ├── performance/
│   │   │   │   ├── evaluation.php
│   │   │   │   ├── workload.php
│   │   │   │   └── disciplinary.php
│   │   │   ├── post-employment/
│   │   │   │   ├── resignation.php
│   │   │   │   ├── clearance.php
│   │   │   │   └── service-record.php
│   │   │   └── audit/
│   │   │       └── logs.php
│   │   │
│   │   ├── clinic/
│   │   │   ├── medical-records/
│   │   │   │   ├── create.php
│   │   │   │   ├── view.php
│   │   │   │   └── update.php
│   │   │   ├── consultation/
│   │   │   │   ├── register.php
│   │   │   │   ├── treatment.php
│   │   │   │   └── history.php
│   │   │   ├── inventory/
│   │   │   │   ├── medicine-list.php
│   │   │   │   ├── dispense.php
│   │   │   │   └── stock-management.php
│   │   │   ├── clearance/
│   │   │   │   ├── issue.php
│   │   │   │   └── verify.php
│   │   │   └── incidents/
│   │   │       ├── report.php
│   │   │       └── list.php
│   │   │
│   │   ├── user/
│   │   │   ├── accounts/
│   │   │   │   ├── create.php
│   │   │   │   ├── edit.php
│   │   │   │   └── list.php
│   │   │   ├── roles/
│   │   │   │   ├── manage.php
│   │   │   │   ├── permissions.php
│   │   │   │   └── assign.php
│   │   │   ├── authentication/
│   │   │   │   ├── login.php
│   │   │   │   ├── logout.php
│   │   │   │   └── mfa.php
│   │   │   ├── audit/
│   │   │   │   ├── activity-logs.php
│   │   │   │   ├── search.php
│   │   │   │   └── reports.php
│   │   │   └── recovery/
│   │   │       ├── forgot-password.php
│   │   │       ├── reset-password.php
│   │   │       └── account-recovery.php
│   │   │
│   │   ├── dashboard/
│   │   │   ├── admin.php
│   │   │   ├── student.php
│   │   │   ├── faculty.php
│   │   │   ├── registrar.php
│   │   │   └── accounting.php
│   │   │
│   │   └── errors/
│   │       ├── 404.php
│   │       ├── 403.php
│   │       ├── 500.php
│   │       └── maintenance.php
│   │
│   └── middlewares/
│       ├── AuthMiddleware.php
│       ├── RoleMiddleware.php
│       ├── PermissionMiddleware.php
│       ├── ValidationMiddleware.php
│       ├── CsrfMiddleware.php
│       ├── RateLimitMiddleware.php
│       └── LoggingMiddleware.php
│
├── public/
│   ├── index.php                 # Application entry point
│   ├── .htaccess                 # Apache rewrite rules
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css
│   │   │   ├── admin.css
│   │   │   ├── student.css
│   │   │   └── components/
│   │   │       ├── forms.css
│   │   │       ├── tables.css
│   │   │       ├── cards.css
│   │   │       └── modals.css
│   │   │
│   │   ├── js/
│   │   │   ├── main.js
│   │   │   ├── app.js
│   │   │   └── modules/
│   │   │       ├── student.js
│   │   │       ├── enrollment.js
│   │   │       ├── curriculum.js
│   │   │       ├── scheduling.js
│   │   │       ├── grades.js
│   │   │       ├── payment.js
│   │   │       ├── document.js
│   │   │       ├── hr.js
│   │   │       ├── clinic.js
│   │   │       └── user.js
│   │   │
│   │   ├── images/
│   │   │   ├── logo.png
│   │   │   ├── icons/
│   │   │   └── backgrounds/
│   │   │
│   │   └── uploads/
│   │       ├── student-photos/
│   │       ├── documents/
│   │       ├── hr-files/
│   │       └── temp/
│   │
│   └── downloads/
│       ├── reports/
│       ├── documents/
│       └── exports/
│
├── storage/
│   ├── logs/
│   │   ├── app.log
│   │   ├── error.log
│   │   ├── access.log
│   │   └── audit.log
│   │
│   ├── cache/
│   │   ├── views/
│   │   └── data/
│   │
│   ├── sessions/
│   │
│   └── temp/
│
├── database/
│   ├── migrations/
│   │   ├── 001_create_users_table.php
│   │   ├── 002_create_roles_table.php
│   │   ├── 003_create_students_table.php
│   │   ├── 004_create_enrollment_table.php
│   │   ├── 005_create_curriculum_table.php
│   │   ├── 006_create_scheduling_table.php
│   │   ├── 007_create_grades_table.php
│   │   ├── 008_create_payment_table.php
│   │   ├── 009_create_document_table.php
│   │   ├── 010_create_hr_table.php
│   │   └── 011_create_clinic_table.php
│   │
│   ├── seeds/
│   │   ├── UserSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   └── DemoDataSeeder.php
│   │
│   └── schema.sql              # Complete database schema
│
├── libraries/
│   ├── PDF/
│   │   ├── PDFGenerator.php
│   │   └── templates/
│   │
│   ├── Email/
│   │   ├── Mailer.php
│   │   └── templates/
│   │
│   ├── Barcode/
│   │   └── BarcodeGenerator.php
│   │
│   ├── QRCode/
│   │   └── QRCodeGenerator.php
│   │
│   ├── Excel/
│   │   ├── ExcelReader.php
│   │   └── ExcelWriter.php
│   │
│   └── FileUpload/
│       └── FileHandler.php
│
├── tests/
│   ├── unit/
│   │   ├── models/
│   │   ├── controllers/
│   │   └── helpers/
│   │
│   └── integration/
│       ├── student/
│       ├── enrollment/
│       ├── curriculum/
│       ├── scheduling/
│       ├── grades/
│       ├── payment/
│       ├── document/
│       ├── hr/
│       ├── clinic/
│       └── user/
│
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── admin.php               # Admin routes
│
├── .env                        # Environment variables
├── .env.example                # Environment template
├── .gitignore
├── composer.json               # PHP dependencies
├── README.md
└── LICENSE
```

## Detailed Module Breakdown

### 1. Student Information Management Sub-system

**Controllers:**

- `StudentProfileController.php` - Handles profile registration and viewing
- `StudentUpdateController.php` - Manages personal information updates
- `AcademicRecordsController.php` - Displays and manages academic history
- `StudentIDController.php` - Generates and manages student IDs
- `StudentStatusController.php` - Tracks student status changes
- `StudentAuditController.php` - Manages activity logs

**Models:**

- `Student.php` - Core student entity
- `StudentProfile.php` - Profile information
- `StudentPersonalInfo.php` - Personal details
- `AcademicRecord.php` - Academic history
- `StudentStatus.php` - Status tracking
- `StudentActivityLog.php` - Audit trail

**Views:**

```
views/student/
├── profile/        (register, view, edit)
├── personal-info/  (update, view)
├── academic-records/ (index, view, print)
├── student-id/     (generate, view)
├── status/         (index, update)
└── audit/          (logs)
```

### 2. Enrollment & Registration Sub-system

**Controllers:**

- `EnrollmentApplicationController.php` - Processes enrollment applications
- `PreEnrollmentController.php` - Handles subject pre-selection
- `EnrollmentValidationController.php` - Validates and approves enrollments
- `EnrollmentStatusController.php` - Tracks enrollment progress
- `EnrollmentReportController.php` - Generates enrollment reports
- `EnrollmentAuditController.php` - Manages enrollment logs

**Models:**

- `Enrollment.php` - Core enrollment entity
- `EnrollmentApplication.php` - Application records
- `PreEnrollment.php` - Pre-enrollment data
- `EnrollmentValidation.php` - Validation records
- `EnrollmentStatus.php` - Status tracking
- `EnrollmentLog.php` - Audit trail

**Views:**

```
views/enrollment/
├── application/     (create, view, list)
├── pre-enrollment/  (subject-selection, schedule-view)
├── validation/      (pending, approve, reject)
├── status/          (tracking, details)
├── reports/         (summary, statistics)
└── audit/           (logs)
```

### 3. Curriculum & Course Management Sub-system

**Controllers:**

- `CurriculumSetupController.php` - Creates and manages curricula
- `CourseCatalogController.php` - Manages course/subject catalog
- `PrerequisiteController.php` - Configures prerequisites and corequisites
- `CourseSchedulingController.php` - Plans course offerings
- `CurriculumRevisionController.php` - Manages curriculum updates
- `CurriculumAuditController.php` - Tracks curriculum changes

**Models:**

- `Curriculum.php` - Curriculum entity
- `Course.php` - Course information
- `Subject.php` - Subject details
- `Prerequisite.php` - Prerequisite relationships
- `Corequisite.php` - Corequisite relationships
- `CurriculumRevision.php` - Version tracking
- `CurriculumLog.php` - Audit trail

**Views:**

```
views/curriculum/
├── setup/           (create, edit, list)
├── course-catalog/  (index, create, edit)
├── prerequisites/   (configure, view)
├── scheduling/      (term-schedule, plan)
├── revision/        (create, compare, history)
└── audit/           (logs)
```

### 4. Class Scheduling & Section Management Sub-system

**Controllers:**

- `SectionController.php` - Creates and manages sections
- `TimetableController.php` - Generates class timetables
- `RoomAssignmentController.php` - Assigns rooms to classes
- `TeacherLoadingController.php` - Manages faculty teaching loads
- `ConflictDetectionController.php` - Detects scheduling conflicts
- `SchedulingAuditController.php` - Tracks scheduling changes

**Models:**

- `Section.php` - Section entity
- `ClassSchedule.php` - Schedule records
- `Timetable.php` - Timetable structure
- `Room.php` - Room information
- `RoomAssignment.php` - Room allocations
- `TeacherLoad.php` - Faculty load tracking
- `ScheduleConflict.php` - Conflict records
- `SchedulingLog.php` - Audit trail

**Views:**

```
views/scheduling/
├── sections/        (create, edit, list)
├── timetable/       (generate, view, print)
├── rooms/           (assignment, availability, manage)
├── teacher-loading/ (assign, view, report)
├── conflicts/       (detection, resolution)
└── audit/           (logs)
```

### 5. Grades & Assessment Management Sub-system

**Controllers:**

- `GradeEncodingController.php` - Handles grade input
- `GradeVerificationController.php` - Verifies and approves grades
- `StudentGradeViewController.php` - Displays student grades
- `GradeCorrectionController.php` - Processes grade corrections
- `GradeReportController.php` - Generates grade reports
- `GradesAuditController.php` - Tracks grade changes

**Models:**

- `Grade.php` - Grade entity
- `GradeEntry.php` - Grade records
- `GradeVerification.php` - Verification status
- `GradeCorrection.php` - Correction requests
- `GradeReport.php` - Report generation
- `GradeLog.php` - Audit trail

**Views:**

```
views/grades/
├── encoding/        (input, batch-input, preview)
├── verification/    (pending, approve, reject)
├── student-view/    (current, history, print)
├── correction/      (request, approve, history)
├── reports/         (class-sheet, summary, analytics)
└── audit/           (logs)
```

### 6. Payment & Accounting Sub-system

**Controllers:**

- `FeeAssessmentController.php` - Calculates student fees
- `PaymentPostingController.php` - Records payments
- `BillingController.php` - Generates billing statements
- `ScholarshipController.php` - Processes scholarships and discounts
- `TransactionLogController.php` - Tracks financial transactions
- `PaymentAuditController.php` - Manages payment logs

**Models:**

- `Payment.php` - Payment entity
- `FeeAssessment.php` - Fee calculations
- `PaymentTransaction.php` - Transaction records
- `Billing.php` - Billing information
- `StatementOfAccount.php` - SOA generation
- `Scholarship.php` - Scholarship records
- `Discount.php` - Discount management
- `PaymentLog.php` - Audit trail

**Views:**

```
views/payment/
├── assessment/      (calculate, view, adjust)
├── posting/         (input, validate, receipt)
├── billing/         (statement, history, print)
├── scholarship/     (apply, process, manage)
├── transactions/    (log, report)
└── audit/           (logs)
```

### 7. Document & Credentials Sub-system

**Controllers:**

- `DocumentRequestController.php` - Handles document requests
- `DocumentProcessingController.php` - Manages workflow
- `DocumentGenerationController.php` - Generates documents
- `DocumentReleaseController.php` - Tracks document release
- `ArchiveController.php` - Manages archived documents
- `DocumentAuditController.php` - Tracks document operations

**Models:**

- `Document.php` - Document entity
- `DocumentRequest.php` - Request records
- `DocumentType.php` - Document types
- `DocumentWorkflow.php` - Processing workflow
- `DocumentRelease.php` - Release tracking
- `ArchivedDocument.php` - Archived records
- `DocumentLog.php` - Audit trail

**Views:**

```
views/document/
├── request/         (create, list, track)
├── processing/      (workflow, approve, queue)
├── generation/      (generate, preview, print)
├── release/         (ready, claim, tracking)
├── archive/         (index, search, view)
└── audit/           (logs)
```

### 8. Human Resource Management Sub-system

**Controllers:**

- `PreEmploymentController.php` - Manages pre-employment process
- `RecruitmentController.php` - Handles recruitment workflow
- `EmploymentRecordsController.php` - Manages employee records
- `PerformanceController.php` - Tracks performance evaluations
- `PostEmploymentController.php` - Handles resignations and clearance
- `HRAuditController.php` - Manages HR logs

**Models:**

- `Employee.php` - Employee entity
- `Applicant.php` - Applicant records
- `PreEmployment.php` - Pre-employment data
- `Recruitment.php` - Recruitment process
- `EmploymentRecord.php` - Employment history
- `Performance.php` - Performance records
- `ServiceRecord.php` - Service history
- `Clearance.php` - Clearance tracking
- `HRLog.php` - Audit trail

**Views:**

```
views/hr/
├── pre-employment/  (applicant-profile, requirements, screening)
├── recruitment/     (interview, evaluation, job-offer)
├── employment/      (records, onboarding, status)
├── performance/     (evaluation, workload, disciplinary)
├── post-employment/ (resignation, clearance, service-record)
└── audit/           (logs)
```

### 9. Clinic & Medical Services Sub-system

**Controllers:**

- `MedicalRecordsController.php` - Manages medical records
- `ConsultationController.php` - Handles consultations
- `MedicineInventoryController.php` - Manages medicine inventory
- `MedicalClearanceController.php` - Issues medical clearances
- `IncidentReportController.php` - Records health incidents

**Models:**

- `MedicalRecord.php` - Medical record entity
- `Consultation.php` - Consultation records
- `Treatment.php` - Treatment details
- `Medicine.php` - Medicine catalog
- `MedicineDispensing.php` - Dispensing records
- `MedicalClearance.php` - Clearance records
- `HealthIncident.php` - Incident reports

**Views:**

```
views/clinic/
├── medical-records/ (create, view, update)
├── consultation/    (register, treatment, history)
├── inventory/       (medicine-list, dispense, stock-management)
├── clearance/       (issue, verify)
└── incidents/       (report, list)
```

### 10. User Management Sub-system

**Controllers:**

- `UserAccountController.php` - Manages user accounts
- `RolePermissionController.php` - Handles roles and permissions
- `AuthenticationController.php` - Manages login and security
- `AuditTrailController.php` - Tracks user activities
- `PasswordRecoveryController.php` - Handles password reset

**Models:**

- `User.php` - User entity
- `Role.php` - Role definition
- `Permission.php` - Permission definition
- `UserRole.php` - User-role relationships
- `RolePermission.php` - Role-permission relationships
- `UserSession.php` - Session tracking
- `ActivityLog.php` - Activity records
- `PasswordReset.php` - Password reset tokens

**Views:**

```
views/user/
├── accounts/        (create, edit, list)
├── roles/           (manage, permissions, assign)
├── authentication/  (login, logout, mfa)
├── audit/           (activity-logs, search, reports)
└── recovery/        (forgot-password, reset-password, account-recovery)
```

## Core MVC Components

### Base Controller (core/Controller.php)

```php
<?php
abstract class Controller {
    protected $view;
    protected $model;

    public function __construct() {
        $this->view = new View();
    }

    protected function loadModel($model) {
        $modelPath = "../app/models/" . $model . ".php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        return null;
    }

    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
```

### Base Model (core/Model.php)

```php
<?php
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";
        return $this->db->query($sql, $data);
    }

    public function update($id, $data) {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');
        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, $data);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id]);
    }
}
```

### Router (core/Router.php)

```php
<?php
class Router {
    private $routes = [];
    private $middlewares = [];

    public function get($uri, $controller) {
        $this->addRoute('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        $this->addRoute('POST', $uri, $controller);
    }

    public function put($uri, $controller) {
        $this->addRoute('PUT', $uri, $controller);
    }

    public function delete($uri, $controller) {
        $this->addRoute('DELETE', $uri, $controller);
    }

    private function addRoute($method, $uri, $controller) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['method'] == $method && $this->matchUri($route['uri'], $uri)) {
                return $this->callController($route['controller']);
            }
        }
        return $this->notFound();
    }

    private function matchUri($routeUri, $requestUri) {
        // Simple pattern matching
        return $routeUri === $requestUri;
    }

    private function callController($controller) {
        list($controllerName, $method) = explode('@', $controller);
        $controllerFile = "../app/controllers/" . $controllerName . ".php";

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerInstance = new $controllerName();
            return $controllerInstance->$method();
        }
    }

    private function notFound() {
        http_response_code(404);
        require_once '../app/views/errors/404.php';
    }
}
```

## Database Schema Overview

### Core Tables Structure

**Users & Authentication:**

- users
- roles
- permissions
- user_roles
- role_permissions
- user_sessions
- activity_logs
- password_resets

**Student Management:**

- students
- student_profiles
- student_personal_info
- academic_records
- student_status
- student_activity_logs

**Enrollment:**

- enrollments
- enrollment_applications
- pre_enrollments
- enrollment_validations
- enrollment_statuses
- enrollment_logs

**Curriculum:**

- curricula
- courses
- subjects
- prerequisites
- corequisites
- curriculum_revisions
- curriculum_logs

**Scheduling:**

- sections
- class_schedules
- timetables
- rooms
- room_assignments
- teacher_loads
- schedule_conflicts
- scheduling_logs

**Grades:**

- grades
- grade_entries
- grade_verifications
- grade_corrections
- grade_reports
- grade_logs

**Payment:**

- payments
- fee_assessments
- payment_transactions
- billings
- statements_of_account
- scholarships
- discounts
- payment_logs

**Documents:**

- documents
- document_requests
- document_types
- document_workflows
- document_releases
- archived_documents
- document_logs

**Human Resources:**

- employees
- applicants
- pre_employments
- recruitments
- employment_records
- performances
- service_records
- clearances
- hr_logs

**Clinic:**

- medical_records
- consultations
- treatments
- medicines
- medicine_dispensings
- medical_clearances
- health_incidents

## Key Features Implementation

### 1. Authentication System

- Login/Logout
- Session management
- Role-based access control (RBAC)
- Multi-factor authentication (optional)
- Password encryption (bcrypt)
- Password reset functionality

### 2. Authorization System

- Role hierarchy (Admin, Registrar, Faculty, Student, Staff)
- Granular permissions
- Module-level access control
- Action-level permissions (create, read, update, delete)

### 3. Audit Trail System

- User activity logging
- Data change tracking
- System event logging
- Log retention and archiving
- Audit report generation

### 4. File Management

- Document upload
- File validation
- Secure file storage
- File type restrictions
- Size limitations
- Virus scanning (recommended)

### 5. PDF Generation

- Transcript of Records
- Registration Forms
- Billing Statements
- Medical Clearances
- Service Records
- ID Cards

### 6. Report Generation

- Enrollment statistics
- Grade reports
- Financial reports
- HR reports
- Medical reports
- Custom report builder

### 7. Notification System

- Email notifications
- SMS notifications (optional)
- In-app notifications
- Notification preferences
- Template management

### 8. Search & Filter

- Advanced search functionality
- Multi-field filtering
- Date range filtering
- Export functionality (CSV, Excel, PDF)

### 9. Data Validation

- Server-side validation
- Client-side validation
- Input sanitization
- XSS prevention
- SQL injection prevention
- CSRF protection

### 10. Backup & Recovery

- Database backup
- File backup
- Automated backup scheduling
- Recovery procedures
- Data archiving

## Technology Stack Recommendations

### Backend:

- PHP 8.0+
- MySQL 8.0+ or PostgreSQL
- Apache/Nginx web server

### Frontend:

- HTML5
- CSS3 (Bootstrap 5 or Tailwind CSS)
- JavaScript (Vanilla JS or jQuery)
- AJAX for asynchronous requests

### Libraries:

- TCPDF or FPDF for PDF generation
- PHPMailer for email
- PHPExcel or PhpSpreadsheet for Excel
- Chart.js for data visualization
- DataTables for advanced tables

### Security:

- HTTPS/SSL
- Input validation
- Prepared statements
- CSRF tokens
- XSS protection
- Rate limiting
- Session security

## Development Best Practices

1. **Code Organization:**
   - Follow PSR-4 autoloading
   - Use namespaces
   - Implement dependency injection
   - Follow SOLID principles

2. **Database:**
   - Use migrations for schema changes
   - Implement database indexing
   - Use transactions for data integrity
   - Regular backups

3. **Security:**
   - Validate all inputs
   - Sanitize outputs
   - Use prepared statements
   - Implement CSRF protection
   - Regular security audits

4. **Performance:**
   - Implement caching
   - Optimize database queries
   - Use lazy loading
   - Minimize HTTP requests
   - Compress assets

5. **Testing:**
   - Unit testing
   - Integration testing
   - User acceptance testing
   - Load testing

6. **Documentation:**
   - Code comments
   - API documentation
   - User manuals
   - System architecture diagrams

This structure provides a solid foundation for building a comprehensive Student and Enrollment Management System with all 10 subsystems properly organized following MVC architecture principles.
