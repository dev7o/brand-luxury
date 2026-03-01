<?php
/**
 * Admin Dashboard — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$admin = currentAdmin();
$pdo = getDB();

// Dashboard stats
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'new_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn(),
];

// Recent orders
$recentOrders = $pdo->query(
    "SELECT o.*, p.name_ar AS product_name FROM orders o 
     LEFT JOIN products p ON p.id = o.product_id
     ORDER BY o.created_at DESC LIMIT 10"
)->fetchAll();

// Top products
$topProducts = $pdo->query(
    "SELECT p.name_ar, COUNT(o.id) AS order_count 
     FROM orders o 
     JOIN products p ON p.id = o.product_id
     GROUP BY o.product_id 
     ORDER BY order_count DESC LIMIT 5"
)->fetchAll();

include __DIR__ . '/includes/admin_header.php';
?>

<!-- Stats Cards -->
<div class="admin-stats-grid">
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
            <p class="stat-value" data-target="<?= $stats['products'] ?>">
                <?= $stats['products'] ?>
            </p>
            <p class="stat-label">المنتجات النشطة</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
            <p class="stat-value" data-target="<?= $stats['orders'] ?>">
                <?= $stats['orders'] ?>
            </p>
            <p class="stat-label">إجمالي الطلبات</p>
        </div>
    </div>
    <div class="stat-card stat-card-highlight">
        <div class="stat-icon"></div>
        <div class="stat-info">
            <p class="stat-value" data-target="<?= $stats['new_orders'] ?>">
                <?= $stats['new_orders'] ?>
            </p>
            <p class="stat-label">طلبات جديدة</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
            <p class="stat-value" data-target="<?= $stats['categories'] ?>">
                <?= $stats['categories'] ?>
            </p>
            <p class="stat-label">التصنيفات</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-quick-actions">
    <a href="<?= url('admin/') ?>products/add.php" class="admin-btn admin-btn-primary">+ إضافة منتج</a>
    <a href="<?= url('admin/') ?>categories/add.php" class="admin-btn admin-btn-outline">+ إضافة تصنيف</a>
    <a href="<?= url('admin/') ?>orders/" class="admin-btn admin-btn-outline">عرض الطلبات</a>
    <a href="<?= url() ?>" target="_blank" class="admin-btn admin-btn-outline"> عرض الموقع</a>
</div>

<div class="admin-two-col">
    <!-- Recent Orders -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>آخر الطلبات</h3>
            <a href="<?= url('admin/') ?>orders/" class="admin-link">عرض الكل</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>العميل</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>
                                <?= e($order['id']) ?>
                            </td>
                            <td>
                                <?= e($order['product_name'] ?? $order['product_name']) ?>
                            </td>
                            <td>
                                <?= e($order['customer_name'] ?: '—') ?>
                            </td>
                            <td>
                                <?= number_format($order['product_price'], 0) ?> ريال
                            </td>
                            <td>
                                <span class="status-badge status-<?= e($order['status']) ?>">
                                    <?php $statuses = ['new' => 'جديد', 'contacted' => 'تم التواصل', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
                                    echo $statuses[$order['status']] ?? $order['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?= date('Y/m/d', strtotime($order['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:#888;">لا توجد طلبات بعد</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>أكثر المنتجات طلباً</h3>
        </div>
        <?php foreach ($topProducts as $i => $tp): ?>
            <div
                style="display:flex;align-items:center;gap:1rem;padding:0.8rem 0;border-bottom:1px solid var(--admin-border);">
                <span
                    style="width:28px;height:28px;border-radius:50%;background:var(--admin-gold);color:#000;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                    <?= $i + 1 ?>
                </span>
                <span style="flex:1;font-size:0.9rem;">
                    <?= e($tp['name_ar']) ?>
                </span>
                <span style="color:var(--admin-gold);font-weight:700;">
                    <?= $tp['order_count'] ?> طلب
                </span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($topProducts)): ?>
            <p style="color:#888;font-size:0.87rem;padding:1rem 0;">لا توجد بيانات بعد</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>