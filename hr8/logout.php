<?php
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';
use HR8\Config\Auth;
use HR8\Config\Database;

if (Auth::isLoggedIn()) {
    try { log_audit(Database::getConnection(), 'System', 'User Logout', 'user', Auth::getUserId()); } catch (Exception $e) {}
}
Auth::logout();
flash('success', 'You have been logged out.');
redirect('/hr8/login.php');
