<?php
/**
 * Middleware Base Class
 * 
 * Base class for all middleware
 */

abstract class Middleware {
    /**
     * Handle the request
     * 
     * @return void
     */
    abstract public function handle();
    
    /**
     * Redirect to login page
     */
    protected function redirectToLogin($message = null) {
        if ($message) {
            flash('warning', $message);
        }
        redirect('login');
    }
    
    /**
     * Abort with forbidden error
     */
    protected function forbidden($message = 'Access forbidden') {
        abort(403, $message);
    }
    
    /**
     * Return unauthorized JSON response
     */
    protected function unauthorizedJson($message = 'Unauthorized') {
        $response = new Response();
        $response->json([
            'success' => false,
            'message' => $message
        ], 401);
        exit;
    }
}