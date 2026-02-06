<?php
/**
 * Helper Functions
 * 
 * Global helper functions available throughout the application
 */

// ============================================================================
// URL & ROUTING HELPERS
// ============================================================================

if (!function_exists('url')) {
    /**
     * Generate a URL for the application
     */
    function url($path = '') {
        return BASE_URL . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for an asset
     */
    function asset($path) {
        return BASE_URL . 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route
     */
    function route($name, $params = []) {
        global $namedRoutes;
        
        if (!isset($namedRoutes[$name])) {
            return url();
        }
        
        $uri = $namedRoutes[$name];
        
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }
        
        return url($uri);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     */
    function redirect($url, $statusCode = 302) {
        header('Location: ' . url($url), true, $statusCode);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect to previous URL
     */
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? url();
        header('Location: ' . $referer);
        exit;
    }
}

// ============================================================================
// VIEW HELPERS
// ============================================================================

if (!function_exists('view')) {
    /**
     * Render a view
     */
    function view($view, $data = [], $layout = 'main') {
        $viewEngine = new View();
        return $viewEngine->render($view, $data, $layout);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     */
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     */
    function old($key, $default = null) {
        return $_SESSION['old'][$key] ?? $default;
    }
}

// ============================================================================
// SESSION & FLASH MESSAGES
// ============================================================================

if (!function_exists('session')) {
    /**
     * Get or set session value
     */
    function session($key = null, $default = null) {
        $session = new Session();
        
        if ($key === null) {
            return $session;
        }
        
        return $session->get($key, $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash message
     */
    function flash($type, $message) {
        $session = new Session();
        $session->setFlash($type, $message);
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get flash message
     */
    function get_flash($type) {
        $session = new Session();
        return $session->getFlash($type);
    }
}

// ============================================================================
// VALIDATION & SECURITY
// ============================================================================

if (!function_exists('csrf_token')) {
    /**
     * Get CSRF token
     */
    function csrf_token() {
        return $_SESSION[CSRF_TOKEN_NAME] ?? '';
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF token field
     */
    function csrf_field() {
        $token = csrf_token();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Verify CSRF token
     */
    function verify_csrf($token) {
        return hash_equals(csrf_token(), $token);
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitize string
     */
    function sanitize($string) {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }
}

// ============================================================================
// AUTHENTICATION
// ============================================================================

if (!function_exists('auth')) {
    /**
     * Get authenticated user
     */
    function auth() {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('user')) {
    /**
     * Get authenticated user
     */
    function user() {
        return auth();
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has a role
     */
    function has_role($role) {
        if (!is_logged_in()) {
            return false;
        }
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (is_array($role)) {
            return in_array($userRole, $role);
        }
        
        return $userRole === $role;
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if user has a permission
     */
    function has_permission($permission) {
        if (!is_logged_in()) {
            return false;
        }
        
        $permissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission, $permissions);
    }
}

// ============================================================================
// STRING HELPERS
// ============================================================================

if (!function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string
     */
    function str_limit($string, $limit = 100, $end = '...') {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }
        
        return mb_substr($string, 0, $limit) . $end;
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate a URL friendly slug
     */
    function str_slug($string, $separator = '-') {
        $string = preg_replace('/[^\p{L}\p{N}]/u', $separator, $string);
        $string = preg_replace('/' . preg_quote($separator) . '+/', $separator, $string);
        $string = trim($string, $separator);
        return strtolower($string);
    }
}

if (!function_exists('str_random')) {
    /**
     * Generate a random string
     */
    function str_random($length = 16) {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('starts_with')) {
    /**
     * Check if string starts with
     */
    function starts_with($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

if (!function_exists('ends_with')) {
    /**
     * Check if string ends with
     */
    function ends_with($haystack, $needle) {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

// ============================================================================
// ARRAY HELPERS
// ============================================================================

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using dot notation
     */
    function array_get($array, $key, $default = null) {
        if (is_null($key)) {
            return $array;
        }
        
        if (isset($array[$key])) {
            return $array[$key];
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
}

if (!function_exists('array_only')) {
    /**
     * Get a subset of items from an array
     */
    function array_only($array, $keys) {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if (!function_exists('array_except')) {
    /**
     * Get all items except specified keys
     */
    function array_except($array, $keys) {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

// ============================================================================
// DATE & TIME HELPERS
// ============================================================================

if (!function_exists('now')) {
    /**
     * Get current datetime
     */
    function now($format = DATETIME_FORMAT) {
        return date($format);
    }
}

if (!function_exists('today')) {
    /**
     * Get current date
     */
    function today($format = DATE_FORMAT) {
        return date($format);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date
     */
    function format_date($date, $format = DISPLAY_DATE_FORMAT) {
        if (empty($date)) return '';
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format a datetime
     */
    function format_datetime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
        if (empty($datetime)) return '';
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('time_ago')) {
    /**
     * Get time ago string
     */
    function time_ago($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        
        if ($difference < 60) {
            return 'just now';
        } elseif ($difference < 3600) {
            return floor($difference / 60) . ' minutes ago';
        } elseif ($difference < 86400) {
            return floor($difference / 3600) . ' hours ago';
        } elseif ($difference < 604800) {
            return floor($difference / 86400) . ' days ago';
        } else {
            return format_date($datetime);
        }
    }
}

// ============================================================================
// FILE HELPERS
// ============================================================================

if (!function_exists('upload_file')) {
    /**
     * Upload a file
     */
    function upload_file($file, $destination, $allowedTypes = null) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Check file size
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return false;
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = $allowedTypes ?? ALLOWED_FILE_TYPES;
        
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $destination . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return false;
    }
}

if (!function_exists('delete_file')) {
    /**
     * Delete a file
     */
    function delete_file($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}

// ============================================================================
// DATABASE HELPERS
// ============================================================================

if (!function_exists('db')) {
    /**
     * Get database instance
     */
    function db() {
        return Database::getInstance();
    }
}

// ============================================================================
// FORMATTING HELPERS
// ============================================================================

if (!function_exists('currency')) {
    /**
     * Format as currency
     */
    function currency($amount, $decimals = 2) {
        return APP_CURRENCY_SYMBOL . number_format($amount, $decimals);
    }
}

if (!function_exists('number_format_short')) {
    /**
     * Format large numbers
     */
    function number_format_short($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }
}

if (!function_exists('percentage')) {
    /**
     * Format as percentage
     */
    function percentage($value, $total, $decimals = 2) {
        if ($total == 0) return '0%';
        return number_format(($value / $total) * 100, $decimals) . '%';
    }
}

// ============================================================================
// DEBUGGING HELPERS
// ============================================================================

if (!function_exists('dd')) {
    /**
     * Dump and die
     */
    function dd(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die();
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variable
     */
    function dump(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }
}

// ============================================================================
// CONFIG HELPERS
// ============================================================================

if (!function_exists('config')) {
    /**
     * Get configuration value
     */
    function config($key, $default = null) {
        $app = Application::getInstance();
        return $app->getConfig($key, $default);
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

// ============================================================================
// RESPONSE HELPERS
// ============================================================================

if (!function_exists('response')) {
    /**
     * Create response
     */
    function response($content = '', $status = 200, $headers = []) {
        return new Response($content, $status, $headers);
    }
}

if (!function_exists('json_response')) {
    /**
     * Create JSON response
     */
    function json_response($data, $status = 200) {
        $response = new Response();
        return $response->json($data, $status);
    }
}

// ============================================================================
// VALIDATION HELPERS
// ============================================================================

if (!function_exists('validate')) {
    /**
     * Validate data
     */
    function validate($data, $rules) {
        $validator = new Validator();
        return $validator->validate($data, $rules);
    }
}

// ============================================================================
// LOGGING HELPERS
// ============================================================================

if (!function_exists('logger')) {
    /**
     * Log a message
     */
    function logger($message, $level = 'info', $context = []) {
        if (!LOG_ENABLED) return;
        
        $logFile = LOG_PATH . 'app-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

// ============================================================================
// MISCELLANEOUS HELPERS
// ============================================================================

if (!function_exists('abort')) {
    /**
     * Abort with error
     */
    function abort($code = 404, $message = null) {
        Application::abort($code, $message);
    }
}

if (!function_exists('is_active')) {
    /**
     * Check if current route is active
     */
    function is_active($route, $class = 'active') {
        $currentUri = Request::getUri();
        return starts_with($currentUri, $route) ? $class : '';
    }
}

if (!function_exists('student_number')) {
    /**
     * Format student number
     */
    function student_number($year, $sequence) {
        return $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('academic_year')) {
    /**
     * Get academic year string
     */
    function academic_year($startYear) {
        return $startYear . '-' . ($startYear + 1);
    }
}

if (!function_exists('semester_name')) {
    /**
     * Get semester name
     */
    function semester_name($semester) {
        $names = [
            1 => 'First Semester',
            2 => 'Second Semester',
            3 => 'Summer'
        ];
        return $names[$semester] ?? 'Unknown';
    }
}

if (!function_exists('grade_remark')) {
    /**
     * Get grade remark
     */
    function grade_remark($grade) {
        return $grade >= PASSING_GRADE ? 'Passed' : 'Failed';
    }
}

if (!function_exists('gpa')) {
    /**
     * Calculate GPA
     */
    function gpa($grades) {
        if (empty($grades)) return 0;
        return array_sum($grades) / count($grades);
    }
}