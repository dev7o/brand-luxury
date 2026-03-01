<?php
/**
 * Shop Page — Brand Luxury Perfumes
 * Products grid with category filter
 */

require_once __DIR__ . '/includes/functions.php';

$categories = getCategories();
$currentCat = htmlspecialchars($_GET['cat'] ?? '', ENT_QUOTES, 'UTF-8');

// Get products (filter or all)
$products = getProducts($currentCat ?: null);

$pageTitle = 'المتجر — ' . setting('site_name_ar', 'براند');
$metaDesc = 'تصفح مجموعتنا الكاملة من العطور الفاخرة';

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb" aria-label="مسار التنقل">
            <a href="./">الرئيسية</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">المتجر</span>
        </div>
        <h1 class="ar-heading" style="color:var(--color-cream);font-size:clamp(2rem,4vw,3rem);margin-bottom:0.5rem;">
            المتجر
        </h1>
        <p style="color:var(--color-text-muted);margin-top:0.5rem;">
            <?= count($products) ?> عطر متاح
        </p>
    </div>
</section>

<!-- Shop Section -->
<section class="section" aria-label="المنتجات">
    <div class="container">

        <!-- Category Filter Tabs -->
        <div class="category-tabs" role="tablist" aria-label="تصنيفات العطور">
            <button class="cat-tab <?= !$currentCat ? 'active' : '' ?>" data-cat="all" role="tab"
                aria-selected="<?= !$currentCat ? 'true' : 'false' ?>">
                الكل
            </button>
            <?php foreach ($categories as $cat): ?>
                <button class="cat-tab <?= $currentCat === $cat['slug'] ? 'active' : '' ?>"
                    data-cat="<?= e($cat['slug']) ?>" role="tab"
                    aria-selected="<?= $currentCat === $cat['slug'] ? 'true' : 'false' ?>">
                    <?= e($cat['name_ar']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
            <div class="products-grid" id="productsGrid">
                <?php foreach ($products as $i => $product): ?>
                    <article class="product-card reveal" data-category="<?= e($product['category_slug'] ?? '') ?>">
                        <a href="product?slug=<?= e($product['slug']) ?>" class="product-card-link"
                            aria-label="عرض تفاصيل <?= e($product['name_ar']) ?>">
                            <div class="product-card-image">
                                <img data-src="<?= productImageUrl($product['main_image']) ?>"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    alt="<?= e($product['name_ar']) ?>" loading="lazy">
                                <?php if ($product['is_featured']): ?>
                                    <span class="product-badge">مميز</span>
                                <?php endif; ?>
                                <?php if (!$product['is_active']): ?>
                                    <span class="product-badge"
                                        style="background:var(--color-bg-card);color:var(--color-text-muted);border:1px solid var(--color-border);">غير
                                        متوفر</span>
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
                            <h2 class="product-name">
                                <?= e($product['name_ar']) ?>
                            </h2>
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
                                <?php if ($product['is_active']): ?>
                                    <a href="<?= buildWhatsAppLink($product['name_ar'], $product['price']) ?>" target="_blank"
                                        rel="noopener" class="btn btn-gold btn-sm wa-order-btn"
                                        data-product-id="<?= e($product['id']) ?>" data-product-name="<?= e($product['name_ar']) ?>"
                                        data-product-price="<?= e($product['price']) ?>"
                                        aria-label="اطلب <?= e($product['name_ar']) ?> عبر واتساب">
                                        اطلب عبر واتساب
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-sm"
                                        style="background:var(--color-bg);border:1px solid var(--color-border);color:var(--color-text-muted);cursor:default;">غير
                                        متوفر</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center;padding:5rem 0;">
                <p style="font-size:3rem;margin-bottom:1rem;">🌿</p>
                <h3 class="ar-heading" style="color:var(--color-cream);margin-bottom:0.5rem;">لا توجد منتجات</h3>
                <p class="muted-text">لا توجد عطور في هذا التصنيف حالياً</p>
                <a href="shop" class="btn btn-outline" style="margin-top:1.5rem;">عرض الكل</a>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>