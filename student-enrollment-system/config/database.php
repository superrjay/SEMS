<?php
/**
 * Database Configuration
 * 
 * This file contains all database-related configuration settings
 * for the Student and Enrollment Management System
 */

// Database connection settings
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'student_enrollment_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Database connection type
define('DB_TYPE', 'mysql'); // mysql, pgsql, sqlite

// Database port
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// Database prefix (optional)
define('DB_PREFIX', '');

// Enable/disable persistent connections
define('DB_PERSISTENT', false);

// Database connection options
$db_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_STRINGIFY_FETCHES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATE,
];

// Connection timeout (in seconds)
define('DB_TIMEOUT', 10);
$db_options[PDO::ATTR_TIMEOUT] = DB_TIMEOUT;

// Persistent connection
if (DB_PERSISTENT) {
    $db_options[PDO::ATTR_PERSISTENT] = true;
}

// Query logging (for development)
define('DB_QUERY_LOGGING', false);

// Maximum number of retries for failed connections
define('DB_MAX_RETRIES', 3);

// Retry delay (in seconds)
define('DB_RETRY_DELAY', 1);

return [
    'host' => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
    'charset' => DB_CHARSET,
    'collation' => DB_COLLATE,
    'port' => DB_PORT,
    'prefix' => DB_PREFIX,
    'options' => $db_options,
    'persistent' => DB_PERSISTENT,
    'query_logging' => DB_QUERY_LOGGING,
    'max_retries' => DB_MAX_RETRIES,
    'retry_delay' => DB_RETRY_DELAY,
];