<?php
/**
 * About Page — Brand Luxury Perfumes
 */

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'من نحن — ' . setting('site_name_ar', 'براند');
$metaDesc = 'تعرف على قصة براند للعطور الفاخرة، رؤيتنا وقيمنا';

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="مسار التنقل">
            <a href="<?= url() ?>">الرئيسية</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">من نحن</span>
        </nav>
        <h1 class="ar-heading" style="color:var(--color-cream);font-size:clamp(2rem,4vw,3rem);">من نحن</h1>
    </div>
</section>

<!-- Brand Story -->
<section class="section about-hero">
    <div class="container">
        <div class="about-story-grid">
            <!-- Image Side -->
            <div class="about-image-wrap reveal-left">
                <div class="about-image-main">
                    <img src="https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=800&q=80"
                        alt="قصة براند للعطور" loading="lazy">
                </div>
                <div class="about-accent"></div>
            </div>
            <!-- Text Side -->
            <div class="reveal">
                <span class="section-eyebrow">Our Story</span>
                <h2 class="section-title ar-heading" style="text-align:right;margin-bottom:1rem;">
                    <?= e(setting('about_title_ar', 'قصة براند')) ?>
                </h2>
                <div class="section-divider" style="margin:1.2rem 0;"></div>
                <div style="color:var(--color-text);line-height:2;font-size:1rem;">
                    <?= nl2br(e(setting('about_text_ar', 'انطلقنا من شغف حقيقي بعالم العطور لنقدم لكم أرقى المقطرات وأعمق التجارب العطرية.'))) ?>
                </div>

                <!-- Values Grid -->
                <div class="values-grid" style="margin-top:2rem;">
                    <div class="value-item">
                        <div class="value-dot"></div>
                        <div>
                            <h4
                                style="color:var(--color-cream);font-family:var(--font-ar);font-size:0.95rem;margin-bottom:0.2rem;">
                                الأصالة</h4>
                            <p style="color:var(--color-text-muted);font-size:0.82rem;">عطور تحمل روح التراث</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-dot"></div>
                        <div>
                            <h4
                                style="color:var(--color-cream);font-family:var(--font-ar);font-size:0.95rem;margin-bottom:0.2rem;">
                                الجودة</h4>
                            <p style="color:var(--color-text-muted);font-size:0.82rem;">مكونات من أرقى المصادر</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-dot"></div>
                        <div>
                            <h4
                                style="color:var(--color-cream);font-family:var(--font-ar);font-size:0.95rem;margin-bottom:0.2rem;">
                                الابتكار</h4>
                            <p style="color:var(--color-text-muted);font-size:0.82rem;">مزج التقليد بالحداثة</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-dot"></div>
                        <div>
                            <h4
                                style="color:var(--color-cream);font-family:var(--font-ar);font-size:0.95rem;margin-bottom:0.2rem;">
                                التميز</h4>
                            <p style="color:var(--color-text-muted);font-size:0.82rem;">لأنك تستحق الأفضل</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section"
    style="background:var(--color-bg-alt);border-top:1px solid var(--color-border);border-bottom:1px solid var(--color-border);">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:2rem;text-align:center;">
            <div class="reveal reveal-delay-1">
                <p style="font-size:3rem;font-weight:700;color:var(--color-gold);font-family:var(--font-en);line-height:1;"
                    data-target="500">0</p>
                <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:0.5rem;">عميل راضٍ</p>
            </div>
            <div class="reveal reveal-delay-2">
                <p style="font-size:3rem;font-weight:700;color:var(--color-gold);font-family:var(--font-en);line-height:1;"
                    data-target="50">0</p>
                <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:0.5rem;">عطر حصري</p>
            </div>
            <div class="reveal reveal-delay-3">
                <p style="font-size:3rem;font-weight:700;color:var(--color-gold);font-family:var(--font-en);line-height:1;"
                    data-target="5">0</p>
                <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:0.5rem;">سنوات من الخبرة</p>
            </div>
            <div class="reveal reveal-delay-4">
                <p
                    style="font-size:3rem;font-weight:700;color:var(--color-gold);font-family:var(--font-en);line-height:1;">
                    100%</p>
                <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:0.5rem;">ضمان الجودة</p>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;" class="responsive-grid-1">
            <div style="background:var(--color-bg-card);border:1px solid var(--color-border);border-radius:var(--radius-md);padding:2.5rem;"
                class="reveal reveal-delay-1">
                <span style="font-size:2.5rem;display:block;margin-bottom:1rem;"></span>
                <h3 class="ar-heading" style="color:var(--color-cream);font-size:1.3rem;margin-bottom:1rem;">رؤيتنا</h3>
                <p style="color:var(--color-text-muted);line-height:1.9;font-size:0.93rem;">
                    أن نكون الخيار الأول لعشاق العطور الراقية في المملكة العربية السعودية ومنطقة الخليج، وأن نقدم تجربة
                    عطرية لا مثيل لها تجمع بين الأصالة العربية والفن العالمي.
                </p>
            </div>
            <div style="background:var(--color-bg-card);border:1px solid var(--color-border);border-radius:var(--radius-md);padding:2.5rem;"
                class="reveal reveal-delay-2">
                <span style="font-size:2.5rem;display:block;margin-bottom:1rem;"></span>
                <h3 class="ar-heading" style="color:var(--color-cream);font-size:1.3rem;margin-bottom:1rem;">رسالتنا
                </h3>
                <p style="color:var(--color-text-muted);line-height:1.9;font-size:0.93rem;">
                    تقديم عطور فاخرة حصرية بأعلى معايير الجودة، مع توفير تجربة شراء استثنائية وخدمة عملاء متميزة عبر
                    واتساب تضمن رضا كل عميل.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Full-width Image -->
<section style="height:400px;overflow:hidden;position:relative;" aria-hidden="true">
    <img src="https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=1600&q=80" alt=""
        style="width:100%;height:100%;object-fit:cover;opacity:0.4;" loading="lazy">
    <div
        style="position:absolute;inset:0;background:linear-gradient(to right,rgba(11,11,11,0.9) 0%,rgba(11,11,11,0.3) 100%);display:flex;align-items:center;">
        <div class="container">
            <p
                style="font-family:var(--font-en);font-style:italic;font-size:clamp(1.5rem,3vw,2.5rem);color:var(--color-cream);max-width:600px;line-height:1.5;">
                "The art of perfumery is the language<br>of the soul"
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<style>
    @media (max-width: 768px) {
        .responsive-grid-1 {
            grid-template-columns: 1fr !important;
        }
    }
</style>
