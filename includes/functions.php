<?php
/**
 * Core Helper Functions
 * Brand Luxury Perfumes
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get internal URL
 */
function url(string $path = ''): string
{
    return DB_BASE_URL . ltrim($path, '/');
}

// =============================================
// SETTINGS
// =============================================

/**
 * Get all site settings as key=>value array (cached)
 */
function getSettings(): array
{
    static $settings = null;
    if ($settings === null) {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

/**
 * Get single setting value with optional default
 */
function setting(string $key, string $default = ''): string
{
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

// =============================================
// PRODUCTS
// =============================================

/**
 * Get all active products, optionally filtered by category slug
 */
function getProducts(?string $categorySlug = null, int $limit = 0): array
{
    $pdo = getDB();
    $sql = "SELECT p.*, c.name_ar AS category_name_ar, c.name_en AS category_name_en, c.slug AS category_slug
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.is_active = 1";
    $params = [];

    if ($categorySlug) {
        $sql .= " AND c.slug = :slug";
        $params['slug'] = $categorySlug;
    }

    $sql .= " ORDER BY p.is_featured DESC, p.sort_order ASC, p.created_at DESC";

    if ($limit > 0) {
        $sql .= " LIMIT " . (int) $limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get featured products for homepage
 */
function getFeaturedProducts(int $limit = 6): array
{
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name_ar AS category_name_ar, c.name_en AS category_name_en
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.is_active = 1 AND p.is_featured = 1
         ORDER BY p.sort_order ASC, p.created_at DESC
         LIMIT :lim"
    );
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get single product by slug
 */
function getProductBySlug(string $slug): ?array
{
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name_ar AS category_name_ar, c.name_en AS category_name_en, c.slug AS category_slug
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.slug = :slug AND p.is_active = 1
         LIMIT 1"
    );
    $stmt->execute(['slug' => $slug]);
    $product = $stmt->fetch();
    return $product ?: null;
}

/**
 * Get related products (same category, excluding current)
 */
function getRelatedProducts(int $categoryId, int $excludeId, int $limit = 4): array
{
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name_ar AS category_name_ar
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.category_id = :cat AND p.id != :exc AND p.is_active = 1
         ORDER BY p.is_featured DESC
         LIMIT :lim"
    );
    $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':exc', $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// =============================================
// CATEGORIES
// =============================================

/**
 * Get all active categories
 */
function getCategories(): array
{
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC"
    );
    return $stmt->fetchAll();
}

// =============================================
// WHATSAPP
// =============================================

/**
 * Build a WhatsApp URL with a pre-filled message
 */
function buildWhatsAppLink(string $productNameAr, float $price, string $phoneOverride = ''): string
{
    $phone = $phoneOverride ?: setting('whatsapp_number', '966500000000');
    // Remove any non-digit characters from phone
    $phone = preg_replace('/\D/', '', $phone);
    $message = "مرحباً، أريد طلب عطر *{$productNameAr}*\nالسعر: {$price} ريال\n\nأرجو التواصل معي 🌹";
    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
}

// =============================================
// SECURITY
// =============================================

/**
 * Sanitize output to prevent XSS
 */
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate CSRF token and store in session
 */
function generateCSRF(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request
 */
function validateCSRF(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Redirect to a URL and exit
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Create URL-safe slug from string
 */
function slugify(string $text): string
{
    // Transliterate Arabic numbers, keep letters/digits, replace spaces
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[\s\-]+/', '-', $text);
    $text = preg_replace('/[^\p{L}\p{N}\-]/u', '', $text);
    $text = trim($text, '-');
    return $text ?: 'product';
}

// =============================================
// IMAGE UPLOAD
// =============================================

/**
 * Handle product image upload
 * Returns filename on success, false on failure
 */
function uploadProductImage(array $file, string &$error = '')
{
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'خطأ في رفع الملف';
        return false;
    }

    if ($file['size'] > $maxSize) {
        $error = 'حجم الصورة كبير جداً (الحد الأقصى 5MB)';
        return false;
    }

    // Verify MIME type using finfo (not just extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowedMime)) {
        $error = 'نوع الملف غير مسموح به (jpg, png, webp فقط)';
        return false;
    }

    // Determine extension from MIME
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $ext = $extensions[$mime];

    // Generate unique filename
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/products/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        $error = 'فشل في حفظ الصورة';
        return false;
    }

    return $filename;
}

/**
 * Get product image URL (with fallback placeholder)
 */
function productImageUrl(?string $filename, string $size = 'medium'): string
{
    if ($filename && file_exists(__DIR__ . '/../uploads/products/' . $filename)) {
        return url('uploads/products/' . $filename);
    }
    // Placeholder image by category size
    $sizes = ['small' => '300/400', 'medium' => '600/700', 'large' => '800/900'];
    $dim = $sizes[$size] ?? '600/700';
    return "https://picsum.photos/{$dim}?grayscale&blur=1";
}

// =============================================
// PAGINATION
// =============================================

/**
 * Simple pagination helper
 * Returns ['offset', 'total_pages', 'current_page']
 */
function paginate(int $total, int $perPage, int $currentPage): array
{
    $totalPages = (int) ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    return ['offset' => $offset, 'total_pages' => $totalPages, 'current_page' => $currentPage];
}

// =============================================
// ORDERS
// =============================================

/**
 * Save a WhatsApp order to the database
 */
function saveOrder(int $productId, string $productName, float $productPrice, string $customerName = '', string $customerPhone = '', string $notes = ''): bool
{
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "INSERT INTO orders (product_id, product_name, product_price, customer_name, customer_phone, notes, status, whatsapp_sent)
         VALUES (:pid, :pname, :pprice, :cname, :cphone, :notes, 'new', 1)"
    );
    return $stmt->execute([
        'pid' => $productId,
        'pname' => $productName,
        'pprice' => $productPrice,
        'cname' => $customerName,
        'cphone' => $customerPhone,
        'notes' => $notes,
    ]);
}
