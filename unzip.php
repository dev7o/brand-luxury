<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$zipFile = 'brand_deploy.zip';

if (!file_exists($zipFile)) {
    die("<div style='color:red;font-size:24px;text-align:center;margin-top:50px;'>لم يتم العثور على ملف $zipFile<br>تأكد من رفعه في نفس المجلد (htdocs).</div>");
}

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    // Extract to current directory (htdocs)
    $zip->extractTo(__DIR__);
    $zip->close();
    echo "<div style='color:green;font-size:24px;text-align:center;margin-top:50px;'>🎉 تم فك الضغط بنجاح! جميع المجلدات (بما في ذلك includes) موجودة الآن.<br>يمكنك الآن زيارة موقعك أو حذف هذا الملف (unzip.php) وملف (brand_deploy.zip).</div>";
} else {
    echo "<div style='color:red;font-size:24px;text-align:center;margin-top:50px;'>❌ حدث خطأ أثناء فك الضغط. كود الخطأ: $res</div>";
}
