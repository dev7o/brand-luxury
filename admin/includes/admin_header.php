<?php
/**
 * Admin Header Include
 * Brand Luxury Perfumes Admin Panel
 */
header('Content-Type: text/html; charset=utf-8');

if (!isset($pageTitle))
    $pageTitle = 'لوحة التحكم';
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

$navLinks = [
    ['label' => '🏠 الرئيسية', 'href' => url('admin/'), 'active' => $currentPage === 'index.php' && $currentDir === 'admin'],
    ['label' => '📦 المنتجات', 'href' => url('admin/') . 'products/', 'active' => $currentDir === 'products'],
    ['label' => '🗂 التصنيفات', 'href' => url('admin/') . 'categories/', 'active' => $currentDir === 'categories'],
    ['label' => '📋 الطلبات', 'href' => url('admin/') . 'orders/', 'active' => $currentDir === 'orders'],
    ['label' => '⚙️ الإعدادات', 'href' => url('admin/') . 'settings.php', 'active' => $currentPage === 'settings.php'],
    ['label' => '🌐 عرض الموقع', 'href' => url(), 'active' => false, 'target' => '_blank'],
    ['label' => '🚪 تسجيل الخروج', 'href' => url('admin/') . 'logout.php', 'active' => false, 'confirm' => 'هل تريد تسجيل الخروج؟'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?= e($pageTitle) ?> — إدارة براند</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Montserrat:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= url() ?>assets/css/admin.css">
    <style>
        /* ============================================================
       COMPLETE MOBILE RESET — إصلاح شامل للجوال
       ============================================================ */

        /* ── شريط التنقل العلوي للجوال ── */
        .mob-nav {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #111;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-family: 'Cairo', sans-serif;
        }

        /* الشريط الرئيسي */
        .mob-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            height: 56px;
        }

        .mob-brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #C6A75E;
            letter-spacing: .1em;
        }

        .mob-right {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .mob-add-btn {
            background: #C6A75E;
            color: #000;
            font-family: 'Cairo', sans-serif;
            font-size: .78rem;
            font-weight: 700;
            padding: .35rem .75rem;
            border-radius: 6px;
            text-decoration: none;
            white-space: nowrap;
        }

        .mob-menu-btn {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 7px;
            cursor: pointer;
            padding: 0;
        }

        .mob-menu-btn span {
            display: block;
            width: 20px;
            height: 2px;
            background: #C6A75E;
            border-radius: 2px;
            transition: .3s;
        }

        /* قائمة الروابط المنسدلة */
        .mob-dropdown {
            display: none;
            flex-direction: column;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding: .5rem 0;
            max-height: calc(100vh - 56px);
            overflow-y: auto;
        }

        .mob-dropdown.open {
            display: flex;
        }

        .mob-dropdown a {
            display: block;
            padding: .75rem 1.25rem;
            color: #aaa;
            font-size: .9rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            transition: background .15s;
        }

        .mob-dropdown a:hover,
        .mob-dropdown a.active {
            background: rgba(198, 167, 94, .1);
            color: #C6A75E;
        }

        /* ── إخفاء السايدبار في الجوال كلياً ── */
        @media (max-width: 900px) {

            /* إظهار شريط الجوال */
            .mob-nav {
                display: block;
            }

            /* إخفاء السايدبار تماماً */
            .admin-sidebar {
                display: none !important;
            }

            /* إخفاء التوب-بار الأصلي */
            .admin-topbar {
                display: none !important;
            }

            /* المحتوى يأخذ الشاشة كاملة */
            .admin-main {
                margin-right: 0 !important;
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 56px !important;
            }

            .admin-content {
                padding: 1rem !important;
            }

            .admin-two-col {
                grid-template-columns: 1fr !important;
            }

            .admin-form-row,
            .admin-form-row-3 {
                grid-template-columns: 1fr !important;
            }

            .admin-stats-grid {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        @media (max-width: 480px) {
            .admin-stats-grid {
                grid-template-columns: 1fr !important;
            }

            .admin-quick-actions {
                flex-direction: column !important;
            }

            .admin-quick-actions .admin-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- ═══════════════════════════════════════════
     شريط التنقل العلوي — للجوال فقط
     ═══════════════════════════════════════════ -->
    <nav class="mob-nav" id="mobNav">
        <div class="mob-topbar">
            <span class="mob-brand">BRAND</span>
            <div class="mob-right">
                <a href="<?= url('admin/') ?>products/add.php" class="mob-add-btn">+ منتج</a>
                <button class="mob-menu-btn" id="mobMenuBtn" onclick="toggleMobMenu()" aria-label="القائمة">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
        <div class="mob-dropdown" id="mobDropdown">
            <?php foreach ($navLinks as $link): ?>
                <a href="<?= $link['href'] ?>" <?= isset($link['target']) ? 'target="' . $link['target'] . '"' : '' ?>
                    <?= isset($link['confirm']) ? 'onclick="return confirm(\'' . $link['confirm'] . '\')"' : '' ?>
                    class="<?= $link['active'] ? 'active' : '' ?>">
                    <?= $link['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- ═══════════════════════════════════════════
     التخطيط الرئيسي — للديسك توب
     ═══════════════════════════════════════════ -->
    <div class="admin-wrapper">

        <!-- ===== SIDEBAR (desktop only) ===== -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-logo">
                <div>
                    <p class="sidebar-logo-text">BRAND</p>
                    <p class="sidebar-logo-sub">Admin Panel</p>
                </div>
            </div>
            <nav class="sidebar-nav">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?= $link['href'] ?>" <?= isset($link['target']) ? 'target="' . $link['target'] . '"' : '' ?>
                        <?= isset($link['confirm']) ? 'onclick="return confirm(\'' . $link['confirm'] . '\')"' : '' ?>
                        class="sidebar-link <?= $link['active'] ? 'active' : '' ?>">
                        <?= $link['label'] ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- ===== MAIN ===== -->
        <div class="admin-main">
            <!-- Top Bar (desktop only) -->
            <header class="admin-topbar">
                <div>
                    <p class="topbar-title"><?= e($pageTitle) ?></p>
                    <p class="topbar-welcome">مرحباً، <?= e(currentAdmin()['full_name']) ?></p>
                </div>
                <div class="topbar-actions">
                    <a href="<?= url('admin/') ?>products/add.php" class="admin-btn admin-btn-primary admin-btn-sm">+
                        منتج جديد</a>
                </div>
            </header>
            <!-- Content -->
            <div class="admin-content">