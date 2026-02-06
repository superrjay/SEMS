<?php
/**
 * Application Class
 * 
 * Main application bootstrap and initialization
 */

class Application {
    private static $instance = null;
    private $router;
    private $config = [];
    
    private function __construct() {
        $this->loadConfiguration();
        $this->setupErrorHandling();
        $this->setupEnvironment();
        $this->initializeComponents();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function loadConfiguration() {
        // Load all configuration files
        $configFiles = [
            'database' => CONFIG_PATH . '/database.php',
            'app' => CONFIG_PATH . '/app.php',
            'mail' => CONFIG_PATH . '/mail.php',
            'security' => CONFIG_PATH . '/security.php',
            'constants' => CONFIG_PATH . '/constants.php',
        ];
        
        foreach ($configFiles as $key => $file) {
            if (file_exists($file)) {
                $this->config[$key] = require $file;
            }
        }
    }
    
    private function setupErrorHandling() {
        // Custom error handler
        set_error_handler([$this, 'handleError']);
        
        // Custom exception handler
        set_exception_handler([$this, 'handleException']);
        
        // Shutdown handler for fatal errors
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    public function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = $this->getErrorType($errno);
        $message = "[$errorType] $errstr in $errfile on line $errline";
        
        // Log the error
        $this->logError($message, $errno);
        
        // Display error in development mode
        if (APP_DEBUG) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px;'>";
            echo "<strong>Error:</strong> $message";
            echo "</div>";
        }
        
        return true;
    }
    
    public function handleException($exception) {
        $message = "Uncaught Exception: " . $exception->getMessage() . 
                   " in " . $exception->getFile() . 
                   " on line " . $exception->getLine();
        
        // Log the exception
        $this->logError($message, E_ERROR);
        
        // Display in development mode
        if (APP_DEBUG) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px;'>";
            echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
            echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
            echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
            echo "<strong>Trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
            echo "</div>";
        } else {
            // Show generic error page in production
            http_response_code(500);
            if (file_exists(APP_PATH . '/views/errors/500.php')) {
                include APP_PATH . '/views/errors/500.php';
            } else {
                echo "An error occurred. Please try again later.";
            }
        }
    }
    
    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    private function getErrorType($errno) {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED',
        ];
        
        return $errorTypes[$errno] ?? 'UNKNOWN';
    }
    
    private function logError($message, $level) {
        if (!LOG_ENABLED) return;
        
        $logFile = LOG_PATH . 'error-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Create log directory if it doesn't exist
        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    private function setupEnvironment() {
        // Set timezone
        date_default_timezone_set(APP_TIMEZONE);
        
        // Set locale
        setlocale(LC_ALL, APP_LOCALE);
        
        // Create necessary directories
        $directories = [
            UPLOAD_PATH,
            STUDENT_PHOTO_PATH,
            DOCUMENT_PATH,
            HR_FILES_PATH,
            TEMP_PATH,
            LOG_PATH,
            CACHE_PATH,
            VIEW_CACHE_PATH,
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        // Clean old files periodically
        $this->cleanupOldFiles();
    }
    
    private function initializeComponents() {
        // Initialize session
        Session::start();
        
        // Check maintenance mode
        if (MAINTENANCE_MODE) {
            $this->checkMaintenanceMode();
        }
        
        // Initialize security headers
        $this->setSecurityHeaders();
        
        // Initialize CSRF protection
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = $this->generateToken();
        }
    }
    
    private function checkMaintenanceMode() {
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
        
        if (!in_array($clientIP, MAINTENANCE_ALLOWED_IPS)) {
            http_response_code(503);
            if (file_exists(APP_PATH . '/views/errors/maintenance.php')) {
                include APP_PATH . '/views/errors/maintenance.php';
            } else {
                echo MAINTENANCE_MESSAGE;
            }
            exit;
        }
    }
    
    private function setSecurityHeaders() {
        // Security headers
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // HSTS header (only with HTTPS)
        if (HSTS_ENABLED && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $hsts = 'max-age=' . HSTS_MAX_AGE;
            if (HSTS_INCLUDE_SUBDOMAINS) {
                $hsts .= '; includeSubDomains';
            }
            if (HSTS_PRELOAD) {
                $hsts .= '; preload';
            }
            header('Strict-Transport-Security: ' . $hsts);
        }
        
        // Content Security Policy
        if (CSP_ENABLED) {
            $csp = $this->config['security']['csp']['directives'] ?? [];
            $cspString = '';
            foreach ($csp as $directive => $sources) {
                $cspString .= $directive . ' ' . implode(' ', $sources) . '; ';
            }
            header('Content-Security-Policy: ' . rtrim($cspString, '; '));
        }
    }
    
    private function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    private function cleanupOldFiles() {
        // Clean temporary files older than 24 hours
        $this->cleanDirectory(TEMP_PATH, 86400);
        
        // Clean old logs based on retention period
        if (defined('LOG_MAX_FILES')) {
            $this->cleanDirectory(LOG_PATH, LOG_MAX_FILES * 86400);
        }
        
        // Clean view cache if disabled
        if (!VIEW_CACHE_ENABLED) {
            $this->cleanDirectory(VIEW_CACHE_PATH, 0);
        }
    }
    
    private function cleanDirectory($directory, $maxAge) {
        if (!is_dir($directory)) return;
        
        $files = glob($directory . '*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($maxAge === 0 || $now - filemtime($file) >= $maxAge) {
                    @unlink($file);
                }
            }
        }
    }
    
    public function run() {
        try {
            // Initialize router
            $this->router = new Router();
            
            // Load routes
            require_once ROOT_PATH . '/routes/web.php';
            
            // Get current URI and method
            $uri = Request::getUri();
            $method = Request::getMethod();
            
            // Dispatch request
            $this->router->dispatch($uri, $method);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    public function getConfig($key = null, $default = null) {
        if ($key === null) {
            return $this->config;
        }
        
        // Support dot notation for nested config
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
    
    public function setConfig($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    public static function abort($code = 404, $message = null) {
        http_response_code($code);
        
        $errorView = APP_PATH . "/views/errors/{$code}.php";
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo $message ?? "Error {$code}";
        }
        exit;
    }
    
    public static function env($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}