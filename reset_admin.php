<?php
/**
 * ⚠️ ملف مؤقت - احذفه فوراً بعد الاستخدام!
 * Admin Password Reset Tool
 */

require_once __DIR__ . '/config/database.php';

$newPassword = 'Brand@2026';
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE admins SET password = :p WHERE username = 'admin'");
    $stmt->execute(['p' => $newHash]);

    if ($stmt->rowCount() > 0) {
        echo '<div style="font-family:sans-serif;text-align:center;padding:50px;">';
        echo '<h2 style="color:green;">✅ تم تغيير كلمة المرور بنجاح!</h2>';
        echo '<p><strong>اسم المستخدم:</strong> admin</p>';
        echo '<p><strong>كلمة المرور الجديدة:</strong> Brand@2026</p>';
        echo '<p style="color:red;font-weight:bold;">⚠️ احذف هذا الملف (reset_admin.php) الآن من السيرفر!</p>';
        echo '<a href="/admin/login" style="display:inline-block;margin-top:20px;padding:10px 30px;background:#C6A75E;color:#fff;text-decoration:none;border-radius:5px;">انتقل لصفحة الدخول</a>';
        echo '</div>';
    } else {
        echo '<div style="font-family:sans-serif;text-align:center;padding:50px;">';
        echo '<h2 style="color:orange;">⚠️ لم يتم العثور على مستخدم admin في قاعدة البيانات.</h2>';
        echo '<p>تأكد من استيراد ملف brand.sql أولاً.</p>';
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div style="font-family:sans-serif;text-align:center;padding:50px;">';
    echo '<h2 style="color:red;">❌ خطأ في الاتصال</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
