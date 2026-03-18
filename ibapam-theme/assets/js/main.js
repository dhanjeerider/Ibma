/**
 * IBAPAM Theme — Main JavaScript
 * Handles: mobile nav, hero slider, back-to-top, ticker, filter tabs, AJAX search
 */
(function () {
    'use strict';

    /* ── DOM ready helper ── */
    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    ready(function () {

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MOBILE NAV
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        var overlay   = document.getElementById('mobile-nav-overlay');
        var mobileNav = document.getElementById('mobile-nav');
        var openBtn   = document.getElementById('menu-toggle');
        var closeBtn  = document.getElementById('mobile-nav-close');

        function openMobileNav() {
            if (mobileNav)  mobileNav.classList.add('open');
            if (overlay)    overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeMobileNav() {
            if (mobileNav)  mobileNav.classList.remove('open');
            if (overlay)    overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (openBtn)  openBtn.addEventListener('click', openMobileNav);
        if (closeBtn) closeBtn.addEventListener('click', closeMobileNav);
        if (overlay)  overlay.addEventListener('click', closeMobileNav);

        /* Mobile sub-menu toggles */
        document.querySelectorAll('.mobile-sub-toggle').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var sub = this.nextElementSibling;
                if (sub) sub.classList.toggle('open');
                var icon = this.querySelector('.sub-arrow');
                if (icon) icon.style.transform = sub && sub.classList.contains('open') ? 'rotate(90deg)' : '';
            });
        });

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           HERO SLIDER
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        var slides    = document.querySelectorAll('.hero-slide');
        var dots      = document.querySelectorAll('.hero-dot');
        var current   = 0;
        var autoTimer = null;

        function goToSlide(n) {
            slides.forEach(function (s) { s.classList.remove('active'); });
            dots.forEach(function (d) { d.classList.remove('active'); });
            current = (n + slides.length) % slides.length;
            if (slides[current]) slides[current].classList.add('active');
            if (dots[current])   dots[current].classList.add('active');
        }
        function startAuto() {
            stopAuto();
            if (slides.length > 1) {
                autoTimer = setInterval(function () { goToSlide(current + 1); }, 5000);
            }
        }
        function stopAuto() { clearInterval(autoTimer); }

        if (slides.length) {
            goToSlide(0);
            startAuto();

            var prevBtn = document.getElementById('hero-prev');
            var nextBtn = document.getElementById('hero-next');
            if (prevBtn) prevBtn.addEventListener('click', function () { stopAuto(); goToSlide(current - 1); startAuto(); });
            if (nextBtn) nextBtn.addEventListener('click', function () { stopAuto(); goToSlide(current + 1); startAuto(); });

            dots.forEach(function (dot, i) {
                dot.addEventListener('click', function () { stopAuto(); goToSlide(i); startAuto(); });
            });

            /* Swipe support */
            var startX = 0;
            var sliderEl = document.querySelector('.hero-slider');
            if (sliderEl) {
                sliderEl.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
                sliderEl.addEventListener('touchend', function (e) {
                    var diff = startX - e.changedTouches[0].clientX;
                    if (Math.abs(diff) > 50) { stopAuto(); goToSlide(diff > 0 ? current + 1 : current - 1); startAuto(); }
                }, { passive: true });
            }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           BACK TO TOP
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        var btt = document.getElementById('back-to-top');
        if (btt) {
            window.addEventListener('scroll', function () {
                btt.classList.toggle('show', window.scrollY > 300);
            }, { passive: true });
            btt.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           STICKY HEADER SHADOW
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        var header = document.getElementById('site-header');
        if (header) {
            window.addEventListener('scroll', function () {
                header.style.boxShadow = window.scrollY > 10 ? '0 4px 30px rgba(0,0,0,.9)' : '0 2px 20px rgba(0,0,0,.8)';
            }, { passive: true });
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           LIGHTBOX for screenshots
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        var screenImgs = document.querySelectorAll('.screenshots-grid img');
        if (screenImgs.length) {
            /* Build lightbox */
            var lb = document.createElement('div');
            lb.id = 'ibapam-lightbox';
            lb.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:99999;align-items:center;justify-content:center;';
            lb.innerHTML = '<button id="lb-close" style="position:absolute;top:15px;right:20px;background:#e50914;border:none;color:#fff;width:38px;height:38px;border-radius:50%;font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>'
                         + '<img id="lb-img" style="max-width:90vw;max-height:88vh;border-radius:8px;object-fit:contain;"/>';
            document.body.appendChild(lb);

            var lbImg = document.getElementById('lb-img');
            document.getElementById('lb-close').addEventListener('click', function () { lb.style.display = 'none'; });
            lb.addEventListener('click', function (e) { if (e.target === lb) lb.style.display = 'none'; });

            screenImgs.forEach(function (img) {
                img.addEventListener('click', function () {
                    lbImg.src = this.src.replace('w780', 'original');
                    lb.style.display = 'flex';
                });
            });
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           LAZY IMAGES FALLBACK
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        if (!('loading' in HTMLImageElement.prototype)) {
            var lazyImgs = document.querySelectorAll('img[loading="lazy"]');
            if ('IntersectionObserver' in window) {
                var obs = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            var img = entry.target;
                            if (img.dataset.src) img.src = img.dataset.src;
                            obs.unobserve(img);
                        }
                    });
                });
                lazyImgs.forEach(function (img) { obs.observe(img); });
            }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           IMAGE ERROR FALLBACK
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        document.querySelectorAll('.card-thumb img, .card-thumb-h img').forEach(function (img) {
            img.addEventListener('error', function () {
                this.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="450" viewBox="0 0 300 450"><rect fill="%231a1a1a" width="300" height="450"/><text fill="%23555" font-family="sans-serif" font-size="14" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle">No Image</text></svg>';
            });
        });

    }); // ready

})();
