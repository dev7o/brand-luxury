<?php
/**
 * Database Configuration
 * Brand Luxury Perfumes
 * 
 * PDO connection with error handling and UTF-8 support
 */

define('DB_HOST', 'sql12.freesqldatabase.com');
define('DB_NAME', 'sql12818463');
define('DB_USER', 'sql12818463');
define('DB_PASS', 'rhUCWdc7Cq');
define('DB_CHARSET', 'utf8mb4');
define('DB_BASE_URL', '/'); // Ezyro root domain: lamasa.unaux.com

/**
 * Get PDO database connection (singleton pattern)
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log the error but don't expose it
            error_log('Database connection failed: ' . $e->getMessage());
            die('<div style="text-align:center;padding:50px;font-family:sans-serif;">
                <h2 style="color:#C6A75E;">⚠ خطأ في الاتصال بقاعدة البيانات</h2>
                <p>تأكد من تشغيل XAMPP وإنشاء قاعدة البيانات.</p>
                <small>Database: ' . DB_NAME . ' | Host: ' . DB_HOST . '</small>
            </div>');
        }
    }

    return $pdo;
}
