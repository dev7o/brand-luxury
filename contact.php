<?php
/**
 * Contact Page — Brand Luxury Perfumes
 */

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'تواصل معنا — ' . setting('site_name_ar', 'براند');
$metaDesc = 'تواصل مع فريق براند للعطور الفاخرة عبر واتساب أو نموذج التواصل';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF()) {
        $error = 'طلب غير صالح. يرجى المحاولة مرة أخرى.';
    } else {
        $name = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
        $phone = trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
        $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

        if (empty($name) || empty($message)) {
            $error = 'يرجى ملء الاسم والرسالة.';
        } elseif (mb_strlen($message) < 10) {
            $error = 'الرسالة قصيرة جداً.';
        } else {
            // Save as order/contact in DB (using orders table with product_id=1 as placeholder)
            $pdo = getDB();
            $stmt = $pdo->prepare(
                "INSERT INTO orders (customer_name, customer_phone, product_id, product_name, product_price, notes, status)
                 VALUES (:name, :phone, 1, 'رسالة تواصل', 0, :msg, 'new')"
            );
            if ($stmt->execute(['name' => $name, 'phone' => $phone, 'msg' => $message])) {
                $success = 'شكراً لتواصلك معنا! سنرد عليك في أقرب وقت.';
            } else {
                $error = 'حدث خطأ. يرجى المحاولة مرة أخرى.';
            }
        }
    }
}

$whatsappNum = preg_replace('/\D/', '', setting('whatsapp_number', '966500000000'));
$csrfToken = generateCSRF();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="مسار التنقل">
            <a href="<?= url() ?>">الرئيسية</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">تواصل معنا</span>
        </nav>
        <h1 class="ar-heading" style="color:var(--color-cream);font-size:clamp(2rem,4vw,3rem);">تواصل معنا</h1>
        <p style="color:var(--color-text-muted);margin-top:0.5rem;">نحن هنا لخدمتك — We're here for you</p>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="contact-grid">

            <!-- Contact Info -->
            <div class="reveal-left">
                <div class="contact-info-card">
                    <h2 class="ar-heading" style="color:var(--color-cream);font-size:1.4rem;margin-bottom:1.5rem;">
                        معلومات التواصل</h2>

                    <div class="contact-item">
                        <span class="contact-item-icon"></span>
                        <div>
                            <p class="contact-item-label">واتساب</p>
                            <a href="https://wa.me/<?= e($whatsappNum) ?>" target="_blank" rel="noopener"
                                class="contact-item-value btn btn-whatsapp btn-sm"
                                style="display:inline-flex;margin-top:0.3rem;">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                تواصل عبر واتساب
                            </a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <span class="contact-item-icon"></span>
                        <div>
                            <p class="contact-item-label">البريد الإلكتروني</p>
                            <a href="mailto:<?= e(setting('email', 'info@brand.com')) ?>" class="contact-item-value">
                                <?= e(setting('email', 'info@brand.com')) ?>
                            </a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <span class="contact-item-icon"></span>
                        <div>
                            <p class="contact-item-label">إنستغرام</p>
                            <a href="<?= e(setting('instagram_url', '#')) ?>" target="_blank" rel="noopener"
                                class="contact-item-value">
                                @brand_perfumes
                            </a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <span class="contact-item-icon"></span>
                        <div>
                            <p class="contact-item-label">ساعات العمل</p>
                            <p class="contact-item-value">السبت – الخميس: 9 صباحاً – 10 مساءً</p>
                        </div>
                    </div>
                </div>

                <!-- Quick WhatsApp CTA -->
                <div
                    style="background:linear-gradient(135deg,rgba(37,211,102,0.1),rgba(18,140,126,0.05));border:1px solid rgba(37,211,102,0.2);border-radius:var(--radius-md);padding:1.5rem;margin-top:1.5rem;text-align:center;">
                    <p
                        style="font-weight:700;color:var(--color-cream);margin-bottom:0.5rem;font-family:var(--font-ar);">
                        هل تريد الطلب مباشرة؟</p>
                    <p style="color:var(--color-text-muted);font-size:0.87rem;margin-bottom:1rem;">ارسل لنا رسالة على
                        واتساب وسنساعدك في اختيار عطرك المثالي</p>
                    <a href="https://wa.me/<?= e($whatsappNum) ?>?text=مرحباً،+أريد+مساعدة+في+اختيار+عطر+مناسب"
                        target="_blank" rel="noopener" class="btn btn-whatsapp">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        ابدأ المحادثة
                    </a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="reveal">
                <div class="form-card">
                    <h2 class="ar-heading" style="color:var(--color-cream);font-size:1.4rem;margin-bottom:1.5rem;">أرسل
                        لنا رسالة</h2>

                    <?php if ($success): ?>
                        <div class="alert alert-success" data-auto-dismiss="5000">
                            <?= e($success) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                        <div class="form-group">
                            <label class="form-label" for="contact-name">الاسم الكريم *</label>
                            <input type="text" id="contact-name" name="name" class="form-control" placeholder="اسمك"
                                value="<?= e($_POST['name'] ?? '') ?>" required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact-phone">رقم الهاتف (اختياري)</label>
                            <input type="tel" id="contact-phone" name="phone" class="form-control"
                                placeholder="966500000000+" value="<?= e($_POST['phone'] ?? '') ?>" maxlength="20">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact-message">رسالتك *</label>
                            <textarea id="contact-message" name="message" class="form-control"
                                placeholder="كيف يمكننا مساعدتك؟" required minlength="10"
                                maxlength="2000"><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-gold btn-lg" style="width:100%;justify-content:center;">
                            إرسال الرسالة
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"
                                height="18">
                                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
