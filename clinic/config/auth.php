<?php
declare(strict_types=1);
namespace Clinic\Config;
if (session_status() === PHP_SESSION_NONE) session_start();

class Auth {
    public static function isLoggedIn(): bool { return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); }
    public static function getUserId(): ?int { return $_SESSION['user_id'] ?? null; }
    public static function getUserRole(): ?string { return $_SESSION['user_role'] ?? null; }
    public static function hasRole(string $role): bool { return self::getUserRole() === $role; }
    public static function hasAnyRole(array $roles): bool { return in_array(self::getUserRole(), $roles, true); }
    public static function login(int $userId, string $role, array $userData = []): void {
        session_regenerate_id(true); $_SESSION['user_id']=$userId; $_SESSION['user_role']=$role; $_SESSION['user_data']=$userData; $_SESSION['last_activity']=time();
    }
    public static function logout(): void { session_unset(); session_destroy(); session_start(); }
    public static function requireAuth(): void { if(!self::isLoggedIn()){header('Location: /clinic/login.php');exit;} }
    public static function requireRole(string|array $roles): void {
        self::requireAuth(); $roles=is_array($roles)?$roles:[$roles];
        if(!self::hasAnyRole($roles)){http_response_code(403);echo "Access denied.";exit;}
    }
}
