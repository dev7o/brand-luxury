<?php
/**
 * Admin Login — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Already logged in
if (!empty($_SESSION['admin_id'])) {
    redirect(url('admin/'));
}

$error = '';
$csrfToken = generateCSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF()) {
        $error = 'طلب غير صالح أو انتهت الجلسة.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
        } else {
            if (attemptLogin($username, $password)) {
                redirect(url('admin/'));
            } else {
                $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
                usleep(500000); // intentional delay
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول الإدارة — براند</title>
    <link rel="stylesheet" href="<?= url() ?>assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Montserrat:wght@400;600&display=swap"
        rel="stylesheet">
    <meta name="robots" content="noindex,nofollow">
</head>

<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-brand">BRAND</h1>
                <p class="login-subtitle">لوحة تحكم المدير</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('admin/') ?>login" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="admin-form-group">
                    <label class="admin-label" for="login-user">اسم المستخدم</label>
                    <input type="text" id="login-user" name="username" class="admin-input" placeholder="admin"
                        value="<?= e($_POST['username'] ?? '') ?>" autocomplete="username" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label" for="login-pass">كلمة المرور</label>
                    <input type="password" id="login-pass" name="password" class="admin-input" placeholder="••••••••"
                        autocomplete="current-password" required>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary" style="width:100%;">
                    تسجيل الدخول
                </button>
            </form>

            <p style="margin-top:1.5rem;font-size:0.78rem;color:#666;text-align:center;">
                الموقع: <a href="<?= url() ?>" style="color:var(--admin-gold);">براند للعطور</a>
            </p>
        </div>
    </div>
</body>

</html>