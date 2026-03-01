<?php
/**
 * Admin Settings — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pdo = getDB();
$pageTitle = 'إعدادات الموقع';
$csrfToken = generateCSRF();
$errors = [];
$success = false;

// Fetch all settings keyed by setting_key
$allSettings = [];
foreach ($pdo->query("SELECT * FROM settings")->fetchAll() as $s) {
    $allSettings[$s['setting_key']] = $s['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF()) {
        $errors[] = 'طلب غير صالح.';
    } else {
        $updates = [
            'site_name_ar' => trim($_POST['site_name_ar'] ?? ''),
            'site_name_en' => trim($_POST['site_name_en'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? ''),
            'color_theme' => trim($_POST['color_theme'] ?? 'classic'),
            'about_title_ar' => trim($_POST['about_title_ar'] ?? ''),
            'about_text_ar' => trim($_POST['about_text_ar'] ?? ''),
        ];

        // Basic validation
        if (empty($updates['whatsapp_number']))
            $errors[] = 'رقم الواتساب مطلوب.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = :v");
            $pdo->beginTransaction();
            try {
                foreach ($updates as $k => $v) {
                    $stmt->execute(['k' => $k, 'v' => $v]);
                    $allSettings[$k] = $v; // update local array for display
                }
                $pdo->commit();
                $success = true;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'حدث خطأ في حفظ الإعدادات: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/includes/admin_header.php';
$v = $allSettings; // shortcut
?>

<div class="admin-page-title">
    <span>
        <?= e($pageTitle) ?>
    </span>
</div>

<?php if ($success): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حفظ الإعدادات بنجاح.</div>
<?php endif; ?>
<?php foreach ($errors as $err): ?>
    <div class="alert alert-error">
        <?= e($err) ?>
    </div>
<?php endforeach; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;" class="responsive-admin-grid">

        <!-- General Info -->
        <div class="admin-card">
            <h4
                style="margin-bottom:1.2rem;color:var(--admin-text);border-bottom:1px solid var(--admin-border);padding-bottom:0.5rem;">
                 الإعدادات العامة</h4>

            <div class="admin-form-group">
                <label class="admin-label">اسم الموقع (العربية)</label>
                <input type="text" name="site_name_ar" class="admin-input" value="<?= e($v['site_name_ar'] ?? '') ?>">
            </div>

            <div class="admin-form-group">
                <label class="admin-label">اسم الموقع (الإنجليزية)</label>
                <input type="text" name="site_name_en" class="admin-input" dir="ltr"
                    value="<?= e($v['site_name_en'] ?? '') ?>">
            </div>

            <div class="admin-form-group">
                <label class="admin-label">الثيم الـ Color (التصميم)</label>
                <select name="color_theme" class="admin-select">
                    <option value="classic" <?= ($v['color_theme'] ?? '') === 'classic' ? 'selected' : '' ?>>Classic
                        Luxury (الذهبي والأسود الكلاسيكي)</option>
                    <option value="modern" <?= ($v['color_theme'] ?? '') === 'modern' ? 'selected' : '' ?>>Clean Modern
                        (أبيض وأسود داكن - Minimal)</option>
                    <option value="royal" <?= ($v['color_theme'] ?? '') === 'royal' ? 'selected' : '' ?>>Royal Dark (نحاسي
                        وخلفية كحلية عميقة)</option>
                </select>
                <small style="color:var(--admin-muted);font-size:0.75rem;margin-top:0.3rem;display:block;">اختر التصميم
                    وسيتم تطبيقه على الفور في واجهة المتجر.</small>
            </div>
        </div>

        <!-- Contact Options -->
        <div class="admin-card">
            <h4
                style="margin-bottom:1.2rem;color:var(--admin-text);border-bottom:1px solid var(--admin-border);padding-bottom:0.5rem;">
                 التواصل</h4>

            <div class="admin-form-group">
                <label class="admin-label">رقم الواتساب لاستقبال الطلبات *</label>
                <input type="text" name="whatsapp_number" class="admin-input" required dir="ltr"
                    value="<?= e($v['whatsapp_number'] ?? '') ?>" placeholder="966500000000">
                <small style="color:var(--admin-muted);font-size:0.75rem;">اكتب الرقم مع رمز الدولة بدون + أو أصفار
                    إضافية (مثال: 9665...)</small>
            </div>

            <div class="admin-form-group">
                <label class="admin-label">البريد الإلكتروني للظهور</label>
                <input type="email" name="email" class="admin-input" dir="ltr" value="<?= e($v['email'] ?? '') ?>"
                    placeholder="info@brand.com">
            </div>

            <div class="admin-form-group">
                <label class="admin-label">رابط إنستغرام</label>
                <input type="url" name="instagram_url" class="admin-input" dir="ltr"
                    value="<?= e($v['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
            </div>
        </div>

        <!-- About Settings -->
        <div class="admin-card" style="grid-column: 1 / -1;">
            <h4
                style="margin-bottom:1.2rem;color:var(--admin-text);border-bottom:1px solid var(--admin-border);padding-bottom:0.5rem;">
                 صفحة من نحن</h4>

            <div class="admin-form-group">
                <label class="admin-label">العنوان الرئيسي</label>
                <input type="text" name="about_title_ar" class="admin-input"
                    value="<?= e($v['about_title_ar'] ?? '') ?>">
            </div>

            <div class="admin-form-group">
                <label class="admin-label">النص التعريفي لقصة البراند</label>
                <textarea name="about_text_ar" class="admin-textarea"
                    rows="4"><?= e($v['about_text_ar'] ?? '') ?></textarea>
            </div>
        </div>

    </div>

    <div style="margin-top:1.5rem;text-align:left;">
        <button type="submit" class="admin-btn admin-btn-primary admin-btn-lg"
            style="padding:0.8rem 2.5rem;font-size:1rem;">
             حفظ الإعدادات
        </button>
    </div>
</form>

<style>
    @media (max-width: 768px) {
        .responsive-admin-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>