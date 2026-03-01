<?php
/**
 * API: Handle WhatsApp Order
 * Brand Luxury Perfumes
 * This endpoint is called via AJAX before redirecting the user to WhatsApp.
 * It strictly saves the lead to the DB to act as a funnel metric/CRM.
 */

require_once __DIR__ . '/../includes/functions.php';

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Verify CSRF Token
if (!validateCSRF()) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Get input
$productId = (int) ($_POST['product_id'] ?? 0);
$name = trim(htmlspecialchars($_POST['customer_name'] ?? '', ENT_QUOTES, 'UTF-8'));
$phone = trim(htmlspecialchars($_POST['customer_phone'] ?? '', ENT_QUOTES, 'UTF-8'));
$notes = trim(htmlspecialchars($_POST['notes'] ?? '', ENT_QUOTES, 'UTF-8'));

if ($productId <= 0 || empty($name) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $pdo = getDB();

    // Fetch product details
    $stmt = $pdo->prepare("SELECT name_ar, price FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found or unavailable']);
        exit;
    }

    // Save order
    $insert = $pdo->prepare("
        INSERT INTO orders (product_id, product_name, product_price, customer_name, customer_phone, notes, status) 
        VALUES (:pid, :pname, :price, :cname, :phone, :notes, 'new')
    ");

    $success = $insert->execute([
        'pid' => $productId,
        'pname' => $product['name_ar'],
        'price' => $product['price'],
        'cname' => $name,
        'phone' => $phone,
        'notes' => $notes
    ]);

    if ($success) {
        // Generate WhatsApp Link
        $whatsAppNum = setting('whatsapp_number', '');
        $url = buildWhatsAppLink($product['name_ar'], $product['price'], $whatsAppNum);

        echo json_encode([
            'success' => true,
            'whatsapp_url' => $url,
            'message' => 'Order lead saved, redirecting to WhatsApp...'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save order in DB']);
    }

} catch (PDOException $e) {
    // Log exception for debug
    error_log("Order Save Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
