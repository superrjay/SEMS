<?php
/**
 * Security Configuration
 * 
 * Security-related settings and configurations
 */

// XSS Protection
define('XSS_CLEAN_INPUT', true);
define('XSS_ALLOWED_TAGS', '<p><br><strong><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6>');

// SQL Injection Protection
define('SQL_INJECTION_PROTECTION', true);
define('USE_PREPARED_STATEMENTS', true);

// CSRF Token Configuration
define('CSRF_TOKEN_REGENERATE', true);
define('CSRF_EXCLUDE_URIS', [
    'api/*',
    'webhook/*',
]);

// Content Security Policy
define('CSP_ENABLED', true);
$csp_directives = [
    'default-src' => ["'self'"],
    'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'https://cdn.jsdelivr.net', 'https://code.jquery.com'],
    'style-src' => ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net', 'https://fonts.googleapis.com'],
    'img-src' => ["'self'", 'data:', 'https:'],
    'font-src' => ["'self'", 'https://fonts.gstatic.com'],
    'connect-src' => ["'self'"],
    'frame-ancestors' => ["'none'"],
];

// Security headers
$security_headers = [
    'X-Frame-Options' => 'SAMEORIGIN',
    'X-XSS-Protection' => '1; mode=block',
    'X-Content-Type-Options' => 'nosniff',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
];

// Strict Transport Security (HSTS)
define('HSTS_ENABLED', false); // Enable in production with HTTPS
define('HSTS_MAX_AGE', 31536000); // 1 year
define('HSTS_INCLUDE_SUBDOMAINS', true);
define('HSTS_PRELOAD', false);

// IP Restrictions
define('IP_RESTRICTION_ENABLED', false);
define('WHITELIST_IPS', [
    '127.0.0.1',
    '::1',
]);
define('BLACKLIST_IPS', []);

// User agent restrictions
define('BLOCK_SUSPICIOUS_USER_AGENTS', true);
$blocked_user_agents = [
    'bot',
    'crawler',
    'spider',
    'scraper',
];

// File upload security
define('SCAN_UPLOADED_FILES', true);
define('ALLOW_EXECUTABLE_UPLOADS', false);
$blocked_extensions = [
    'php', 'php3', 'php4', 'php5', 'phtml',
    'exe', 'bat', 'cmd', 'sh', 'bash',
    'js', 'jar', 'msi', 'app',
];

// Password policy
$password_policy = [
    'min_length' => PASSWORD_MIN_LENGTH,
    'require_uppercase' => PASSWORD_REQUIRE_UPPERCASE,
    'require_lowercase' => PASSWORD_REQUIRE_LOWERCASE,
    'require_numbers' => PASSWORD_REQUIRE_NUMBERS,
    'require_special' => PASSWORD_REQUIRE_SPECIAL,
    'prevent_reuse' => true,
    'reuse_history' => 5, // Don't allow last 5 passwords
    'expiry_days' => 90, // Password expires after 90 days
    'expiry_warning_days' => 7, // Warn 7 days before expiry
];

// Session security
$session_security = [
    'regenerate_id' => true,
    'regenerate_interval' => 300, // 5 minutes
    'fingerprint' => true, // Browser fingerprinting
    'ip_check' => false, // Check IP address (can cause issues with mobile users)
    'user_agent_check' => true,
];

// Two-Factor Authentication
define('TWO_FACTOR_AUTH_ENABLED', false);
define('TWO_FACTOR_AUTH_REQUIRED_ROLES', ['super_admin', 'admin']);
define('TWO_FACTOR_AUTH_METHOD', 'email'); // email, sms, authenticator

// API Security
define('API_REQUIRE_AUTHENTICATION', true);
define('API_TOKEN_TYPE', 'bearer'); // bearer, jwt
define('API_CORS_ENABLED', true);
$api_cors_settings = [
    'allowed_origins' => ['*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => true,
];

// Audit trail settings
$audit_settings = [
    'enabled' => true,
    'log_login' => true,
    'log_logout' => true,
    'log_failed_login' => true,
    'log_create' => true,
    'log_update' => true,
    'log_delete' => true,
    'log_view' => false, // Usually too verbose
    'retention_days' => 365, // Keep audit logs for 1 year
];

// Encryption settings
define('ENCRYPTION_CIPHER', 'AES-256-CBC');
define('ENCRYPTION_HASH_ALGO', 'sha256');

// Sensitive data fields (should be encrypted in database)
$encrypt_fields = [
    'password',
    'ssn',
    'tax_id',
    'bank_account',
    'credit_card',
];

// Honeypot settings (for bot protection)
define('HONEYPOT_ENABLED', true);
define('HONEYPOT_FIELD_NAME', 'website_url'); // Field name that should remain empty

// Rate limiting by action
$rate_limits = [
    'login' => [
        'max_attempts' => 5,
        'decay_minutes' => 15,
    ],
    'password_reset' => [
        'max_attempts' => 3,
        'decay_minutes' => 60,
    ],
    'api_request' => [
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
    'enrollment_submission' => [
        'max_attempts' => 3,
        'decay_minutes' => 60,
    ],
];

// Suspicious activity detection
$suspicious_activity = [
    'enabled' => true,
    'multiple_failed_logins' => 5,
    'rapid_requests' => 100, // requests per minute
    'unusual_hours' => false, // Flag logins during unusual hours
    'geo_location_change' => false, // Flag sudden location changes
    'notification_email' => 'security@sems.edu',
];

// Input validation rules
$validation_patterns = [
    'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
    'phone' => '/^[0-9]{10,11}$/',
    'student_number' => '/^[0-9]{4}-[0-9]{5}$/',
    'employee_number' => '/^EMP-[0-9]{5}$/',
    'alphanumeric' => '/^[a-zA-Z0-9]+$/',
    'alpha' => '/^[a-zA-Z]+$/',
    'numeric' => '/^[0-9]+$/',
];

// Backup encryption
define('BACKUP_ENCRYPTION_ENABLED', true);
define('BACKUP_ENCRYPTION_KEY', getenv('BACKUP_ENCRYPTION_KEY') ?: ENCRYPTION_KEY);

// Database encryption
define('DB_ENCRYPTION_ENABLED', false); // Enable for sensitive data
define('DB_ENCRYPTION_KEY', getenv('DB_ENCRYPTION_KEY') ?: ENCRYPTION_KEY);

return [
    'xss_protection' => XSS_CLEAN_INPUT,
    'csrf_protection' => true,
    'csrf_token_name' => CSRF_TOKEN_NAME,
    'csrf_token_length' => CSRF_TOKEN_LENGTH,
    'csrf_exclude_uris' => CSRF_EXCLUDE_URIS,
    'csp' => [
        'enabled' => CSP_ENABLED,
        'directives' => $csp_directives,
    ],
    'headers' => $security_headers,
    'hsts' => [
        'enabled' => HSTS_ENABLED,
        'max_age' => HSTS_MAX_AGE,
        'include_subdomains' => HSTS_INCLUDE_SUBDOMAINS,
        'preload' => HSTS_PRELOAD,
    ],
    'ip_restriction' => [
        'enabled' => IP_RESTRICTION_ENABLED,
        'whitelist' => WHITELIST_IPS,
        'blacklist' => BLACKLIST_IPS,
    ],
    'password_policy' => $password_policy,
    'session_security' => $session_security,
    'two_factor_auth' => [
        'enabled' => TWO_FACTOR_AUTH_ENABLED,
        'required_roles' => TWO_FACTOR_AUTH_REQUIRED_ROLES,
        'method' => TWO_FACTOR_AUTH_METHOD,
    ],
    'api' => [
        'require_authentication' => API_REQUIRE_AUTHENTICATION,
        'token_type' => API_TOKEN_TYPE,
        'cors' => array_merge(['enabled' => API_CORS_ENABLED], $api_cors_settings),
    ],
    'audit' => $audit_settings,
    'encryption' => [
        'cipher' => ENCRYPTION_CIPHER,
        'key' => ENCRYPTION_KEY,
        'fields' => $encrypt_fields,
    ],
    'rate_limits' => $rate_limits,
    'suspicious_activity' => $suspicious_activity,
    'validation_patterns' => $validation_patterns,
    'file_upload' => [
        'blocked_extensions' => $blocked_extensions,
        'scan_files' => SCAN_UPLOADED_FILES,
    ],
];