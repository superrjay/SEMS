<?php
// Helper functions for route generation

function route($name, $params = []) {
    global $namedRoutes;
    
    if (!isset($namedRoutes[$name])) {
        throw new Exception("Route {$name} not found");
    }
    
    $uri = $namedRoutes[$name];
    
    foreach ($params as $key => $value) {
        $uri = str_replace('{' . $key . '}', $value, $uri);
    }
    
    return BASE_URL . trim($uri, '/');
}

function url($path = '') {
    return BASE_URL . trim($path, '/');
}

function asset($path) {
    return BASE_URL . 'assets/' . trim($path, '/');
}

function redirect($url) {
    header('Location: ' . url($url));
    exit;
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? url();
    header('Location: ' . $referer);
    exit;
}