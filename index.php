<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Homepage — Brand Luxury Perfumes
 */

require_once __DIR__ . '/includes/functions.php';

$settings = getSettings();
$featuredProducts = getFeaturedProducts(6);
$pageTitle = $settings['meta_title_ar'] ?? 'براند للعطور الفاخرة';
$metaDesc = $settings['meta_description_ar'] ?? '';
$whatsappNum = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '966500000000');

include __DIR__ . '/includes/header.php';
?>

<!-- ====================================================
     HERO SECTION
     ==================================================== -->
<section class="hero" aria-label="صورة البانر الرئيسية">
    <div class="hero-bg">
        <?php if (!empty($settings['hero_image'])): ?>
            <img src="uploads/<?= e($settings['hero_image']) ?>" alt="براند للعطور الفاخرة" loading="eager">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1541643600914-78b084683702?w=1600&q=80" alt="براند للعطور الفاخرة"
                loading="eager">
        <?php endif; ?>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-eyebrow">مجموعة حصرية — Exclusive Collection</span>
        <h1 class="hero-title">
            <?= e($settings['hero_title_ar'] ?? 'عطر يعبّر عن شخصيتك') ?>
        </h1>
        <p class="hero-subtitle">
            <?= e($settings['hero_subtitle_ar'] ?? 'مجموعة حصرية من أرقى العطور الشرقية والغربية') ?>
        </p>
        <div class="hero-actions">
            <a href="shop" class="btn btn-gold btn-lg">
                تصفح العطور
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <a href="https://wa.me/<?= e($whatsappNum) ?>" target="_blank" rel="noopener"
                class="btn btn-outline btn-lg">
                <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                تواصل عبر واتساب
            </a>
        </div>
    </div>
    <div class="hero-scroll" aria-hidden="true">
        <span class="scroll-line"></span>
        <span>اكتشف</span>
    </div>
</section>

<!-- ====================================================
     FEATURED PRODUCTS
     ==================================================== -->
<section class="section" id="featured-products" aria-label="المنتجات المميزة">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Exclusive Selection</span>
            <h2 class="section-title ar-heading">مجموعتنا المميزة</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">اكتشف أرقى العطور التي تجمع بين الفن والأصالة والرقي</p>
        </div>

        <?php if (!empty($featuredProducts)): ?>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $i => $product): ?>
                    <article class="product-card reveal reveal-delay-<?= ($i % 4) + 1 ?>"
                        data-category="<?= e($product['category_slug'] ?? '') ?>">
                        <a href="product?slug=<?= e($product['slug']) ?>" class="product-card-link">
                            <div class="product-card-image">
                                <img data-src="<?= productImageUrl($product['main_image']) ?>"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    alt="<?= e($product['name_ar']) ?>" loading="lazy">
                                <?php if ($product['is_featured']): ?>
                                    <span class="product-badge">مميز</span>
                                <?php endif; ?>
                                <div class="product-overlay">
                                    <span class="btn btn-outline btn-sm">عرض التفاصيل</span>
                                </div>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <p class="product-category">
                                <?= e($product['category_name_ar'] ?? '') ?>
                            </p>
                            <h3 class="product-name">
                                <?= e($product['name_ar']) ?>
                            </h3>
                            <p class="product-name-en">
                                <?= e($product['name_en']) ?>
                            </p>
                            <div class="product-footer">
                                <div class="product-price">
                                    <span class="price-current">
                                        <?= number_format($product['price'], 0) ?> ريال
                                    </span>
                                    <?php if ($product['old_price']): ?>
                                        <span class="price-old">
                                            <?= number_format($product['old_price'], 0) ?> ريال
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= buildWhatsAppLink($product['name_ar'], $product['price']) ?>" target="_blank"
                                    rel="noopener" class="btn btn-gold btn-sm wa-order-btn"
                                    data-product-id="<?= e($product['id']) ?>" data-product-name="<?= e($product['name_ar']) ?>"
                                    data-product-price="<?= e($product['price']) ?>"
                                    aria-label="اطلب <?= e($product['name_ar']) ?> عبر واتساب">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                    اطلب الآن
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center muted-text" style="padding:3rem 0;">لا توجد منتجات مميزة حالياً</p>
        <?php endif; ?>

        <div class="text-center" style="margin-top:3rem;">
            <a href="shop" class="btn btn-outline">عرض جميع العطور</a>
        </div>
    </div>
</section>

<!-- ====================================================
     WHY US SECTION
     ==================================================== -->
<section class="section"
    style="background:var(--color-bg-alt);border-top:1px solid var(--color-border);border-bottom:1px solid var(--color-border);"
    aria-label="لماذا نحن">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Our Promise</span>
            <h2 class="section-title ar-heading">لماذا براند؟</h2>
            <div class="section-divider"></div>
        </div>

        <div class="why-us-grid">
            <div class="why-card reveal reveal-delay-1">
                <span class="why-icon">🌺</span>
                <h3 class="why-title">مكونات فاخرة</h3>
                <p class="why-desc">نختار أرقى المكونات والخامات من حول العالم لضمان جودة لا مثيل لها</p>
            </div>
            <div class="why-card reveal reveal-delay-2">
                <span class="why-icon">✨</span>
                <h3 class="why-title">حصري وأصيل</h3>
                <p class="why-desc">كل عطر من مجموعتنا حصري ومميز، صُنع ليعبّر عن شخصية لا تتكرر</p>
            </div>
            <div class="why-card reveal reveal-delay-3">
                <span class="why-icon">📦</span>
                <h3 class="why-title">توصيل سريع</h3>
                <p class="why-desc">نضمن وصول طلبك في أسرع وقت ممكن مع عناية فائقة في التغليف</p>
            </div>
            <div class="why-card reveal reveal-delay-4">
                <span class="why-icon">💎</span>
                <h3 class="why-title">ضمان الجودة</h3>
                <p class="why-desc">نلتزم بأعلى معايير الجودة في كل قطرة عطر نقدمها لعملائنا</p>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================
     BRAND STATEMENT BANNER
     ==================================================== -->
<section class="section" aria-label="رسالة البراند">
    <div class="container">
        <div style="text-align:center;max-width:700px;margin:0 auto;" class="reveal">
            <span class="section-eyebrow">Brand Philosophy</span>
            <h2 class="section-title ar-heading" style="font-size:clamp(1.8rem,4vw,3rem);margin-bottom:1rem;">
                "العطر هو الجزء غير المرئي<br>من شخصيتك"
            </h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">
                نؤمن أن العطر ليس مجرد رائحة، بل هو تعبير عن الهوية والذوق والأناقة.
                كل قطرة من عطورنا تروي قصة فريدة.
            </p>
        </div>
    </div>
</section>

<!-- ====================================================
     TESTIMONIALS
     ==================================================== -->
<section class="section" style="background:var(--color-bg-alt);border-top:1px solid var(--color-border);"
    aria-label="آراء العملاء">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Customer Reviews</span>
            <h2 class="section-title ar-heading">ماذا يقول عملاؤنا</h2>
            <div class="section-divider"></div>
        </div>

        <div class="testimonials-slider">
            <div class="testimonial-card reveal reveal-delay-1">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"عطر عنبر الملوك رائع جداً، ثبات الرائحة مذهل وأحصل دائماً على مدح من
                    المحيطين بي. أنصح به بشدة!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">م</div>
                    <div>
                        <p class="author-name">محمد العتيبي</p>
                        <p class="author-title">الرياض، المملكة العربية السعودية</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-2">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"منذ أن تعرفت على براند، أصبحت عطورهم رفيقتي الدائمة. سر الجنة عطر استثنائي
                    يليق بكل مناسبة."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">س</div>
                    <div>
                        <p class="author-name">سارة القحطاني</p>
                        <p class="author-title">جدة، المملكة العربية السعودية</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-3">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"جودة العطور تفوق التوقعات والتغليف يعكس الفخامة الحقيقية. ذهب الصحراء أصبح
                    توقيعي الشخصي."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">خ</div>
                    <div>
                        <p class="author-name">خالد المنصور</p>
                        <p class="author-title">الدمام، المملكة العربية السعودية</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================
     CTA SECTION
     ==================================================== -->
<section class="section" aria-label="الطلب عبر واتساب">
    <div class="container">
        <div style="background:var(--color-bg-card);border:1px solid var(--color-border);border-radius:16px;padding:clamp(2rem,5vw,4rem);text-align:center;"
            class="reveal">
            <span class="section-eyebrow">Order via WhatsApp</span>
            <h2 class="section-title ar-heading" style="margin-bottom:1rem;">اطلب عطرك الآن</h2>
            <p style="color:var(--color-text-muted);margin-bottom:2rem;font-size:1rem;">
                تواصل معنا مباشرةً عبر واتساب ونحن نتولى الباقي
            </p>
            <a href="https://wa.me/<?= e($whatsappNum) ?>?text=مرحباً،+أريد+الاستفسار+عن+عطور+براند" target="_blank"
                rel="noopener" class="btn btn-whatsapp btn-lg">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                تواصل عبر واتساب الآن
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>