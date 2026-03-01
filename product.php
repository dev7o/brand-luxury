<?php
/**
 * Product Detail Page — Brand Luxury Perfumes
 */

require_once __DIR__ . '/includes/functions.php';

// Get product slug from URL
$slug = htmlspecialchars($_GET['slug'] ?? '', ENT_QUOTES, 'UTF-8');

if (!$slug) {
    header('Location: shop.php');
    exit;
}

$product = getProductBySlug($slug);

if (!$product) {
    http_response_code(404);
    $pageTitle = '404 — المنتج غير موجود';
    include __DIR__ . '/includes/header.php';
    echo '<div class="section" style="text-align:center;padding-top:calc(var(--header-h)+4rem);">
        <h1 class="ar-heading" style="color:var(--color-cream);">المنتج غير موجود</h1>
        <a href="shop" class="btn btn-gold" style="margin-top:2rem;">العودة للمتجر</a>
    </div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Parse gallery images
$gallery = [];
if (!empty($product['gallery'])) {
    $decoded = json_decode($product['gallery'], true);
    if (is_array($decoded))
        $gallery = $decoded;
}

// Get related products
$related = getRelatedProducts((int) $product['category_id'], (int) $product['id'], 4);

$pageTitle = e($product['name_ar']) . ' — ' . setting('site_name_ar', 'براند');
$metaDesc = mb_substr(strip_tags($product['description_ar'] ?? ''), 0, 160);
$whatsappLink = buildWhatsAppLink($product['name_ar'], $product['price']);

include __DIR__ . '/includes/header.php';
?>

<!-- Product Detail -->
<section class="product-detail" aria-label="تفاصيل المنتج">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="مسار التنقل">
            <a href="./">الرئيسية</a>
            <span class="breadcrumb-sep">›</span>
            <a href="shop">المتجر</a>
            <?php if ($product['category_slug']): ?>
                <span class="breadcrumb-sep">›</span>
                <a href="shop?cat=<?= e($product['category_slug']) ?>">
                    <?= e($product['category_name_ar']) ?>
                </a>
            <?php endif; ?>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">
                <?= e($product['name_ar']) ?>
            </span>
        </nav>

        <!-- Product Grid -->
        <div class="product-detail-grid">

            <!-- Gallery Column -->
            <div class="product-gallery">
                <div class="gallery-main" id="galleryMain">
                    <img src="<?= productImageUrl($product['main_image'], 'large') ?>"
                        alt="<?= e($product['name_ar']) ?>" id="mainGalleryImg" loading="eager">
                </div>
                <?php if (!empty($gallery)): ?>
                    <div class="gallery-thumbs" role="list" aria-label="صور إضافية">
                        <!-- Main image thumb -->
                        <div class="gallery-thumb active" role="listitem" tabindex="0">
                            <img src="<?= productImageUrl($product['main_image'], 'small') ?>" alt="الصورة الرئيسية">
                        </div>
                        <?php foreach ($gallery as $img): ?>
                            <div class="gallery-thumb" role="listitem" tabindex="0">
                                <img src="<?= productImageUrl($img, 'small') ?>" alt="صورة <?= e($product['name_ar']) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Column -->
            <div class="product-info">
                <div class="product-info-header">
                    <p class="product-category"
                        style="font-size:0.72rem;letter-spacing:0.2em;text-transform:uppercase;">
                        <?= e($product['category_name_ar'] ?? '') ?>
                    </p>
                    <h1 class="product-info-title ar-heading">
                        <?= e($product['name_ar']) ?>
                    </h1>
                    <p class="product-info-en">
                        <?= e($product['name_en']) ?>
                    </p>
                    <p class="product-info-price">
                        <?= number_format($product['price'], 0) ?> <span
                            style="font-size:1rem;font-weight:400;">ريال</span>
                        <?php if ($product['old_price']): ?>
                            <span
                                style="font-size:1rem;font-weight:400;color:var(--color-text-muted);text-decoration:line-through;margin-right:0.5rem;">
                                <?= number_format($product['old_price'], 0) ?> ريال
                            </span>
                        <?php endif; ?>
                    </p>
                    <?php if ($product['size_ml']): ?>
                        <p style="color:var(--color-text-muted);font-size:0.87rem;">
                            الحجم:
                            <?= e($product['size_ml']) ?> مل
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <?php if ($product['description_ar']): ?>
                    <div
                        style="margin-bottom:var(--space-md);padding-bottom:var(--space-md);border-bottom:1px solid var(--color-border);">
                        <p style="color:var(--color-text);line-height:1.9;font-size:0.95rem;">
                            <?= nl2br(e($product['description_ar'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Fragrance Notes -->
                <?php if ($product['top_notes_ar'] || $product['heart_notes_ar'] || $product['base_notes_ar']): ?>
                    <div class="notes-section">
                        <p class="notes-title">🌸 نوتات العطر — Fragrance Notes</p>
                        <div class="notes-row">
                            <?php if ($product['top_notes_ar']): ?>
                                <div class="note-item">
                                    <span class="note-label">النوتة الأولى<br>Top Notes</span>
                                    <span class="note-value">
                                        <?= e($product['top_notes_ar']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['heart_notes_ar']): ?>
                                <div class="note-item">
                                    <span class="note-label">النوتة القلبية<br>Heart Notes</span>
                                    <span class="note-value">
                                        <?= e($product['heart_notes_ar']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['base_notes_ar']): ?>
                                <div class="note-item">
                                    <span class="note-label">النوتة الأساسية<br>Base Notes</span>
                                    <span class="note-value">
                                        <?= e($product['base_notes_ar']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Order Button -->
                <div style="margin-top:var(--space-md);">
                    <?php if ($product['is_active']): ?>
                        <a href="<?= $whatsappLink ?>" target="_blank" rel="noopener"
                            class="btn btn-whatsapp btn-lg wa-order-btn" style="width:100%;justify-content:center;"
                            data-product-id="<?= e($product['id']) ?>" data-product-name="<?= e($product['name_ar']) ?>"
                            data-product-price="<?= e($product['price']) ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                            اطلب عبر واتساب
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info" style="text-align:center;">هذا المنتج غير متوفر حالياً</div>
                    <?php endif; ?>

                    <a href="shop" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:0.8rem;">
                        ← العودة للمتجر
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
            <div style="margin-top:var(--space-xl);">
                <div class="section-header reveal">
                    <span class="section-eyebrow">You May Also Like</span>
                    <h2 class="section-title ar-heading">منتجات مشابهة</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="products-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
                    <?php foreach ($related as $i => $rel): ?>
                        <article class="product-card reveal reveal-delay-<?= $i + 1 ?>">
                            <a href="product?slug=<?= e($rel['slug']) ?>">
                                <div class="product-card-image">
                                    <img data-src="<?= productImageUrl($rel['main_image']) ?>"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        alt="<?= e($rel['name_ar']) ?>" loading="lazy">
                                    <div class="product-overlay">
                                        <span class="btn btn-outline btn-sm">عرض</span>
                                    </div>
                                </div>
                            </a>
                            <div class="product-card-body">
                                <h3 class="product-name">
                                    <?= e($rel['name_ar']) ?>
                                </h3>
                                <div class="product-footer">
                                    <span class="price-current">
                                        <?= number_format($rel['price'], 0) ?> ريال
                                    </span>
                                    <a href="<?= buildWhatsAppLink($rel['name_ar'], $rel['price']) ?>" target="_blank"
                                        rel="noopener" class="btn btn-gold btn-sm wa-order-btn"
                                        data-product-id="<?= e($rel['id']) ?>" data-product-name="<?= e($rel['name_ar']) ?>"
                                        data-product-price="<?= e($rel['price']) ?>">اطلب</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>