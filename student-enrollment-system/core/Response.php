<?php
/**
 * Response Class
 * 
 * Handles HTTP responses
 */

class Response {
    private $content;
    private $statusCode = 200;
    private $headers = [];
    
    public function __construct($content = '', $statusCode = 200, $headers = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
    
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }
    
    public function getStatusCode() {
        return $this->statusCode;
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }
    
    public function setHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    public function getHeaders() {
        return $this->headers;
    }
    
    public function send() {
        // Set status code
        http_response_code($this->statusCode);
        
        // Send headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        // Send content
        echo $this->content;
        
        return $this;
    }
    
    public function json($data, $statusCode = 200, $headers = []) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        $this->setHeaders($headers);
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $this->send();
    }
    
    public function jsonSuccess($message, $data = null, $statusCode = 200) {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    public function jsonError($message, $errors = null, $statusCode = 400) {
        return $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this->send();
    }
    
    public function redirectToRoute($route, $params = [], $statusCode = 302) {
        $url = route($route, $params);
        return $this->redirect($url, $statusCode);
    }
    
    public function back($statusCode = 302) {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        return $this->redirect($referer, $statusCode);
    }
    
    public function download($filePath, $fileName = null, $headers = []) {
        if (!file_exists($filePath)) {
            return $this->setStatusCode(404)->send();
        }
        
        $fileName = $fileName ?? basename($filePath);
        
        $this->setHeader('Content-Type', mime_content_type($filePath));
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->setHeader('Content-Length', filesize($filePath));
        $this->setHeaders($headers);
        
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        readfile($filePath);
        exit;
    }
    
    public function file($filePath, $headers = []) {
        if (!file_exists($filePath)) {
            return $this->setStatusCode(404)->send();
        }
        
        $this->setHeader('Content-Type', mime_content_type($filePath));
        $this->setHeader('Content-Length', filesize($filePath));
        $this->setHeaders($headers);
        
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        readfile($filePath);
        exit;
    }
    
    public function view($view, $data = [], $layout = 'main') {
        $viewEngine = new View();
        $content = $viewEngine->render($view, $data, $layout);
        $this->setContent($content);
        return $this->send();
    }
    
    public function noContent($statusCode = 204) {
        $this->setStatusCode($statusCode);
        $this->setContent('');
        return $this->send();
    }
    
    public function xml($data, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/xml');
        
        $xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $this->arrayToXml($data, $xml);
        
        $this->setContent($xml->asXML());
        return $this->send();
    }
    
    private function arrayToXml($data, &$xml) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }
    
    public function stream($callback) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        
        call_user_func($callback);
        exit;
    }
    
    public function cookie($name, $value, $minutes = null, $path = '/', $domain = null, $secure = false, $httpOnly = true) {
        $minutes = $minutes ?? (COOKIE_LIFETIME / 60);
        $expire = time() + ($minutes * 60);
        
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        return $this;
    }
    
    public function deleteCookie($name, $path = '/', $domain = null) {
        setcookie($name, '', time() - 3600, $path, $domain);
        return $this;
    }
    
    public function withHeaders($headers) {
        $this->setHeaders($headers);
        return $this;
    }
    
    public function withStatus($statusCode) {
        $this->setStatusCode($statusCode);
        return $this;
    }
    
    public static function make($content = '', $statusCode = 200, $headers = []) {
        return new self($content, $statusCode, $headers);
    }
}