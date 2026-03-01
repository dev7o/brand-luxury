<?php
/**
 * Admin Authentication Helpers
 * Brand Luxury Perfumes
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is logged in, redirect to login if not
 */
function requireAdmin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . url('admin/login'));
        exit;
    }
}

/**
 * Attempt admin login
 * Returns true on success, false on failure
 */
function attemptLogin(string $username, string $password): bool
{
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT id, username, password, full_name FROM admins WHERE username = :u LIMIT 1"
    );
    $stmt->execute(['u' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_full_name'] = $admin['full_name'];
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        return true;
    }
    return false;
}

/**
 * Logout admin
 */
function logoutAdmin(): void
{
    $_SESSION = [];
    session_destroy();
    header('Location: ' . url('admin/login'));
    exit;
}

/**
 * Get logged-in admin info
 */
function currentAdmin(): array
{
    return [
        'id' => $_SESSION['admin_id'] ?? 0,
        'username' => $_SESSION['admin_username'] ?? '',
        'full_name' => $_SESSION['admin_full_name'] ?? 'Admin',
    ];
}
