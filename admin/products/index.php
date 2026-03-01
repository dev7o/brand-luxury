<?php
/**
 * Admin Products List — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo = getDB();
$pageTitle = 'المنتجات';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!validateCSRF())
        die('طلب غير صالح');
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => (int) $_POST['delete_id']]);
    header('Location: ' . url('admin/') . 'products/?deleted=1');
    exit;
}

// Get products
$products = $pdo->query(
    "SELECT p.*, c.name_ar AS cat_name FROM products p 
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC"
)->fetchAll();

$csrfToken = generateCSRF();
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-page-title">
    <span>المنتجات (
        <?= count($products) ?>)
    </span>
    <a href="<?= url('admin/') ?>products/add.php" class="admin-btn admin-btn-primary">+ إضافة منتج</a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حفظ المنتج بنجاح</div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حذف المنتج</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>صورة</th>
                    <th>الاسم</th>
                    <th>التصنيف</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>مميز</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?= productImageUrl($product['main_image'], 'small') ?>" alt="" class="product-thumb">
                        </td>
                        <td>
                            <strong style="display:block;color:var(--admin-text);">
                                <?= e($product['name_ar']) ?>
                            </strong>
                            <small style="color:var(--admin-muted);">
                                <?= e($product['name_en']) ?>
                            </small>
                        </td>
                        <td><span style="font-size:0.82rem;">
                                <?= e($product['cat_name'] ?? '—') ?>
                            </span></td>
                        <td><strong style="color:var(--admin-gold);">
                                <?= number_format($product['price'], 0) ?> ريال
                            </strong></td>
                        <td>
                            <span
                                class="status-badge <?= $product['is_active'] ? 'status-completed' : 'status-cancelled' ?>">
                                <?= $product['is_active'] ? 'متوفر' : 'غير متوفر' ?>
                            </span>
                        </td>
                        <td>
                            <?= $product['is_featured'] ? '' : '—' ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/') ?>products/edit.php?id=<?= $product['id'] ?>"
                                class="admin-btn admin-btn-outline admin-btn-sm">تعديل</a>
                            <form method="POST" style="display:inline;"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="delete_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--admin-muted);padding:2rem;">لا توجد منتجات</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>