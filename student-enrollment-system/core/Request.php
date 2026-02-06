<?php
/**
 * Request Class
 * 
 * Handles HTTP request data and operations
 */

class Request {
    private static $instance = null;
    private $data = [];
    private $files = [];
    private $headers = [];
    private $method;
    private $uri;
    
    private function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->parseUri();
        $this->parseRequestData();
        $this->parseHeaders();
        $this->sanitizeInput();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function parseUri() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Remove base path if application is in subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        
        // Ensure URI starts with /
        $uri = '/' . ltrim($uri, '/');
        
        return $uri;
    }
    
    private function parseRequestData() {
        // Parse GET data
        $this->data = $_GET;
        
        // Parse POST data
        if ($this->method === 'POST') {
            $this->data = array_merge($this->data, $_POST);
            
            // Parse JSON body if content type is application/json
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $json = file_get_contents('php://input');
                $jsonData = json_decode($json, true);
                if ($jsonData) {
                    $this->data = array_merge($this->data, $jsonData);
                }
            }
        }
        
        // Parse PUT/PATCH/DELETE data
        if (in_array($this->method, ['PUT', 'PATCH', 'DELETE'])) {
            parse_str(file_get_contents('php://input'), $putData);
            $this->data = array_merge($this->data, $putData);
        }
        
        // Parse uploaded files
        $this->files = $_FILES;
    }
    
    private function parseHeaders() {
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            // Fallback for nginx
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) === 'HTTP_') {
                    $headerName = str_replace(' ', '-', 
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $this->headers[$headerName] = $value;
                }
            }
        }
    }
    
    private function sanitizeInput() {
        if (!XSS_CLEAN_INPUT) return;
        
        array_walk_recursive($this->data, function(&$value) {
            if (is_string($value)) {
                $value = $this->cleanXSS($value);
            }
        });
    }
    
    private function cleanXSS($data) {
        // Remove null bytes
        $data = str_replace(chr(0), '', $data);
        
        // Fix &entity\n;
        $data = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', '$1;', $data);
        $data = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', '$1$2;', $data);
        
        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>#iU', '$1>', $data);
        
        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU', '$1=$2novbscript...', $data);
        
        // Only allow certain tags
        if (defined('XSS_ALLOWED_TAGS') && XSS_ALLOWED_TAGS) {
            $data = strip_tags($data, XSS_ALLOWED_TAGS);
        }
        
        return $data;
    }
    
    public static function getMethod() {
        return self::getInstance()->method;
    }
    
    public static function getUri() {
        return self::getInstance()->uri;
    }
    
    public function get($key, $default = null) {
        return $this->data[$key] ?? $default;
    }
    
    public function all() {
        return $this->data;
    }
    
    public function only($keys) {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_intersect_key($this->data, array_flip($keys));
    }
    
    public function except($keys) {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_diff_key($this->data, array_flip($keys));
    }
    
    public function has($key) {
        return isset($this->data[$key]);
    }
    
    public function filled($key) {
        return $this->has($key) && !empty($this->data[$key]);
    }
    
    public function file($key) {
        return $this->files[$key] ?? null;
    }
    
    public function hasFile($key) {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }
    
    public function header($key, $default = null) {
        return $this->headers[$key] ?? $default;
    }
    
    public function bearerToken() {
        $header = $this->header('Authorization', '');
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return null;
    }
    
    public function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    public function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    public function isGet() {
        return $this->method === 'GET';
    }
    
    public function isPost() {
        return $this->method === 'POST';
    }
    
    public function isPut() {
        return $this->method === 'PUT';
    }
    
    public function isDelete() {
        return $this->method === 'DELETE';
    }
    
    public function isPatch() {
        return $this->method === 'PATCH';
    }
    
    public function isAjax() {
        return strtolower($this->header('X-Requested-With', '')) === 'xmlhttprequest';
    }
    
    public function isJson() {
        return strpos($this->header('Content-Type', ''), 'application/json') !== false;
    }
    
    public function wantsJson() {
        $acceptable = $this->header('Accept', '');
        return strpos($acceptable, 'application/json') !== false;
    }
    
    public function isSecure() {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }
    
    public function validate($rules) {
        $validator = new Validator();
        return $validator->validate($this->data, $rules);
    }
    
    public function old($key, $default = null) {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    public function flash() {
        $_SESSION['old'] = $this->data;
    }
    
    public function segment($index, $default = null) {
        $segments = explode('/', trim($this->uri, '/'));
        return $segments[$index] ?? $default;
    }
    
    public function segments() {
        return explode('/', trim($this->uri, '/'));
    }
    
    public function fullUrl() {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . '://' . $host . $uri;
    }
    
    public function url() {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . $this->uri;
    }
    
    public function query($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->data;
        }
        
        // Support dot notation
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $this->data;
            
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return $default;
                }
            }
            
            return $value;
        }
        
        return $this->data[$key] ?? $default;
    }
    
    public function merge($data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    public function replace($data) {
        $this->data = $data;
        return $this;
    }
}