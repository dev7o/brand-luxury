<?php
/**
 * Admin Categories List — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo = getDB();
$pageTitle = 'التصنيفات';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!validateCSRF())
        die('طلب غير صالح');

    // Check if category has products
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
    $check->execute(['id' => (int) $_POST['delete_id']]);
    if ($check->fetchColumn() > 0) {
        header('Location: ' . url('admin/') . 'categories/?error=in_use');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => (int) $_POST['delete_id']]);
    header('Location: ' . url('admin/') . 'categories/?deleted=1');
    exit;
}

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id DESC")->fetchAll();

$csrfToken = generateCSRF();
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-page-title">
    <span>التصنيفات (
        <?= count($categories) ?>)
    </span>
    <a href="<?= url('admin/') ?>categories/add.php" class="admin-btn admin-btn-primary">+ إضافة تصنيف</a>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حفظ التصنيف بنجاح</div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حذف التصنيف</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'in_use'): ?>
    <div class="alert alert-error" data-auto-dismiss="6000"> لا يمكن حذف التصنيف لوجود عطور مرتبطة به. يرجى نقل التخلص من
        العطور أولاً.</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الترتيب</th>
                    <th>الاسم العربي</th>
                    <th>الاسم الإنجليزي</th>
                    <th>الرابط (Slug)</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td style="color:var(--admin-muted);font-weight:700;">
                            <?= $cat['sort_order'] ?>
                        </td>
                        <td><strong style="color:var(--admin-text);">
                                <?= e($cat['name_ar']) ?>
                            </strong></td>
                        <td style="color:var(--admin-muted);">
                            <?= e($cat['name_en']) ?>
                        </td>
                        <td style="color:var(--admin-gold);font-size:0.8rem;">
                            <?= e($cat['slug']) ?>
                        </td>
                        <td>
                            <span class="status-badge <?= $cat['is_active'] ? 'status-completed' : 'status-cancelled' ?>">
                                <?= $cat['is_active'] ? 'نشط' : 'غير نشط' ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('admin/') ?>categories/add.php?id=<?= $cat['id'] ?>"
                                class="admin-btn admin-btn-outline admin-btn-sm">تعديل</a>
                            <form method="POST" style="display:inline;"
                                onsubmit="return confirm('تحذير: لن تتمكن من حذف التصنيف إذا كان يحتوى على منتجات. هل أنت متأكد؟')">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--admin-muted);padding:2rem;">لا توجد تصنيفات
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>