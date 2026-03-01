<?php
/**
 * Admin Add/Edit Category — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo = getDB();
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$cat = null;

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int) $_GET['id']]);
    $cat = $stmt->fetch();
    if (!$cat) {
        header('Location: ' . url('admin/') . 'categories/');
        exit;
    }
}

$pageTitle = $isEdit ? 'تعديل التصنيف' : 'إضافة تصنيف جديد';
$csrfToken = generateCSRF();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF()) {
        $errors[] = 'طلب غير صالح.';
        goto render;
    }

    $name_ar = trim($_POST['name_ar'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $sort_order = (int) ($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name_ar)) {
        $errors[] = 'الاسم العربي مطلوب.';
    }

    $slug = slugify($name_en ?: $name_ar);

    // Check slug uniqueness
    $slugCheck = $pdo->prepare("SELECT id FROM categories WHERE slug = :slug" . ($isEdit ? " AND id != :eid" : ""));
    $slugCheck->execute(['slug' => $slug] + ($isEdit ? ['eid' => (int) $_GET['id']] : []));
    if ($slugCheck->fetch()) {
        $slug .= '-' . rand(10, 99);
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE categories SET name_ar=:ar, name_en=:en, slug=:slug, sort_order=:so, is_active=:act WHERE id=:id");
            $res = $stmt->execute([
                'ar' => $name_ar,
                'en' => $name_en,
                'slug' => $slug,
                'so' => $sort_order,
                'act' => $is_active,
                'id' => (int) $_GET['id']
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name_ar, name_en, slug, sort_order, is_active) VALUES (:ar, :en, :slug, :so, :act)");
            $res = $stmt->execute([
                'ar' => $name_ar,
                'en' => $name_en,
                'slug' => $slug,
                'so' => $sort_order,
                'act' => $is_active
            ]);
        }

        if ($res) {
            header('Location: ' . url('admin/') . 'categories/?saved=1');
            exit;
        } else {
            $errors[] = 'حدث خطأ في حفظ البيانات.';
        }
    }
}

render:
include __DIR__ . '/../includes/admin_header.php';
$v = $cat ?? [];
?>

<div class="admin-page-title">
    <span>
        <?= e($pageTitle) ?>
    </span>
    <a href="<?= url('admin/') ?>categories/" class="admin-btn admin-btn-outline"> رجوع</a>
</div>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error">
        <?= e($err) ?>
    </div>
<?php endforeach; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

    <div style="max-width:800px;">
        <div class="admin-card">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-label">الاسم عربي *</label>
                    <input type="text" name="name_ar" class="admin-input" required
                        value="<?= e($_POST['name_ar'] ?? $v['name_ar'] ?? '') ?>" placeholder="عطور رجالية">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">الاسم إنجليزي</label>
                    <input type="text" name="name_en" class="admin-input"
                        value="<?= e($_POST['name_en'] ?? $v['name_en'] ?? '') ?>" placeholder="Men's Fragrances">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-label">الترتيب</label>
                    <input type="number" name="sort_order" class="admin-input" min="0"
                        value="<?= e($_POST['sort_order'] ?? $v['sort_order'] ?? 0) ?>">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">الحالة</label>
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;margin-top:0.5rem;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" <?= (isset($_POST['is_active']) || !empty($v['is_active']) || (!$isEdit && empty($_POST))) ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.9rem;">تصنيف نشط</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary" style="margin-top:1rem;">
                <?= $isEdit ? ' حفظ التعديلات' : ' إضافة التصنيف' ?>
            </button>
        </div>
    </div>
</form>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>