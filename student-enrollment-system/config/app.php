<?php
/**
 * Application Configuration
 * 
 * This file contains general application settings
 * for the Student and Enrollment Management System
 */

// Application Information
define('APP_NAME', 'Student and Enrollment Management System');
define('APP_SHORT_NAME', 'SEMS');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Comprehensive system for managing student information and enrollment');

// Environment (development, staging, production)
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Debug mode
define('APP_DEBUG', APP_ENV === 'development');

// Application URL
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/student-enrollment-system');
define('BASE_URL', APP_URL . '/public/');

// Application timezone
define('APP_TIMEZONE', 'Asia/Manila');
date_default_timezone_set(APP_TIMEZONE);

// Application locale
define('APP_LOCALE', 'en_US');
define('APP_FALLBACK_LOCALE', 'en');

// Currency
define('APP_CURRENCY', 'PHP');
define('APP_CURRENCY_SYMBOL', '₱');

// Date and Time formats
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'F d, Y');
define('DISPLAY_DATETIME_FORMAT', 'F d, Y g:i A');

// Academic year settings
define('ACADEMIC_YEAR_START_MONTH', 6); // June
define('SEMESTERS_PER_YEAR', 2);

// Pagination
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGINATION_LINKS', 7);

// File upload settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
define('ALLOWED_FILE_TYPES', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOCUMENT_TYPES));

// Upload paths
define('UPLOAD_PATH', ROOT_PATH . '/public/assets/uploads/');
define('STUDENT_PHOTO_PATH', UPLOAD_PATH . 'student-photos/');
define('DOCUMENT_PATH', UPLOAD_PATH . 'documents/');
define('HR_FILES_PATH', UPLOAD_PATH . 'hr-files/');
define('TEMP_PATH', UPLOAD_PATH . 'temp/');

// Image settings
define('THUMBNAIL_WIDTH', 150);
define('THUMBNAIL_HEIGHT', 150);
define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);

// Session settings
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_NAME', 'SEMS_SESSION');
define('SESSION_SECURE', false); // Set to true in production with HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Lax'); // Lax, Strict, or None

// Cookie settings
define('COOKIE_LIFETIME', 86400); // 24 hours
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', false); // Set to true in production with HTTPS
define('COOKIE_HTTPONLY', true);

// Security settings
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'your-encryption-key-here-change-in-production');
define('HASH_ALGORITHM', 'sha256');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// CSRF Protection
define('CSRF_TOKEN_NAME', '_token');
define('CSRF_TOKEN_LENGTH', 32);
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_MAX_ATTEMPTS', 5);
define('RATE_LIMIT_DECAY_MINUTES', 1);

// Login attempts
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Password requirements
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);
define('PASSWORD_REQUIRE_NUMBERS', true);
define('PASSWORD_REQUIRE_SPECIAL', true);

// Email settings
define('MAIL_ENABLED', true);
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'noreply@sems.edu');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// Logging
define('LOG_ENABLED', true);
define('LOG_PATH', ROOT_PATH . '/storage/logs/');
define('LOG_LEVEL', APP_DEBUG ? 'debug' : 'error'); // debug, info, warning, error, critical
define('LOG_MAX_FILES', 30); // Keep logs for 30 days
define('LOG_QUERIES', APP_DEBUG);

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_DRIVER', 'file'); // file, memcached, redis
define('CACHE_PATH', ROOT_PATH . '/storage/cache/');
define('CACHE_LIFETIME', 3600); // 1 hour
define('CACHE_PREFIX', 'sems_');

// View settings
define('VIEW_CACHE_ENABLED', !APP_DEBUG);
define('VIEW_CACHE_PATH', ROOT_PATH . '/storage/cache/views/');

// API settings
define('API_ENABLED', true);
define('API_PREFIX', 'api');
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 60); // requests per minute
define('API_TOKEN_LIFETIME', 3600); // 1 hour

// Maintenance mode
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'System is currently under maintenance. Please try again later.');
define('MAINTENANCE_ALLOWED_IPS', ['127.0.0.1', '::1']);

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// PHP settings
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');
ini_set('post_max_size', '20M');
ini_set('upload_max_filesize', '20M');

// Default admin credentials (change these after first login)
define('DEFAULT_ADMIN_USERNAME', 'admin');
define('DEFAULT_ADMIN_EMAIL', 'admin@sems.edu');
define('DEFAULT_ADMIN_PASSWORD', 'Admin@123'); // Change this immediately

// Student number format
define('STUDENT_NUMBER_PREFIX', '');
define('STUDENT_NUMBER_LENGTH', 10); // Including prefix
define('STUDENT_NUMBER_YEAR_FORMAT', 'Y'); // Y for 4-digit year, y for 2-digit

// Faculty/Employee number format
define('EMPLOYEE_NUMBER_PREFIX', 'EMP-');
define('EMPLOYEE_NUMBER_LENGTH', 10);

// Document tracking number format
define('DOCUMENT_TRACKING_PREFIX', 'DOC-');
define('DOCUMENT_TRACKING_LENGTH', 12);

// Receipt number format
define('RECEIPT_NUMBER_PREFIX', 'OR-');
define('RECEIPT_NUMBER_LENGTH', 10);

// Grading system
define('GRADING_SYSTEM', 'numeric'); // numeric, letter, percentage
define('PASSING_GRADE', 75);
define('GRADE_DECIMAL_PLACES', 2);

// Grade computation weights (in percentage)
define('MIDTERM_WEIGHT', 40);
define('FINAL_WEIGHT', 60);

// GPA calculation
define('GPA_SCALE', 5.0); // 1.0 to 5.0 scale (Philippine system)
define('GPA_PASSING', 3.0);

// Enrollment settings
define('ENROLLMENT_UNIT_MINIMUM', 12);
define('ENROLLMENT_UNIT_MAXIMUM', 24);
define('ENROLLMENT_UNIT_OVERLOAD', 21); // Units above this require approval

// Class size
define('CLASS_SIZE_DEFAULT', 40);
define('CLASS_SIZE_MINIMUM', 15);
define('CLASS_SIZE_MAXIMUM', 50);

// Schedule time slots (in minutes)
define('TIME_SLOT_DURATION', 60);
define('CLASS_START_TIME', '07:00');
define('CLASS_END_TIME', '21:00');

// Notification settings
define('NOTIFICATIONS_ENABLED', true);
define('NOTIFICATION_EMAIL', true);
define('NOTIFICATION_SMS', false); // Requires SMS gateway configuration

// Backup settings
define('BACKUP_ENABLED', true);
define('BACKUP_PATH', ROOT_PATH . '/storage/backups/');
define('BACKUP_KEEP_DAYS', 30);

// System features toggles
define('FEATURE_ONLINE_ENROLLMENT', true);
define('FEATURE_ONLINE_PAYMENT', true);
define('FEATURE_DOCUMENT_REQUEST', true);
define('FEATURE_GRADE_VIEWING', true);
define('FEATURE_SCHEDULE_VIEWING', true);
define('FEATURE_MOBILE_APP', false);

// Third-party integrations
define('GOOGLE_ANALYTICS_ID', '');
define('FACEBOOK_APP_ID', '');
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// SMS Gateway (optional)
define('SMS_PROVIDER', 'semaphore'); // semaphore, twilio, etc.
define('SMS_API_KEY', '');

// Payment Gateway (optional)
define('PAYMENT_GATEWAY', 'paymongo'); // paymongo, paypal, stripe
define('PAYMENT_PUBLIC_KEY', '');
define('PAYMENT_SECRET_KEY', '');

// Export formats
define('EXPORT_FORMATS', ['pdf', 'excel', 'csv']);
define('DEFAULT_EXPORT_FORMAT', 'pdf');

// PDF settings
define('PDF_ORIENTATION', 'portrait'); // portrait, landscape
define('PDF_PAPER_SIZE', 'letter'); // letter, legal, a4
define('PDF_FONT', 'helvetica');
define('PDF_FONT_SIZE', 10);

// Excel settings
define('EXCEL_FORMAT', 'xlsx'); // xlsx, xls, csv

// System modules (can be toggled on/off)
define('MODULE_STUDENT_MANAGEMENT', true);
define('MODULE_ENROLLMENT', true);
define('MODULE_CURRICULUM', true);
define('MODULE_SCHEDULING', true);
define('MODULE_GRADES', true);
define('MODULE_PAYMENT', true);
define('MODULE_DOCUMENT', true);
define('MODULE_HR', true);
define('MODULE_CLINIC', true);
define('MODULE_USER_MANAGEMENT', true);

return [
    'app' => [
        'name' => APP_NAME,
        'short_name' => APP_SHORT_NAME,
        'version' => APP_VERSION,
        'description' => APP_DESCRIPTION,
        'env' => APP_ENV,
        'debug' => APP_DEBUG,
        'url' => APP_URL,
        'timezone' => APP_TIMEZONE,
        'locale' => APP_LOCALE,
    ],
    'security' => [
        'encryption_key' => ENCRYPTION_KEY,
        'csrf_protection' => true,
        'password_min_length' => PASSWORD_MIN_LENGTH,
        'max_login_attempts' => MAX_LOGIN_ATTEMPTS,
    ],
    'session' => [
        'lifetime' => SESSION_LIFETIME,
        'name' => SESSION_NAME,
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
    ],
    'features' => [
        'online_enrollment' => FEATURE_ONLINE_ENROLLMENT,
        'online_payment' => FEATURE_ONLINE_PAYMENT,
        'document_request' => FEATURE_DOCUMENT_REQUEST,
        'grade_viewing' => FEATURE_GRADE_VIEWING,
    ],
];