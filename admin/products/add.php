<?php
/**
 * Admin Add/Edit Product — Brand Luxury Perfumes
 * This file handles both Add (no ID) and Edit (with ?id=X)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo = getDB();
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$product = null;

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int) $_GET['id']]);
    $product = $stmt->fetch();
    if (!$product) {
        header('Location: ' . url('admin/') . 'products/');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$pageTitle = $isEdit ? 'تعديل المنتج' : 'إضافة منتج جديد';
$csrfToken = generateCSRF();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF()) {
        $errors[] = 'طلب غير صالح.';
        goto render;
    }

    // Collect and sanitize inputs
    $fields = [
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'name_ar' => trim($_POST['name_ar'] ?? ''),
        'name_en' => trim($_POST['name_en'] ?? ''),
        'description_ar' => trim($_POST['description_ar'] ?? ''),
        'description_en' => trim($_POST['description_en'] ?? ''),
        'top_notes_ar' => trim($_POST['top_notes_ar'] ?? ''),
        'top_notes_en' => trim($_POST['top_notes_en'] ?? ''),
        'heart_notes_ar' => trim($_POST['heart_notes_ar'] ?? ''),
        'heart_notes_en' => trim($_POST['heart_notes_en'] ?? ''),
        'base_notes_ar' => trim($_POST['base_notes_ar'] ?? ''),
        'base_notes_en' => trim($_POST['base_notes_en'] ?? ''),
        'size_ml' => (int) ($_POST['size_ml'] ?? 100),
        'price' => (float) ($_POST['price'] ?? 0),
        'old_price' => !empty($_POST['old_price']) ? (float) $_POST['old_price'] : null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    // Validate
    if (empty($fields['name_ar']))
        $errors[] = 'الاسم العربي مطلوب.';
    if ($fields['price'] <= 0)
        $errors[] = 'السعر يجب أن يكون أكبر من صفر.';
    if ($fields['category_id'] <= 0)
        $errors[] = 'يرجى اختيار التصنيف.';

    // Slug
    $slug = slugify($fields['name_en'] ?: $fields['name_ar']);
    // Ensure unique slug (exclude current product if editing)
    $slugCheck = $pdo->prepare("SELECT id FROM products WHERE slug = :slug" . ($isEdit ? " AND id != :eid" : ""));
    $slugCheck->execute(['slug' => $slug] + ($isEdit ? ['eid' => (int) $_GET['id']] : []));
    if ($slugCheck->fetch()) {
        $slug .= '-' . time();
    }
    $fields['slug'] = $slug;

    // Handle main image upload
    if (!empty($_FILES['main_image']['name'])) {
        $imgErr = '';
        $imgFile = uploadProductImage($_FILES['main_image'], $imgErr);
        if ($imgFile) {
            $fields['main_image'] = $imgFile;
        } else {
            $errors[] = $imgErr;
        }
    } elseif ($isEdit) {
        $fields['main_image'] = $product['main_image'] ?? null;
    } else {
        $fields['main_image'] = null;
    }

    // Handle gallery (multiple images)
    $gallery = $isEdit ? json_decode($product['gallery'] ?? '[]', true) ?: [] : [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $i => $tmp) {
            if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                $gErr = '';
                $gFile = uploadProductImage([
                    'name' => $_FILES['gallery']['name'][$i],
                    'tmp_name' => $tmp,
                    'error' => $_FILES['gallery']['error'][$i],
                    'size' => $_FILES['gallery']['size'][$i],
                    'type' => $_FILES['gallery']['type'][$i],
                ], $gErr);
                if ($gFile)
                    $gallery[] = $gFile;
            }
        }
    }
    $fields['gallery'] = json_encode($gallery);

    if (empty($errors)) {
        if ($isEdit) {
            $sql = "UPDATE products SET 
                category_id=:category_id, name_ar=:name_ar, name_en=:name_en, slug=:slug,
                description_ar=:description_ar, description_en=:description_en,
                top_notes_ar=:top_notes_ar, top_notes_en=:top_notes_en,
                heart_notes_ar=:heart_notes_ar, heart_notes_en=:heart_notes_en,
                base_notes_ar=:base_notes_ar, base_notes_en=:base_notes_en,
                size_ml=:size_ml, price=:price, old_price=:old_price,
                main_image=:main_image, gallery=:gallery,
                is_featured=:is_featured, is_active=:is_active, sort_order=:sort_order
                WHERE id=:id";
            $fields['id'] = (int) $_GET['id'];
        } else {
            $sql = "INSERT INTO products 
                (category_id, name_ar, name_en, slug, description_ar, description_en,
                 top_notes_ar, top_notes_en, heart_notes_ar, heart_notes_en,
                 base_notes_ar, base_notes_en, size_ml, price, old_price, main_image, gallery,
                 is_featured, is_active, sort_order)
                VALUES
                (:category_id, :name_ar, :name_en, :slug, :description_ar, :description_en,
                 :top_notes_ar, :top_notes_en, :heart_notes_ar, :heart_notes_en,
                 :base_notes_ar, :base_notes_en, :size_ml, :price, :old_price, :main_image, :gallery,
                 :is_featured, :is_active, :sort_order)";
        }
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($fields)) {
            header('Location: ' . url('admin/') . 'products/?saved=1');
            exit;
        } else {
            $errors[] = 'حدث خطأ في حفظ البيانات.';
        }
    }
}

render:
include __DIR__ . '/../includes/admin_header.php';

// Default values from existing product
$v = $product ?? [];
$v = array_merge(array_fill_keys(array_keys($v), ''), $v);
?>

<div class="admin-page-title">
    <span>
        <?= e($pageTitle) ?>
    </span>
    <a href="<?= url('admin/') ?>products/" class="admin-btn admin-btn-outline"> رجوع</a>
</div>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error">
        <?= e($err) ?>
    </div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data" action="">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="responsive-admin-grid">
        <!-- Left Column -->
        <div>
            <div class="admin-card">
                <h4 style="margin-bottom:1.2rem;color:var(--admin-text);">المعلومات الأساسية</h4>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-label">الاسم عربي *</label>
                        <input type="text" name="name_ar" class="admin-input" required
                            value="<?= e($v['name_ar'] ?? '') ?>" placeholder="عطر عنبر الملوك">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">الاسم إنجليزي</label>
                        <input type="text" name="name_en" class="admin-input" value="<?= e($v['name_en'] ?? '') ?>"
                            placeholder="Amber Royal">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">الوصف عربي</label>
                    <textarea name="description_ar" class="admin-textarea" rows="4"
                        placeholder="وصف مفصل للعطر..."><?= e($v['description_ar'] ?? '') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">الوصف إنجليزي</label>
                    <textarea name="description_en" class="admin-textarea" rows="3"
                        placeholder="Detailed fragrance description..."><?= e($v['description_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="admin-card">
                <h4 style="margin-bottom:1.2rem;color:var(--admin-text);"> نوتات العطر — Fragrance Notes</h4>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-label">النوتة الأولى (عربي)</label>
                        <input type="text" name="top_notes_ar" class="admin-input"
                            value="<?= e($v['top_notes_ar'] ?? '') ?>" placeholder="البرغموت، الليمون">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Top Notes (EN)</label>
                        <input type="text" name="top_notes_en" class="admin-input"
                            value="<?= e($v['top_notes_en'] ?? '') ?>" placeholder="Bergamot, Lemon">
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-label">النوتة القلبية (عربي)</label>
                        <input type="text" name="heart_notes_ar" class="admin-input"
                            value="<?= e($v['heart_notes_ar'] ?? '') ?>" placeholder="الورد، العود">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Heart Notes (EN)</label>
                        <input type="text" name="heart_notes_en" class="admin-input"
                            value="<?= e($v['heart_notes_en'] ?? '') ?>" placeholder="Rose, Oud">
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-label">النوتة الأساسية (عربي)</label>
                        <input type="text" name="base_notes_ar" class="admin-input"
                            value="<?= e($v['base_notes_ar'] ?? '') ?>" placeholder="المسك، الخشب">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Base Notes (EN)</label>
                        <input type="text" name="base_notes_en" class="admin-input"
                            value="<?= e($v['base_notes_en'] ?? '') ?>" placeholder="Musk, Wood">
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <h4 style="margin-bottom:1.2rem;color:var(--admin-text);"> الصور</h4>
                <?php if ($isEdit && !empty($v['main_image'])): ?>
                    <p style="font-size:0.8rem;color:var(--admin-muted);margin-bottom:0.5rem;">الصورة الحالية:</p>
                    <img src="<?= productImageUrl($v['main_image'], 'small') ?>" class="img-preview"
                        style="margin-bottom:1rem;">
                <?php endif; ?>
                <div class="admin-form-group">
                    <label class="admin-label">الصورة الرئيسية
                        <?= $isEdit ? '(اتركها فارغة للإبقاء على الحالية)' : '' ?>
                    </label>
                    <input type="file" name="main_image" class="admin-input" accept="image/jpeg,image/png,image/webp"
                        <?= $isEdit ? '' : 'required' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">صور إضافية (Gallery - يمكن اختيار أكثر من صورة)</label>
                    <input type="file" name="gallery[]" class="admin-input" accept="image/jpeg,image/png,image/webp"
                        multiple>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <div class="admin-card">
                <h4 style="margin-bottom:1.2rem;color:var(--admin-text);">التسعير والتصنيف</h4>
                <div class="admin-form-group">
                    <label class="admin-label">التصنيف *</label>
                    <select name="category_id" class="admin-select" required>
                        <option value="">— اختر تصنيفاً —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ((int) ($v['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name_ar']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-label">السعر (ريال) *</label>
                        <input type="number" name="price" class="admin-input" required min="0" step="0.01"
                            value="<?= e($v['price'] ?? '') ?>" placeholder="450">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">السعر القديم</label>
                        <input type="number" name="old_price" class="admin-input" min="0" step="0.01"
                            value="<?= e($v['old_price'] ?? '') ?>" placeholder="550">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">الحجم (مل)</label>
                    <input type="number" name="size_ml" class="admin-input" min="1" max="500"
                        value="<?= e($v['size_ml'] ?? 100) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">الترتيب</label>
                    <input type="number" name="sort_order" class="admin-input" min="0"
                        value="<?= e($v['sort_order'] ?? 0) ?>">
                </div>
            </div>

            <div class="admin-card">
                <h4 style="margin-bottom:1.2rem;color:var(--admin-text);">الحالة والخيارات</h4>
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" <?= (!empty($v['is_active']) || !$isEdit) ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.9rem;">متوفر للبيع</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_featured" value="1" <?= !empty($v['is_featured']) ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.9rem;"> منتج مميز (يظهر في الرئيسية)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary"
                style="width:100%;padding:0.85rem;font-size:0.95rem;">
                <?= $isEdit ? ' حفظ التعديلات' : ' إضافة المنتج' ?>
            </button>
        </div>
    </div>
</form>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
<style>
    @media (max-width: 768px) {
        .responsive-admin-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>