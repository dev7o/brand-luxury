/**
 * Brand Luxury Perfumes — Main JavaScript
 * Animations, interactions, mobile nav, scroll effects
 */

'use strict';

// =============================================
// 1. HEADER SCROLL EFFECT
// =============================================
const header = document.getElementById('site-header');
if (header) {
    const onScroll = () => {
        header.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
}

// =============================================
// 2. MOBILE NAVIGATION
// =============================================
const navToggle = document.getElementById('navToggle');
const siteNav = document.getElementById('siteNav');

if (navToggle && siteNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = siteNav.classList.toggle('open');
        navToggle.classList.toggle('active', isOpen);
        navToggle.setAttribute('aria-expanded', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on nav link click (mobile)
    siteNav.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            siteNav.classList.remove('open');
            navToggle.classList.remove('active');
            navToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!header.contains(e.target) && siteNav.classList.contains('open')) {
            siteNav.classList.remove('open');
            navToggle.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
}

// =============================================
// 3. SCROLL REVEAL ANIMATIONS
// =============================================
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal, .reveal-left').forEach(el => {
    revealObserver.observe(el);
});

// =============================================
// 4. HERO ENTRY ANIMATION
// =============================================
const hero = document.querySelector('.hero');
if (hero) {
    requestAnimationFrame(() => {
        setTimeout(() => hero.classList.add('loaded'), 100);
    });
}

// =============================================
// 5. LAZY IMAGE LOADING
// =============================================
const lazyImages = document.querySelectorAll('img[data-src]');
if (lazyImages.length) {
    const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imgObserver.unobserve(img);
                img.addEventListener('load', () => img.classList.add('loaded'));
            }
        });
    }, { rootMargin: '100px' });
    lazyImages.forEach(img => imgObserver.observe(img));
}

// =============================================
// 6. PRODUCT GALLERY (Product Detail Page)
// =============================================
const galleryMain = document.querySelector('.gallery-main img');
const thumbs = document.querySelectorAll('.gallery-thumb');

thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
        // Update main image
        if (galleryMain) {
            galleryMain.style.opacity = '0';
            setTimeout(() => {
                galleryMain.src = thumb.querySelector('img').src;
                galleryMain.style.opacity = '1';
            }, 200);
        }
        // Update active state
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    });
});

// Gallery main image transition
if (galleryMain) {
    galleryMain.style.transition = 'opacity 0.25s ease';
}

// =============================================
// 7. CATEGORY FILTER TABS (Shop Page)
// =============================================
const catTabs = document.querySelectorAll('.cat-tab');
const productItems = document.querySelectorAll('[data-category]');

if (catTabs.length && productItems.length) {
    catTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const cat = tab.dataset.cat;

            // Update active tab
            catTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Filter products
            productItems.forEach(item => {
                const show = !cat || cat === 'all' || item.dataset.category === cat;
                if (show) {
                    item.style.display = '';
                    setTimeout(() => item.classList.add('visible'), 10);
                } else {
                    item.classList.remove('visible');
                    setTimeout(() => { item.style.display = 'none'; }, 300);
                }
            });

            // Update URL without reload
            const url = new URL(window.location);
            if (cat && cat !== 'all') {
                url.searchParams.set('cat', cat);
            } else {
                url.searchParams.delete('cat');
            }
            history.replaceState({}, '', url);
        });
    });

    // Activate tab from URL param on load
    const urlCat = new URL(window.location).searchParams.get('cat');
    if (urlCat) {
        const matchTab = document.querySelector(`.cat-tab[data-cat="${urlCat}"]`);
        if (matchTab) matchTab.click();
    }
}

// =============================================
// 8. FLASH MESSAGES AUTO-DISMISS
// =============================================
document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
    const delay = parseInt(alert.dataset.autoDismiss) || 4000;
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, delay);
});

// =============================================
// 9. SMOOTH SCROLL FOR ANCHOR LINKS
// =============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        const target = document.querySelector(anchor.getAttribute('href'));
        if (target) {
            e.preventDefault();
            const headerOffset = parseInt(getComputedStyle(document.documentElement)
                .getPropertyValue('--header-h')) || 80;
            const top = target.getBoundingClientRect().top + window.scrollY - headerOffset - 20;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});

// =============================================
// 10. WHATSAPP ORDER SAVE (before redirect)
// =============================================
document.querySelectorAll('.wa-order-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const productId = btn.dataset.productId;
        const productName = btn.dataset.productName;
        const productPrice = btn.dataset.productPrice;

        // Save order in background (fire-and-forget)
        if (productId) {
            fetch(BASE_URL + 'api/save_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, product_name: productName, product_price: productPrice })
            }).catch(() => { }); // Ignore errors, WhatsApp redirect continues
        }
    });
});

// =============================================
// 11. NUMBER COUNTER ANIMATION (Dashboard/Stats)
// =============================================
function animateCounter(el) {
    const target = parseInt(el.dataset.target) || 0;
    const duration = 1500;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = Math.round(current).toLocaleString('ar-SA');
        if (current >= target) clearInterval(timer);
    }, 16);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(entry.target);
            counterObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('[data-target]').forEach(el => counterObserver.observe(el));

// =============================================
// 12. IMAGE ZOOM ON HOVER (Product Cards)
// =============================================
// Handled via CSS — no JS needed

console.log('%cBrand Luxury Perfumes', 'color:#C6A75E;font-size:1.2rem;font-weight:bold;');
