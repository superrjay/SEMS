<?php
/**
 * Email/Mail Configuration
 * 
 * Configuration for email sending functionality
 */

// Mail driver (smtp, sendmail, mail)
define('MAIL_DRIVER', getenv('MAIL_DRIVER') ?: 'smtp');

// SMTP Configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls'); // tls, ssl, or null

// Mail settings
define('MAIL_CHARSET', 'UTF-8');
define('MAIL_DEBUG', 0); // 0 = off, 1 = client, 2 = server
define('MAIL_AUTH', true);
define('MAIL_TIMEOUT', 30);

// Email templates path
define('EMAIL_TEMPLATE_PATH', ROOT_PATH . '/app/views/emails/');

// Email queue settings
define('MAIL_QUEUE_ENABLED', false);
define('MAIL_QUEUE_PATH', ROOT_PATH . '/storage/mail-queue/');

// Email notifications configuration
$email_notifications = [
    'enrollment' => [
        'enabled' => true,
        'subject' => 'Enrollment Confirmation',
        'template' => 'enrollment_confirmation',
    ],
    'payment' => [
        'enabled' => true,
        'subject' => 'Payment Receipt',
        'template' => 'payment_receipt',
    ],
    'grade_release' => [
        'enabled' => true,
        'subject' => 'Grades Available',
        'template' => 'grade_release',
    ],
    'document_ready' => [
        'enabled' => true,
        'subject' => 'Document Ready for Claiming',
        'template' => 'document_ready',
    ],
    'password_reset' => [
        'enabled' => true,
        'subject' => 'Password Reset Request',
        'template' => 'password_reset',
    ],
    'account_created' => [
        'enabled' => true,
        'subject' => 'Welcome to ' . APP_NAME,
        'template' => 'account_created',
    ],
    'schedule_change' => [
        'enabled' => true,
        'subject' => 'Schedule Change Notification',
        'template' => 'schedule_change',
    ],
];

return [
    'driver' => MAIL_DRIVER,
    'smtp' => [
        'host' => SMTP_HOST,
        'port' => SMTP_PORT,
        'username' => SMTP_USERNAME,
        'password' => SMTP_PASSWORD,
        'encryption' => SMTP_ENCRYPTION,
        'auth' => MAIL_AUTH,
    ],
    'from' => [
        'address' => MAIL_FROM_ADDRESS,
        'name' => MAIL_FROM_NAME,
    ],
    'charset' => MAIL_CHARSET,
    'debug' => MAIL_DEBUG,
    'timeout' => MAIL_TIMEOUT,
    'queue' => [
        'enabled' => MAIL_QUEUE_ENABLED,
        'path' => MAIL_QUEUE_PATH,
    ],
    'notifications' => $email_notifications,
];